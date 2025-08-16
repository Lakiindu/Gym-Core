<?php
session_start();

$errorMsg = "";
if (isset($_SESSION['register_error'])) {
    $errorMsg = $_SESSION['register_error'];
    unset($_SESSION['register_error']);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>GYM Core - Register</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            primary: "#ff6600",
          },
          animation: {
            fadeInUp: "fadeInUp 0.6s ease-out both",
          },
          keyframes: {
            fadeInUp: {
              '0%': { opacity: 0, transform: 'translateY(20px)' },
              '100%': { opacity: 1, transform: 'translateY(0)' },
            }
          }
        },
      },
    };
  </script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
</head>
<body class="bg-gray-900 text-white font-sans transition duration-300">

  <!-- Navbar -->
  <header class="bg-gray-800 fixed w-full top-0 z-50 shadow-md">
    <div class="max-w-7xl mx-auto px-4 py-4 flex items-center justify-between">
      <!-- ✅ GYM CORE + Icon wrapped in link -->
      <a href="index.php" class="flex items-center gap-2">
        <span class="text-3xl font-bold text-primary">GYM CORE</span>
        <i class="fas fa-dumbbell text-white text-xl"></i>
      </a>
      <nav class="hidden md:flex space-x-6 text-sm uppercase tracking-wide">
        <a href="index.php" class="hover:text-primary">Home</a>
        <a href="about.php" class="hover:text-primary">About Us</a>
        <a href="supplements.php" class="hover:text-primary">Supplements</a>
        <a href="instructors.php" class="hover:text-primary">Instructors</a>
        <a href="contact.php" class="hover:text-primary">Contact</a>
      </nav>
      <div class="flex items-center gap-3">
        <a href="login.php" class="hidden md:inline-block text-sm px-4 py-2 border border-primary text-primary rounded hover:bg-primary hover:text-white">Login</a>
        <a href="register.php" class="hidden md:inline-block text-sm px-4 py-2 bg-primary text-white rounded hover:bg-primary/90">Register</a>
        <button id="menu-toggle" class="md:hidden text-white text-xl focus:outline-none"><i class="fas fa-bars"></i></button>
      </div>
    </div>
    <div id="mobile-menu" class="hidden md:hidden bg-gray-800 px-4 pb-4">
      <nav class="flex flex-col gap-3 text-center">
        <a href="index.php" class="hover:text-primary">Home</a>
        <a href="about.php" class="hover:text-primary">About Us</a>
        <a href="supplements.php" class="hover:text-primary">Supplements</a>
        <a href="instructors.php" class="hover:text-primary">Instructors</a>
        <a href="contact.php" class="hover:text-primary">Contact</a>
        <a href="login.php" class="border border-primary text-primary py-2 rounded">Login</a>
        <a href="register.php" class="bg-primary text-white py-2 rounded">Register</a>
      </nav>
    </div>
  </header>

  <!-- Register Form Section -->
  <main class="relative flex items-center justify-center min-h-screen pt-24 px-4 bg-cover bg-center" style="background-image: url('images/Register/register.jpg');">
    <div class="absolute inset-0 bg-black bg-opacity-60 z-0"></div>

    <section class="relative z-10 backdrop-blur-md bg-gray-800/80 p-8 rounded-2xl shadow-2xl max-w-md w-full animate-fadeInUp">
      <h2 class="text-3xl font-extrabold mb-6 text-center text-primary tracking-wide">Create Your Account</h2>

      <!-- Error Message -->
      <?php if ($errorMsg): ?>
        <div class="mb-4 p-3 bg-red-600 text-white rounded text-center font-semibold">
          <?= htmlspecialchars($errorMsg) ?>
        </div>
      <?php endif; ?>

      <form action="register_process.php" method="POST" class="space-y-5">
        <div>
          <label for="username" class="block mb-1 font-medium">Username</label>
          <input type="text" id="username" name="username" required placeholder="Enter username"
            class="w-full px-4 py-2 bg-gray-900/70 border border-gray-700 text-white rounded-lg focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition duration-200" />
        </div>
        <div>
          <label for="email" class="block mb-1 font-medium">Email</label>
          <input type="email" id="email" name="email" required placeholder="you@example.com"
            class="w-full px-4 py-2 bg-gray-900/70 border border-gray-700 text-white rounded-lg focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition duration-200" />
        </div>
        <div>
          <label for="password" class="block mb-1 font-medium">Password</label>
          <input type="password" id="password" name="password" required placeholder="••••••••"
            class="w-full px-4 py-2 bg-gray-900/70 border border-gray-700 text-white rounded-lg focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition duration-200" />
        </div>
        <div>
          <label for="confirm_password" class="block mb-1 font-medium">Confirm Password</label>
          <input type="password" id="confirm_password" name="confirm_password" required placeholder="••••••••"
            class="w-full px-4 py-2 bg-gray-900/70 border border-gray-700 text-white rounded-lg focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition duration-200" />
        </div>
        <button type="submit"
          class="w-full bg-primary hover:bg-orange-600 text-white font-bold py-3 rounded-lg transition duration-300 shadow-md hover:shadow-lg">
          Register
        </button>
      </form>
      <p class="mt-6 text-center text-gray-400 text-sm">
        Already have an account?
        <a href="login.php" class="text-primary hover:underline">Login here</a>
      </p>
    </section>
  </main>

  <!-- Footer -->
  <footer class="text-center text-gray-400 py-6 bg-gray-900 border-t border-gray-700 transition-colors duration-300">
    &copy; <?= date('Y') ?> GYM Core. All rights reserved.
  </footer>
  
  <!-- Scripts -->
  <script>
    // Mobile menu toggle
    document.getElementById("menu-toggle").addEventListener("click", () => {
      document.getElementById("mobile-menu").classList.toggle("hidden");
    });
  </script>
</body>
</html>
