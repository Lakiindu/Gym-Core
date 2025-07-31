<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

require 'db.php'; // Your PDO connection file

$user_id = $_SESSION['user_id'];

$username = trim($_POST['username'] ?? '');
$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';
$confirm_password = $_POST['confirm_password'] ?? '';

// Basic validation
if (empty($username) || empty($email)) {
    $_SESSION['update_error'] = "Username and Email cannot be empty.";
    header("Location: profile.php");
    exit();
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['update_error'] = "Invalid email format.";
    header("Location: profile.php");
    exit();
}

if ($password !== '' || $confirm_password !== '') {
    if ($password !== $confirm_password) {
        $_SESSION['update_error'] = "Passwords do not match.";
        header("Location: profile.php");
        exit();
    }
    if (strlen($password) < 6) {
        $_SESSION['update_error'] = "Password must be at least 6 characters.";
        header("Location: profile.php");
        exit();
    }
}

// Check if username or email already exist for other users
$stmt = $pdo->prepare("SELECT id FROM users WHERE (username = ? OR email = ?) AND id != ?");
$stmt->execute([$username, $email, $user_id]);
if ($stmt->fetch()) {
    $_SESSION['update_error'] = "Username or Email already taken by another user.";
    header("Location: profile.php");
    exit();
}

// Prepare update query
if ($password !== '') {
    $hashed_password = password_hash($password, PASSWORD_BCRYPT);
    $stmt = $pdo->prepare("UPDATE users SET username = ?, email = ?, password = ? WHERE id = ?");
    $updated = $stmt->execute([$username, $email, $hashed_password, $user_id]);
} else {
    $stmt = $pdo->prepare("UPDATE users SET username = ?, email = ? WHERE id = ?");
    $updated = $stmt->execute([$username, $email, $user_id]);
}

if ($updated) {
    $_SESSION['update_success'] = "Profile updated successfully.";
} else {
    $_SESSION['update_error'] = "Failed to update profile. Please try again.";
}

header("Location: profile.php");
exit();
