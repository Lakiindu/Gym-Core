<?php
session_start();

// Access control: Only logged in users with role 'member' can access this page
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'member') {
    header("Location: login.php");
    exit;
}

// DB connection
$host = "localhost";
$dbname = "gym_db";
$user = "postgres";
$password = "lakindu";

try {
    $conn = new PDO("pgsql:host=$host;dbname=$dbname", $user, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

$user_id = $_SESSION['user_id'];

// Fetch user basic info
$stmt = $conn->prepare("SELECT username, email FROM users WHERE id = :uid");
$stmt->execute([':uid' => $user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Fetch latest active membership from memberships table
$stmt = $conn->prepare("
    SELECT start_date, end_date, status
    FROM memberships
    WHERE user_id = :uid
    ORDER BY end_date DESC
    LIMIT 1
");
$stmt->execute([':uid' => $user_id]);
$membershipData = $stmt->fetch(PDO::FETCH_ASSOC);

$membership_status = "No active membership";
$membership_expiry_date = null;
$active_subscriptions = 0;

if ($membershipData) {
    $start_date = new DateTime($membershipData['start_date']);
    $end_date = new DateTime($membershipData['end_date']);
    $today = new DateTime();

    if ($membershipData['status'] === 'active' && $end_date >= $today) {
        $membership_status = "Active";
        $membership_expiry_date = $end_date->format('M d, Y');
        $active_subscriptions = 1;
    } else {
        $membership_status = "Expired";
        $membership_expiry_date = $end_date->format('M d, Y');
    }
}

// Fetch total workouts completed
$stmt = $conn->prepare("SELECT COUNT(*) FROM workouts WHERE user_id = :uid");
$stmt->execute([':uid' => $user_id]);
$workouts_completed = $stmt->fetchColumn() ?: 0;

// Fetch total calories burned
$stmt = $conn->prepare("SELECT COALESCE(SUM(calories_burned), 0) FROM workouts WHERE user_id = :uid");
$stmt->execute([':uid' => $user_id]);
$calories_burned = $stmt->fetchColumn() ?: 0;

// Fetch total orders placed
$stmt = $conn->prepare("SELECT COUNT(*) FROM orders WHERE user_id = :uid");
$stmt->execute([':uid' => $user_id]);
$orders_placed = $stmt->fetchColumn() ?: 0;

// Fetch recent orders (last 5) without order_date, using id and description
$stmt = $conn->prepare("SELECT id, customer_name, description, price, status FROM orders WHERE user_id = :uid ORDER BY id DESC LIMIT 5");
$stmt->execute([':uid' => $user_id]);
$recent_orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch weekly workout data for Chart.js (last 7 days)
$labels = [];
$dataPoints = [];
for ($i = 6; $i >= 0; $i--) {
    $date = new DateTime("-$i days");
    $labels[] = $date->format('D'); // e.g. Mon, Tue
    $dateStr = $date->format('Y-m-d');
    $stmt = $conn->prepare("SELECT COUNT(*) FROM workouts WHERE user_id = :uid AND workout_date = :wdate");
    $stmt->execute([':uid' => $user_id, ':wdate' => $dateStr]);
    $count = $stmt->fetchColumn() ?: 0;
    $dataPoints[] = (int)$count;
}

// Dummy next scheduled workout (replace with your actual schedule logic if any)
$next_workout_date = new DateTime("+2 days");
$next_workout_str = $next_workout_date->format('D, M d, Y');
?>
<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>User Dashboard -GYM Core</title>

  <!-- Tailwind CSS -->
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: { primary: "#ff6600" },
          keyframes: {
            slideIn: { '0%': { transform: 'translateX(-100%)' }, '100%': { transform: 'translateX(0)' } },
            slideOut: { '0%': { transform: 'translateX(0)' }, '100%': { transform: 'translateX(-100%)' } },
            fadeIn: { '0%': { opacity: 0 }, '100%': { opacity: 1 } }
          },
          animation: {
            slideIn: 'slideIn 0.3s ease forwards',
            slideOut: 'slideOut 0.3s ease forwards',
            fadeIn: 'fadeIn 0.5s ease forwards',
          }
        }
      }
    }
  </script>

  <!-- FontAwesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />

  <!-- Chart.js -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-gray-900 text-white font-sans transition-colors duration-300 min-h-screen flex flex-col">

  <!-- Sidebar -->
  <aside id="sidebar" class="fixed top-0 left-0 h-full w-64 bg-gray-800 shadow-lg z-50 transform -translate-x-0 md:translate-x-0 transition-transform duration-300">
    <div class="flex items-center justify-between px-6 py-4 border-b border-gray-700">
      <h1 class="text-2xl font-bold text-primary flex items-center">
        <i class="fas fa-dumbbell mr-2"></i> GYM Core
      </h1>
      <button id="sidebar-close" class="text-gray-400 hover:text-white focus:outline-none md:hidden" aria-label="Close sidebar">
        <i class="fas fa-times text-xl"></i>
      </button>
    </div>
    <nav class="mt-6 px-4 space-y-4">
      <a href="user_dashboard.php" class="block px-4 py-3 rounded bg-primary text-white font-semibold" aria-current="page">
        <i class="fas fa-home mr-3"></i> Dashboard
      </a>
      <a href="profile.php" class="block px-4 py-3 rounded hover:bg-primary/20">
        <i class="fas fa-user mr-3"></i> Profile
      </a>
      <a href="supplements_dashboard.php" class="block px-4 py-3 rounded hover:bg-primary/20">
        <i class="fas fa-capsules mr-3"></i> Supplements
      </a>
      <a href="buy_membership.php" class="block px-4 py-3 rounded hover:bg-primary/20">
        <i class="fas fa-id-card mr-3"></i> Memberships
      </a>
      <a href="orders_dashboard.php" class="block px-4 py-3 rounded hover:bg-primary/20">
        <i class="fas fa-box-open mr-3"></i> Membership Purchases and Order Details
      </a>
<a href="book_trainer_dashboard.php" class="block px-4 py-3 rounded hover:bg-primary/20">
    <i class="fas fa-dumbbell mr-3"></i> Trainers
</a>

      <a href="logout.php" class="block px-4 py-3 rounded hover:bg-red-600 text-red-500 hover:text-white">
        <i class="fas fa-sign-out-alt mr-3"></i> Logout
      </a>
    </nav>
  </aside>

  <!-- Main Content -->
  <div class="md:pl-64 flex flex-col flex-grow min-h-screen">
    <!-- Top Navbar -->
    <header class="flex items-center justify-between bg-gray-800 p-4 shadow fixed top-0 left-0 right-0 z-40 md:pl-64">
      <button id="sidebar-toggle" class="text-white md:hidden" aria-label="Open sidebar">
        <i class="fas fa-bars text-2xl"></i>
      </button>
      <h2 class="text-xl font-semibold">User Dashboard</h2>
      <div class="text-sm text-gray-300 hidden md:block">
        Logged in as <strong><?= htmlspecialchars($user['username']) ?></strong>
      </div>
    </header>

    <!-- Dashboard Content -->
    <main class="flex-1 pt-20 pb-8 px-6 bg-gray-900 overflow-auto">
      <!-- Welcome -->
      <section class="mb-8 animate-fadeIn max-w-5xl mx-auto">
        <h1 class="text-3xl font-bold mb-1">
          Welcome back, <span class="text-primary"><?= htmlspecialchars($user['username']) ?></span>!
        </h1>
        <p class="text-gray-400">Here's your fitness dashboard overview.</p>
      </section>

      <!-- Info Cards -->
      <section class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-10 max-w-5xl mx-auto animate-fadeIn">
        <div class="bg-gray-800 rounded-lg p-6 shadow-lg hover:shadow-orange-600 transition-shadow">
          <div class="flex items-center mb-4">
            <div class="text-primary text-3xl mr-4">
              <i class="fas fa-dumbbell"></i>
            </div>
            <h3 class="text-xl font-semibold">Workouts Completed</h3>
          </div>
          <p class="text-4xl font-bold"><?= $workouts_completed ?></p>
        </div>
        <div class="bg-gray-800 rounded-lg p-6 shadow-lg hover:shadow-orange-600 transition-shadow">
          <div class="flex items-center mb-4">
            <div class="text-primary text-3xl mr-4">
              <i class="fas fa-fire"></i>
            </div>
            <h3 class="text-xl font-semibold">Calories Burned</h3>
          </div>
          <p class="text-4xl font-bold"><?= number_format($calories_burned) ?></p>
        </div>
        <div class="bg-gray-800 rounded-lg p-6 shadow-lg hover:shadow-orange-600 transition-shadow">
          <div class="flex items-center mb-4">
            <div class="text-primary text-3xl mr-4">
              <i class="fas fa-shopping-cart"></i>
            </div>
            <h3 class="text-xl font-semibold">Orders Placed</h3>
          </div>
          <p class="text-4xl font-bold"><?= $orders_placed ?></p>
        </div>
        <div class="bg-gray-800 rounded-lg p-6 shadow-lg hover:shadow-orange-600 transition-shadow">
          <div class="flex items-center mb-4">
            <div class="text-primary text-3xl mr-4">
              <i class="fas fa-id-card"></i>
            </div>
            <h3 class="text-xl font-semibold">Active Memberships</h3>
          </div>
          <p class="text-4xl font-bold"><?= $active_subscriptions ?></p>
          <?php if ($membership_expiry_date): ?>
            <p class="text-gray-400 text-sm mt-1">Expires on <?= $membership_expiry_date ?></p>
          <?php endif; ?>
        </div>
      </section>

      <!-- Next Workout Reminder -->
      <section class="max-w-5xl mx-auto mb-10 bg-gray-800 p-6 rounded-lg shadow-lg animate-fadeIn">
        <h3 class="text-xl font-semibold mb-4">Next Scheduled Workout</h3>
        <p class="text-gray-300">Mark your calendar! Your next workout is scheduled for <strong><?= $next_workout_str ?></strong>.</p>
      </section>

      <!-- Recent Orders Table -->
      <section class="max-w-5xl mx-auto bg-gray-800 rounded-lg shadow-lg p-6 animate-fadeIn">
        <h3 class="text-xl font-semibold mb-4">Recent Orders</h3>
        <?php if (count($recent_orders) === 0): ?>
          <p class="text-gray-400">You have no recent orders.</p>
        <?php else: ?>
          <div class="overflow-x-auto">
            <table class="min-w-full table-auto border-collapse border border-gray-700">
              <thead>
                <tr class="bg-gray-700 text-left">
                  <th class="px-4 py-2 border border-gray-600">Order ID</th>
                  <th class="px-4 py-2 border border-gray-600">Customer Name</th>
                  <th class="px-4 py-2 border border-gray-600">Description</th>
                  <th class="px-4 py-2 border border-gray-600">Status</th>
                  <th class="px-4 py-2 border border-gray-600">Price (LKR)</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($recent_orders as $order): ?>
                  <tr class="border border-gray-700 hover:bg-gray-700 transition-colors cursor-pointer">
                    <td class="px-4 py-2 border border-gray-600"><?= htmlspecialchars($order['id']) ?></td>
                    <td class="px-4 py-2 border border-gray-600"><?= htmlspecialchars($order['customer_name']) ?></td>
                    <td class="px-4 py-2 border border-gray-600"><?= htmlspecialchars($order['description']) ?></td>
                    <td class="px-4 py-2 border border-gray-600 capitalize">
                      <?php
                        $status = strtolower($order['status']);
                        $colorClass = 'text-gray-300';
                        if ($status === 'pending') $colorClass = 'text-yellow-400';
                        elseif ($status === 'completed') $colorClass = 'text-green-400';
                        elseif ($status === 'cancelled') $colorClass = 'text-red-400';
                      ?>
                      <span class="<?= $colorClass ?> font-semibold"><?= htmlspecialchars($order['status']) ?></span>
                    </td>
                    <td class="px-4 py-2 border border-gray-600 font-mono">LKR <?= number_format($order['price'], 2) ?></td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        <?php endif; ?>
      </section>

      <!-- Workout Chart -->
      <section class="max-w-5xl mx-auto mt-10 bg-gray-800 rounded-lg p-6 shadow-lg animate-fadeIn">
        <h3 class="text-xl font-semibold mb-4">Weekly Workout Activity</h3>
        <canvas id="workoutChart" class="w-full h-64"></canvas>
      </section>
    </main>
  </div>

  <!-- Scripts -->
  <script>
    // Sidebar toggle
    const sidebar = document.getElementById('sidebar');
    const sidebarToggle = document.getElementById('sidebar-toggle');
    const sidebarClose = document.getElementById('sidebar-close');

    sidebarToggle?.addEventListener('click', () => {
      sidebar.classList.toggle('-translate-x-full');
    });
    sidebarClose?.addEventListener('click', () => {
      sidebar.classList.add('-translate-x-full');
    });

    // Chart.js Weekly Workout Chart
    const ctx = document.getElementById('workoutChart').getContext('2d');
    const workoutChart = new Chart(ctx, {
      type: 'line',
      data: {
        labels: <?= json_encode($labels) ?>,
        datasets: [{
          label: 'Workouts',
          data: <?= json_encode($dataPoints) ?>,
          borderColor: '#ff6600',
          backgroundColor: 'rgba(255,102,0,0.3)',
          fill: true,
          tension: 0.3,
          pointRadius: 5,
          pointHoverRadius: 7,
          borderWidth: 3,
          cubicInterpolationMode: 'monotone'
        }]
      },
      options: {
        responsive: true,
        scales: {
          y: {
            beginAtZero: true,
            ticks: { color: 'white' },
            grid: { color: 'rgba(255,255,255,0.1)' }
          },
          x: {
            ticks: { color: 'white' },
            grid: { color: 'rgba(255,255,255,0.1)' }
          }
        },
        plugins: {
          legend: {
            labels: { color: 'white', font: { size: 14 } }
          }
        }
      }
    });
  </script>
</body>
</html>