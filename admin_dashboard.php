<?php
session_start();

// Redirect non-admin users to login page
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

// Database connection parameters
$host = "localhost"; 
$dbname = "gym_db";
$user = "postgres"; 
$password = "lakindu";

try {
    // Create PDO connection to PostgreSQL with error exceptions enabled
    $pdo = new PDO("pgsql:host=$host;dbname=$dbname", $user, $password, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
} catch (PDOException $e) {
    // If connection fails, stop script and show error
    die("DB connection failed: " . $e->getMessage());
}

// Generate a CSRF token if not set, return it
function csrf_token() {
    if (empty($_SESSION['csrf_token'])) $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    return $_SESSION['csrf_token'];
}

// Check if provided CSRF token matches session one (to prevent CSRF attacks)
function check_csrf_token($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

$msg = $error = '';  // Message and error variables to show feedback
$action = $_GET['action'] ?? '';  // Get current action (add, edit, delete, etc.)

// Handle Add User POST request
if ($action === 'add' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check CSRF token validity
    if (!check_csrf_token($_POST['csrf_token'] ?? '')) $error = "Invalid CSRF token.";
    else {
        // Sanitize input
        $username = trim($_POST['username'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $role = $_POST['role'] ?? 'member';

        // Validate required fields
        if ($username === '' || $email === '' || $password === '') $error = "All fields required.";
        else {
            // Check if username or email already exists
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE username = ? OR email = ?");
            $stmt->execute([$username, $email]);
            if ($stmt->fetchColumn() > 0) $error = "Username or Email exists.";
            else {
                try {
                    // Use transaction to ensure both inserts succeed or fail together
                    $pdo->beginTransaction();
                    // Insert new user with hashed password
                    $stmt = $pdo->prepare("INSERT INTO users (username,email,password) VALUES (?,?,?)");
                    $stmt->execute([$username, $email, password_hash($password, PASSWORD_DEFAULT)]);
                    // Get new user's ID
                    $userId = $pdo->lastInsertId();
                    // Insert role for user
                    $pdo->prepare("INSERT INTO user_roles (user_id, role) VALUES (?, ?)")->execute([$userId, $role]);
                    $pdo->commit();
                    // Redirect to dashboard anchor users (refresh page)
                    header("Location: admin_dashboard.php#users"); exit;
                } catch (Exception $e) {
                    $pdo->rollBack();
                    $error = "Add user failed: " . $e->getMessage();
                }
            }
        }
    }
}

// Handle Edit User POST request
if ($action === 'edit' && isset($_GET['id']) && $_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!check_csrf_token($_POST['csrf_token'] ?? '')) $error = "Invalid CSRF token.";
    else {
        $id = (int)$_GET['id'];
        $username = trim($_POST['username'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $role = $_POST['role'] ?? 'member';

        if ($username === '' || $email === '') $error = "Username and Email required.";
        else {
            try {
                $pdo->beginTransaction();

                // Update user info; hash password only if new password provided
                if ($password !== '') {
                    $pdo->prepare("UPDATE users SET username=?, email=?, password=? WHERE id=?")
                        ->execute([$username, $email, password_hash($password, PASSWORD_DEFAULT), $id]);
                } else {
                    $pdo->prepare("UPDATE users SET username=?, email=? WHERE id=?")
                        ->execute([$username, $email, $id]);
                }

                // Check if role exists, then update or insert accordingly
                $stmt = $pdo->prepare("SELECT COUNT(*) FROM user_roles WHERE user_id=?");
                $stmt->execute([$id]);
                if ($stmt->fetchColumn() > 0) {
                    $pdo->prepare("UPDATE user_roles SET role=? WHERE user_id=?")->execute([$role, $id]);
                } else {
                    $pdo->prepare("INSERT INTO user_roles (user_id, role) VALUES (?, ?)")->execute([$id, $role]);
                }

                $pdo->commit();
                header("Location: admin_dashboard.php#users"); exit;
            } catch (Exception $e) {
                $pdo->rollBack();
                $error = "Update failed: " . $e->getMessage();
            }
        }
    }
}

// Handle Delete User POST request
if ($action === 'delete' && $_SERVER['REQUEST_METHOD'] === 'POST' && check_csrf_token($_POST['csrf_token'] ?? '') && isset($_POST['id'])) {
    try {
        $pdo->beginTransaction();
        // Delete user role first (foreign key constraint)
        $pdo->prepare("DELETE FROM user_roles WHERE user_id=?")->execute([(int)$_POST['id']]);
        // Delete user
        $pdo->prepare("DELETE FROM users WHERE id=?")->execute([(int)$_POST['id']]);
        $pdo->commit();
        header("Location: admin_dashboard.php#users"); exit;
    } catch (Exception $e) {
        $pdo->rollBack();
        $error = "Delete failed: " . $e->getMessage();
    }
}

// Search users if search term is provided
$search = trim($_GET['search'] ?? '');
$q = $search !== '' ? "WHERE u.username ILIKE :s OR u.email ILIKE :s" : "";
$stmt = $pdo->prepare("SELECT u.id, u.username, u.email, ur.role FROM users u LEFT JOIN user_roles ur ON u.id=ur.user_id $q ORDER BY u.id ASC");
$stmt->execute($search !== '' ? ['s' => "%$search%"] : []);
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

// If editing, fetch user data for pre-filling form
$editUser = null;
if ($action === 'edit' && isset($_GET['id'])) {
    $stmt = $pdo->prepare("SELECT u.id, u.username, u.email, ur.role FROM users u LEFT JOIN user_roles ur ON u.id=ur.user_id WHERE u.id=?");
    $stmt->execute([(int)$_GET['id']]);
    $editUser = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
  <body class="min-h-screen bg-orange-50 bg-gray-100 text-gray-800 font-sans">

<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Admin Dashboard - GYM Core</title>
<script src="https://cdn.tailwindcss.com"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
<style>
  /* General page & layout styles */
  html, body {
    height: 100%;
    overflow-y: auto;
  }
  /* Form group style for inputs and labels */
  .form-group { position: relative; margin-bottom: 1.5rem; }
  .form-group input, .form-group select {
    width: 100%; padding: 1.1rem 0.75rem 0.3rem; border: 1.5px solid #ddd6fe; border-radius: 0.5rem;
    background: #f9fafb; outline: none; font-size: 1rem; transition: border-color 0.3s ease;
  }
  .form-group input:focus, .form-group select:focus {
    border-color: #f97316; background: white; box-shadow: 0 0 0 3px rgb(249 115 22 / 0.3);
  }
  /* Floating label style */
  .form-group label {
    position: absolute; top: 1.1rem; left: 0.75rem; font-size: 1rem; color: #a78bfa;
    background: #f9fafb; padding: 0 0.3rem; pointer-events: none; transition: 0.3s; border-radius: 0.25rem;
  }
  input:not(:placeholder-shown) + label,
  select:not([value=""]) + label,
  input:focus + label,
  select:focus + label {
    top: -0.5rem; font-size: 0.75rem; color: #f97316; font-weight: 700;
  }
  /* Table header styles */
  thead th {
    position: sticky; top: 0; background: #f97316; color: white; font-weight: 700; z-index: 10;
    padding: 0.75rem 1rem; text-align: left;
  }
  /* Table container with scroll */
  .table-container {
    max-height: 420px; overflow-y: auto; border-radius: 0.75rem; border: 1.5px solid #fed7aa;
    box-shadow: 0 6px 18px rgb(249 115 22 / 0.18);
  }
  /* Table row hover & zebra striping */
  tbody tr:hover { background-color: #fff7ed; transition: background-color 0.3s ease; }
  tbody tr:nth-child(odd) { background-color: #fffaf0; }
  /* Table cell padding */
  tbody td { padding: 0.7rem 1rem; vertical-align: middle; }
  /* Sidebar wrapper styles */
  .sidebar-wrapper {
    position: fixed; top: 5rem; left: 0; width: 16rem; height: calc(100vh - 5rem);
    background-color: #1e293b; padding: 1.5rem; overflow-y: auto;
  }
  /* Placeholder to keep main content aligned next to sidebar */
  .sidebar-placeholder { width: 16rem; flex-shrink: 0; }
  /* Sidebar navigation links */
  aside nav a {
    display: flex; align-items: center; gap: 0.75rem; padding: 0.6rem 1rem; border-radius: 0.5rem;
    color: #cbd5e1; font-weight: 600; text-decoration: none; transition: background-color 0.3s ease, color 0.3s ease;
  }
  aside nav a:hover { background-color: #334155; color: #f97316; }
  aside nav a.active, aside nav a[aria-current="page"] {
    background-color: #f97316; color: white;
  }
  /* Header bar */
  header { height: 5rem; }
  /* Focus outlines for accessibility */
  a:focus, button:focus, input:focus, select:focus {
    outline: 2px solid #f97316; outline-offset: 2px;
  }
  /* Slight icon scale effect on hover */
  a > i { transition: transform 0.2s ease; }
  a:hover > i { transform: scale(1.1); }
</style>
</head>
<body class="bg-gray-50 text-gray-900 font-sans">

<!-- Fixed header with site name and logout -->
<header class="bg-white shadow fixed w-full top-0 z-50 flex items-center px-6">
  <div class="max-w-7xl mx-auto flex justify-between w-full">
    <div class="text-2xl font-extrabold text-orange-500 flex items-center gap-2 select-none" aria-label="Logo and site name">
      <i class="fa-solid fa-dumbbell"></i> <span>GYM Core Admin</span>
    </div>
    <div class="flex items-center gap-6 text-gray-700 select-none">
      <span class="text-sm font-semibold">Welcome, <?=htmlspecialchars($_SESSION['username'] ?? 'Admin')?></span>
      <a href="logout.php" class="text-red-500 hover:text-red-700 font-bold flex items-center gap-1 transition" aria-label="Logout">
        <i class="fa-solid fa-right-from-bracket"></i> Logout
      </a>
    </div>
  </div>
</header>

<!-- Main page flex container with sidebar + main content -->
<div class="flex pt-20 max-w-7xl mx-auto min-h-[calc(100vh-5rem)] px-6">
  
  <!-- Sidebar navigation -->
  <aside class="sidebar-wrapper" aria-label="Admin sidebar navigation">
    <nav>
      <a href="admin_dashboard.php#users" class="active" aria-current="page"><i class="fa-solid fa-users"></i> Manage Users</a>
      <a href="manage_supplements.php"><i class="fa-solid fa-capsules"></i> Manage Supplements</a>
      <a href="manage_memberships.php"><i class="fa-solid fa-id-card"></i> Manage Memberships</a>
      <a href="manage_orders.php"><i class="fa-solid fa-cart-shopping"></i> Manage Orders</a>
      <a href="manage_payments.php"><i class="fa-solid fa-credit-card"></i> Manage Payments</a>
      <a href="manage_delivery.php"><i class="fa-solid fa-truck"></i> Delivery Details</a>
    </nav>
  </aside>

  <!-- Empty div to maintain layout next to fixed sidebar -->
  <div class="sidebar-placeholder"></div>

  <!-- Main content section -->
  <main class="flex-1 p-10 bg-white rounded-xl shadow-lg">

    <!-- Show success message -->
    <?php if ($msg): ?>
      <div class="bg-green-100 text-green-800 p-4 rounded-md mb-6 border border-green-300 shadow-sm"><?=htmlspecialchars($msg)?></div>
    <?php endif; ?>
    <!-- Show error message -->
    <?php if ($error): ?>
      <div class="bg-red-100 text-red-800 p-4 rounded-md mb-6 border border-red-300 shadow-sm"><?=htmlspecialchars($error)?></div>
    <?php endif; ?>

    <!-- User management section -->
    <div id="users" aria-label="User management section" tabindex="-1" class="focus:outline-none">
      <section>
        <div class="flex justify-between items-center mb-8 border-b border-gray-200">
          <h2 class="text-3xl font-extrabold text-orange-600 flex items-center gap-3 select-none">
            <i class="fa-solid fa-users"></i> User Management
          </h2>

          <!-- Show "Add New User" button if not editing -->
          <?php if (!$editUser): ?>
            <button type="button" id="toggleAddUserBtn" aria-expanded="false" aria-controls="addUserForm" class="bg-orange-600 text-white px-6 py-2 rounded-lg shadow-md hover:bg-orange-700 transition font-semibold flex items-center gap-2 focus:outline-none focus:ring-2 focus:ring-orange-500">
              <i class="fa-solid fa-plus"></i> Add New User
            </button>
          <?php else: ?>
            <!-- Cancel edit button -->
            <a href="admin_dashboard.php#users" class="bg-gray-500 text-white px-6 py-2 rounded-lg shadow-md hover:bg-gray-600 transition font-semibold flex items-center gap-2">
              <i class="fa-solid fa-ban"></i> Cancel Edit
            </a>
          <?php endif; ?>
        </div>

        <!-- Search form -->
        <form method="get" action="admin_dashboard.php" class="mb-10 flex max-w-md gap-3" role="search" aria-label="Search users form">
          <input type="search" name="search" placeholder="Search username or email" value="<?=htmlspecialchars($search)?>" class="peer border border-gray-300 rounded-lg p-3 focus:outline-none focus:ring-2 focus:ring-orange-600 flex-grow" aria-label="Search username or email" />
          <button type="submit" class="bg-orange-600 text-white px-5 py-3 rounded-lg hover:bg-orange-700 transition font-semibold flex items-center gap-2"><i class="fa-solid fa-magnifying-glass"></i> Search</button>
          <a href="admin_dashboard.php" class="text-gray-600 hover:underline flex items-center gap-1 px-3 py-3 rounded-lg border border-gray-300 select-none" role="button">Reset</a>
        </form>

        <!-- Add User form (hidden if editing user) -->
        <form id="addUserForm" action="admin_dashboard.php?action=add#users" method="POST" class="mb-12 space-y-8 p-8 border border-gray-200 rounded-lg bg-gray-50 shadow-md max-w-4xl <?= $editUser ? 'hidden' : '' ?>" autocomplete="off" aria-label="Add new user form">
          <input type="hidden" name="csrf_token" value="<?=csrf_token()?>" />
          <h3 class="text-2xl font-bold text-gray-700 mb-8 select-none">Add New User</h3>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <?php
            // Dynamically create fields for username, email, password
            $fields = [['type'=>'text','name'=>'username','label'=>'Username'], ['type'=>'email','name'=>'email','label'=>'Email'], ['type'=>'password','name'=>'password','label'=>'Password']];
            foreach($fields as $f): ?>
              <div class="form-group">
                <input type="<?= $f['type'] ?>" name="<?= $f['name'] ?>" id="<?= $f['name'] ?>" placeholder=" " required aria-required="true" autocomplete="<?= $f['type'] === 'password' ? 'new-password' : 'off' ?>" />
                <label for="<?= $f['name'] ?>"><?= $f['label'] ?></label>
              </div>
            <?php endforeach; ?>
            <div class="form-group">
              <select name="role" id="role" required aria-required="true" class="appearance-none">
                <option value="" disabled selected>Choose a role</option>
                <option value="admin">Admin</option>
                <option value="member">Member</option>
                <option value="trainer">Trainer</option>
                <option value="stock manager">Stock Manager</option>
                <option value="rider">Rider</option>
              </select>
              <label for="role">Role</label>
            </div>
          </div>
          <button type="submit" class="bg-orange-600 text-white px-8 py-3 rounded-lg shadow-lg hover:bg-orange-700 transition font-bold w-full md:w-auto" aria-label="Add user button">Add User</button>
        </form>

        <!-- Edit User form (only shows if editing) -->
        <?php if ($editUser): ?>
        <form id="editUserForm" action="admin_dashboard.php?action=edit&id=<?= $editUser['id'] ?>#users" method="POST" class="mb-12 space-y-8 p-8 border border-gray-200 rounded-lg bg-gray-50 shadow-md max-w-4xl" autocomplete="off" aria-label="Edit user form">
          <input type="hidden" name="csrf_token" value="<?=csrf_token()?>" />
          <h3 class="text-2xl font-bold text-gray-700 mb-8 select-none">Edit User: <?=htmlspecialchars($editUser['username'])?></h3>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <div class="form-group">
              <input type="text" name="username" id="username" placeholder=" " value="<?=htmlspecialchars($editUser['username'])?>" required aria-required="true" />
              <label for="username">Username</label>
            </div>
            <div class="form-group">
              <input type="email" name="email" id="email" placeholder=" " value="<?=htmlspecialchars($editUser['email'])?>" required aria-required="true" />
              <label for="email">Email</label>
            </div>
            <div class="form-group">
              <input type="password" name="password" id="password" placeholder=" " aria-describedby="passHelp" autocomplete="new-password" />
              <label for="password">Password (leave blank to keep unchanged)</label>
              <p id="passHelp" class="text-sm text-gray-500 mt-1">You can leave this empty to keep the current password.</p>
            </div>
            <div class="form-group">
              <select name="role" id="role" required aria-required="true" class="appearance-none">
                <option value="admin" <?= $editUser['role'] === 'admin' ? 'selected' : '' ?>>Admin</option>
                <option value="member" <?= $editUser['role'] === 'member' ? 'selected' : '' ?>>Member</option>
                <option value="trainer" <?= $editUser['role'] === 'trainer' ? 'selected' : '' ?>>Trainer</option>
                <option value="stock manager" <?= $editUser['role'] === 'stock manager' ? 'selected' : '' ?>>Stock Manager</option>
                <option value="rider" <?= $editUser['role'] === 'rider' ? 'selected' : '' ?>>Rider</option>
              </select>
              <label for="role">Role</label>
            </div>
          </div>
          <button type="submit" class="bg-orange-600 text-white px-8 py-3 rounded-lg shadow-lg hover:bg-orange-700 transition font-bold w-full md:w-auto" aria-label="Update user button">Update User</button>
        </form>
        <?php endif; ?>

        <!-- Users list table -->
        <div class="table-container">
          <table class="min-w-full border-separate border-spacing-0 rounded-lg overflow-hidden" aria-label="Users list table">
            <thead>
              <tr>
                <th scope="col" class="p-3">Username</th>
                <th scope="col" class="p-3">Email</th>
                <th scope="col" class="p-3">Role</th>
                <th scope="col" class="p-3 text-center" style="width:130px;">Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php if (count($users) === 0): ?>
                <tr><td colspan="4" class="text-center py-6 text-gray-400 italic">No users found.</td></tr>
              <?php else: foreach ($users as $user): ?>
                <tr>
                  <td class="p-3 font-semibold"><?=htmlspecialchars($user['username'])?></td>
                  <td class="p-3"><?=htmlspecialchars($user['email'])?></td>
                  <td class="p-3 capitalize"><?=htmlspecialchars($user['role'] ?? 'member')?></td>
                  <td class="p-3 text-center space-x-2">
                    <!-- Edit user link -->
                    <a href="admin_dashboard.php?action=edit&id=<?= $user['id'] ?>#users" class="text-blue-600 hover:text-blue-800" aria-label="Edit user <?=htmlspecialchars($user['username'])?>">
                      <i class="fa-solid fa-pen-to-square"></i>
                    </a>
                    <!-- Delete user form -->
                    <form action="admin_dashboard.php?action=delete#users" method="POST" style="display:inline" onsubmit="return confirm('Delete user <?=htmlspecialchars($user['username'])?>?');" aria-label="Delete user <?=htmlspecialchars($user['username'])?>">
                      <input type="hidden" name="csrf_token" value="<?=csrf_token()?>" />
                      <input type="hidden" name="id" value="<?= $user['id'] ?>" />
                      <button type="submit" class="text-red-600 hover:text-red-800" aria-label="Delete user <?=htmlspecialchars($user['username'])?>">
                        <i class="fa-solid fa-trash"></i>
                      </button>
                    </form>
                  </td>
                </tr>
              <?php endforeach; endif; ?>
            </tbody>
          </table>
        </div>

      </section>
    </div>
  </main>
</div>

<script>
  // Focus #users div when page loads if hash is #users (good for accessibility)
  document.addEventListener('DOMContentLoaded', () => {
    if (window.location.hash === '#users') {
      const usersSection = document.getElementById('users');
      if (usersSection) {
        usersSection.focus();
      }
    }
  });

  // Toggle the "Add New User" form visibility on button click
  const toggleBtn = document.getElementById('toggleAddUserBtn');
  if (toggleBtn) {
    toggleBtn.addEventListener('click', () => {
      const form = document.getElementById('addUserForm');
      if (form) {
        const expanded = toggleBtn.getAttribute('aria-expanded') === 'true';
        toggleBtn.setAttribute('aria-expanded', !expanded);
        form.classList.toggle('hidden');
        if (!expanded) form.querySelector('input[name="username"]').focus();
      }
    });
  }
</script>

</body>
</html>
