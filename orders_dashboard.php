<?php
session_start();

// Access control
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$userId = $_SESSION['user_id'];

// DB credentials
$host = "localhost";
$dbname = "gym_db";
$user = "postgres";
$password = "lakindu";

// Connect to DB
try {
    $pdo = new PDO("pgsql:host=$host;dbname=$dbname", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("DB connection failed: " . $e->getMessage());
}

// Fetch orders
$orderStmt = $pdo->prepare("SELECT * FROM orders WHERE user_id = :user_id ORDER BY id DESC");
$orderStmt->execute(['user_id' => $userId]);
$orders = $orderStmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch payments
$paymentStmt = $pdo->prepare("SELECT * FROM payments WHERE user_id = :user_id ORDER BY id DESC");
$paymentStmt->execute(['user_id' => $userId]);
$payments = $paymentStmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>My Orders & Payments - GYM Core</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-900 text-white min-h-screen flex flex-col">

  <!-- Header -->
  <header class="bg-gray-800 p-4 flex justify-between items-center">
    <h1 class="text-xl font-bold text-orange-500">📦💳  My Orders & Payments</h1>
    <a href="user_dashboard.php" class="bg-orange-600 hover:bg-orange-700 px-4 py-2 rounded">← Dashboard</a>
  </header>

  <!-- Main Content -->
  <main class="flex-grow container mx-auto p-6 space-y-12">

    <!-- Orders Section -->
    <section>
      <h2 class="text-2xl font-bold text-orange-400 mb-4">🛒 Orders</h2>
      <div class="bg-gray-800 p-6 rounded shadow-lg overflow-x-auto">
        <?php if (count($orders) === 0): ?>
          <p class="text-center text-gray-400">No orders found.</p>
        <?php else: ?>
          <table class="min-w-full table-auto border-collapse border border-gray-700 text-sm">
            <thead>
              <tr class="bg-gray-700 text-gray-300">
                <th class="border border-gray-600 px-4 py-2">ID</th>
                <th class="border border-gray-600 px-4 py-2">Customer</th>
                <th class="border border-gray-600 px-4 py-2">Product</th>
                <th class="border border-gray-600 px-4 py-2 text-center">Qty</th>
                <th class="border border-gray-600 px-4 py-2 text-right">Price</th>
                <th class="border border-gray-600 px-4 py-2 text-center">Status</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($orders as $order): ?>
                <?php
                  $status = strtolower($order['status']);
                  $statusClasses = [
                      'paid' => 'bg-green-600 text-green-100 font-semibold',
                      'pending' => 'bg-yellow-500 text-yellow-900 font-semibold',
                      'canceled' => 'bg-red-600 text-red-100 font-semibold',
                  ];
                  $statusClass = $statusClasses[$status] ?? 'bg-gray-600 text-gray-100';
                ?>
                <tr class="border border-gray-700 hover:bg-gray-700">
                  <td class="border border-gray-600 px-4 py-2"><?= htmlspecialchars($order['id']) ?></td>
                  <td class="border border-gray-600 px-4 py-2"><?= htmlspecialchars($order['customer_name']) ?></td>
                  <td class="border border-gray-600 px-4 py-2"><?= htmlspecialchars($order['description']) ?></td>
                  <td class="border border-gray-600 px-4 py-2 text-center"><?= (int)$order['quantity'] ?></td>
                  <td class="border border-gray-600 px-4 py-2 text-right"><?= number_format($order['price'], 2) ?> LKR</td>
                  <td class="border border-gray-600 px-4 py-2 text-center rounded <?= $statusClass ?>">
                    <?= ucfirst($status) ?>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        <?php endif; ?>
      </div>
    </section>

    <!-- Payments Section -->
    <section>
      <h2 class="text-2xl font-bold text-orange-400 mb-4">💳 Payments</h2>
      <div class="bg-gray-800 p-6 rounded shadow-lg overflow-x-auto">
        <?php if (count($payments) === 0): ?>
          <p class="text-center text-gray-400">No payment history available.</p>
        <?php else: ?>
          <table class="min-w-full table-auto border-collapse border border-gray-700 text-sm">
            <thead>
              <tr class="bg-gray-700 text-gray-300">
                <th class="border border-gray-600 px-4 py-2">ID</th>
                <th class="border border-gray-600 px-4 py-2">Membership ID</th>
                <th class="border border-gray-600 px-4 py-2 text-right">Amount</th>
                <th class="border border-gray-600 px-4 py-2">Method</th>
                <th class="border border-gray-600 px-4 py-2 text-center">Status</th>
                <th class="border border-gray-600 px-4 py-2 text-center">Paid At</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($payments as $payment): ?>
                <tr class="border border-gray-700 hover:bg-gray-700">
                  <td class="border border-gray-600 px-4 py-2"><?= (int)$payment['id'] ?></td>
                  <td class="border border-gray-600 px-4 py-2"><?= htmlspecialchars($payment['membership_id']) ?></td>
                  <td class="border border-gray-600 px-4 py-2 text-right"><?= number_format($payment['amount'], 2) ?> LKR</td>
                  <td class="border border-gray-600 px-4 py-2"><?= htmlspecialchars($payment['payment_method']) ?></td>
                  <td class="border border-gray-600 px-4 py-2 text-center text-green-400 font-semibold"><?= htmlspecialchars($payment['status']) ?></td>
                  <td class="border border-gray-600 px-4 py-2 text-center"><?= date("Y-m-d H:i", strtotime($payment['paid_at'])) ?></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        <?php endif; ?>
      </div>
    </section>

  </main>

  <!-- Footer -->
  <footer class="bg-gray-800 p-4 text-center text-gray-400">
    &copy; <?= date('Y') ?> GYM Core | My Orders & Payments
  </footer>

</body>
</html>
