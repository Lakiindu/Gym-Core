<?php
session_start();
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'trainer') {
    header("Location: login.php");
    exit;
}

$trainer_id = (int)$_SESSION['user_id'];

$host = "localhost";
$dbname = "gym_db";
$user = "postgres";
$password = "lakindu";

try {
    $pdo = new PDO("pgsql:host=$host;dbname=$dbname", $user, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
} catch (PDOException $e) {
    die("DB connection failed: " . $e->getMessage());
}

// Handle status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['booking_id'], $_POST['action'])) {
    $booking_id = (int)$_POST['booking_id'];
    $action = $_POST['action'];
    if (!in_array($action, ['accepted', 'rejected'])) $action = 'pending';

    $stmt = $pdo->prepare("UPDATE trainer_bookings SET status=:status WHERE id=:id AND trainer_id=:tid");
    $stmt->execute([
        ':status' => $action,
        ':id' => $booking_id,
        ':tid' => $trainer_id
    ]);
}

// Fetch bookings for this trainer
$bookings = $pdo->prepare("
    SELECT tb.id, tb.booking_date, tb.booking_time, tb.status, u.username AS member_name
    FROM trainer_bookings tb
    JOIN users u ON u.id = tb.user_id
    WHERE tb.trainer_id = :tid
    ORDER BY tb.booking_date ASC, tb.booking_time ASC
");
$bookings->execute([':tid' => $trainer_id]);
$bookings = $bookings->fetchAll(PDO::FETCH_ASSOC);

// Fetch assigned clients
$clients = $pdo->prepare("
    SELECT tc.client_id, u.username AS client_name, u.email
    FROM trainer_clients tc
    JOIN users u ON u.id = tc.client_id
    WHERE tc.trainer_id = :tid
");
$clients->execute([':tid' => $trainer_id]);
$clients = $clients->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Trainer Dashboard</title>
<!-- TailwindCSS -->
<script src="https://cdn.tailwindcss.com"></script>
<!-- Google Fonts -->
<link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
<!-- AOS -->
<link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
<style>
    body { font-family: 'Roboto', sans-serif; background: linear-gradient(135deg,#667eea,#764ba2); }
    .card { backdrop-filter: blur(10px); background: rgba(255,255,255,0.2); transition: all 0.3s ease; }
    .card:hover { transform: translateY(-5px) scale(1.02); box-shadow: 0 10px 25px rgba(0,0,0,0.2); }
</style>
</head>
<body class="min-h-screen">

<!-- Navbar -->
<nav class="bg-gray-900/70 backdrop-blur-md text-white shadow-md sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 py-3 flex justify-between items-center">
        <a href="index.php" class="text-2xl font-bold hover:text-blue-400 transition">Gym Core</a>
        <div class="space-x-4">
            <a href="index.php" class="hover:text-blue-400 transition">Home</a>
            <a href="logout.php" class="hover:text-red-400 transition">Logout</a>
        </div>
    </div>
</nav>

<div class="max-w-7xl mx-auto p-6">

    <h1 class="text-5xl font-bold mb-8 text-center text-white drop-shadow-lg">Trainer Bookings</h1>

    <?php if($bookings): ?>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <?php foreach($bookings as $b): ?>
                <div class="card rounded-xl p-6 border-l-4
                    <?php 
                        echo $b['status'] === 'accepted' ? 'border-green-400' : ($b['status']==='rejected' ? 'border-red-400' : 'border-yellow-400');
                    ?>
                    " data-aos="fade-up" data-aos-duration="800">
                    <h3 class="text-2xl font-bold text-white drop-shadow-md"><?= htmlspecialchars($b['member_name']) ?></h3>
                    <p class="text-white mt-2"><strong>Date:</strong> <?= htmlspecialchars($b['booking_date']) ?></p>
                    <p class="text-white"><strong>Time:</strong> <?= htmlspecialchars($b['booking_time']) ?></p>
                    <p class="text-white mt-1"><strong>Status:</strong> 
                        <span class="<?php 
                            echo $b['status'] === 'accepted' ? 'text-green-300' : ($b['status']==='rejected' ? 'text-red-300' : 'text-yellow-300');
                        ?>">
                        <?= htmlspecialchars(ucfirst($b['status'])) ?>
                        </span>
                    </p>

                    <?php if($b['status'] === 'pending'): ?>
                        <form method="POST" class="mt-4 flex space-x-3">
                            <input type="hidden" name="booking_id" value="<?= $b['id'] ?>">
                            <button type="submit" name="action" value="accepted" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-xl transition">Accept</button>
                            <button type="submit" name="action" value="rejected" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-xl transition">Reject</button>
                        </form>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p class="text-white text-center text-xl mt-12">No bookings available.</p>
    <?php endif; ?>

    <h1 class="text-5xl font-bold mb-8 text-center text-white drop-shadow-lg mt-16">Assigned Clients</h1>

    <?php if($clients): ?>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <?php foreach($clients as $c): ?>
                <div class="card rounded-xl p-6 border-l-4 border-blue-400" data-aos="fade-up" data-aos-duration="800">
                    <h3 class="text-2xl font-bold text-white drop-shadow-md"><?= htmlspecialchars($c['client_name']) ?></h3>
                    <p class="text-white mt-2"><strong>Email:</strong> <?= htmlspecialchars($c['email']) ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p class="text-white text-center text-xl mt-12">No clients assigned yet.</p>
    <?php endif; ?>

</div>

<!-- AOS -->
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
  AOS.init();
</script>

</body>
</html>
