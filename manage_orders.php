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
  <body class="bg-orange-50 min-h-screen">

  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Manage Orders</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            primary: '#f97316',
            darkbg: '#1e293b',
            card: '#f1f5f9'
          }
        }
      }
    };
  </script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
  <style>
    .animate-fadeIn {
      animation: fadeIn 0.6s ease-in-out;
    }
    @keyframes fadeIn {
      0% { opacity: 0; transform: translateY(10px); }
      100% { opacity: 1; transform: translateY(0); }
    }
  </style>
</head>
<body class="min-h-screen bg-gradient-to-b from-gray-100 via-gray-200 to-gray-100 text-gray-800 font-sans">

  <!-- Navbar -->
  <header class="bg-white shadow-md fixed w-full top-0 z-50">
    <div class="max-w-7xl mx-auto px-6 py-4 flex justify-between items-center">
      <div class="text-2xl font-bold text-primary flex items-center gap-2">
        <i class="fa-solid fa-dumbbell"></i> <span>GYM Core Admin</span>
      </div>
      <div class="flex items-center gap-4">
        <span class="text-sm font-medium">Welcome, <?= htmlspecialchars($_SESSION['username'] ?? 'Admin') ?></span>
        <a href="logout.php" class="text-red-500 hover:text-red-700 font-bold flex items-center gap-1">
          <i class="fa-solid fa-right-from-bracket"></i> Logout
        </a>
      </div>
    </div>
  </header>

  <div class="flex pt-16 min-h-screen">

    <!-- Sidebar -->
    <aside class="w-64 bg-gray-800 text-white p-6 space-y-6 sticky top-16 h-screen flex-shrink-0">
      <nav class="space-y-4">
        <a href="admin_dashboard.php#users" class="block px-4 py-2 rounded hover:bg-primary transition text-gray-300 hover:text-white flex items-center gap-2">
          <i class="fa-solid fa-users"></i> Manage Users
        </a>
        <a href="manage_supplements.php" class="block px-4 py-2 rounded hover:bg-gray-700 transition text-gray-300 hover:text-white flex items-center gap-2">
          <i class="fa-solid fa-capsules"></i> Manage Supplements
        </a>
        <a href="manage_memberships.php" class="block px-4 py-2 rounded hover:bg-gray-700 transition text-gray-300 hover:text-white flex items-center gap-2">
          <i class="fa-solid fa-id-card"></i> Manage Memberships
        </a>
        <a href="manage_orders.php" class="block px-4 py-2 rounded bg-primary text-white flex items-center gap-2" aria-current="page">
          <i class="fa-solid fa-cart-shopping"></i> Manage Orders
        </a>
        <a href="manage_payments.php" class="block px-4 py-2 rounded hover:bg-gray-700 transition text-gray-300 hover:text-white flex items-center gap-2">
          <i class="fa-solid fa-credit-card"></i> Manage Payments
        </a>
        <a href="manage_delivery.php" class="block px-4 py-2 rounded hover:bg-gray-700 transition text-gray-300 hover:text-white flex items-center gap-2">
          <i class="fa-solid fa-truck"></i> Delivery Details
        </a>
      </nav>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 p-8 max-w-7xl mx-auto" style="margin-left: 0rem;">
      <!-- margin-left: 0rem to offset sidebar width -->

      <!-- Page Header -->
      <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-primary flex items-center gap-2">
          <i class="fa-solid fa-cart-shopping"></i> Manage Orders
        </h1>
      </div>

      <!-- Messages -->
      <?php if ($msg): ?>
        <div class="mb-4 bg-green-100 text-green-800 p-4 rounded"><?= htmlspecialchars($msg) ?></div>
      <?php endif; ?>
      <?php if ($error): ?>
        <div class="mb-4 bg-red-100 text-red-800 p-4 rounded"><?= htmlspecialchars($error) ?></div>
      <?php endif; ?>

      <!-- Search Form -->
      <form method="GET" class="mb-6 flex gap-2 max-w-4xl mx-auto">
        <input name="search" value="<?= htmlspecialchars($searchTerm) ?>" placeholder="Search orders..." class="flex-1 p-3 border border-gray-300 rounded shadow-sm" />
        <button type="submit" class="bg-primary text-white px-5 py-2 rounded hover:bg-orange-600 transition">Search</button>
      </form>

      <!-- Add/Edit Order Form -->
      <form method="POST" class="bg-card p-6 rounded-lg shadow-md mb-10 grid grid-cols-1 md:grid-cols-6 gap-4 max-w-4xl mx-auto">
        <input type="hidden" name="order_id" value="<?= $editOrder['id'] ?? '' ?>">
        <input type="text" name="customer_name" placeholder="Customer Name" required class="col-span-2 p-3 border border-gray-300 rounded" value="<?= htmlspecialchars($editOrder['customer_name'] ?? '') ?>">
        <input type="text" name="description" placeholder="Description" required class="col-span-2 p-3 border border-gray-300 rounded" value="<?= htmlspecialchars($editOrder['description'] ?? '') ?>">
        <input type="number" name="quantity" placeholder="Quantity" min="1" required class="p-3 border border-gray-300 rounded" value="<?= htmlspecialchars($editOrder['quantity'] ?? '') ?>">
        <input type="number" step="0.01" name="price" placeholder="Price" min="0" required class="p-3 border border-gray-300 rounded" value="<?= htmlspecialchars($editOrder['price'] ?? '') ?>">
        <select name="status" required class="border border-gray-300 rounded p-3 col-span-1">
          <option value="pending" <?= (isset($editOrder['status']) && $editOrder['status']=='pending')?'selected':'' ?>>Pending</option>
          <option value="paid" <?= (isset($editOrder['status']) && $editOrder['status']=='paid')?'selected':'' ?>>Paid</option>
          <option value="canceled" <?= (isset($editOrder['status']) && $editOrder['status']=='canceled')?'selected':'' ?>>Canceled</option>
        </select>
        <button type="submit" name="save_order" class="col-span-full md:col-span-1 bg-primary text-white px-6 py-3 rounded hover:bg-orange-600 transition font-semibold"><?= $editOrder ? 'Update' : 'Add' ?></button>
      </form>

      <!-- Orders Table -->
      <div class="overflow-x-auto bg-white rounded shadow-md max-w-4xl mx-auto">
        <table class="min-w-full table-auto">
          <thead class="bg-gray-100 border-b">
            <tr>
              <th class="text-left px-6 py-3 border">ID</th>
              <th class="text-left px-6 py-3 border">Customer</th>
              <th class="text-left px-6 py-3 border">Description</th>
              <th class="text-left px-6 py-3 border">Qty</th>
              <th class="text-left px-6 py-3 border">Price</th>
              <th class="text-left px-6 py-3 border">Status</th>
              <th class="text-left px-6 py-3 border">Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($orders as $o): ?>
              <tr class="hover:bg-gray-50 border-b">
                <td class="px-6 py-3 border"><?= $o['id'] ?></td>
                <td class="px-6 py-3 border"><?= htmlspecialchars($o['customer_name']) ?></td>
                <td class="px-6 py-3 border"><?= htmlspecialchars($o['description']) ?></td>
                <td class="px-6 py-3 border"><?= $o['quantity'] ?></td>
                <td class="px-6 py-3 border">$<?= number_format($o['price'],2) ?></td>
                <td class="px-6 py-3 border capitalize"><?= htmlspecialchars($o['status']) ?></td>
                <td class="px-6 py-3 border flex gap-2">
                  <a href="?edit_id=<?= $o['id'] ?>" class="bg-yellow-400 text-white px-3 py-1 rounded hover:bg-yellow-500 transition">Edit</a>
                  <a href="?delete_id=<?= $o['id'] ?>" onclick="return confirm('Delete this order?')" class="bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600 transition">Delete</a>
                </td>
              </tr>
            <?php endforeach; ?>
            <?php if (empty($orders)): ?>
              <tr><td colspan="7" class="py-4 text-center text-gray-500">No orders found.</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>

    </main>
  </div>

</body>
</html>
