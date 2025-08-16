<?php
session_start();

// Ensure admin access
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

// Database connection
$host = "localhost";
$dbname = "gym_db";
$user = "postgres";
$password = "lakindu";

try {
    $pdo = new PDO("pgsql:host=$host;dbname=$dbname", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Update payment
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_payment'])) {
    $paymentId = $_POST['payment_id'];
    $userId = $_POST['user_id'];
    $membershipId = $_POST['membership_id'];
    $amount = $_POST['amount'];
    $method = $_POST['payment_method'];
    $status = $_POST['status'];

    $stmt = $pdo->prepare("UPDATE payments SET user_id = ?, membership_id = ?, amount = ?, payment_method = ?, status = ? WHERE id = ?");
    $stmt->execute([$userId, $membershipId, $amount, $method, $status, $paymentId]);

    header("Location: manage_payments.php");
    exit;
}

// Insert payment
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_payment'])) {
    $userId = $_POST['user_id'];
    $membershipId = $_POST['membership_id'];
    $amount = $_POST['amount'];
    $method = $_POST['payment_method'];
    $status = $_POST['status'];

    $stmt = $pdo->prepare("INSERT INTO payments (user_id, membership_id, amount, payment_method, status) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$userId, $membershipId, $amount, $method, $status]);

    header("Location: manage_payments.php");
    exit;
}

// Get payment to edit
$editPayment = null;
if (isset($_GET['edit'])) {
    $paymentId = $_GET['edit'];
    $stmt = $pdo->prepare("SELECT * FROM payments WHERE id = ?");
    $stmt->execute([$paymentId]);
    $editPayment = $stmt->fetch(PDO::FETCH_ASSOC);
}

// Fetch data
$payments = $pdo->query("SELECT p.*, u.username, m.plan 
                         FROM payments p 
                         JOIN users u ON p.user_id = u.id 
                         JOIN memberships m ON p.membership_id = m.id 
                         ORDER BY p.paid_at DESC")->fetchAll(PDO::FETCH_ASSOC);

$users = $pdo->query("SELECT id, username FROM users ORDER BY id")->fetchAll(PDO::FETCH_ASSOC);
$memberships = $pdo->query("SELECT id, plan FROM memberships ORDER BY id")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Manage Payments</title>
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
        <a href="manage_payments.php" class="block px-4 py-2 rounded bg-orange-600 text-white flex items-center gap-2" aria-current="page">
          <i class="fa-solid fa-credit-card"></i> Manage Payments
        </a>
        <a href="manage_delivery.php" class="block px-4 py-2 rounded hover:bg-gray-700 transition text-gray-300 hover:text-white flex items-center gap-2">
          <i class="fa-solid fa-truck"></i> Delivery Details
        </a>
      </nav>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 p-8 max-w-6xl mx-auto" style="margin-left: 4rem;">

      <h1 class="text-3xl font-bold text-orange-600 mb-6 flex items-center gap-3">
        <i class="fa-solid fa-credit-card"></i> Manage Payments
      </h1>

      <section class="bg-white p-6 rounded-lg shadow mb-10">
        <h2 class="text-2xl font-semibold text-orange-600 mb-4"><?= $editPayment ? "Edit Payment" : "Add New Payment" ?></h2>

        <!-- Payment Form -->
        <form method="POST" class="grid grid-cols-1 md:grid-cols-6 gap-4">
          <?php if ($editPayment): ?>
            <input type="hidden" name="payment_id" value="<?= $editPayment['id'] ?>">
          <?php endif; ?>

          <select name="user_id" required class="p-2 border border-orange-400 rounded focus:ring-2 focus:ring-orange-500 focus:outline-none">
            <option value="">Select User</option>
            <?php foreach ($users as $u): ?>
              <option value="<?= $u['id'] ?>" <?= $editPayment && $editPayment['user_id'] == $u['id'] ? 'selected' : '' ?>>
                <?= htmlspecialchars($u['username']) ?>
              </option>
            <?php endforeach; ?>
          </select>

          <select name="membership_id" required class="p-2 border border-orange-400 rounded focus:ring-2 focus:ring-orange-500 focus:outline-none">
            <option value="">Select Membership</option>
            <?php foreach ($memberships as $m): ?>
              <option value="<?= $m['id'] ?>" <?= $editPayment && $editPayment['membership_id'] == $m['id'] ? 'selected' : '' ?>>
                <?= htmlspecialchars($m['plan']) ?>
              </option>
            <?php endforeach; ?>
          </select>

          <input type="number" step="0.01" name="amount" required placeholder="Amount" class="p-2 border border-orange-400 rounded focus:ring-2 focus:ring-orange-500 focus:outline-none" value="<?= $editPayment ? $editPayment['amount'] : '' ?>" />
          <input type="text" name="payment_method" required placeholder="Payment Method" class="p-2 border border-orange-400 rounded focus:ring-2 focus:ring-orange-500 focus:outline-none" value="<?= $editPayment ? htmlspecialchars($editPayment['payment_method']) : '' ?>" />

          <select name="status" required class="p-2 border border-orange-400 rounded focus:ring-2 focus:ring-orange-500 focus:outline-none">
            <option value="Paid" <?= $editPayment && $editPayment['status'] == 'Paid' ? 'selected' : '' ?>>Paid</option>
            <option value="Pending" <?= $editPayment && $editPayment['status'] == 'Pending' ? 'selected' : '' ?>>Pending</option>
          </select>

          <button type="submit" name="<?= $editPayment ? 'update_payment' : 'add_payment' ?>" class="bg-orange-600 text-white px-4 py-2 rounded hover:bg-green-700 col-span-full md:col-span-1">
            <?= $editPayment ? 'Update' : 'Add' ?> Payment
          </button>
        </form>
      </section>

      <!-- Payment History Table -->
      <section class="overflow-x-auto bg-white rounded shadow">
        <table class="min-w-full border text-sm">
          <thead class="bg-gray-100 text-orange-600 font-semibold">
            <tr>
              <th class="py-2 px-4 border border-gray-300">ID</th>
              <th class="py-2 px-4 border border-gray-300">User</th>
              <th class="py-2 px-4 border border-gray-300">Membership</th>
              <th class="py-2 px-4 border border-gray-300">Amount</th>
              <th class="py-2 px-4 border border-gray-300">Method</th>
              <th class="py-2 px-4 border border-gray-300">Status</th>
              <th class="py-2 px-4 border border-gray-300">Date</th>
              <th class="py-2 px-4 border border-gray-300">Action</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($payments as $pay): ?>
              <tr class="border-t hover:bg-orange-50">
                <td class="py-2 px-4 border border-gray-300"><?= $pay['id'] ?></td>
                <td class="py-2 px-4 border border-gray-300"><?= htmlspecialchars($pay['username']) ?></td>
                <td class="py-2 px-4 border border-gray-300"><?= htmlspecialchars($pay['plan']) ?></td>
                <td class="py-2 px-4 border border-gray-300 text-green-700 font-semibold">$<?= number_format($pay['amount'],2) ?></td>
                <td class="py-2 px-4 border border-gray-300"><?= htmlspecialchars($pay['payment_method']) ?></td>
                <td class="py-2 px-4 border border-gray-300 <?= strtolower($pay['status']) == 'paid' ? 'text-green-600' : 'text-red-600' ?> font-semibold capitalize"><?= htmlspecialchars($pay['status']) ?></td>
                <td class="py-2 px-4 border border-gray-300"><?= date("Y-m-d H:i", strtotime($pay['paid_at'])) ?></td>
                <td class="py-2 px-4 border border-gray-300">
                  <a href="?edit=<?= $pay['id'] ?>" class="text-orange-600 hover:underline font-semibold">Edit</a>
                </td>
              </tr>
            <?php endforeach; ?>
            <?php if (empty($payments)): ?>
              <tr><td colspan="8" class="py-4 text-center text-gray-500">No payments found.</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </section>

    </main>
  </div>

</body>
</html>
