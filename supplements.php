<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>GYM Core - Supplements</title>
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
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet" />
  <link href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css" rel="stylesheet" />
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
        <a href="supplements.php" class="text-primary font-bold">Supplements</a>
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
        <a href="supplements.php" class="text-primary font-bold">Supplements</a>
        <a href="contact.php" class="hover:text-primary">Contact</a>
        <a href="login.php" class="px-4 py-2 border border-primary text-primary rounded w-5/6 text-center">Login</a>
        <a href="register.php" class="px-4 py-2 bg-primary text-white rounded w-5/6 text-center">Register</a>
      </nav>
    </div>
  </header>

  <!-- Main Content -->
  <main class="pt-28 max-w-7xl mx-auto px-4">
    <h1 class="text-4xl font-bold mb-6 text-center">Available Supplements</h1>
    <p class="text-gray-400 text-center mb-10 max-w-2xl mx-auto">Explore our best-selling supplements designed to help you reach your fitness goals faster and more effectively.</p>

    <hr class="border-gray-700 mb-10" />

    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-10 mb-20">

      <?php
        $animations = ['fade-up', 'fade-right', 'fade-left', 'zoom-in'];
        $supplements = [
          ["name" => "Whey Protein", "desc" => "Supports muscle growth and recovery.", "price" => "$49.99", "image" => "whey.jpg", "rating" => 5],
          ["name" => "Creatine", "desc" => "Boosts performance in high-intensity training.", "price" => "$29.99", "image" => "creatine.jpg", "rating" => 4],
          ["name" => "BCAA Capsules", "desc" => "Improves recovery and reduces fatigue.", "price" => "$19.99", "image" => "bcaa.jpg", "rating" => 4],
          ["name" => "Mass Gainer", "desc" => "Helps you bulk and gain lean muscle.", "price" => "$59.99", "image" => "mass.jpg", "rating" => 5],
          ["name" => "Pre-Workout", "desc" => "Enhances focus and energy before workouts.", "price" => "$34.99", "image" => "preworkout.jpg", "rating" => 4],
          ["name" => "L-Glutamine", "desc" => "Boosts recovery and immunity.", "price" => "$24.99", "image" => "glutamine.jpg", "rating" => 3],
          ["name" => "ZMA Recovery", "desc" => "Supports endurance and deep sleep.", "price" => "$27.99", "image" => "zma.jpg", "rating" => 4],
          ["name" => "Multivitamin", "desc" => "Daily vitamins for athletes.", "price" => "$14.99", "image" => "multi.jpg", "rating" => 5],
          ["name" => "Fat Burner", "desc" => "Increases metabolism & promotes fat loss.", "price" => "$39.99", "image" => "burner.jpg", "rating" => 3]
        ];

        foreach ($supplements as $index => $supp) {
          $animation = $animations[$index % count($animations)];
          $delay = $index * 100;
          $stars = str_repeat('<i class="fas fa-star text-yellow-400"></i>', $supp['rating']) .
                   str_repeat('<i class="far fa-star text-yellow-600"></i>', 5 - $supp['rating']);

          echo '
          <div data-aos="' . $animation . '" data-aos-delay="' . $delay . '" data-aos-duration="800" class="bg-gray-800 rounded-xl overflow-hidden shadow-lg hover:shadow-2xl transition duration-300">
            <div class="aspect-[4/3] overflow-hidden">
              <img src="images/' . $supp['image'] . '" alt="' . $supp['name'] . '" loading="lazy" class="w-full h-full object-cover transition-transform duration-300 hover:scale-105 hover:brightness-110" onerror="this.src=\'images/default.jpg\'" />
            </div>
            <div class="p-5">
              <h2 class="text-xl font-bold text-center mb-2">' . $supp['name'] . '</h2>
              <p class="text-gray-400 text-sm text-center mb-3">' . $supp['desc'] . '</p>
              <div class="flex justify-center mb-2">' . $stars . '</div>
              <div class="flex justify-between items-center px-2 mt-3">
                <span class="text-primary font-bold text-lg">' . $supp['price'] . '</span>
                <a href="login.php" class="bg-primary text-white px-4 py-1 rounded hover:bg-primary/90 transition">Buy</a>
              </div>
            </div>
          </div>';
        }
      ?>
    </div>
  </main>

 <!-- Footer -->
<footer class="bg-[#1f1f1f] text-gray-300 pt-16 pb-10 border-t-4 border-primary mt-16">
  <div class="max-w-7xl mx-auto px-4 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-10 border-b border-gray-700 pb-12">

    <!-- Brand Info -->
    <div class="pr-4 border-r border-gray-700">
      <h3 class="text-white text-2xl font-extrabold mb-4 tracking-wide">GYM CORE</h3>
      <p class="text-sm leading-relaxed">Your ultimate online fitness hub for training, transformation, and supplements.</p>
      <div class="flex items-center space-x-3 mt-5">
        <a href="https://www.facebook.com/share/1DqCn5Ubdz/" target="_blank" class="w-9 h-9 flex items-center justify-center bg-gray-700 hover:bg-blue-600 text-white rounded-full transition">
          <i class="fab fa-facebook-f"></i>
        </a>
        <a href="https://www.instagram.com/_.lakiyaaa?igsh=MXA5MGs5ZXJsaHFiaQ==" target="_blank" class="w-9 h-9 flex items-center justify-center bg-gray-700 hover:bg-pink-500 text-white rounded-full transition">
          <i class="fab fa-instagram"></i>
        </a>
        <a href="https://wa.me/qr/5KU4P7OCDNLDG1" target="_blank" class="w-9 h-9 flex items-center justify-center bg-gray-700 hover:bg-green-500 text-white rounded-full transition">
          <i class="fab fa-whatsapp"></i>
        </a>
        <a href="https://www.youtube.com/@LakinduRansika-sw2mc" target="_blank" class="w-9 h-9 flex items-center justify-center bg-gray-700 hover:bg-red-600 text-white rounded-full transition">
          <i class="fab fa-youtube"></i>
        </a>
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
        <li><i class="fas fa-phone-alt text-primary mr-2"></i> 076-561-4545</li>
        <li><i class="fas fa-map-marker-alt text-primary mr-2"></i> Colombo, Sri Lanka</li>
      </ul>
    </div>

    <!-- Newsletter -->
    <div class="sm:pl-4">
      <h4 class="text-white font-semibold text-lg mb-4 tracking-wider">Subscribe to Newsletter</h4>
      <p class="text-sm mb-4">Get the latest updates, deals, and fitness tips in your inbox.</p>
      <form action="subscribe.php" method="get" class="flex flex-col space-y-3">
        <button type="submit"
          class="bg-primary hover:bg-primary/90 text-white py-2 rounded font-bold transition">
          Subscribe
        </button>
      </form>
    </div>
  </div>

  <!-- Bottom Note with only top border -->
  <div class="text-center text-sm text-gray-500 pt-6 border-t-4 border-primary mt-6">
    &copy; <?php echo date("Y"); ?> GYM Core. All rights reserved.
  </div>
</footer>

  <!-- Scripts -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>
  <script>
    AOS.init({ once: true, easing: 'ease-out-cubic' });

    document.getElementById('menu-toggle').addEventListener('click', () => {
      document.getElementById('mobile-menu').classList.toggle('hidden');
    });

    const modeToggle = document.getElementById('mode-toggle');
    const sunIcon = modeToggle.querySelector('.fa-sun');
    const moonIcon = modeToggle.querySelector('.fa-moon');
    modeToggle.addEventListener('click', () => {
      document.documentElement.classList.toggle('dark');
      sunIcon.classList.toggle('hidden');
      moonIcon.classList.toggle('hidden');
      localStorage.setItem('theme', document.documentElement.classList.contains('dark') ? 'dark' : 'light');
    });
    if (localStorage.getItem('theme') === 'dark') {
      document.documentElement.classList.add('dark');
      sunIcon.classList.remove('hidden');
      moonIcon.classList.add('hidden');
    }
  </script>

  <style>
    .dark body {
      background-color: #121212;
      color: #e0e0e0;
    }
    .dark .bg-gray-900 { background-color: #121212 !important; }
    .dark .bg-gray-800 { background-color: #1e1e1e !important; }
    .dark .text-gray-400 { color: #a3a3a3 !important; }
    .dark .border-gray-700 { border-color: #333 !important; }
  </style>
</body>
</html>
