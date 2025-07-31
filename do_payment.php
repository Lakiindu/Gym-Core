<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$host = "localhost";
$dbname = "gym_db";
$user = "postgres";
$password = "lakindu";

try {
    $pdo = new PDO("pgsql:host=$host;dbname=$dbname", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("DB connection failed: " . $e->getMessage());
}

$user_id = $_SESSION['user_id'];

$membershipStmt = $pdo->prepare("
    SELECT * FROM memberships 
    WHERE user_id = :user_id AND status = 'active'
    ORDER BY start_date DESC
    LIMIT 1
");
$membershipStmt->execute(['user_id' => $user_id]);
$membership = $membershipStmt->fetch(PDO::FETCH_ASSOC);

$userStmt = $pdo->prepare("SELECT username FROM users WHERE id = :user_id");
$userStmt->execute(['user_id' => $user_id]);
$userData = $userStmt->fetch(PDO::FETCH_ASSOC);
$customer_name = $userData ? $userData['username'] : 'Unknown Customer';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $payment_method = $_POST['payment_method'] ?? '';
    $delivery_option = $_POST['delivery_option'] ?? 'Visit Gym';
    $delivery_address = trim($_POST['delivery_address'] ?? '');

    if (!$membership) {
        $_SESSION['error'] = "No active membership found for this user.";
    } else {
        $membership_id = $membership['id'];

        $amount = match (strtolower($membership['plan'])) {
            'gold' => 50000.00,
            'silver' => 25000.00,
            'platinum' => 100000.00,
            default => 30000.00,
        };

        try {
            $pdo->beginTransaction();

            $stmt = $pdo->prepare("
                INSERT INTO payments (user_id, membership_id, amount, payment_method, status, paid_at)
                VALUES (:user_id, :membership_id, :amount, :payment_method, 'Paid', NOW())
            ");
            $stmt->execute([
                ':user_id' => $user_id,
                ':membership_id' => $membership_id,
                ':amount' => $amount,
                ':payment_method' => $payment_method
            ]);

            $cartStmt = $pdo->prepare("SELECT supplement_id, quantity FROM cart WHERE user_id = :user_id");
            $cartStmt->execute(['user_id' => $user_id]);
            $cartItems = $cartStmt->fetchAll(PDO::FETCH_ASSOC);

            if ($cartItems) {
                $updateStockStmt = $pdo->prepare("UPDATE supplements SET stock = stock - :qty WHERE id = :supplement_id");
                $insertOrderStmt = $pdo->prepare("
                    INSERT INTO orders (customer_name, description, quantity, price, status)
                    VALUES (:customer_name, :description, :quantity, :price, 'pending') RETURNING id
                ");

                foreach ($cartItems as $item) {
                    $supplementStmt = $pdo->prepare("SELECT name, price FROM supplements WHERE id = :id");
                    $supplementStmt->execute(['id' => $item['supplement_id']]);
                    $supplement = $supplementStmt->fetch(PDO::FETCH_ASSOC);

                    if (!$supplement) continue;

                    $totalPrice = $supplement['price'] * $item['quantity'];

                    $updateStockStmt->execute([
                        ':qty' => $item['quantity'],
                        ':supplement_id' => $item['supplement_id']
                    ]);

                    $insertOrderStmt->execute([
                        ':customer_name' => $customer_name,
                        ':description' => $supplement['name'],
                        ':quantity' => $item['quantity'],
                        ':price' => $totalPrice
                    ]);

                    $order_id = $insertOrderStmt->fetchColumn();

                    if ($delivery_option === 'Delivery' && !empty($delivery_address)) {
                        $insertDelivery = $pdo->prepare("
                            INSERT INTO deliveries (order_id, delivery_address, delivery_status, delivery_date)
                            VALUES (:order_id, :delivery_address, 'In Transit', NOW())
                        ");
                        $insertDelivery->execute([
                            ':order_id' => $order_id,
                            ':delivery_address' => $delivery_address
                        ]);
                    }
                }
            }

            $clearCartStmt = $pdo->prepare("DELETE FROM cart WHERE user_id = :user_id");
            $clearCartStmt->execute(['user_id' => $user_id]);

            $pdo->commit();
            $_SESSION['message'] = "Payment successful. Membership: " . htmlspecialchars($membership['plan']) . ". Orders placed.";
            header("Location: do_payment.php");
            exit;

        } catch (Exception $e) {
            $pdo->rollBack();
            $_SESSION['error'] = "Payment failed: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Do Payment - GYM Core</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    function toggleAddressInput() {
      const delivery = document.getElementById('delivery');
      const addressInput = document.getElementById('addressDiv');
      addressInput.style.display = delivery.checked ? 'block' : 'none';
    }
  </script>
</head>
<body class="bg-gray-900 text-white min-h-screen flex flex-col">
  <header class="bg-gray-800 p-4 flex justify-between items-center">
    <h1 class="text-xl font-bold text-orange-500">💳 Make Payment</h1>
    <a href="user_dashboard.php" class="bg-orange-600 hover:bg-orange-700 px-4 py-2 rounded">← Back</a>
  </header>

  <main class="flex-grow container mx-auto p-6">
    <?php if (!empty($_SESSION['message'])): ?>
      <div class="bg-green-600 p-4 mb-4 rounded">
        <?= htmlspecialchars($_SESSION['message']) ?>
      </div>
      <?php unset($_SESSION['message']); ?>
    <?php elseif (!empty($_SESSION['error'])): ?>
      <div class="bg-red-600 p-4 mb-4 rounded">
        <?= htmlspecialchars($_SESSION['error']) ?>
      </div>
      <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <div class="bg-gray-800 p-6 rounded shadow-lg max-w-xl mx-auto">
      <?php if ($membership): ?>
        <div class="mb-4">
          <p class="text-lg font-semibold mb-2">Membership: <span class="text-orange-400"><?= htmlspecialchars($membership['plan']) ?></span></p>
          <p>Start: <?= htmlspecialchars($membership['start_date']) ?> | End: <?= htmlspecialchars($membership['end_date']) ?></p>
          <p>Status: <span class="uppercase font-bold text-green-400"><?= htmlspecialchars($membership['status']) ?></span></p>
        </div>

        <form method="POST" novalidate>
          <div class="mb-4">
            <label class="block mb-2 font-semibold">Payment Method</label>
            <div class="flex gap-4">
              <?php foreach (['Credit Card', 'PayPal', 'Cash'] as $method): ?>
                <label class="flex items-center">
                  <input type="radio" name="payment_method" value="<?= $method ?>" required class="mr-2" />
                  <?= $method ?>
                </label>
              <?php endforeach; ?>
            </div>
          </div>

          <div class="mb-4">
            <label class="block mb-2 font-semibold">How would you like to receive your supplements?</label>
            <div class="flex gap-4">
              <label class="flex items-center">
                <input type="radio" name="delivery_option" value="Visit Gym" onclick="toggleAddressInput()" checked class="mr-2">
                Visit Gym
              </label>
              <label class="flex items-center">
                <input type="radio" id="delivery" name="delivery_option" value="Delivery" onclick="toggleAddressInput()" class="mr-2">
                Delivery
              </label>
            </div>
          </div>

          <div class="mb-4 hidden" id="addressDiv">
            <label class="block mb-2 font-semibold">Delivery Address</label>
            <input type="text" name="delivery_address" class="w-full px-3 py-2 rounded text-black" placeholder="Enter your address">
          </div>

          <button type="submit" class="w-full bg-orange-600 hover:bg-orange-700 p-3 rounded font-bold text-white">
            Pay Now
          </button>
        </form>
      <?php else: ?>
        <p class="text-red-400 font-semibold">No active membership available to pay for.</p>
      <?php endif; ?>
    </div>
  </main>

  <footer class="bg-gray-800 p-4 text-center text-gray-400">
    &copy; <?= date('Y') ?> GYM Core | Secure Payment Portal
  </footer>
</body>
</html>
