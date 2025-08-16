<?php
session_start();

// PostgreSQL Connection
$host = "localhost";
$dbname = "gym_db";
$user = "postgres";
$password = "lakindu";

try {
    $pdo = new PDO("pgsql:host=$host;dbname=$dbname", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("DB Connection Failed: " . $e->getMessage());
}

// Fetch users for the dropdown (id + username)
$users = $pdo->query("SELECT id, username FROM users ORDER BY username ASC")->fetchAll(PDO::FETCH_ASSOC);

// Insert/Update Membership
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_membership'])) {
    $id = $_POST['membership_id'] ?? null;
    $user_id = $_POST['user_id'] ?? null;
    $plan = $_POST['plan'];
    $start = $_POST['start_date'];
    $end = $_POST['end_date'];
    $status = $_POST['status'];

    if (!$user_id) {
        die("User must be selected.");
    }

    if ($id) {
        $stmt = $pdo->prepare("UPDATE memberships SET user_id=?, plan=?, start_date=?, end_date=?, status=? WHERE id=?");
        $stmt->execute([$user_id, $plan, $start, $end, $status, $id]);
    } else {
        $stmt = $pdo->prepare("INSERT INTO memberships (user_id, plan, start_date, end_date, status) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$user_id, $plan, $start, $end, $status]);
    }

    header("Location: manage_memberships.php");
    exit;
}

// Delete Membership
if (isset($_GET['delete_id'])) {
    $stmt = $pdo->prepare("DELETE FROM memberships WHERE id = ?");
    $stmt->execute([$_GET['delete_id']]);
    header("Location: manage_memberships.php");
    exit;
}

// Search by username
$search = $_GET['search'] ?? '';
if ($search) {
    $stmt = $pdo->prepare("
        SELECT memberships.*, users.username AS member_name
        FROM memberships
        LEFT JOIN users ON memberships.user_id = users.id
        WHERE users.username ILIKE ?
        ORDER BY memberships.id ASC
    ");
    $stmt->execute(['%' . $search . '%']);
} else {
    $stmt = $pdo->query("
        SELECT memberships.*, users.username AS member_name
        FROM memberships
        LEFT JOIN users ON memberships.user_id = users.id
        ORDER BY memberships.id ASC
    ");
}
$memberships = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Manage Memberships</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
</head>
<body class="min-h-screen bg-orange-50 bg-gray-100 text-gray-800 font-sans">


  <!-- Navbar -->
  <header class="bg-white shadow fixed w-full top-0 z-50">
    <div class="max-w-7xl mx-auto px-6 py-4 flex justify-between items-center">
      <div class="text-2xl font-bold text-orange-500 flex items-center gap-2">
        <i class="fa-solid fa-dumbbell"></i> GYM Core Admin
      </div>
      <div class="flex items-center gap-4">
        <span class="text-sm font-medium">Welcome, <?php echo htmlspecialchars($_SESSION['username'] ?? 'Admin'); ?></span>
        <a href="logout.php" class="text-red-500 hover:text-red-700 font-bold flex items-center gap-1">
          <i class="fa-solid fa-right-from-bracket"></i> Logout
        </a>
      </div>
    </div>
  </header>

  <div class="flex pt-16 min-h-screen">

    <!-- Sidebar -->
    <aside class="w-64 bg-gray-800 text-white p-6 space-y-6 sticky top-16 h-screen">
      <nav class="space-y-4">
        <a href="admin_dashboard.php#users" class="block px-4 py-2 rounded hover:bg-orange-500 transition text-gray-300 hover:text-white flex items-center gap-2">
          <i class="fa-solid fa-users"></i> Manage Users
        </a>
        <a href="manage_supplements.php" class="block px-4 py-2 rounded hover:bg-gray-700 transition text-gray-300 hover:text-white flex items-center gap-2">
          <i class="fa-solid fa-capsules"></i> Manage Supplements
        </a>
        <a href="manage_memberships.php" class="block px-4 py-2 rounded bg-orange-500 text-white flex items-center gap-2" aria-current="page">
          <i class="fa-solid fa-id-card"></i> Manage Memberships
        </a>
        <a href="manage_orders.php" class="block px-4 py-2 rounded hover:bg-gray-700 transition text-gray-300 hover:text-white flex items-center gap-2">
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
    <main class="flex-1 max-w-6xl mx-auto p-6 space-y-6">

      <!-- Page Header -->
      <h1 class="text-3xl font-bold text-orange-500 flex items-center gap-2">
        <i class="fa-solid fa-id-card"></i> Manage Memberships
      </h1>

      <!-- Search Form -->
      <form method="GET" class="mb-6 flex gap-3 max-w-md">
        <input
          type="text"
          name="search"
          placeholder="Search by member username..."
          value="<?= htmlspecialchars($search) ?>"
          class="flex-grow p-3 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-orange-400"
        />
        <button type="submit" class="bg-orange-500 text-white px-5 py-2 rounded hover:bg-orange-600 transition">
          Search
        </button>
      </form>

      <!-- Add/Edit Membership Form -->
      <form method="POST" class="bg-white p-6 rounded shadow max-w-6xl grid grid-cols-1 md:grid-cols-6 gap-4 mb-6">
        <input type="hidden" name="membership_id" id="membership_id" />
        
        <select name="user_id" id="user_id" required class="p-3 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-orange-400 col-span-2">
          <option value="" disabled selected>Select Member</option>
          <?php foreach ($users as $user): ?>
            <option value="<?= $user['id'] ?>"><?= htmlspecialchars($user['username']) ?></option>
          <?php endforeach; ?>
        </select>
        
        <input
          type="text"
          name="plan"
          id="plan"
          placeholder="Plan (e.g. Gold, Silver)"
          required
          class="p-3 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-orange-400 col-span-1"
        />
        <input
          type="date"
          name="start_date"
          id="start_date"
          required
          class="p-3 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-orange-400 col-span-1"
        />
        <input
          type="date"
          name="end_date"
          id="end_date"
          required
          class="p-3 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-orange-400 col-span-1"
        />
        <select name="status" id="status" class="p-3 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-orange-400 col-span-1">
          <option value="active">Active</option>
          <option value="inactive">Inactive</option>
          <option value="expired">Expired</option>
        </select>

        <div class="col-span-6 flex justify-center">
          <button
            type="submit"
            name="save_membership"
            class="bg-orange-500 text-white font-semibold rounded hover:bg-orange-600 transition px-3 py-3 text-lg w-3/3"
          >
            Save Membership
          </button>
        </div>
      </form>

      <!-- Membership Table -->
      <div class="overflow-x-auto bg-white rounded shadow max-w-6xl">
        <table class="min-w-full table-auto border-collapse">
          <thead>
            <tr class="bg-orange-100 text-orange-700">
              <th class="text-left px-6 py-3">ID</th>
              <th class="text-left px-6 py-3">Member Username</th>
              <th class="text-left px-6 py-3">Plan</th>
              <th class="text-left px-6 py-3">Start</th>
              <th class="text-left px-6 py-3">End</th>
              <th class="text-left px-6 py-3">Status</th>
              <th class="text-left px-6 py-3">Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($memberships as $mem): ?>
              <tr class="border-b hover:bg-orange-50">
                <td class="px-6 py-3"><?= $mem['id'] ?></td>
                <td class="px-6 py-3"><?= htmlspecialchars($mem['member_name'] ?? 'N/A') ?></td>
                <td class="px-6 py-3"><?= htmlspecialchars($mem['plan']) ?></td>
                <td class="px-6 py-3"><?= $mem['start_date'] ?></td>
                <td class="px-6 py-3"><?= $mem['end_date'] ?></td>
                <td class="px-6 py-3">
                  <span class="px-2 py-1 rounded text-white
                    <?= $mem['status'] === 'active' ? 'bg-green-500' : ($mem['status'] === 'inactive' ? 'bg-yellow-500' : 'bg-red-500') ?>">
                    <?= ucfirst($mem['status']) ?>
                  </span>
                </td>
                <td class="px-6 py-3 flex gap-4">
                  <button
                    onclick='editMembership(<?= json_encode($mem) ?>)'
                    class="bg-yellow-400 hover:bg-yellow-500 text-white px-3 py-1 rounded"
                  >
                    Edit
                  </button>
                  <a
                    href="?delete_id=<?= $mem['id'] ?>"
                    onclick="return confirm('Are you sure?')"
                    class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded"
                  >
                    Delete
                  </a>
                </td>
              </tr>
            <?php endforeach; ?>
            <?php if (count($memberships) === 0): ?>
              <tr>
                <td colspan="7" class="text-center py-6 italic text-gray-500">No memberships found.</td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>

    </main>
  </div>

  <script>
    function editMembership(mem) {
      document.getElementById('membership_id').value = mem.id;
      document.getElementById('user_id').value = mem.user_id || '';
      document.getElementById('plan').value = mem.plan;
      document.getElementById('start_date').value = mem.start_date;
      document.getElementById('end_date').value = mem.end_date;
      document.getElementById('status').value = mem.status;
      window.scrollTo({ top: 0, behavior: 'smooth' });
    }
  </script>

</body>
</html>
