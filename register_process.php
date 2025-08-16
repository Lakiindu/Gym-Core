<?php
session_start(); // Start session at the top

// DB Connection
$host = 'localhost';
$db = 'gym_db';
$user = 'postgres'; // Default PostgreSQL user unless you changed it
$pass = 'lakindu';

try {
    $pdo = new PDO("pgsql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    $_SESSION['register_error'] = "Database connection failed.";
    header("Location: register.php");
    exit;
}

// Get form data
$username = trim($_POST['username'] ?? '');
$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';
$confirm_password = $_POST['confirm_password'] ?? '';

// Validate input
if (empty($username) || empty($email) || empty($password) || empty($confirm_password)) {
    $_SESSION['register_error'] = "Please fill in all fields.";
    header("Location: register.php");
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['register_error'] = "Invalid email format.";
    header("Location: register.php");
    exit;
}

if ($password !== $confirm_password) {
    $_SESSION['register_error'] = "Passwords do not match.";
    header("Location: register.php");
    exit;
}

// Hash password
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

// Check for existing user
$stmt = $pdo->prepare("SELECT id FROM users WHERE username = :username OR email = :email");
$stmt->execute(['username' => $username, 'email' => $email]);

if ($stmt->fetch()) {
    $_SESSION['register_error'] = "Username or email already exists.";
    header("Location: register.php");
    exit;
}

// Insert user with role
try {
    $pdo->beginTransaction();

    // Insert into users table
    $stmt = $pdo->prepare("INSERT INTO users (username, email, password) VALUES (:username, :email, :password) RETURNING id");
    $stmt->execute([
        'username' => $username,
        'email' => $email,
        'password' => $hashedPassword
    ]);
    $user_id = $stmt->fetchColumn();

    // Insert default role
    $stmt = $pdo->prepare("INSERT INTO user_roles (user_id, role) VALUES (:user_id, :role)");
    $stmt->execute([
        'user_id' => $user_id,
        'role' => 'member'
    ]);

    $pdo->commit();

    // Registration success - redirect to login with success query
    header("Location: login.php?success=1");
    exit;

} catch (Exception $e) {
    $pdo->rollBack();
    $_SESSION['register_error'] = "Registration failed: " . $e->getMessage();
    header("Location: register.php");
    exit;
}
?>
