<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
$userId = $_SESSION['user_id'];

$host = "localhost";
$dbname = "gym_db";
$user = "postgres";
$password = "lakindu";

try {
    $pdo = new PDO("pgsql:host=$host;dbname=$dbname", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Fetch orders
    $orderStmt = $pdo->prepare("SELECT * FROM orders WHERE user_id = :user_id ORDER BY id DESC");
    $orderStmt->execute(['user_id' => $userId]);
    $orders = $orderStmt->fetchAll(PDO::FETCH_ASSOC);

    // Fetch payments
    $paymentStmt = $pdo->prepare("SELECT * FROM payments WHERE user_id = :user_id ORDER BY id DESC");
    $paymentStmt->execute(['user_id' => $userId]);
    $payments = $paymentStmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("DB connection failed: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en" class="">
<head>
  <meta charset="UTF-8" />
  <title>Orders & Payments - GYM Core</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            primary: "#F97316",
            background: "#1e293b",
          },
          animation: {
            fadeInUp: "fadeInUp 0.6s ease-out",
          },
          keyframes: {
            fadeInUp: {
              "0%": { opacity: 0, transform: "translateY(20px)" },
              "100%": { opacity: 1, transform: "translateY(0)" },
            },
          },
        },
      },
    }
  </script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
  <style>
    .glass {
      background: rgba(255, 255, 255, 0.05);
      backdrop-filter: blur(10px);
      border: 1px solid rgba(255, 255, 255, 0.1);
    }
  </style>
</head>
<body class="relative bg-cover bg-center bg-no-repeat text-white font-sans min-h-screen" style="background-image: url('images/User Dashboard/orderspayment.jpg');">

  <!-- Sidebar -->
  <aside class="fixed top-0 left-0 h-full w-64 bg-gray-900 bg-opacity-90 shadow-lg z-50 backdrop-blur-md">
    <div class="px-6 py-5 border-b border-gray-700">
      <h1 class="text-2xl font-bold text-primary flex items-center">
        <i class="fas fa-dumbbell mr-2"></i> GYM Core
      </h1>
    </div>
    <nav class="mt-6 px-4 space-y-4">
      <a href="user_dashboard.php" class="block px-4 py-3 rounded hover:bg-primary/20 transition">
        <i class="fas fa-home mr-3"></i> Dashboard
      </a>
      <a href="profile.php" class="block px-4 py-3 rounded hover:bg-primary/20 transition">
        <i class="fas fa-user mr-3"></i> Profile
      </a>
      <a href="orders_dashboard.php" class="block px-4 py-3 rounded bg-primary text-white">
        <i class="fas fa-box-open mr-3"></i> Orders
      </a>
      <a href="supplements_dashboard.php" class="block px-4 py-3 rounded hover:bg-primary/20 transition">
        <i class="fas fa-capsules mr-3"></i> Supplements
      </a>
      <a href="logout.php" class="block px-4 py-3 rounded hover:bg-red-600 transition text-red-500 hover:text-white">
        <i class="fas fa-sign-out-alt mr-3"></i> Logout
      </a>
    </nav>
  </aside>

  <!-- Main Content -->
  <div class="md:ml-64 p-6 flex items-center justify-center min-h-screen">
    <div class="bg-gradient-to-br from-gray-800/60 via-gray-700/50 to-gray-800/60 
                backdrop-blur-xl border border-gray-600 rounded-3xl shadow-2xl 
                w-full max-w-6xl p-10 animate-fadeInUp overflow-auto max-h-[85vh]">

      <h2 class="text-4xl font-bold mb-8 text-center text-primary">Orders & Payments</h2>

      <!-- Orders Section -->
      <section class="mb-12">
        <h3 class="text-3xl font-semibold mb-6 text-orange-400">🛒 Order History</h3>
        <div class="glass rounded-xl p-6 shadow-lg overflow-auto max-h-[300px]">
          <?php if (empty($orders)): ?>
            <p class="text-center text-gray-400 italic">No orders placed yet.</p>
          <?php else: ?>
            <table class="min-w-full text-sm text-left border-separate border-spacing-y-2">
              <thead>
                <tr class="text-orange-300 font-semibold">
                  <th class="px-4 py-2">#</th>
                  <th class="px-4 py-2">Customer</th>
                  <th class="px-4 py-2">Product</th>
                  <th class="px-4 py-2 text-center">Qty</th>
                  <th class="px-4 py-2 text-right">Price</th>
                  <th class="px-4 py-2 text-center">Status</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($orders as $order): ?>
                  <?php
                    $status = strtolower($order['status']);
                    $badge = match ($status) {
                      'paid' => 'bg-green-600 text-white',
                      'pending' => 'bg-yellow-500 text-black',
                      'canceled' => 'bg-red-600 text-white',
                      default => 'bg-gray-600 text-white'
                    };
                  ?>
                  <tr class="hover:bg-gray-800 transition rounded">
                    <td class="px-4 py-2"><?= htmlspecialchars($order['id']) ?></td>
                    <td class="px-4 py-2"><?= htmlspecialchars($order['customer_name']) ?></td>
                    <td class="px-4 py-2"><?= htmlspecialchars($order['description']) ?></td>
                    <td class="px-4 py-2 text-center"><?= (int)$order['quantity'] ?></td>
                    <td class="px-4 py-2 text-right"><?= number_format($order['price'], 2) ?> LKR</td>
                    <td class="px-4 py-2 text-center">
                      <span class="inline-block px-3 py-1 text-xs rounded-full <?= $badge ?>">
                        <?= ucfirst($status) ?>
                      </span>
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
        <h3 class="text-3xl font-semibold mb-6 text-orange-400">💳 Payment History</h3>
        <div class="glass rounded-xl p-6 shadow-lg overflow-auto max-h-[300px]">
          <?php if (empty($payments)): ?>
            <p class="text-center text-gray-400 italic">No payment records available.</p>
          <?php else: ?>
            <table class="min-w-full text-sm text-left border-separate border-spacing-y-2">
              <thead>
                <tr class="text-orange-300 font-semibold">
                  <th class="px-4 py-2">#</th>
                  <th class="px-4 py-2">Membership ID</th>
                  <th class="px-4 py-2 text-right">Amount</th>
                  <th class="px-4 py-2">Method</th>
                  <th class="px-4 py-2 text-center">Status</th>
                  <th class="px-4 py-2 text-center">Paid At</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($payments as $payment): ?>
                  <tr class="hover:bg-gray-800 transition rounded">
                    <td class="px-4 py-2"><?= (int)$payment['id'] ?></td>
                    <td class="px-4 py-2"><?= htmlspecialchars($payment['membership_id']) ?></td>
                    <td class="px-4 py-2 text-right"><?= number_format($payment['amount'], 2) ?> LKR</td>
                    <td class="px-4 py-2"><?= htmlspecialchars($payment['payment_method']) ?></td>
                    <td class="px-4 py-2 text-center">
                      <span class="inline-block bg-green-600 text-white px-3 py-1 text-xs rounded-full">
                        <?= htmlspecialchars($payment['status']) ?>
                      </span>
                    </td>
                    <td class="px-4 py-2 text-center"><?= date("Y-m-d H:i", strtotime($payment['paid_at'])) ?></td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          <?php endif; ?>
        </div>
      </section>

    </div>
  </div>

</body>
</html>
