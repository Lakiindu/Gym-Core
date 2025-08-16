<?php
session_start();

// DB credentials
$host = "localhost";
$dbname = "gym_db";
$user = "postgres";
$password = "lakindu";

// Connect to PostgreSQL
try {
    $pdo = new PDO("pgsql:host=$host;dbname=$dbname", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Get user ID from session or mock (for dev/testing)
$user_id = $_SESSION['user_id'] ?? 1;

// Handle item removal from cart
if (isset($_GET['remove_id'])) {
    $remove_id = (int) $_GET['remove_id'];
    $stmt = $pdo->prepare("DELETE FROM cart WHERE id = :id AND user_id = :user_id");
    $stmt->execute(['id' => $remove_id, 'user_id' => $user_id]);
    $_SESSION['message'] = "Item removed from cart.";
    header("Location: cart.php");
    exit;
}

// Fetch cart items for the user
$stmt = $pdo->prepare("
    SELECT 
        cart.id AS cart_id,
        supplements.name,
        supplements.image_path,
        supplements.price,
        cart.quantity,
        (supplements.price * cart.quantity) AS subtotal
    FROM cart
    JOIN supplements ON cart.supplement_id = supplements.id
    WHERE cart.user_id = :user_id
");
$stmt->execute(['user_id' => $user_id]);
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Calculate total cart value
$total = array_sum(array_column($items, 'subtotal'));
?>

<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Cart - GYM Core</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            primary: "#ff6600",
            secondary: "#222",
          },
        },
      },
    };
  </script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
</head>
<body class="bg-gray-900 text-white min-h-screen flex flex-col">

  <!-- Header -->
  <header class="bg-gray-800 p-4 shadow-lg flex justify-between items-center">
    <h1 class="text-2xl font-bold text-primary flex items-center">
      <i class="fas fa-cart-shopping mr-2"></i> Your Cart
    </h1>
    <a href="supplements_dashboard.php" class="bg-primary hover:bg-orange-700 px-4 py-2 rounded font-semibold">
      ← Continue Shopping
    </a>
  </header>

  <main class="container mx-auto p-6 flex-grow">
    <!-- Flash Messages -->
    <?php if (isset($_SESSION['message'])): ?>
      <div class="mb-4 p-4 bg-green-600 rounded">
        <?= htmlspecialchars($_SESSION['message']) ?>
      </div>
      <?php unset($_SESSION['message']); ?>
    <?php endif; ?>

    <?php if (count($items) === 0): ?>
      <p class="text-gray-400 text-center text-lg mt-20">Your cart is empty. 🛒</p>
    <?php else: ?>
      <div class="overflow-x-auto rounded-lg shadow-lg">
        <table class="w-full bg-gray-800 text-left border-collapse">
          <thead>
            <tr class="bg-primary text-white">
              <th class="p-4">Image</th>
              <th class="p-4">Name</th>
              <th class="p-4">Price (LKR)</th>
              <th class="p-4">Quantity</th>
              <th class="p-4">Subtotal</th>
              <th class="p-4">Action</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($items as $item): ?>
              <tr class="border-b border-gray-700 hover:bg-gray-700 transition">
                <td class="p-4">
                  <img src="<?= htmlspecialchars($item['image_path']) ?>" alt="<?= htmlspecialchars($item['name']) ?>" class="h-16 w-16 object-cover rounded" />
                </td>
                <td class="p-4 font-semibold"><?= htmlspecialchars($item['name']) ?></td>
                <td class="p-4"><?= number_format($item['price'], 2) ?></td>
                <td class="p-4"><?= $item['quantity'] ?></td>
                <td class="p-4 font-bold text-green-400"><?= number_format($item['subtotal'], 2) ?></td>
                <td class="p-4">
                  <a href="?remove_id=<?= $item['cart_id'] ?>" class="text-red-500 hover:text-red-700 font-bold" onclick="return confirm('Remove this item?')">
                    <i class="fas fa-trash-alt"></i> Remove
                  </a>
                </td>
              </tr>
            <?php endforeach; ?>
            <tr class="bg-gray-900 text-lg font-bold">
              <td colspan="4" class="text-right p-4">Total:</td>
              <td class="p-4 text-green-400"><?= number_format($total, 2) ?> LKR</td>
              <td class="p-4"></td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Checkout Button -->
      <div class="mt-6 text-right">
        <a href="do_payment.php" class="bg-green-600 hover:bg-green-700 px-6 py-3 rounded text-white font-bold shadow-lg inline-block">
          Proceed to Checkout <i class="fas fa-arrow-right ml-2"></i>
        </a>
      </div>
    <?php endif; ?>
  </main>

  <footer class="bg-gray-800 text-center p-4 text-gray-400">
    &copy; <?= date('Y') ?> GYM Core. All rights reserved.
  </footer>

</body>
</html>
