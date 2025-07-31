<?php
session_start();

// Ensure admin access
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

// DB connection
$host = "localhost"; $dbname = "gym_db"; $user = "postgres"; $password = "lakindu";
try {
    $pdo = new PDO("pgsql:host=$host;dbname=$dbname", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e){
    die("DB connection failed: " . $e->getMessage());
}

// Messages
$msg = ""; $error = "";

// Handle form submission (insert/update)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_order'])) {
    $id = $_POST['order_id'] ?? null;
    $customer = trim($_POST['customer_name']);
    $desc = trim($_POST['description']);
    $quantity = (int)($_POST['quantity']);
    $price = (float)($_POST['price']);
    $status = $_POST['status'];

    if ($id) {
        $stmt = $pdo->prepare("UPDATE orders SET customer_name=?, description=?, quantity=?, price=?, status=? WHERE id=?");
        $stmt->execute([$customer, $desc, $quantity, $price, $status, $id]);
        $msg = "Order updated successfully.";
    } else {
        $stmt = $pdo->prepare("INSERT INTO orders (customer_name, description, quantity, price, status) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$customer, $desc, $quantity, $price, $status]);
        $msg = "Order added successfully.";
    }
}

// Handle delete
if (isset($_GET['delete_id'])) {
    $stmt = $pdo->prepare("DELETE FROM orders WHERE id = ?");
    $stmt->execute([$_GET['delete_id']]);
    $msg = "Order deleted.";
}

// Search
$searchTerm = trim($_GET['search'] ?? '');
if ($searchTerm !== '') {
    $stmt = $pdo->prepare("SELECT * FROM orders WHERE customer_name ILIKE ? OR description ILIKE ? ORDER BY id DESC");
    $stmt->execute(["%$searchTerm%", "%$searchTerm%"]);
} else {
    $stmt = $pdo->query("SELECT * FROM orders ORDER BY id DESC");
}
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Load order for editing
$editOrder = null;
if (isset($_GET['edit_id'])) {
    $stmt = $pdo->prepare("SELECT * FROM orders WHERE id = ?");
    $stmt->execute([$_GET['edit_id']]);
    $editOrder = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Manage Orders</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-6">
  <div class="mb-4">
    <a href="admin_dashboard.php" class="bg-gray-800 text-white px-4 py-2 rounded hover:bg-gray-900">&larr; Back to Admin Dashboard</a>
  </div>

  <?php if ($msg): ?>
    <div class="mb-4 bg-green-100 text-green-800 p-4 rounded"><?= htmlspecialchars($msg) ?></div>
  <?php endif; ?>
  <?php if ($error): ?>
    <div class="mb-4 bg-red-100 text-red-800 p-4 rounded"><?= htmlspecialchars($error) ?></div>
  <?php endif; ?>

  <div class="bg-white p-6 rounded shadow">
    <h1 class="text-2xl font-bold mb-6">Order Management</h1>

    <!-- Search Form -->
    <form method="GET" class="mb-6 flex flex-wrap gap-2">
      <input name="search" value="<?= htmlspecialchars($searchTerm) ?>" placeholder="Search orders…" class="border rounded p-2 flex-1" />
      <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Search</button>
    </form>

    <!-- Add/Edit Order Form -->
    <form method="POST" class="grid grid-cols-1 md:grid-cols-6 gap-4 mb-6">
      <input type="hidden" name="order_id" value="<?= $editOrder['id'] ?? '' ?>">
      <input type="text" name="customer_name" placeholder="Customer Name" required class="border rounded p-2 col-span-2" value="<?= htmlspecialchars($editOrder['customer_name'] ?? '') ?>">
      <input type="text" name="description" placeholder="Description" required class="border rounded p-2 col-span-2" value="<?= htmlspecialchars($editOrder['description'] ?? '') ?>">
      <input type="number" name="quantity" placeholder="Quantity" min="1" required class="border rounded p-2" value="<?= htmlspecialchars($editOrder['quantity'] ?? '') ?>">
      <input type="number" step="0.01" name="price" placeholder="Price" min="0" required class="border rounded p-2" value="<?= htmlspecialchars($editOrder['price'] ?? '') ?>">
      <select name="status" required class="border rounded p-2 col-span-1">
        <option value="pending" <?= (isset($editOrder['status']) && $editOrder['status']=='pending')?'selected':'' ?>>Pending</option>
        <option value="paid" <?= (isset($editOrder['status']) && $editOrder['status']=='paid')?'selected':'' ?>>Paid</option>
        <option value="canceled" <?= (isset($editOrder['status']) && $editOrder['status']=='canceled')?'selected':'' ?>>Canceled</option>
      </select>
      <button type="submit" name="save_order" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 col-span-full md:col-span-1"><?= $editOrder ? 'Update' : 'Add' ?></button>
    </form>

    <!-- Orders Table -->
    <div class="overflow-x-auto">
      <table class="min-w-full bg-white border rounded">
        <thead class="bg-gray-100">
          <tr>
            <th class="py-2 px-4 border">ID</th>
            <th class="py-2 px-4 border">Customer</th>
            <th class="py-2 px-4 border">Description</th>
            <th class="py-2 px-4 border">Qty</th>
            <th class="py-2 px-4 border">Price</th>
            <th class="py-2 px-4 border">Status</th>
            <th class="py-2 px-4 border">Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($orders as $o): ?>
            <tr class="text-center border-b hover:bg-gray-50">
              <td class="py-2 px-4 border"><?= $o['id'] ?></td>
              <td class="py-2 px-4 border"><?= htmlspecialchars($o['customer_name']) ?></td>
              <td class="py-2 px-4 border"><?= htmlspecialchars($o['description']) ?></td>
              <td class="py-2 px-4 border"><?= $o['quantity'] ?></td>
              <td class="py-2 px-4 border">$<?= number_format($o['price'],2) ?></td>
              <td class="py-2 px-4 border capitalize"><?= htmlspecialchars($o['status']) ?></td>
              <td class="py-2 px-4 border space-x-2">
                <a href="?edit_id=<?= $o['id'] ?>" class="bg-yellow-400 text-white px-3 py-1 rounded hover:bg-yellow-500">Edit</a>
                <a href="?delete_id=<?= $o['id'] ?>" onclick="return confirm('Delete this order?')" class="bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600">Delete</a>
              </td>
            </tr>
          <?php endforeach; ?>
          <?php if (empty($orders)): ?>
            <tr><td colspan="7" class="py-4">No orders found.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</body>
</html>
