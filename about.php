<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>GYM Core - About Us</title>
  <script src="https://cdn.tailwindcss.com"></script>

  <!-- Font Awesome & AOS -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
  <link rel="stylesheet" href="https://unpkg.com/aos@2.3.1/dist/aos.css" />
  <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>

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
        <a href="about.php" class="text-primary">About Us</a>
        <a href="supplements.php" class="hover:text-primary">Supplements</a>
        <a href="instructors.php" class="hover:text-primary">Instructors</a>
        <a href="contact.php" class="hover:text-primary">Contact</a>
      </nav>
      <div class="flex items-center space-x-3">
        <a href="login.php" class="hidden md:inline-block text-sm px-4 py-2 border border-primary text-primary rounded hover:bg-primary hover:text-white transition">Login</a>
        <a href="register.php" class="hidden md:inline-block text-sm px-4 py-2 bg-primary text-white rounded hover:bg-primary/90 transition">Register</a>
        <button id="menu-toggle" class="md:hidden text-white text-xl focus:outline-none">
          <i class="fas fa-bars"></i>
        </button>
      </div>
    </div>
    <div id="mobile-menu" class="hidden bg-gray-800 md:hidden">
      <nav class="flex flex-col items-center py-4 space-y-3">
        <a href="index.php" class="hover:text-primary">Home</a>
        <a href="about.php" class="text-primary">About Us</a>
        <a href="supplements.php" class="hover:text-primary">Supplements</a>
        <a href="instructors.php" class="hover:text-primary">Instructors</a>
        <a href="contact.php" class="hover:text-primary">Contact</a>
        <a href="login.php" class="px-4 py-2 border border-primary text-primary rounded w-5/6 text-center">Login</a>
        <a href="register.php" class="px-4 py-2 bg-primary text-white rounded w-5/6 text-center">Register</a>
      </nav>
    </div>
  </header>

  <!-- Hero Section -->
  <section class="relative h-[60vh] bg-cover bg-center flex items-center justify-center px-6" style="background-image: url('images/About/about us.jpg'); margin-top: 64px;">
    <div class="bg-black/70 p-10 md:p-16 rounded-xl text-center animate-fadeIn">
      <h1 class="text-4xl md:text-6xl font-extrabold">ABOUT <span class="text-primary">US</span></h1>
      <p class="mt-4 text-gray-300 max-w-2xl mx-auto">We are dedicated to helping you achieve your fitness goals with personalized training, premium supplements, and expert support.</p>
    </div>
  </section>

  <!-- About Section -->
  <section class="py-16 bg-gray-900">
    <div class="max-w-6xl mx-auto px-4 text-center">
      <h2 class="text-3xl font-bold mb-4">Who We Are</h2>
      <p class="text-gray-400 max-w-3xl mx-auto">
        Gym Core is a modern, full-featured web platform designed for fitness lovers, trainers, and gym managers. We believe in combining technology with health, enabling users to follow structured workout plans, book classes, buy quality supplements, and track their progress.
      </p>
    </div>
  </section>

  <!-- Our Mission & Vision -->
  <section class="py-16 bg-gray-800">
    <div class="max-w-6xl mx-auto px-4 grid md:grid-cols-2 gap-10">
      <div data-aos="fade-up">
        <h3 class="text-2xl font-bold text-primary mb-2">Our Mission</h3>
        <p class="text-gray-400">
          To revolutionize the way people train, shop, and stay healthy by providing a seamless, digital-first gym experience.
        </p>
      </div>
      <div data-aos="fade-up" data-aos-delay="200">
        <h3 class="text-2xl font-bold text-primary mb-2">Our Vision</h3>
        <p class="text-gray-400">
          To be the leading digital fitness solution provider that empowers everyone to live a stronger, healthier life.
        </p>
      </div>
    </div>
  </section>

  <!-- Meet the Team -->
  <section class="py-16 bg-gray-900">
    <div class="max-w-6xl mx-auto px-4 text-center">
      <h2 class="text-3xl font-bold mb-8 text-white" data-aos="fade-down" data-aos-duration="1000">Meet Our Team</h2>
      <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <div 
          class="bg-gray-800 p-6 rounded-lg shadow text-center transform transition-transform duration-300 hover:scale-105 cursor-pointer"
          data-aos="fade-up" data-aos-delay="100" data-aos-duration="800"
        >
          <img 
            src="images/Instructors/instructor1.jpg" 
            class="rounded-full mx-auto mb-4 transition-transform duration-500 ease-in-out hover:scale-110 hover:shadow-lg hover:shadow-primary/60 cursor-pointer" 
            alt="Trainer" 
            loading="lazy"
          />
          <h4 class="text-xl font-semibold text-white">Coach Alex</h4>
          <p class="text-gray-400 text-sm">Head Trainer</p>
        </div>
        <div 
          class="bg-gray-800 p-6 rounded-lg shadow text-center transform transition-transform duration-300 hover:scale-105 cursor-pointer"
          data-aos="fade-up" data-aos-delay="200" data-aos-duration="800"
        >
          <img 
            src="images/Instructors/instructor2.jpg" 
            class="rounded-full mx-auto mb-4 transition-transform duration-500 ease-in-out hover:scale-110 hover:shadow-lg hover:shadow-primary/60 cursor-pointer" 
            alt="Admin" 
            loading="lazy"
          />
          <h4 class="text-xl font-semibold text-white">Sarah Kim</h4>
          <p class="text-gray-400 text-sm">Admin Coordinator</p>
        </div>
        <div 
          class="bg-gray-800 p-6 rounded-lg shadow text-center transform transition-transform duration-300 hover:scale-105 cursor-pointer"
          data-aos="fade-up" data-aos-delay="300" data-aos-duration="800"
        >
          <img 
            src="images/Instructors/instructor3.jpg" 
            class="rounded-full mx-auto mb-4 transition-transform duration-500 ease-in-out hover:scale-110 hover:shadow-lg hover:shadow-primary/60 cursor-pointer" 
            alt="Nutritionist" 
            loading="lazy"
          />
          <h4 class="text-xl font-semibold text-white">Mike Lee</h4>
          <p class="text-gray-400 text-sm">Nutrition Expert</p>
        </div>
      </div>
    </div>
  </section>

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
          <li><a href="instructors.php" class="hover:text-white transition">🏋️‍♂️ Instructors</a></li>
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

  <!-- JS -->
  <script>
    document.getElementById("menu-toggle").addEventListener("click", () => {
      const menu = document.getElementById("mobile-menu");
      menu.classList.toggle("hidden");
    });

    AOS.init({
      duration: 1000,
      once: true,
    });
  </script>
</body>
</html>
