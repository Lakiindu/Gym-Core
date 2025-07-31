<?php
// DB Connection
$host = 'localhost';
$db = 'gym_db';
$user = 'postgres'; // Default PostgreSQL user unless you changed it
$pass = 'lakindu';

try {
    $pdo = new PDO("pgsql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Get form data
$username = trim($_POST['username']);
$email = trim($_POST['email']);
$password = $_POST['password'];
$confirm_password = $_POST['confirm_password'];

// Validate input
if (empty($username) || empty($email) || empty($password) || empty($confirm_password)) {
    die("Please fill in all fields.");
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    die("Invalid email format.");
}

if ($password !== $confirm_password) {
    die("Passwords do not match.");
}

// Hash password
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

// Check for existing user
$stmt = $pdo->prepare("SELECT id FROM users WHERE username = :username OR email = :email");
$stmt->execute(['username' => $username, 'email' => $email]);

if ($stmt->fetch()) {
    die("Username or email already exists.");
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

    // Redirect
    header("Location: login.php?success=1");
    exit;

} catch (Exception $e) {
    $pdo->rollBack();
    die("Registration failed: " . $e->getMessage());
}
?>
