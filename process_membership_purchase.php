<?php
session_start();

if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'member') {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: buy_membership.php");
    exit;
}

$host = "localhost";
$dbname = "gym_db";
$user = "postgres";
$password = "lakindu";

try {
    $conn = new PDO("pgsql:host=$host;dbname=$dbname", $user, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Sanitize and validate inputs
    $membership_id = filter_input(INPUT_POST, 'membership_id', FILTER_VALIDATE_INT);
    $card_name = trim($_POST['card_name'] ?? '');
    $card_number = preg_replace('/\D/', '', $_POST['card_number'] ?? '');
    $expiry_date = trim($_POST['expiry_date'] ?? '');
    $cvv = trim($_POST['cvv'] ?? '');

    // Basic validations
    if (!$membership_id || empty($card_name) || strlen($card_number) !== 16 || !preg_match('/^(0[1-9]|1[0-2])\/\d{2}$/', $expiry_date) || strlen($cvv) !== 3) {
        $_SESSION['error_message'] = "Invalid payment or membership details.";
        header("Location: buy_membership.php");
        exit;
    }

    // Check membership exists and is active
    $stmt = $conn->prepare("SELECT * FROM buy_membership WHERE id = :id AND is_active = TRUE");
    $stmt->execute(['id' => $membership_id]);
    $membership = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$membership) {
        $_SESSION['error_message'] = "Selected membership plan not found.";
        header("Location: buy_membership.php");
        exit;
    }

    // Store the purchase details - Assuming table membership_purchases:
    // Columns: id (serial), user_id (int), membership_id (int), purchase_date (timestamp), price (numeric), payment_card_name, payment_card_number (hashed or masked), payment_expiry, payment_cvv (don't store CVV in real world!)
    
    // WARNING: Never store CVV in real payment systems. Here it's just a demo.
    
    // Mask card number for storage - store last 4 digits only
    $masked_card_number = str_repeat('*', 12) . substr($card_number, -4);

    $stmt = $conn->prepare("INSERT INTO membership_purchases (user_id, membership_id, purchase_date, price, payment_card_name, payment_card_number, payment_expiry) VALUES (:user_id, :membership_id, NOW(), :price, :card_name, :card_number, :expiry)");
    $stmt->execute([
        'user_id' => $_SESSION['user_id'],
        'membership_id' => $membership_id,
        'price' => $membership['price'],
        'card_name' => $card_name,
        'card_number' => $masked_card_number,
        'expiry' => $expiry_date
    ]);

    $_SESSION['success_message'] = "Membership purchased successfully! Thank you.";
    header("Location: buy_membership.php");
    exit;
} catch (PDOException $e) {
    $_SESSION['error_message'] = "Database error: " . $e->getMessage();
    header("Location: buy_membership.php");
    exit;
}
