<?php
session_start();

// Check if admin is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

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

// Helpers
function redirectToDashboard() {
    header("Location: admin_dashboard.php");
    exit;
}

// Handle CRUD actions
$action = $_GET['action'] ?? '';
$msg = '';
$error = '';

// --- CREATE new user ---
if ($action === 'add' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $role = $_POST['role'] ?? 'member';

    if ($username === '' || $email === '' || $password === '') {
        $error = "All fields are required.";
    } else {
        // Check if username or email already exists
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE username = ? OR email = ?");
        $stmt->execute([$username, $email]);
        if ($stmt->fetchColumn() > 0) {
            $error = "Username or Email already exists.";
        } else {
            // Insert user and role
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $pdo->beginTransaction();
            try {
                $stmt = $pdo->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
                $stmt->execute([$username, $email, $hashed]);
                $userId = $pdo->lastInsertId();

                $stmt = $pdo->prepare("INSERT INTO user_roles (user_id, role) VALUES (?, ?)");
                $stmt->execute([$userId, $role]);

                $pdo->commit();
                $msg = "User added successfully.";
                redirectToDashboard();
            } catch (Exception $e) {
                $pdo->rollBack();
                $error = "Failed to add user: " . $e->getMessage();
            }
        }
    }
}

// --- UPDATE user ---
if ($action === 'edit' && isset($_GET['id']) && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)$_GET['id'];
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $role = $_POST['role'] ?? 'member';

    if ($username === '' || $email === '') {
        $error = "Username and Email are required.";
    } else {
        try {
            $pdo->beginTransaction();

            // Update user
            if ($password !== '') {
                $hashed = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("UPDATE users SET username = ?, email = ?, password = ? WHERE id = ?");
                $stmt->execute([$username, $email, $hashed, $id]);
            } else {
                $stmt = $pdo->prepare("UPDATE users SET username = ?, email = ? WHERE id = ?");
                $stmt->execute([$username, $email, $id]);
            }

            // Update role (if exists)
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM user_roles WHERE user_id = ?");
            $stmt->execute([$id]);
            if ($stmt->fetchColumn() > 0) {
                $stmt = $pdo->prepare("UPDATE user_roles SET role = ? WHERE user_id = ?");
                $stmt->execute([$role, $id]);
            } else {
                $stmt = $pdo->prepare("INSERT INTO user_roles (user_id, role) VALUES (?, ?)");
                $stmt->execute([$id, $role]);
            }

            $pdo->commit();
            $msg = "User updated successfully.";
            redirectToDashboard();
        } catch (Exception $e) {
            $pdo->rollBack();
            $error = "Failed to update user: " . $e->getMessage();
        }
    }
}

// --- DELETE user ---
if ($action === 'delete' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    try {
        $pdo->beginTransaction();

        $stmt = $pdo->prepare("DELETE FROM user_roles WHERE user_id = ?");
        $stmt->execute([$id]);

        $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
        $stmt->execute([$id]);

        $pdo->commit();
        $msg = "User deleted successfully.";
        redirectToDashboard();
    } catch (Exception $e) {
        $pdo->rollBack();
        $error = "Failed to delete user: " . $e->getMessage();
    }
}

// --- SEARCH users ---
$search = trim($_GET['search'] ?? '');

// Fetch users (with search)
try {
    if ($search !== '') {
        $stmt = $pdo->prepare("
            SELECT u.id, u.username, u.email, ur.role
            FROM users u
            LEFT JOIN user_roles ur ON u.id = ur.user_id
            WHERE u.username ILIKE :search OR u.email ILIKE :search
            ORDER BY u.id ASC
        ");
        $stmt->execute(['search' => "%$search%"]);
    } else {
        $stmt = $pdo->prepare("
            SELECT u.id, u.username, u.email, ur.role
            FROM users u
            LEFT JOIN user_roles ur ON u.id = ur.user_id
            ORDER BY u.id ASC
        ");
        $stmt->execute();
    }
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error fetching users: " . $e->getMessage());
}

// For edit form, if action=edit and id set, fetch that user
$editUser = null;
if ($action === 'edit' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $stmt = $pdo->prepare("SELECT u.id, u.username, u.email, ur.role FROM users u LEFT JOIN user_roles ur ON u.id = ur.user_id WHERE u.id = ?");
    $stmt->execute([$id]);
    $editUser = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Admin Dashboard - GYM Core</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            primary: "#ff6600",
          },
        },
      },
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
<body class="bg-gray-100 text-gray-900 font-sans">

  <!-- Navbar -->
  <header class="bg-white shadow-md fixed w-full top-0 z-50">
    <div class="max-w-7xl mx-auto px-6 py-4 flex justify-between items-center">
      <div class="text-2xl font-bold text-primary">Admin Dashboard</div>
      <div class="flex items-center gap-4">
        <span class="text-sm font-medium">Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?></span>
        <a href="logout.php" class="text-red-500 hover:text-red-700 font-bold">Logout</a>
      </div>
    </div>
  </header>

  <!-- Sidebar + Main Content -->
  <div class="flex pt-20 min-h-screen">
    <!-- Sidebar -->
    <aside class="w-64 bg-gray-800 text-white p-6 space-y-6">
      <nav class="space-y-4 sticky top-20">
        <a href="#users" class="block px-4 py-2 rounded bg-primary text-white">Manage Users</a>
        <a href="manage_supplements.php" class="block px-4 py-2 hover:bg-gray-700 rounded">Manage Supplements</a>
        <a href="manage_memberships.php" class="block px-4 py-2 hover:bg-gray-700 rounded">Manage Memberships</a>
        <a href="manage_orders.php" class="block px-4 py-2 hover:bg-gray-700 rounded">Manage Orders</a>
        <a href="manage_payments.php" class="block px-4 py-2 hover:bg-gray-700 rounded">Manage payments</a>
        <a href="manage_delivery.php" class="block px-4 py-2 hover:bg-gray-700 rounded">Delivery details</a>
      </nav>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 p-8 space-y-12">

      <!-- Messages -->
      <?php if ($msg): ?>
        <div class="bg-green-100 text-green-800 p-4 rounded"><?php echo htmlspecialchars($msg); ?></div>
      <?php endif; ?>
      <?php if ($error): ?>
        <div class="bg-red-100 text-red-800 p-4 rounded"><?php echo htmlspecialchars($error); ?></div>
      <?php endif; ?>

      <!-- User Management Section -->
      <section id="users" class="bg-white p-6 rounded-lg shadow animate-fadeIn">
        <h2 class="text-2xl font-semibold mb-4 text-primary flex justify-between items-center">
          User Management
          <?php if (!$editUser): ?>
          <button onclick="document.getElementById('addUserForm').classList.toggle('hidden')" class="bg-primary text-white px-4 py-2 rounded hover:bg-primary/90">
            Add New User
          </button>
          <?php else: ?>
          <a href="admin_dashboard.php#users" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">Cancel Edit</a>
          <?php endif; ?>
        </h2>

        <!-- Search Form -->
        <form method="get" action="admin_dashboard.php" class="mb-6 flex gap-2">
          <input
            type="hidden" name="action" value=""
          />
          <input
            type="text" name="search" placeholder="Search by username or email"
            value="<?php echo htmlspecialchars($search); ?>"
            class="border px-3 py-2 rounded w-full max-w-xs"
          />
          <button type="submit" class="bg-primary text-white px-4 py-2 rounded hover:bg-primary/90">Search</button>
          <a href="admin_dashboard.php" class="ml-2 text-gray-600 hover:underline">Reset</a>
        </form>

        <!-- Add User Form -->
        <form
          id="addUserForm"
          action="admin_dashboard.php?action=add#users"
          method="POST"
          class="mb-8 space-y-4 p-4 border rounded bg-gray-50 <?php echo $editUser ? 'hidden' : ''; ?>"
          autocomplete="off"
        >
          <h3 class="text-xl font-semibold text-gray-700">Add New User</h3>
          <div>
            <label for="username" class="block font-medium mb-1">Username</label>
            <input type="text" name="username" id="username" required class="w-full border px-3 py-2 rounded" />
          </div>
          <div>
            <label for="email" class="block font-medium mb-1">Email</label>
            <input type="email" name="email" id="email" required class="w-full border px-3 py-2 rounded" />
          </div>
          <div>
            <label for="password" class="block font-medium mb-1">Password</label>
            <input type="password" name="password" id="password" required class="w-full border px-3 py-2 rounded" />
          </div>
          <div>
            <label for="role" class="block font-medium mb-1">Role</label>
            <select name="role" id="role" class="w-full border px-3 py-2 rounded">
              <option value="member" selected>Member</option>
              <option value="trainer">Trainer</option>
              <option value="admin">Admin</option>
              <option value="stock manager">Stock Manager</option>
              <option value="rider">rider</option>
            </select>
          </div>
          <button type="submit" class="bg-primary text-white px-5 py-2 rounded hover:bg-primary/90 font-semibold">Add User</button>
        </form>

        <!-- Edit User Form -->
        <?php if ($editUser): ?>
        <form
          action="admin_dashboard.php?action=edit&id=<?php echo $editUser['id']; ?>#users"
          method="POST"
          class="mb-8 space-y-4 p-4 border rounded bg-gray-50 animate-fadeIn"
          autocomplete="off"
        >
          <h3 class="text-xl font-semibold text-gray-700">Edit User ID #<?php echo $editUser['id']; ?></h3>
          <div>
            <label for="username" class="block font-medium mb-1">Username</label>
            <input
              type="text"
              name="username"
              id="username"
              required
              value="<?php echo htmlspecialchars($editUser['username']); ?>"
              class="w-full border px-3 py-2 rounded"
            />
          </div>
          <div>
            <label for="email" class="block font-medium mb-1">Email</label>
            <input
              type="email"
              name="email"
              id="email"
              required
              value="<?php echo htmlspecialchars($editUser['email']); ?>"
              class="w-full border px-3 py-2 rounded"
            />
          </div>
          <div>
            <label for="password" class="block font-medium mb-1">
              Password (leave blank to keep current)
            </label>
            <input
              type="password"
              name="password"
              id="password"
              class="w-full border px-3 py-2 rounded"
            />
          </div>
          <div>
            <label for="role" class="block font-medium mb-1">Role</label>
            <select name="role" id="role" class="w-full border px-3 py-2 rounded">
              <option value="member" <?php echo $editUser['role'] === 'member' ? 'selected' : ''; ?>>Member</option>
              <option value="trainer" <?php echo $editUser['role'] === 'trainer' ? 'selected' : ''; ?>>Trainer</option>
              <option value="admin" <?php echo $editUser['role'] === 'admin' ? 'selected' : ''; ?>>Admin</option>
              <option value="stock manager" <?php echo $editUser['role'] === 'stock manager' ? 'selected' : ''; ?>>stock manager</option>
              <option value="rider" <?php echo $editUser['role'] === 'rider' ? 'selected' : ''; ?>>rider</option>
            </select>
          </div>
          <button type="submit" class="bg-primary text-white px-5 py-2 rounded hover:bg-primary/90 font-semibold">Update User</button>
        </form>
        <?php endif; ?>

        <!-- User Table -->
        <div class="overflow-x-auto">
          <table class="min-w-full table-auto border-collapse border border-gray-300">
            <thead class="bg-primary text-white">
              <tr>
                <th class="border border-gray-300 px-4 py-2">ID</th>
                <th class="border border-gray-300 px-4 py-2">Username</th>
                <th class="border border-gray-300 px-4 py-2">Email</th>
                <th class="border border-gray-300 px-4 py-2">Role</th>
                <th class="border border-gray-300 px-4 py-2">Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php if (count($users) === 0): ?>
                <tr><td colspan="5" class="text-center py-4">No users found.</td></tr>
              <?php else: ?>
                <?php foreach ($users as $user): ?>
                  <tr class="odd:bg-gray-50 even:bg-white hover:bg-gray-100">
                    <td class="border border-gray-300 px-4 py-2"><?php echo $user['id']; ?></td>
                    <td class="border border-gray-300 px-4 py-2"><?php echo htmlspecialchars($user['username']); ?></td>
                    <td class="border border-gray-300 px-4 py-2"><?php echo htmlspecialchars($user['email']); ?></td>
                    <td class="border border-gray-300 px-4 py-2 capitalize"><?php echo htmlspecialchars($user['role'] ?? 'member'); ?></td>
                    <td class="border border-gray-300 px-4 py-2 space-x-2">
                      <a
                        href="admin_dashboard.php?action=edit&id=<?php echo $user['id']; ?>#users"
                        class="text-blue-600 hover:text-blue-900"
                        title="Edit User"
                      >
                        <i class="fa-solid fa-pen-to-square"></i>
                      </a>
                      <a
                        href="admin_dashboard.php?action=delete&id=<?php echo $user['id']; ?>"
                        class="text-red-600 hover:text-red-900"
                        title="Delete User"
                        onclick="return confirm('Are you sure you want to delete this user?');"
                      >
                        <i class="fa-solid fa-trash"></i>
                      </a>
                    </td>
                  </tr>
                <?php endforeach; ?>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </section>

      </section>
    </main>
  </div>
</body>
</html>
