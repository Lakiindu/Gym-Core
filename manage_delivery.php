<?php
session_start();

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
    <title>Manage Deliveries</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-6">
    <div class="mb-6">
        <a href="admin_dashboard.php" class="bg-gray-700 text-white px-4 py-2 rounded hover:bg-gray-800">&larr; Back to Admin Dashboard</a>
    </div>

    <section class="bg-white p-6 rounded-lg shadow">
        <h2 class="text-2xl font-bold text-blue-600 mb-4">Manage Deliveries</h2>

        <!-- Form -->
        <form method="POST" class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <?php if ($editDelivery): ?>
                <input type="hidden" name="delivery_id" value="<?= $editDelivery['id'] ?>" />
            <?php endif; ?>

            <select name="order_id" required class="p-2 border rounded">
                <option value="">Select Order</option>
                <?php foreach ($orders as $o): ?>
                    <option value="<?= $o['id'] ?>" <?= $editDelivery && $editDelivery['order_id'] == $o['id'] ? 'selected' : '' ?>>
                        <?= "Order #" . $o['id'] . " - " . htmlspecialchars($o['customer_name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <input type="text" name="delivery_address" required placeholder="Address" value="<?= $editDelivery['delivery_address'] ?? '' ?>" class="p-2 border rounded" />

            <select name="delivery_status" required class="p-2 border rounded">
                <option value="Pending" <?= isset($editDelivery) && $editDelivery['delivery_status'] == 'Pending' ? 'selected' : '' ?>>Pending</option>
                <option value="In Transit" <?= isset($editDelivery) && $editDelivery['delivery_status'] == 'In Transit' ? 'selected' : '' ?>>In Transit</option>
                <option value="Delivered" <?= isset($editDelivery) && $editDelivery['delivery_status'] == 'Delivered' ? 'selected' : '' ?>>Delivered</option>
            </select>

            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                <?= $editDelivery ? 'Update' : 'Add' ?> Delivery
            </button>
        </form>

        <!-- Delivery Table -->
        <div class="overflow-x-auto">
            <table class="min-w-full border text-sm">
                <thead class="bg-gray-200">
                    <tr>
                        <th class="py-2 px-4">ID</th>
                        <th class="py-2 px-4">Order</th>
                        <th class="py-2 px-4">Address</th>
                        <th class="py-2 px-4">Status</th>
                        <th class="py-2 px-4">Date</th>
                        <th class="py-2 px-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($deliveries as $d): ?>
                        <tr class="border-t hover:bg-gray-50">
                            <td class="py-2 px-4"><?= $d['id'] ?></td>
                            <td class="py-2 px-4"><?= "Order #" . $d['order_id'] . " - " . htmlspecialchars($d['customer_name']) ?></td>
                            <td class="py-2 px-4"><?= htmlspecialchars($d['delivery_address']) ?></td>
                            <td class="py-2 px-4 <?= $d['delivery_status'] == 'Delivered' ? 'text-green-600' : 'text-yellow-600' ?>"><?= $d['delivery_status'] ?></td>
                            <td class="py-2 px-4"><?= date("Y-m-d H:i", strtotime($d['delivery_date'])) ?></td>
                            <td class="py-2 px-4 space-x-2">
                                <a href="?edit=<?= $d['id'] ?>" class="text-blue-600 hover:underline">Edit</a>
                                <a href="?delete=<?= $d['id'] ?>" onclick="return confirm('Delete this delivery?');" class="text-red-600 hover:underline">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </section>
</body>
</html>
