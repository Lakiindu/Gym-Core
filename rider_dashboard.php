<?php
session_start();
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'rider') {
    header("Location: login.php");
    exit;
}

$rider_id = (int)$_SESSION['user_id'];

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

// Handle picking a delivery
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['pick_delivery'])) {
    $delivery_id = (int)$_POST['delivery_id'];
    $stmt = $pdo->prepare("UPDATE deliveries SET rider_id = :rid WHERE id = :did AND rider_id IS NULL");
    $stmt->execute([':rid' => $rider_id, ':did' => $delivery_id]);
    header("Location: rider_dashboard.php");
    exit;
}

// Handle marking as delivered
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['deliver_delivery'])) {
    $delivery_id = (int)$_POST['delivery_id'];
    $stmt = $pdo->prepare("UPDATE deliveries SET delivery_status = 'Delivered' WHERE id = :did AND rider_id = :rid");
    $stmt->execute([':did' => $delivery_id, ':rid' => $rider_id]);
    header("Location: rider_dashboard.php");
    exit;
}

// Counters
$totalAssignedStmt = $pdo->prepare("SELECT COUNT(*) FROM deliveries WHERE rider_id = :rid AND delivery_status='In Transit'");
$totalAssignedStmt->execute([':rid'=>$rider_id]);
$totalAssigned = $totalAssignedStmt->fetchColumn();

$totalDeliveredStmt = $pdo->prepare("SELECT COUNT(*) FROM deliveries WHERE rider_id = :rid AND delivery_status='Delivered'");
$totalDeliveredStmt->execute([':rid'=>$rider_id]);
$totalDelivered = $totalDeliveredStmt->fetchColumn();

$inTransitStmt = $pdo->prepare("SELECT COUNT(*) FROM deliveries WHERE delivery_status='In Transit' AND rider_id IS NULL");
$inTransitStmt->execute();
$inTransit = $inTransitStmt->fetchColumn();

// Fetch current deliveries
$deliveries = $pdo->prepare("
    SELECT d.id, d.delivery_address, d.delivery_status, d.delivery_date, o.id AS order_id, u.username AS member_name, d.rider_id
    FROM deliveries d
    JOIN orders o ON o.id = d.order_id
    JOIN users u ON u.id = o.user_id
    WHERE d.delivery_status = 'In Transit' AND (d.rider_id IS NULL OR d.rider_id = :rid)
    ORDER BY d.delivery_date ASC
");
$deliveries->execute([':rid' => $rider_id]);
$deliveries = $deliveries->fetchAll(PDO::FETCH_ASSOC);

// Fetch delivery history
$history = $pdo->prepare("
    SELECT d.id, d.delivery_address, d.delivery_status, d.delivery_date, o.id AS order_id, u.username AS member_name
    FROM deliveries d
    JOIN orders o ON o.id = d.order_id
    JOIN users u ON u.id = o.user_id
    WHERE d.rider_id = :rid AND d.delivery_status='Delivered'
    ORDER BY d.delivery_date DESC
");
$history->execute([':rid'=>$rider_id]);
$history = $history->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Rider Dashboard - GYM Core</title>
<script src="https://cdn.tailwindcss.com"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
<script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
<style>
body { font-family: 'Poppins', sans-serif; }
.bg-glass { background: rgba(255,255,255,0.25); backdrop-filter: blur(15px); }
.section-title { 
    @apply text-2xl font-bold text-gray-800 mb-6 relative;
}
.section-title::after {
    content: "";
    display: block;
    width: 60px;
    height: 4px;
    background: linear-gradient(to right, #7c3aed, #f97316);
    margin-top: 6px;
    border-radius: 2px;
}
</style>
</head>
<body class="bg-gradient-to-br from-indigo-100 via-pink-50 to-yellow-50 min-h-screen">

<!-- Navbar -->
<nav class="bg-gray-900 bg-opacity-90 backdrop-blur-md text-white shadow-lg fixed w-full z-10">
    <div class="max-w-7xl mx-auto px-6 py-3 flex justify-between items-center">
        <a href="index.php" class="text-2xl font-bold bg-gradient-to-r from-purple-400 to-pink-400 bg-clip-text text-transparent">Gym Core Rider</a>
        <div class="space-x-6 font-medium">
            <a href="index.php" class="hover:text-yellow-400 transition">Home</a>
            <a href="logout.php" class="hover:text-red-400 transition">Logout</a>
        </div>
    </div>
</nav>

<div class="max-w-7xl mx-auto p-6 pt-28">

    <!-- Page Title -->
    <h1 class="text-4xl font-extrabold mb-12 text-center text-gray-800 animate__animated animate__fadeInDown tracking-tight">
        🚴 Rider Dashboard
    </h1>

    <!-- Counters -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-16">
        <div class="bg-glass p-8 rounded-2xl shadow-lg text-center hover:scale-105 transition duration-300">
            <i class="fas fa-truck text-4xl text-yellow-500 mb-3"></i>
            <h2 class="text-lg font-semibold text-gray-700">Assigned</h2>
            <p class="text-4xl font-extrabold text-yellow-600 mt-2"><?= $totalAssigned ?></p>
        </div>
        <div class="bg-glass p-8 rounded-2xl shadow-lg text-center hover:scale-105 transition duration-300">
            <i class="fas fa-box-open text-4xl text-blue-500 mb-3"></i>
            <h2 class="text-lg font-semibold text-gray-700">Available</h2>
            <p class="text-4xl font-extrabold text-blue-600 mt-2"><?= $inTransit ?></p>
        </div>
        <div class="bg-glass p-8 rounded-2xl shadow-lg text-center hover:scale-105 transition duration-300">
            <i class="fas fa-check-circle text-4xl text-green-500 mb-3"></i>
            <h2 class="text-lg font-semibold text-gray-700">Delivered</h2>
            <p class="text-4xl font-extrabold text-green-600 mt-2"><?= $totalDelivered ?></p>
        </div>
    </div>

    <!-- Current Deliveries -->
    <section class="mb-16">
        <h2 class="section-title">📦 Current Deliveries</h2>
        <?php if($deliveries): ?>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <?php foreach($deliveries as $d): ?>
                    <div class="bg-glass shadow-xl rounded-2xl p-6 border-l-6 <?= $d['delivery_status']=='Delivered'?'border-green-500':'border-yellow-500' ?> hover:scale-105 transition duration-300 animate__animated animate__fadeInUp">
                        <h3 class="text-xl font-bold text-gray-800 mb-2">Order #<?= $d['order_id'] ?></h3>
                        <p class="text-gray-700"><i class="fas fa-user mr-2 text-gray-500"></i> <?= htmlspecialchars($d['member_name']) ?></p>
                        <p class="text-gray-700 mt-1"><i class="fas fa-map-marker-alt mr-2 text-gray-500"></i> <?= htmlspecialchars($d['delivery_address']) ?></p>
                        <p class="text-gray-700 mt-1"><strong>Status:</strong> <span class="text-yellow-600 font-semibold"><?= htmlspecialchars($d['delivery_status']) ?></span></p>
                        <p class="text-gray-500 mt-1 text-sm"><i class="far fa-clock mr-1"></i> <?= date('d M Y H:i', strtotime($d['delivery_date'])) ?></p>

                        <?php if(is_null($d['rider_id'])): ?>
                            <form method="POST" class="mt-5">
                                <input type="hidden" name="delivery_id" value="<?= $d['id'] ?>">
                                <button type="submit" name="pick_delivery" class="w-full py-2 rounded-lg bg-gradient-to-r from-blue-500 to-blue-700 text-white font-semibold shadow-md hover:shadow-lg hover:scale-105 transition">Pick Delivery</button>
                            </form>
                        <?php elseif($d['rider_id']==$rider_id): ?>
                            <form method="POST" class="mt-5">
                                <input type="hidden" name="delivery_id" value="<?= $d['id'] ?>">
                                <button type="submit" name="deliver_delivery" class="w-full py-2 rounded-lg bg-gradient-to-r from-green-500 to-green-700 text-white font-semibold shadow-md hover:shadow-lg hover:scale-105 transition">Mark as Delivered</button>
                            </form>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p class="text-gray-500 text-center text-lg mt-6">No current deliveries available 🚚</p>
        <?php endif; ?>
    </section>

    <!-- Delivery History -->
    <section>
        <h2 class="section-title">📜 Delivery History</h2>
        <?php if($history): ?>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <?php foreach($history as $h): ?>
                    <div class="bg-glass shadow-xl rounded-2xl p-6 border-l-6 border-green-500 hover:scale-105 transition duration-300 animate__animated animate__fadeInUp">
                        <h3 class="text-xl font-bold text-gray-800 mb-2">Order #<?= $h['order_id'] ?></h3>
                        <p class="text-gray-700"><i class="fas fa-user mr-2 text-gray-500"></i> <?= htmlspecialchars($h['member_name']) ?></p>
                        <p class="text-gray-700 mt-1"><i class="fas fa-map-marker-alt mr-2 text-gray-500"></i> <?= htmlspecialchars($h['delivery_address']) ?></p>
                        <p class="text-green-600 font-semibold mt-1"><i class="fas fa-check-circle mr-1"></i> Delivered</p>
                        <p class="text-gray-500 mt-1 text-sm"><i class="far fa-clock mr-1"></i> <?= date('d M Y H:i', strtotime($h['delivery_date'])) ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p class="text-gray-500 text-center text-lg mt-6">No delivery history yet 📭</p>
        <?php endif; ?>
    </section>

</div>
</body>
</html>
