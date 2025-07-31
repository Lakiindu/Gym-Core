<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>GYM Core - Contact Us</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            primary: "#ff6600",
          },
          keyframes: {
            fadeIn: {
              '0%': { opacity: 0 },
              '100%': { opacity: 1 },
            },
          },
          animation: {
            fadeIn: "fadeIn 1.5s ease-out",
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
      <a href="index.php" class="flex items-center">
  <span class="text-3xl font-bold text-primary">GYM CORE</span>
  <i class="fas fa-dumbbell ml-2 text-white text-xl"></i>
</a>

      <nav class="hidden md:flex space-x-6 text-sm uppercase tracking-wide">
        <a href="index.php" class="hover:text-primary">Home</a>
        <a href="about.php" class="hover:text-primary">About Us</a>
        <a href="supplements.php" class="hover:text-primary">Supplements</a>
        <a href="contact.php" class="text-primary">Contact</a>
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
        <a href="contact.php" class="text-primary">Contact</a>
        <a href="login.php" class="px-4 py-2 border border-primary text-primary rounded w-5/6 text-center">Login</a>
        <a href="register.php" class="px-4 py-2 bg-primary text-white rounded w-5/6 text-center">Register</a>
      </nav>
    </div>
  </header>

  <!-- Hero Section -->
  <section class="relative h-[60vh] bg-cover bg-center flex items-center justify-center px-6" style="background-image: url('contact.jpg'); margin-top: 64px;">
    <div class="bg-black/70 p-10 md:p-16 rounded-xl text-center animate-fadeIn">
      <h1 class="text-4xl md:text-6xl font-extrabold">CONTACT <span class="text-primary">US</span></h1>
      <p class="mt-4 text-gray-300 max-w-2xl mx-auto">We’d love to hear from you! Fill out the form below or reach us directly.</p>
    </div>
  </section>

  <!-- Contact Form Section -->
  <section class="py-16 bg-gray-900">
    <div class="max-w-4xl mx-auto px-4">
      <div class="bg-gray-800 p-8 rounded-lg shadow-lg">
        <form action="#" method="POST" class="space-y-6">
          <div>
            <label class="block mb-1 text-sm font-semibold">Name</label>
            <input type="text" name="name" required class="w-full p-3 rounded bg-gray-700 text-white focus:outline-none focus:ring-2 focus:ring-primary" />
          </div>
          <div>
            <label class="block mb-1 text-sm font-semibold">Email</label>
            <input type="email" name="email" required class="w-full p-3 rounded bg-gray-700 text-white focus:outline-none focus:ring-2 focus:ring-primary" />
          </div>
          <div>
            <label class="block mb-1 text-sm font-semibold">Message</label>
            <textarea name="message" rows="5" required class="w-full p-3 rounded bg-gray-700 text-white focus:outline-none focus:ring-2 focus:ring-primary"></textarea>
          </div>
          <button type="submit" class="bg-primary px-6 py-3 rounded text-white font-semibold hover:bg-primary/90 transition">Send Message</button>
        </form>
      </div>
    </div>
  </section>

   <!-- Beautiful Modern Footer with Top & Bottom Line -->
  <footer class="bg-gray-900 text-gray-400 pt-16 pb-10 border-t border-gray-700">
    <div class="max-w-7xl mx-auto px-4 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-10 border-b border-gray-700 pb-12">

      <!-- Brand -->
      <div class="pr-4 border-r border-gray-700">
        <h3 class="text-white text-2xl font-extrabold mb-4 tracking-wide">GYM CORE</h3>
        <p class="leading-relaxed">Your ultimate online fitness hub for training, transformation, and supplements.</p>
        <div class="flex items-center space-x-3 mt-5">
          <a href="#" class="w-9 h-9 flex items-center justify-center bg-gray-700 hover:bg-primary rounded-full transition"><i class="fab fa-facebook-f"></i></a>
          <a href="#" class="w-9 h-9 flex items-center justify-center bg-gray-700 hover:bg-primary rounded-full transition"><i class="fab fa-instagram"></i></a>
          <a href="#" class="w-9 h-9 flex items-center justify-center bg-gray-700 hover:bg-primary rounded-full transition"><i class="fab fa-twitter"></i></a>
          <a href="#" class="w-9 h-9 flex items-center justify-center bg-gray-700 hover:bg-primary rounded-full transition"><i class="fab fa-youtube"></i></a>
        </div>
      </div>

      <!-- Quick Links -->
      <div class="sm:pl-4 sm:border-r border-gray-700">
        <h4 class="text-white font-semibold text-lg mb-4 tracking-wider">Quick Links</h4>
        <ul class="space-y-3 text-sm">
          <li><a href="index.php" class="hover:text-white transition">🏠 Home</a></li>
          <li><a href="about.php" class="hover:text-white transition">👥 About Us</a></li>
          <li><a href="supplements.php" class="hover:text-white transition">💊 Supplements</a></li>
          <li><a href="contact.php" class="hover:text-white transition">📞 Contact</a></li>
        </ul>
      </div>

      <!-- Contact Info -->
      <div class="sm:pl-4 sm:border-r border-gray-700">
        <h4 class="text-white font-semibold text-lg mb-4 tracking-wider">Contact Us</h4>
        <ul class="space-y-3 text-sm">
          <li><i class="fas fa-envelope text-primary mr-2"></i> support@gymcore.com</li>
          <li><i class="fas fa-phone-alt text-primary mr-2"></i> +123-456-7890</li>
          <li><i class="fas fa-map-marker-alt text-primary mr-2"></i> Colombo, Sri Lanka</li>
        </ul>
      </div>

      <!-- Newsletter -->
      <div class="sm:pl-4">
        <h4 class="text-white font-semibold text-lg mb-4 tracking-wider">Subscribe to Newsletter</h4>
        <p class="text-sm mb-4">Get the latest updates, deals, and fitness tips in your inbox.</p>
        <form class="flex flex-col space-y-3">
          <input type="email" placeholder="Enter your email" class="px-4 py-2 rounded bg-gray-800 border border-gray-700 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary" />
          <button type="submit" class="bg-primary hover:bg-primary/90 text-white py-2 rounded font-bold transition">Subscribe</button>
        </form>
      </div>
    </div>

    <div class="text-center text-sm text-gray-500 pt-6">
      &copy; <?php echo date('Y'); ?> GYM CORE. All rights reserved.
    </div>
  </footer>

  <!-- JavaScript -->
  <script>
    document.getElementById("menu-toggle").addEventListener("click", function () {
      document.getElementById("mobile-menu").classList.toggle("hidden");
    });
    const modeToggle = document.getElementById("mode-toggle");
    modeToggle.addEventListener("click", function () {
      document.body.classList.toggle("bg-gray-900");
      document.body.classList.toggle("bg-gray-100");
      document.body.classList.toggle("text-white");
      document.body.classList.toggle("text-gray-900");
      const sun = modeToggle.querySelector(".fa-sun");
      const moon = modeToggle.querySelector(".fa-moon");
      sun.classList.toggle("hidden");
      moon.classList.toggle("hidden");
    });
  </script>
</body>
</html>
