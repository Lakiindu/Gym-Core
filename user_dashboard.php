<?php
session_start();

// Access control: Only logged in users with role 'user' can access this page
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'member') {
    header("Location: login.php");
    exit;
}

// Optional: Database connection (you can use this later if you want to fetch dynamic user data)
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
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>GYM Core - User Dashboard</title>

  <!-- Tailwind CSS -->
  <script src="https://cdn.tailwindcss.com"></script>

  <!-- Tailwind custom config -->
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            primary: "#ff6600",
          },
          keyframes: {
            slideIn: {
              '0%': { transform: 'translateX(-100%)' },
              '100%': { transform: 'translateX(0)' },
            },
            slideOut: {
              '0%': { transform: 'translateX(0)' },
              '100%': { transform: 'translateX(-100%)' },
            },
            fadeIn: {
              '0%': { opacity: 0 },
              '100%': { opacity: 1 },
            }
          },
          animation: {
            slideIn: 'slideIn 0.3s ease forwards',
            slideOut: 'slideOut 0.3s ease forwards',
            fadeIn: 'fadeIn 0.5s ease forwards',
          },
        },
      },
    }
  </script>

  <!-- FontAwesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />

  <!-- Chart.js -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-gray-900 text-white font-sans transition-colors duration-300">

  <!-- Sidebar -->
  <aside id="sidebar" class="fixed top-0 left-0 h-full w-64 bg-gray-800 shadow-lg z-50">
    <div class="flex items-center justify-between px-6 py-4 border-b border-gray-700">
      <h1 class="text-2xl font-bold text-primary flex items-center">
        <i class="fas fa-dumbbell mr-2"></i> GYM Core
      </h1>
      <button id="sidebar-close" class="text-gray-400 hover:text-white focus:outline-none md:hidden">
        <i class="fas fa-times text-xl"></i>
      </button>
    </div>
    <nav class="mt-6 px-4 space-y-4">
      <a href="user_dashboard.php" class="block px-4 py-3 rounded bg-primary text-white font-semibold">
        <i class="fas fa-home mr-3"></i> User Dashboard
      </a>
      <a href="profile.php" class="block px-4 py-3 rounded hover:bg-primary/20">
        <i class="fas fa-user mr-3"></i> Profile
      </a>
      <a href="orders_dashboard.php" class="block px-4 py-3 rounded hover:bg-primary/20">
        <i class="fas fa-box-open mr-3"></i> Orders
      </a>
      <a href="supplements_dashboard.php" class="block px-4 py-3 rounded hover:bg-primary/20">
        <i class="fas fa-capsules mr-3"></i> Supplements
      </a>
      <a href="buy_membership.php" class="block px-4 py-3 rounded hover:bg-primary/20">
        <i class="fas fa-id-card mr-3"></i> Membership
      </a>
      <a href="logout.php" class="block px-4 py-3 rounded hover:bg-red-600 text-red-500 hover:text-white">
        <i class="fas fa-sign-out-alt mr-3"></i> Logout
      </a>
    </nav>
  </aside>

  <!-- Main Content -->
  <div class="md:pl-64 flex flex-col min-h-screen">
    <!-- Top Navbar -->
    <header class="flex items-center justify-between bg-gray-800 p-4 shadow fixed top-0 left-0 right-0 z-40 md:pl-64">
      <button id="sidebar-toggle" class="text-white md:hidden">
        <i class="fas fa-bars text-2xl"></i>
      </button>
      <h2 class="text-xl font-semibold">Dashboard</h2>
      <button id="mode-toggle" class="text-xl focus:outline-none">
        <i class="fas fa-sun hidden"></i>
        <i class="fas fa-moon"></i>
      </button>
    </header>

    <!-- Dashboard Content -->
    <main class="flex-1 pt-20 pb-8 px-6 bg-gray-900">
      <!-- Welcome -->
      <section class="mb-8 animate-fadeIn">
        <h1 class="text-3xl font-bold mb-1">Welcome back, <span id="username"><?php echo htmlspecialchars($_SESSION['username']); ?></span>!</h1>
        <p class="text-gray-400">Here is your fitness dashboard overview.</p>
      </section>

      <!-- Info Cards -->
      <section class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10 animate-fadeIn">
        <div class="bg-gray-800 rounded-lg p-6 shadow-lg">
          <div class="flex items-center mb-4">
            <div class="text-primary text-3xl mr-4">
              <i class="fas fa-dumbbell"></i>
            </div>
            <h3 class="text-xl font-semibold">Workouts Completed</h3>
          </div>
          <p class="text-4xl font-bold">24</p>
        </div>
        <div class="bg-gray-800 rounded-lg p-6 shadow-lg">
          <div class="flex items-center mb-4">
            <div class="text-primary text-3xl mr-4">
              <i class="fas fa-shopping-cart"></i>
            </div>
            <h3 class="text-xl font-semibold">Orders Placed</h3>
          </div>
          <p class="text-4xl font-bold">5</p>
        </div>
        <div class="bg-gray-800 rounded-lg p-6 shadow-lg">
          <div class="flex items-center mb-4">
            <div class="text-primary text-3xl mr-4">
              <i class="fas fa-heart"></i>
            </div>
            <h3 class="text-xl font-semibold">Active Subscriptions</h3>
          </div>
          <p class="text-4xl font-bold">2</p>
        </div>
      </section>

      <!-- Workout Chart -->
      <section class="bg-gray-800 rounded-lg p-6 shadow-lg">
        <h3 class="text-xl font-semibold mb-4">Weekly Workout Activity</h3>
        <canvas id="workoutChart" class="w-full h-64"></canvas>
      </section>
    </main>
  </div>

  <!-- Scripts -->
  <script>
    // Sidebar toggle
    document.getElementById('sidebar-toggle')?.addEventListener('click', () => {
      document.getElementById('sidebar')?.classList.toggle('-translate-x-full');
    });
    document.getElementById('sidebar-close')?.addEventListener('click', () => {
      document.getElementById('sidebar')?.classList.add('-translate-x-full');
    });

    // Theme toggle
    const modeToggle = document.getElementById('mode-toggle');
    modeToggle.addEventListener('click', () => {
      document.body.classList.toggle('bg-gray-900');
      document.body.classList.toggle('bg-gray-100');
      document.body.classList.toggle('text-white');
      document.body.classList.toggle('text-gray-900');
      modeToggle.querySelector('.fa-sun')?.classList.toggle('hidden');
      modeToggle.querySelector('.fa-moon')?.classList.toggle('hidden');
    });

    // Chart.js
    const ctx = document.getElementById('workoutChart').getContext('2d');
    new Chart(ctx, {
      type: 'line',
      data: {
        labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
        datasets: [{
          label: 'Workouts',
          data: [2, 3, 2, 4, 3, 1, 0],
          borderColor: '#ff6600',
          backgroundColor: 'rgba(255,102,0,0.3)',
          fill: true,
          tension: 0.3,
          pointRadius: 5,
          pointHoverRadius: 7,
          borderWidth: 3
        }]
      },
      options: {
        responsive: true,
        plugins: {
          legend: { labels: { color: 'white', font: { size: 14 } } }
        },
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
        }
      }
    });
  </script>

</body>
</html>
