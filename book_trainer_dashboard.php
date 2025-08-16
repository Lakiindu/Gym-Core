<?php
session_start(); 
// Start PHP session to track logged-in users

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
// Access control: if user not logged in, redirect to login page

$host = "localhost";
$dbname = "gym_db";
$user = "postgres";
$password = "lakindu";
// Database connection parameters

try {
    $pdo = new PDO("pgsql:host=$host;dbname=$dbname", $user, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
// Create PDO connection to PostgreSQL with error handling

$member_id = (int)$_SESSION['user_id'];
// Store logged-in member's ID for use in queries

// Handle booking submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['trainer_id'], $_POST['booking_date'], $_POST['booking_time'])) {
    $trainer_id = (int)$_POST['trainer_id'];
    $booking_date = $_POST['booking_date'];
    $booking_time = $_POST['booking_time'];
    // Get form inputs

    // Check if trainer is already booked at selected date/time
    $chk = $pdo->prepare("SELECT 1 FROM trainer_bookings WHERE trainer_id=:tid AND booking_date=:bdate AND booking_time=:btime");
    $chk->execute([
        ':tid' => $trainer_id,
        ':bdate' => $booking_date,
        ':btime' => $booking_time
    ]);
    if ($chk->fetch()) {
        $error = "Trainer already booked at this time!";
    } else {
        // Insert new booking if no conflict
        $stmt = $pdo->prepare("INSERT INTO trainer_bookings (user_id, trainer_id, booking_date, booking_time) VALUES (:uid, :tid, :bdate, :btime)");
        $stmt->execute([
            ':uid' => $member_id,
            ':tid' => $trainer_id,
            ':bdate' => $booking_date,
            ':btime' => $booking_time
        ]);
        $success = "Booking successful!";
    }
}

// Fetch list of trainers for dropdown
$trainers = $pdo->query("
    SELECT u.id, u.username
    FROM users u
    JOIN user_roles ur ON ur.user_id = u.id
    WHERE ur.role = 'trainer'
")->fetchAll(PDO::FETCH_ASSOC);

// Fetch all bookings for logged-in member
$bookings = $pdo->prepare("
    SELECT tb.id, tb.booking_date, tb.booking_time, u.username AS trainer_name
    FROM trainer_bookings tb
    JOIN users u ON u.id = tb.trainer_id
    WHERE tb.user_id = :uid
    ORDER BY tb.booking_date DESC, tb.booking_time DESC
");
$bookings->execute([':uid' => $member_id]);
$bookings = $bookings->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Book Trainer Dashboard</title>
<script src="https://cdn.tailwindcss.com"></script>
<style>
    body {
        background: linear-gradient(to right, #1f1c2c, #928dab);
    }
    /* Glassmorphism card style */
    .glass-card {
        backdrop-filter: blur(10px);
        background: rgba(255, 255, 255, 0.1);
        border: 1px solid rgba(255, 255, 255, 0.2);
    }
    /* Gradient button style */
    .glass-button {
        background: linear-gradient(90deg, #667eea, #764ba2);
    }
</style>
</head>
<body class="min-h-screen font-sans text-white">
<!-- Navbar -->
<nav class="bg-gradient-to-r from-purple-700 via-pink-600 to-red-500 shadow-lg">
    <div class="max-w-7xl mx-auto px-4 py-3 flex justify-between items-center">
        <a href="index.php" class="text-2xl font-bold hover:text-yellow-300 transition">Gym Core</a>
        <!-- Navigation links -->
        <div class="space-x-4">
            <a href="user_dashboard.php" class="hover:text-yellow-300 transition">User dashboard</a>
            <a href="logout.php" class="hover:text-yellow-300 transition">Logout</a>
        </div>
    </div>
</nav>

<div class="max-w-6xl mx-auto p-6">
    <h1 class="text-4xl font-bold mb-6 text-center text-yellow-200">Book a Trainer</h1>

    <!-- Error / Success messages -->
    <?php if(isset($error)): ?>
        <div class="bg-red-600 bg-opacity-30 border border-red-400 text-red-200 px-4 py-3 rounded mb-6">
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>
    <?php if(isset($success)): ?>
        <div class="bg-green-600 bg-opacity-30 border border-green-400 text-green-200 px-4 py-3 rounded mb-6">
            <?= htmlspecialchars($success) ?>
        </div>
    <?php endif; ?>

    <!-- Booking Form -->
    <div class="glass-card shadow-lg rounded-xl p-6 mb-8">
        <h2 class="text-2xl font-semibold mb-4 text-yellow-200">New Booking</h2>
        <form method="POST" class="space-y-4">
            <!-- Trainer selection -->
            <div>
                <label for="trainer_id" class="block font-medium mb-1 text-yellow-100">Select Trainer</label>
                <select name="trainer_id" id="trainer_id" class="w-full border border-yellow-200 rounded-lg p-2 focus:ring-2 focus:ring-yellow-300 bg-transparent text-white">
                    <?php foreach($trainers as $t): ?>
                        <option value="<?= $t['id'] ?>"><?= htmlspecialchars($t['username']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Date picker -->
            <div>
                <label for="booking_date" class="block font-medium mb-1 text-yellow-100">Select Date</label>
                <input type="date" name="booking_date" id="booking_date" class="w-full border border-yellow-200 rounded-lg p-2 focus:ring-2 focus:ring-yellow-300 bg-transparent text-white" required>
            </div>

            <!-- Time picker -->
            <div>
                <label for="booking_time" class="block font-medium mb-1 text-yellow-100">Select Time</label>
                <input type="time" name="booking_time" id="booking_time" class="w-full border border-yellow-200 rounded-lg p-2 focus:ring-2 focus:ring-yellow-300 bg-transparent text-white" required>
            </div>

            <!-- Submit button -->
            <button type="submit" class="glass-button hover:opacity-90 text-white font-semibold px-6 py-2 rounded-lg transition">Book Trainer</button>
        </form>
    </div>

    <!-- Bookings List -->
    <h2 class="text-3xl font-semibold mb-4 text-yellow-200">Your Bookings</h2>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <?php if($bookings): ?>
            <?php foreach($bookings as $b):
                $booking_date = new DateTime($b['booking_date']);
                $today = new DateTime();
                $is_past = $booking_date < $today;
            ?>
                <div class="glass-card shadow-md rounded-xl p-4 border-l-4 border-yellow-400 hover:shadow-xl transition <?= $is_past ? 'opacity-50' : '' ?>">
                    <h3 class="text-xl font-bold"><?= htmlspecialchars($b['trainer_name']) ?></h3>
                    <p class="mt-1"><strong>Date:</strong> <?= htmlspecialchars($b['booking_date']) ?></p>
                    <p><strong>Time:</strong> <?= htmlspecialchars($b['booking_time']) ?></p>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="text-yellow-200 col-span-full">You have no bookings yet.</p>
        <?php endif; ?>
    </div>
</div>
</body>
</html>
