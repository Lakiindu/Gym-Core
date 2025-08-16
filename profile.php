<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

require 'db.php'; // Your DB connection file

$user_id = $_SESSION['user_id'];

// Fetch user info from DB
$stmt = $pdo->prepare("SELECT username, email FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

if (!$user) {
    echo "User not found.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>GYM Core - Profile</title>
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
    }
  </script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
</head>
<body class="relative bg-cover bg-center bg-no-repeat text-white font-sans min-h-screen"
      style="background-image: url('login.jpg');"> 

  <!-- Overlay -->
  <div class="absolute inset-0 bg-black bg-opacity-70 z-0"></div>

  <!-- Sidebar -->
  <aside class="fixed top-0 left-0 h-full w-64 bg-gray-800 shadow-lg z-50">
    <div class="px-6 py-4 border-b border-gray-700">
      <h1 class="text-2xl font-bold text-primary">
        <i class="fas fa-dumbbell mr-2"></i> GYM Core
      </h1>
    </div>
    <nav class="mt-6 px-4 space-y-4">
      <a href="user_dashboard.php" class="block px-4 py-3 rounded hover:bg-primary/20 transition">
        <i class="fas fa-home mr-3"></i> Dashboard
      </a>
      <a href="profile.php" class="block px-4 py-3 rounded bg-primary text-white">
        <i class="fas fa-user mr-3"></i> Profile
      </a>
      <a href="supplements_dashboard.php" class="block px-4 py-3 rounded hover:bg-primary/20 transition">
        <i class="fas fa-capsules mr-3"></i> Supplements
      </a>
      <a href="buy_membership.php" class="block px-4 py-3 rounded hover:bg-primary/20 transition">
      <i class="fas fa-id-card mr-3"></i> Memberships
      </a>
            <a href="orders_dashboard.php" class="block px-4 py-3 rounded hover:bg-primary/20 transition">
        <i class="fas fa-box-open mr-3"></i> Membership Purchases and Order Details
      </a>
<a href="book_trainer_dashboard.php" class="block px-4 py-3 rounded hover:bg-primary/20">
    <i class="fas fa-dumbbell mr-3"></i> Trainers
</a>


      <a href="logout.php" class="block px-4 py-3 rounded hover:bg-red-600 transition text-red-500 hover:text-white">
        <i class="fas fa-sign-out-alt mr-3"></i> Logout
      </a>
    </nav>
  </aside>

  <!-- Main Content -->
  <div class="relative z-10 md:ml-64 flex items-center justify-center min-h-screen p-6">
    <div class="bg-gray-900 bg-opacity-70 backdrop-blur-md p-8 rounded-xl shadow-xl w-full max-w-xl">
      <h2 class="text-3xl font-bold mb-6 text-center">My Profile</h2>

      <?php if (isset($_SESSION['update_error'])): ?>
        <div class="mb-4 text-red-500 font-semibold text-center"><?= htmlspecialchars($_SESSION['update_error']) ?></div>
        <?php unset($_SESSION['update_error']); ?>
      <?php endif; ?>
      <?php if (isset($_SESSION['update_success'])): ?>
        <div class="mb-4 text-green-500 font-semibold text-center"><?= htmlspecialchars($_SESSION['update_success']) ?></div>
        <?php unset($_SESSION['update_success']); ?>
      <?php endif; ?>

      <form action="update_profile.php" method="POST" class="space-y-6">
        <div>
          <label class="block text-sm mb-2 font-semibold" for="username">Username</label>
          <input type="text" name="username" id="username" value="<?= htmlspecialchars($user['username']) ?>" class="w-full px-4 py-2 rounded bg-gray-800 text-white border border-gray-600 focus:ring-2 focus:ring-primary focus:outline-none" required>
        </div>

        <div>
          <label class="block text-sm mb-2 font-semibold" for="email">Email</label>
          <input type="email" name="email" id="email" value="<?= htmlspecialchars($user['email']) ?>" class="w-full px-4 py-2 rounded bg-gray-800 text-white border border-gray-600 focus:ring-2 focus:ring-primary focus:outline-none" required>
        </div>

        <div>
          <label class="block text-sm mb-2 font-semibold" for="password">New Password</label>
          <input type="password" name="password" id="password" placeholder="Enter new password" class="w-full px-4 py-2 rounded bg-gray-800 text-white border border-gray-600 focus:ring-2 focus:ring-primary focus:outline-none">
        </div>

        <div>
          <label class="block text-sm mb-2 font-semibold" for="confirm_password">Confirm Password</label>
          <input type="password" name="confirm_password" id="confirm_password" placeholder="Confirm new password" class="w-full px-4 py-2 rounded bg-gray-800 text-white border border-gray-600 focus:ring-2 focus:ring-primary focus:outline-none">
        </div>

        <button type="submit" class="w-full bg-primary text-white px-6 py-2 rounded hover:bg-orange-700 transition font-semibold mt-4">
          Save Changes
        </button>
      </form>
    </div>
  </div>

</body>
</html>
