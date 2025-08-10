<!DOCTYPE html>
<html lang="en" class="">
<head>
  <meta charset="UTF-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title> GYM Core - Login</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            primary: "#ff6600",
          },
          animation: {
            fadeIn: "fadeIn 1s ease-out"
          },
          keyframes: {
            fadeIn: {
              "0%": { opacity: "0", transform: "translateY(10px)" },
              "100%": { opacity: "1", transform: "translateY(0)" },
            },
          }
        },
      },
    };
  </script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
</head>

<body class="bg-gray-900 text-white font-sans transition-all duration-300">

  <!-- Navbar -->
  <header class="bg-gray-800 fixed w-full top-0 z-50 shadow-md transition-colors duration-300">
    <div class="max-w-7xl mx-auto px-4 py-4 flex items-center justify-between">
      <div class="flex items-center space-x-2">
        <!-- 🔥 CLICKABLE LOGO START -->
        <a href="index.php" class="flex items-center space-x-2">
          <span class="text-3xl font-bold text-primary">GYM CORE</span>
          <i class="fas fa-dumbbell text-white text-xl transition-colors duration-300"></i>
        </a>
        <!-- 🔥 CLICKABLE LOGO END -->
      </div>
      <nav class="hidden md:flex space-x-6 text-sm uppercase tracking-wider">
        <a href="index.php" class="hover:text-primary transition">Home</a>
        <a href="about.php" class="hover:text-primary transition">About</a>
        <a href="supplements.php" class="hover:text-primary transition">Supplements</a>
        <a href="instructors.php" class="hover:text-primary">Instructors</a>
        <a href="contact.php" class="hover:text-primary transition">Contact</a>
      </nav>
      <div class="flex items-center gap-3">
        <a href="login.php" class="hidden md:inline-block px-4 py-2 text-primary border border-primary rounded hover:bg-primary hover:text-white transition">Login</a>
        <a href="register.php" class="hidden md:inline-block px-4 py-2 bg-primary text-white rounded hover:bg-primary/90 transition">Register</a>
        <button id="menu-toggle" class="md:hidden text-xl text-white focus:outline-none transition-colors duration-300"><i class="fas fa-bars"></i></button>
      </div>
    </div>

    <!-- Mobile Menu -->
    <div id="mobile-menu" class="hidden md:hidden bg-gray-800 transition-colors duration-300">
      <nav class="flex flex-col items-center py-4 space-y-4">
        <a href="index.php" class="hover:text-primary">Home</a>
        <a href="about.php" class="hover:text-primary">About</a>
        <a href="supplements.php" class="hover:text-primary">Supplements</a>
        <a href="instructors.php" class="hover:text-primary">Instructors</a>
        <a href="contact.php" class="hover:text-primary">Contact</a>
        <a href="login.php" class="px-4 py-2 border border-primary text-primary rounded w-4/5 text-center">Login</a>
        <a href="register.php" class="px-4 py-2 bg-primary text-white rounded w-4/5 text-center">Register</a>
      </nav>
    </div>
  </header>

  <!-- Login Section -->
  <main class="relative flex items-center justify-center min-h-screen pt-24 px-4 bg-[url('images/Login/login.jpg')] bg-cover bg-center transition-colors duration-300">
    <div class="absolute inset-0 bg-black bg-opacity-70 z-0 transition-colors duration-300"></div>

    <!-- Login Card -->
    <section class="relative z-10 backdrop-blur-md bg-white/10 border border-white/20 p-8 rounded-xl shadow-xl max-w-md w-full animate-fadeIn transition-colors duration-300">
      <h2 class="text-3xl font-bold text-center text-white mb-6">Welcome Back</h2>
      <form action="login_process.php" method="POST" class="space-y-6">
        <div>
          <label for="email" class="block mb-1 font-semibold text-white">Email</label>
          <input type="email" id="email" name="email" required placeholder="you@example.com"
            class="w-full px-4 py-2 rounded bg-gray-800 border border-gray-600 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary transition duration-300" />
        </div>
        <div>
          <label for="password" class="block mb-1 font-semibold text-white">Password</label>
          <input type="password" id="password" name="password" required placeholder="••••••••"
            class="w-full px-4 py-2 rounded bg-gray-800 border border-gray-600 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary transition duration-300" />
        </div>
        <button type="submit"
          class="w-full bg-primary hover:bg-primary/90 text-white font-bold py-3 rounded transition duration-300">
          Login
        </button>
      </form>
      <p class="mt-6 text-center text-gray-300">
        Don’t have an account?
        <a href="register.php" class="text-primary hover:underline">Register here</a>
      </p>
    </section>
  </main>

  <!-- Footer -->
  <footer class="text-center text-gray-400 py-6 bg-gray-900 border-t border-gray-700 transition-colors duration-300">
    &copy; <?= date('Y') ?> GYM Core. All rights reserved.
  </footer>

  <!-- JavaScript -->
  <script>
    // Mobile menu toggle
    document.getElementById("menu-toggle").addEventListener("click", () => {
      document.getElementById("mobile-menu").classList.toggle("hidden");
    });
  </script>
</body>
</html>
