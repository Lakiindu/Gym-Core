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
</head>
<body class="bg-gray-100 p-6">
  <a href="admin_dashboard.php" class="bg-gray-800 text-white px-4 py-2 rounded hover:bg-gray-900 mb-4 inline-block">&larr; Back to Admin Dashboard</a>

  <section class="bg-white p-6 rounded-lg shadow-lg">
    <h2 class="text-2xl font-bold text-green-600 mb-6">Membership Management</h2>

    <!-- Search Bar -->
    <form method="GET" class="mb-4 flex flex-wrap gap-2">
      <input
        type="text"
        name="search"
        placeholder="Search by member username..."
        value="<?= htmlspecialchars($search) ?>"
        class="p-2 border rounded w-1/2"
      />
      <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">Search</button>
    </form>

    <!-- Add/Edit Form -->
    <form method="POST" class="grid md:grid-cols-6 gap-4 mb-6">
      <input type="hidden" name="membership_id" id="membership_id" />
      
      <!-- User dropdown -->
      <select name="user_id" id="user_id" required class="p-2 border rounded col-span-2">
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
        class="p-2 border rounded col-span-1"
      />
      <input
        type="date"
        name="start_date"
        id="start_date"
        required
        class="p-2 border rounded col-span-1"
      />
      <input
        type="date"
        name="end_date"
        id="end_date"
        required
        class="p-2 border rounded col-span-1"
      />
      <select name="status" id="status" class="p-2 border rounded col-span-1">
        <option value="active">Active</option>
        <option value="inactive">Inactive</option>
        <option value="expired">Expired</option>
      </select>
      <button
        type="submit"
        name="save_membership"
        class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600 col-span-6"
      >
        Save Membership
      </button>
    </form>

    <!-- Membership Table -->
    <div class="overflow-x-auto">
      <table class="w-full border">
        <thead class="bg-gray-200">
          <tr>
            <th class="py-2 px-3 text-left">ID</th>
            <th class="py-2 px-3 text-left">Member Username</th>
            <th class="py-2 px-3 text-left">Plan</th>
            <th class="py-2 px-3 text-left">Start</th>
            <th class="py-2 px-3 text-left">End</th>
            <th class="py-2 px-3 text-left">Status</th>
            <th class="py-2 px-3 text-left">Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($memberships as $mem): ?>
            <tr class="border-t hover:bg-gray-50">
              <td class="px-3 py-2"><?= $mem['id'] ?></td>
              <td class="px-3 py-2"><?= htmlspecialchars($mem['member_name'] ?? 'N/A') ?></td>
              <td class="px-3 py-2"><?= htmlspecialchars($mem['plan']) ?></td>
              <td class="px-3 py-2"><?= $mem['start_date'] ?></td>
              <td class="px-3 py-2"><?= $mem['end_date'] ?></td>
              <td class="px-3 py-2">
                <span class="px-2 py-1 rounded text-white
                  <?= $mem['status'] === 'active' ? 'bg-green-500' : ($mem['status'] === 'inactive' ? 'bg-yellow-500' : 'bg-red-500') ?>">
                  <?= ucfirst($mem['status']) ?>
                </span>
              </td>
              <td class="px-3 py-2 space-x-2">
                <button
                  onclick='editMembership(<?= json_encode($mem) ?>)'
                  class="bg-yellow-400 text-white px-3 py-1 rounded hover:bg-yellow-500"
                >
                  Edit
                </button>
                <a
                  href="?delete_id=<?= $mem['id'] ?>"
                  onclick="return confirm('Are you sure?')"
                  class="bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600"
                >
                  Delete
                </a>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </section>

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
