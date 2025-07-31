<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>GYM Core - Login</title>
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
</head>
<body class="bg-gray-900 text-white font-sans transition-colors duration-300">

  <!-- Navbar -->
  <header class="bg-gray-800 fixed w-full top-0 z-50 shadow">
    <div class="max-w-7xl mx-auto px-4 py-4 flex items-center justify-between">
      <div class="flex items-center">
        <span class="text-3xl font-bold text-primary">GYM CORE</span>
        <i class="fas fa-dumbbell ml-2 text-white text-xl"></i>
      </div>
      <nav class="hidden md:flex space-x-6 text-sm uppercase tracking-wide">
        <a href="index.php" class="hover:text-primary">Home</a>
        <a href="about.php" class="hover:text-primary">About Us</a>
        <a href="supplements.php" class="hover:text-primary">Supplements</a>
        <a href="contact.php" class="hover:text-primary">Contact</a>
      </nav>
      <div class="flex items-center space-x-3">
        <a href="login.php" class="hidden md:inline-block text-sm px-4 py-2 border border-primary text-primary rounded hover:bg-primary hover:text-white transition">Login</a>
        <a href="register.php" class="hidden md:inline-block text-sm px-4 py-2 bg-primary text-white rounded hover:bg-primary/90 transition">Register</a>
        <button id="menu-toggle" class="md:hidden text-white text-xl focus:outline-none"><i class="fas fa-bars"></i></button>
        <button id="mode-toggle" class="focus:outline-none text-xl">
          <i class="fas fa-sun hidden"></i>
          <i class="fas fa-moon"></i>
        </button>
      </div>
    </div>
    <div id="mobile-menu" class="hidden bg-gray-800 md:hidden">
      <nav class="flex flex-col items-center py-4 space-y-3">
        <a href="index.php" class="hover:text-primary">Home</a>
        <a href="about.php" class="hover:text-primary">About Us</a>
        <a href="supplements.php" class="hover:text-primary">Supplements</a>
        <a href="contact.php" class="hover:text-primary">Contact</a>
        <a href="login.php" class="px-4 py-2 border border-primary text-primary rounded w-5/6 text-center">Login</a>
        <a href="register.php" class="px-4 py-2 bg-primary text-white rounded w-5/6 text-center">Register</a>
      </nav>
    </div>
  </header>

  <!-- Login Form Section with Background Image -->
  <main class="relative flex items-center justify-center min-h-screen pt-20 px-4 bg-cover bg-center" style="background-image: url('login.jpg');">
    <!-- Dark Overlay -->
    <div class="absolute inset-0 bg-black opacity-60 z-0"></div>

    <!-- Login Form -->
    <section class="relative z-10 bg-gray-800 bg-opacity-90 p-8 rounded-lg shadow-lg max-w-md w-full animate-fadeIn">
      <h2 class="text-3xl font-bold mb-6 text-center">Login to GYM Core</h2>
      <form action="login_process.php" method="POST" class="space-y-6">
        <div>
          <label for="email" class="block mb-1 font-semibold">Email</label>
          <input type="email" id="email" name="email" required class="w-full px-4 py-2 rounded border border-gray-600 bg-gray-900 focus:border-primary focus:outline-none" placeholder="you@example.com" />
        </div>
        <div>
          <label for="password" class="block mb-1 font-semibold">Password</label>
          <input type="password" id="password" name="password" required class="w-full px-4 py-2 rounded border border-gray-600 bg-gray-900 focus:border-primary focus:outline-none" placeholder="••••••••" />
        </div>
        <button type="submit" class="w-full bg-primary hover:bg-primary/90 text-white font-bold py-3 rounded transition duration-300">Login</button>
      </form>
      <p class="mt-6 text-center text-gray-400">
        Don't have an account? 
        <a href="register.php" class="text-primary hover:underline">Register here</a>
      </p>
    </section>
  </main>

  <!-- JavaScript -->
  <script>
    // Mobile Menu Toggle
    document.getElementById("menu-toggle").addEventListener("click", function () {
      document.getElementById("mobile-menu").classList.toggle("hidden");
    });

    // Dark/Light Mode Toggle
    const modeToggle = document.getElementById("mode-toggle");
    modeToggle.addEventListener("click", () => {
      document.body.classList.toggle("bg-gray-900");
      document.body.classList.toggle("bg-gray-100");
      document.body.classList.toggle("text-white");
      document.body.classList.toggle("text-gray-900");
      modeToggle.querySelector(".fa-sun").classList.toggle("hidden");
      modeToggle.querySelector(".fa-moon").classList.toggle("hidden");
    });
  </script>
</body>
</html>
