<?php
session_start();
include 'db.php'; // <-- make sure this connects to your database

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $email = trim($_POST['email']);

  if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['error'] = "Invalid email format.";
    header("Location: subscribe.php");
    exit;
  }

  // Check if email already subscribed
  $check = $conn->prepare("SELECT * FROM subscribers WHERE email = ?");
  $check->execute([$email]);
  if ($check->rowCount() > 0) {
    $_SESSION['error'] = "You're already subscribed!";
    header("Location: subscribe.php");
    exit;
  }

  // Insert new email
  $stmt = $conn->prepare("INSERT INTO subscribers (email) VALUES (?)");
  if ($stmt->execute([$email])) {
    $_SESSION['success'] = "🎉 Thank you for subscribing!";
  } else {
    $_SESSION['error'] = "Something went wrong. Please try again.";
  }
}

header("Location: subscribe.php");
exit;
