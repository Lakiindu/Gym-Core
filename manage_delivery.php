<?php
session_start();

// Ensure admin access
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

// DB connection
$host = "localhost";
$dbname = "gym_db";
$user = "postgres";
$password = "lakindu";

try {
    $pdo = new PDO("pgsql:host=$host;dbname=$dbname", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("DB Connection failed: " . $e->getMessage());
}

// Create/Update Delivery
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $order_id = $_POST['order_id'];
    $address = $_POST['delivery_address'];
    $status = $_POST['delivery_status'];

    if (isset($_POST['delivery_id']) && !empty($_POST['delivery_id'])) {
        // Update
        $id = $_POST['delivery_id'];
        $stmt = $pdo->prepare("UPDATE deliveries SET order_id = ?, delivery_address = ?, delivery_status = ? WHERE id = ?");
        $stmt->execute([$order_id, $address, $status, $id]);
    } else {
        // Insert
        $stmt = $pdo->prepare("INSERT INTO deliveries (order_id, delivery_address, delivery_status) VALUES (?, ?, ?)");
        $stmt->execute([$order_id, $address, $status]);
    }
    header("Location: manage_delivery.php");
    exit;
}

// Delete
if (isset($_GET['delete'])) {
    $stmt = $pdo->prepare("DELETE FROM deliveries WHERE id = ?");
    $stmt->execute([$_GET['delete']]);
    header("Location: manage_delivery.php");
    exit;
}

// Edit
$editDelivery = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM deliveries WHERE id = ?");
    $stmt->execute([$_GET['edit']]);
    $editDelivery = $stmt->fetch(PDO::FETCH_ASSOC);
}

// Get all deliveries
$deliveries = $pdo->query("SELECT d.*, o.customer_name 
                           FROM deliveries d 
                           JOIN orders o ON d.order_id = o.id 
                           ORDER BY d.delivery_date DESC")->fetchAll(PDO::FETCH_ASSOC);

// Get orders for dropdown
$orders = $pdo->query("SELECT id, customer_name FROM orders ORDER BY id")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Manage Deliveries</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
</head>
<body class="bg-orange-50 min-h-screen">


  <!-- Navbar -->
  <header class="bg-white shadow fixed top-0 w-full z-50">
    <div class="max-w-7xl mx-auto px-6 py-4 flex justify-between items-center">
      <div class="text-2xl font-bold text-orange-500 flex items-center gap-2">
        <i class="fa-solid fa-dumbbell"></i> <span>GYM Core Admin</span>
      </div>
      <div class="flex items-center gap-4">
        <span class="text-sm font-medium text-gray-700">Welcome, <?= htmlspecialchars($_SESSION['username'] ?? 'Admin') ?></span>
        <a href="logout.php" class="text-red-600 hover:text-red-800 font-bold flex items-center gap-1">
          <i class="fa-solid fa-right-from-bracket"></i> Logout
        </a>
      </div>
    </div>
  </header>

  <div class="flex pt-16 min-h-screen">

    <!-- Sidebar -->
    <aside class="w-64 bg-gray-800 text-white p-6 sticky top-16 h-screen flex-shrink-0">
      <nav class="space-y-4">
        <a href="admin_dashboard.php#users" class="block px-4 py-2 rounded hover:bg-orange-600 transition text-gray-300 hover:text-white flex items-center gap-2">
          <i class="fa-solid fa-users"></i> Manage Users
        </a>
        <a href="manage_supplements.php" class="block px-4 py-2 rounded hover:bg-gray-700 transition text-gray-300 hover:text-white flex items-center gap-2">
          <i class="fa-solid fa-capsules"></i> Manage Supplements
        </a>
        <a href="manage_memberships.php" class="block px-4 py-2 rounded hover:bg-gray-700 transition text-gray-300 hover:text-white flex items-center gap-2">
          <i class="fa-solid fa-id-card"></i> Manage Memberships
        </a>
        <a href="manage_orders.php" class="block px-4 py-2 rounded hover:bg-gray-700 transition text-gray-300 hover:text-white flex items-center gap-2">
          <i class="fa-solid fa-cart-shopping"></i> Manage Orders
        </a>
        <a href="manage_payments.php" class="block px-4 py-2 rounded hover:bg-gray-700 transition text-gray-300 hover:text-white flex items-center gap-2">
          <i class="fa-solid fa-credit-card"></i> Manage Payments
        </a>
        <a href="manage_delivery.php" class="block px-4 py-2 rounded bg-orange-600 text-white flex items-center gap-2" aria-current="page">
          <i class="fa-solid fa-truck"></i> Delivery Details
        </a>
      </nav>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 p-8 max-w-6xl mx-auto" style="margin-left: 4rem;">

      <h1 class="text-3xl font-bold text-orange-600 mb-6 flex items-center gap-3">
        <i class="fa-solid fa-truck"></i> Manage Deliveries
      </h1>

      <section class="bg-white p-6 rounded-lg shadow mb-10">
        <h2 class="text-2xl font-semibold text-orange-600 mb-4"><?= $editDelivery ? "Edit Delivery" : "Add New Delivery" ?></h2>

        <!-- Form -->
        <form method="POST" class="grid grid-cols-1 md:grid-cols-4 gap-4">
          <?php if ($editDelivery): ?>
            <input type="hidden" name="delivery_id" value="<?= $editDelivery['id'] ?>" />
          <?php endif; ?>

          <select name="order_id" required class="p-2 border border-orange-400 rounded focus:ring-2 focus:ring-orange-500 focus:outline-none">
            <option value="">Select Order</option>
            <?php foreach ($orders as $o): ?>
              <option value="<?= $o['id'] ?>" <?= $editDelivery && $editDelivery['order_id'] == $o['id'] ? 'selected' : '' ?>>
                <?= "Order #" . $o['id'] . " - " . htmlspecialchars($o['customer_name']) ?>
              </option>
            <?php endforeach; ?>
          </select>

          <input type="text" name="delivery_address" required placeholder="Address" value="<?= $editDelivery['delivery_address'] ?? '' ?>" class="p-2 border border-orange-400 rounded focus:ring-2 focus:ring-orange-500 focus:outline-none" />

          <select name="delivery_status" required class="p-2 border border-orange-400 rounded focus:ring-2 focus:ring-orange-500 focus:outline-none">
            <option value="Pending" <?= isset($editDelivery) && $editDelivery['delivery_status'] == 'Pending' ? 'selected' : '' ?>>Pending</option>
            <option value="In Transit" <?= isset($editDelivery) && $editDelivery['delivery_status'] == 'In Transit' ? 'selected' : '' ?>>In Transit</option>
            <option value="Delivered" <?= isset($editDelivery) && $editDelivery['delivery_status'] == 'Delivered' ? 'selected' : '' ?>>Delivered</option>
          </select>

          <button type="submit" class="bg-orange-600 text-white px-4 py-2 rounded hover:bg-orange-700 col-span-full md:col-span-1">
            <?= $editDelivery ? 'Update' : 'Add' ?> Delivery
          </button>
        </form>
      </section>

      <!-- Delivery Table -->
      <section class="overflow-x-auto bg-white rounded shadow">
        <table class="min-w-full border text-sm">
          <thead class="bg-gray-100 text-orange-600 font-semibold">
            <tr>
              <th class="py-2 px-4 border border-gray-300">ID</th>
              <th class="py-2 px-4 border border-gray-300">Order</th>
              <th class="py-2 px-4 border border-gray-300">Address</th>
              <th class="py-2 px-4 border border-gray-300">Status</th>
              <th class="py-2 px-4 border border-gray-300">Date</th>
              <th class="py-2 px-4 border border-gray-300">Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($deliveries as $d): ?>
              <tr class="border-t hover:bg-orange-50">
                <td class="py-2 px-4 border border-gray-300"><?= $d['id'] ?></td>
                <td class="py-2 px-4 border border-gray-300"><?= "Order #" . $d['order_id'] . " - " . htmlspecialchars($d['customer_name']) ?></td>
                <td class="py-2 px-4 border border-gray-300"><?= htmlspecialchars($d['delivery_address']) ?></td>
                <td class="py-2 px-4 border border-gray-300 <?= strtolower($d['delivery_status']) == 'delivered' ? 'text-green-600' : 'text-yellow-600' ?> font-semibold capitalize"><?= htmlspecialchars($d['delivery_status']) ?></td>
                <td class="py-2 px-4 border border-gray-300"><?= date("Y-m-d H:i", strtotime($d['delivery_date'])) ?></td>
                <td class="py-2 px-4 border border-gray-300 space-x-2">
                  <a href="?edit=<?= $d['id'] ?>" class="text-orange-600 hover:underline font-semibold">Edit</a>
                  <a href="?delete=<?= $d['id'] ?>" onclick="return confirm('Delete this delivery?');" class="text-red-600 hover:underline font-semibold">Delete</a>
                </td>
              </tr>
            <?php endforeach; ?>
            <?php if (empty($deliveries)): ?>
              <tr><td colspan="6" class="py-4 text-center text-gray-500">No deliveries found.</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </section>

    </main>
  </div>

</body>
</html>
