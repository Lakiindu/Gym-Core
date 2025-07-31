<?php
session_start();

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
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Manage Payments</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-6">
  <div class="mb-6">
    <a href="admin_dashboard.php" class="bg-gray-700 text-white px-4 py-2 rounded hover:bg-gray-800">&larr; Back to Admin Dashboard</a>
  </div>

  <section class="bg-white p-6 rounded-lg shadow">
    <h2 class="text-2xl font-bold text-green-600 mb-4"><?= $editPayment ? "Edit Payment" : "Add New Payment" ?></h2>

    <!-- Payment Form -->
    <form method="POST" class="grid grid-cols-1 md:grid-cols-6 gap-4 mb-6">
      <?php if ($editPayment): ?>
        <input type="hidden" name="payment_id" value="<?= $editPayment['id'] ?>">
      <?php endif; ?>

      <select name="user_id" required class="p-2 border rounded">
        <option value="">Select User</option>
        <?php foreach ($users as $u): ?>
          <option value="<?= $u['id'] ?>" <?= $editPayment && $editPayment['user_id'] == $u['id'] ? 'selected' : '' ?>>
            <?= htmlspecialchars($u['username']) ?>
          </option>
        <?php endforeach; ?>
      </select>

      <select name="membership_id" required class="p-2 border rounded">
        <option value="">Select Membership</option>
        <?php foreach ($memberships as $m): ?>
          <option value="<?= $m['id'] ?>" <?= $editPayment && $editPayment['membership_id'] == $m['id'] ? 'selected' : '' ?>>
            <?= htmlspecialchars($m['plan']) ?>
          </option>
        <?php endforeach; ?>
      </select>

      <input type="number" step="0.01" name="amount" required placeholder="Amount" class="p-2 border rounded" value="<?= $editPayment ? $editPayment['amount'] : '' ?>" />
      <input type="text" name="payment_method" required placeholder="Payment Method" class="p-2 border rounded" value="<?= $editPayment ? htmlspecialchars($editPayment['payment_method']) : '' ?>" />

      <select name="status" required class="p-2 border rounded">
        <option value="Paid" <?= $editPayment && $editPayment['status'] == 'Paid' ? 'selected' : '' ?>>Paid</option>
        <option value="Pending" <?= $editPayment && $editPayment['status'] == 'Pending' ? 'selected' : '' ?>>Pending</option>
      </select>

      <button type="submit" name="<?= $editPayment ? 'update_payment' : 'add_payment' ?>" class="bg-<?= $editPayment ? 'blue' : 'green' ?>-600 text-white px-4 py-2 rounded hover:bg-<?= $editPayment ? 'blue' : 'green' ?>-700">
        <?= $editPayment ? 'Update' : 'Add' ?> Payment
      </button>
    </form>

    <!-- Payment History Table -->
    <div class="overflow-x-auto">
      <table class="min-w-full border text-sm">
        <thead class="bg-gray-200">
          <tr>
            <th class="py-2 px-4">ID</th>
            <th class="py-2 px-4">User</th>
            <th class="py-2 px-4">Membership</th>
            <th class="py-2 px-4">Amount</th>
            <th class="py-2 px-4">Method</th>
            <th class="py-2 px-4">Status</th>
            <th class="py-2 px-4">Date</th>
            <th class="py-2 px-4">Action</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($payments as $pay): ?>
            <tr class="border-t hover:bg-gray-50">
              <td class="py-2 px-4"><?= $pay['id'] ?></td>
              <td class="py-2 px-4"><?= htmlspecialchars($pay['username']) ?></td>
              <td class="py-2 px-4"><?= htmlspecialchars($pay['plan']) ?></td>
              <td class="py-2 px-4 text-green-700">$<?= $pay['amount'] ?></td>
              <td class="py-2 px-4"><?= htmlspecialchars($pay['payment_method']) ?></td>
              <td class="py-2 px-4 <?= $pay['status'] == 'Paid' ? 'text-green-600' : 'text-red-500' ?>"><?= $pay['status'] ?></td>
              <td class="py-2 px-4"><?= date("Y-m-d H:i", strtotime($pay['paid_at'])) ?></td>
              <td class="py-2 px-4">
                <a href="?edit=<?= $pay['id'] ?>" class="text-blue-600 hover:underline">Edit</a>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </section>
</body>
</html>
