<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: login.php");
    exit;
}

// DB credentials
$host = "localhost";
$dbname = "gym_db";
$user = "postgres";
$password = "lakindu";

// Connect to PostgreSQL
try {
    $pdo = new PDO("pgsql:host=$host;dbname=$dbname", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    $_SESSION['error'] = "Database connection failed: " . $e->getMessage();
    header("Location: login.php");
    exit;
}

// Get form data
$email = trim($_POST['email'] ?? '');
$passwordInput = $_POST['password'] ?? '';

// Validate input
if (empty($email) || empty($passwordInput)) {
    $_SESSION['error'] = "Email and password are required.";
    header("Location: login.php");
    exit;
}

try {
    // Check if user exists
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");
    $stmt->execute(['email' => $email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($passwordInput, $user['password'])) {
        // Fetch role from user_roles table or default to 'member'
        $roleStmt = $pdo->prepare("SELECT role FROM user_roles WHERE user_id = :user_id");
        $roleStmt->execute(['user_id' => $user['id']]);
        $roleRow = $roleStmt->fetch(PDO::FETCH_ASSOC);
        $role = $roleRow ? $roleRow['role'] : 'member';

        // Set session variables
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['role'] = $role;

        // Redirect user based on role
        if ($role === 'admin') {
            header("Location: admin_dashboard.php");
        } else {
            header("Location: user_dashboard.php");
        }
        exit;
    } else {
        $_SESSION['error'] = "Invalid email or password.";
        header("Location: login.php");
        exit;
    }
} catch (PDOException $e) {
    $_SESSION['error'] = "Error: " . $e->getMessage();
    header("Location: login.php");
    exit;
}
?>
