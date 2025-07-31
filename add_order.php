<?php
session_start();

// Access control
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$userId = $_SESSION['user_id'];

// DB connection info
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

// If form submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate and sanitize inputs (example fields)
    $customerName = trim($_POST['customer_name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $quantity = (int)($_POST['quantity'] ?? 0);
    $price = floatval($_POST['price'] ?? 0);
    $status = 'pending'; // Default status for new orders

    if ($customerName === '' || $description === '' || $quantity <= 0 || $price <= 0) {
        $error = "Please fill all fields correctly.";
    } else {
        // Insert query with user_id from session
        $stmt = $pdo->prepare("INSERT INTO orders (user_id, customer_name, description, quantity, price, status) 
                               VALUES (:user_id, :customer_name, :description, :quantity, :price, :status)");
        $stmt->execute([
            ':user_id' => $userId,
            ':customer_name' => $customerName,
            ':description' => $description,
            ':quantity' => $quantity,
            ':price' => $price,
            ':status' => $status
        ]);

        header("Location: orders_dashboard.php"); // Redirect after success
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Add Order - GYM Core</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-900 text-white min-h-screen flex flex-col items-center justify-center p-6">

  <h1 class="text-3xl font-bold text-orange-500 mb-8">Add New Order</h1>

  <?php if (!empty($error)): ?>
    <p class="mb-4 text-red-400"><?= htmlspecialchars($error) ?></p>
  <?php endif; ?>

  <form method="POST" class="bg-gray-800 p-6 rounded shadow-md w-full max-w-md space-y-4">
    <div>
      <label for="customer_name" class="block mb-1">Customer Name</label>
      <input type="text" id="customer_name" name="customer_name" required
             class="w-full p-2 rounded bg-gray-700 border border-gray-600" />
    </div>

    <div>
      <label for="description" class="block mb-1">Product Description</label>
      <input type="text" id="description" name="description" required
             class="w-full p-2 rounded bg-gray-700 border border-gray-600" />
    </div>

    <div>
      <label for="quantity" class="block mb-1">Quantity</label>
      <input type="number" id="quantity" name="quantity" min="1" required
             class="w-full p-2 rounded bg-gray-700 border border-gray-600" />
    </div>

    <div>
      <label for="price" class="block mb-1">Price (LKR)</label>
      <input type="number" id="price" name="price" step="0.01" min="0.01" required
             class="w-full p-2 rounded bg-gray-700 border border-gray-600" />
    </div>

    <button type="submit" class="w-full bg-orange-600 hover:bg-orange-700 py-2 rounded font-bold">
      Submit Order
    </button>
  </form>

  <a href="my_orders.php" class="mt-6 text-orange-400 hover:underline">← Back to My Orders</a>

</body>
</html>
