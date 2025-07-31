<?php
session_start();

// Access control: Only logged in users with role 'member' can access this page
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'member') {
    header("Location: login.php");
    exit;
}

// DB connection details
$host = "localhost";
$dbname = "gym_db";
$user = "postgres";
$password = "lakindu";

try {
    $conn = new PDO("pgsql:host=$host;dbname=$dbname", $user, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Fetch memberships that are active
    $stmt = $conn->query("SELECT * FROM buy_membership WHERE is_active = TRUE ORDER BY id ASC");
    $memberships = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("DB Error: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Buy Memberships - GYM Core</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link
    rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
  />
</head>
<body class="bg-gray-900 text-white">

<!-- Sidebar -->
<aside class="fixed top-0 left-0 h-full w-64 bg-gray-800 shadow z-50">
  <div class="px-6 py-4 border-b border-gray-700">
    <h1 class="text-2xl font-bold text-orange-500">
      <i class="fas fa-dumbbell mr-2"></i>GYM Core
    </h1>
  </div>
  <nav class="mt-6 px-4 space-y-4">
    <a href="user_dashboard.php" class="block px-4 py-3 rounded hover:bg-orange-600">
      <i class="fas fa-home mr-2"></i>Dashboard
    </a>
    <a href="supplements_dashboard.php" class="block px-4 py-3 rounded hover:bg-orange-600">
      <i class="fas fa-capsules mr-2"></i>Supplements
    </a>
    <a href="buy_membership.php" class="block px-4 py-3 rounded bg-orange-600">
      <i class="fas fa-id-card mr-2"></i>Memberships
    </a>
    <a
      href="logout.php"
      class="block px-4 py-3 rounded hover:bg-red-600 text-red-500 hover:text-white"
    >
      <i class="fas fa-sign-out-alt mr-2"></i>Logout
    </a>
  </nav>
</aside>

<!-- Main Content -->
<main class="md:ml-64 p-8 min-h-screen">
  <h2 class="text-3xl font-bold mb-6">Available Memberships</h2>

  <!-- Success and Error messages -->
  <?php if (!empty($_SESSION['success_message'])): ?>
    <div
      class="mb-4 p-4 bg-green-600 rounded text-white font-semibold"
      role="alert"
    >
      <?= htmlspecialchars($_SESSION['success_message']) ?>
    </div>
    <?php unset($_SESSION['success_message']); ?>
  <?php endif; ?>

  <?php if (!empty($_SESSION['error_message'])): ?>
    <div
      class="mb-4 p-4 bg-red-600 rounded text-white font-semibold"
      role="alert"
    >
      <?= htmlspecialchars($_SESSION['error_message']) ?>
    </div>
    <?php unset($_SESSION['error_message']); ?>
  <?php endif; ?>

  <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    <?php foreach ($memberships as $membership): ?>
      <div class="bg-gray-800 rounded-lg shadow-lg p-6 flex flex-col justify-between">
        <div>
          <h3 class="text-2xl font-semibold text-orange-400 mb-2">
            <?= htmlspecialchars($membership['plan_name']) ?>
          </h3>
          <p class="mb-2 text-gray-300">
            <?= htmlspecialchars($membership['description']) ?>
          </p>
          <p class="mb-1">
            <strong>Duration:</strong> <?= htmlspecialchars($membership['duration_days']) ?> days
          </p>
          <p class="mb-3">
            <strong>Price:</strong> Rs. <?= number_format($membership['price'], 2) ?>
          </p>
          <?php if (!empty($membership['features'])): ?>
            <p class="mb-3 text-sm text-gray-400">
              <strong>Features:</strong> <?= htmlspecialchars($membership['features']) ?>
            </p>
          <?php endif; ?>
        </div>
        <form action="process_membership_purchase.php" method="POST" class="mt-4">
          <input type="hidden" name="membership_id" value="<?= $membership['id'] ?>" />
          <button
            type="submit"
            class="bg-orange-500 hover:bg-orange-600 text-white px-4 py-2 rounded w-full font-semibold"
          >
            Buy Now
          </button>
        </form>
      </div>
    <?php endforeach; ?>
  </div>
</main>

</body>
</html>
