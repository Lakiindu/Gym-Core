<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>GYM Core - Home</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: { colors: { primary: "#ff6600" } },
      },
    };
  </script>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet" />
  <link href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper/swiper-bundle.min.css" />
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
        <a href="index.php" class="text-primary font-bold" aria-current="page">Home</a>
        <a href="about.php" class="hover:text-primary">About Us</a>
        <a href="supplements.php" class="hover:text-primary">Supplements</a>
        <a href="instructors.php" class="hover:text-primary">Instructors</a>
        <a href="contact.php" class="hover:text-primary">Contact</a>
      </nav>
      <div class="flex items-center space-x-3">
        <a href="login.php" class="hidden md:inline-block text-sm px-4 py-2 border border-primary text-primary rounded hover:bg-primary hover:text-white transition">Login</a>
        <a href="register.php" class="hidden md:inline-block text-sm px-4 py-2 bg-primary text-white rounded hover:bg-primary/90 transition">Register</a>
        <button id="menu-toggle" class="md:hidden text-white text-xl focus:outline-none"><i class="fas fa-bars"></i></button>
      </div>
    </div>
    <div id="mobile-menu" class="hidden bg-gray-800 md:hidden">
      <nav class="flex flex-col items-center py-4 space-y-3">
        <a href="index.php" class="text-primary font-bold" aria-current="page">Home</a>
        <a href="about.php" class="hover:text-primary">About Us</a>
        <a href="supplements.php" class="hover:text-primary">Supplements</a>
        <a href="instructors.php" class="hover:text-primary">Instructors</a>
        <a href="contact.php" class="hover:text-primary">Contact</a>
        <a href="login.php" class="px-4 py-2 border border-primary text-primary rounded w-5/6 text-center">Login</a>
        <a href="register.php" class="px-4 py-2 bg-primary text-white rounded w-5/6 text-center">Register</a>
      </nav>
    </div>
  </header>

  <!-- Hero Section Slider -->
  <section class="relative h-screen mt-16">
    <div class="swiper heroSwiper h-full">
      <div class="swiper-wrapper">
        
        <!-- Slide 1 -->
        <div class="swiper-slide h-full bg-cover bg-center" style="background-image: url('images/Home Slides/gym2.jpg');">
          <div class="bg-black/70 h-full flex items-center px-6">
            <div class="max-w-lg p-8 md:p-12 rounded-xl animate-fadeIn">
              <h4 class="uppercase text-sm tracking-widest text-gray-300">Shape Your Body</h4>
              <h1 class="text-4xl md:text-6xl font-extrabold mt-4 leading-tight">
                BE <span class="text-primary">STRONG</span><br> TRAIN HARD
              </h1>
              <p class="mt-4 text-gray-300">Join us and transform your fitness journey with personalized training and premium supplements.</p>
              <a href="about.php" class="mt-6 inline-block bg-primary hover:bg-primary/90 text-white text-sm font-bold px-8 py-3 rounded transition">GET INFO</a>
            </div>
          </div>
        </div>

        <!-- Slide 2 -->
        <div class="swiper-slide h-full bg-cover bg-center" style="background-image: url('images/Home Slides/gym1.jpg');">
          <div class="bg-black/70 h-full flex items-center px-6">
            <div class="max-w-lg p-8 md:p-12 rounded-xl animate-fadeIn">
              <h4 class="uppercase text-sm tracking-widest text-gray-300">Unleash Your Power</h4>
              <h1 class="text-4xl md:text-6xl font-extrabold mt-4 leading-tight">
                PUSH <span class="text-primary">LIMITS</span><br> BREAK BARRIERS
              </h1>
              <p class="mt-4 text-gray-300">Empower your workouts with real-time results and expert trainers.</p>
              <a href="register.php" class="mt-6 inline-block bg-primary hover:bg-primary/90 text-white text-sm font-bold px-8 py-3 rounded transition">JOIN NOW</a>
            </div>
          </div>
        </div>

        <!-- Slide 3 -->
        <div class="swiper-slide h-full bg-cover bg-center" style="background-image: url('images/Home Slides/gym3.jpg');">
          <div class="bg-black/70 h-full flex items-center px-6">
            <div class="max-w-lg p-8 md:p-12 rounded-xl animate-fadeIn">
              <h4 class="uppercase text-sm tracking-widest text-gray-300">Fuel Your Fitness</h4>
              <h1 class="text-4xl md:text-6xl font-extrabold mt-4 leading-tight">
                SHOP <span class="text-primary">SUPPLEMENTS</span><br> LEVEL UP
              </h1>
              <p class="mt-4 text-gray-300">Top-quality supplements delivered to your doorstep.</p>
              <a href="supplements.php" class="mt-6 inline-block bg-primary hover:bg-primary/90 text-white text-sm font-bold px-8 py-3 rounded transition">SHOP NOW</a>
            </div>
          </div>
        </div>

        <!-- Slide 4 -->
        <div class="swiper-slide h-full bg-cover bg-center" style="background-image: url('images/Home Slides/gym4.jpg');">
          <div class="bg-black/70 h-full flex items-center px-6">
            <div class="max-w-lg p-8 md:p-12 rounded-xl animate-fadeIn">
              <h4 class="uppercase text-sm tracking-widest text-gray-300">Train with Pros</h4>
              <h1 class="text-4xl md:text-6xl font-extrabold mt-4 leading-tight">
                EXPERT <span class="text-primary">COACHES</span><br> PERSONAL PLANS
              </h1>
              <p class="mt-4 text-gray-300">Get trained by certified professionals with workouts tailored to you.</p>
              <a href="instructors.php" class="mt-6 inline-block bg-primary hover:bg-primary/90 text-white text-sm font-bold px-8 py-3 rounded transition">MEET COACHES</a>
            </div>
          </div>
        </div>

        <!-- Slide 5 -->
        <div class="swiper-slide h-full bg-cover bg-center" style="background-image: url('images/Home Slides/gym5.jpg');">
          <div class="bg-black/70 h-full flex items-center px-6">
            <div class="max-w-lg p-8 md:p-12 rounded-xl animate-fadeIn">
              <h4 class="uppercase text-sm tracking-widest text-gray-300">Stay Updated</h4>
              <h1 class="text-4xl md:text-6xl font-extrabold mt-4 leading-tight">
                LATEST <span class="text-primary">DEALS</span><br> & EVENTS
              </h1>
              <p class="mt-4 text-gray-300">Subscribe and never miss updates on promotions, events, and news.</p>
              <a href="subscribe.php" class="mt-6 inline-block bg-primary hover:bg-primary/90 text-white text-sm font-bold px-8 py-3 rounded transition">SUBSCRIBE</a>
            </div>
          </div>
        </div>
      </div>

      <!-- Navigation Buttons -->
      <div class="swiper-button-next"></div>
      <div class="swiper-button-prev"></div>
      <div class="swiper-pagination"></div>
    </div>
  </section>

  <!-- Features Section -->
  <section class="py-20 bg-gray-900">
    <div class="max-w-7xl mx-auto px-4">
      <h2 class="text-4xl font-bold text-center mb-14 text-white tracking-wide">Our Features</h2>
      <div class="grid grid-cols-1 md:grid-cols-3 gap-10">

        <!-- Feature Card -->
        <div class="group relative p-1 rounded-xl bg-gradient-to-tr from-purple-700 via-blue-600 to-indigo-700 hover:via-pink-500 transition-all duration-700 shadow-xl hover:scale-105">
          <div class="bg-gray-800/90 backdrop-blur-xl p-6 rounded-xl h-full transition-transform duration-500 ease-in-out group-hover:-translate-y-2 group-hover:shadow-2xl">
            <div class="text-4xl text-primary mb-4 transform transition-transform duration-500 group-hover:rotate-[12deg] group-hover:scale-125">
              <i class="fas fa-user-check"></i>
            </div>
            <h3 class="text-2xl font-semibold text-white mb-2">User Authentication</h3>
            <p class="text-gray-300">Secure login, account management, and password recovery.</p>
          </div>
        </div>

        <!-- Feature Card -->
        <div class="group relative p-1 rounded-xl bg-gradient-to-tr from-green-700 via-emerald-600 to-teal-700 hover:via-yellow-500 transition-all duration-700 shadow-xl hover:scale-105">
          <div class="bg-gray-800/90 backdrop-blur-xl p-6 rounded-xl h-full transition-transform duration-500 ease-in-out group-hover:-translate-y-2 group-hover:shadow-2xl">
            <div class="text-4xl text-primary mb-4 transform transition-transform duration-500 group-hover:-rotate-[12deg] group-hover:scale-125">
              <i class="fas fa-dumbbell"></i>
            </div>
            <h3 class="text-2xl font-semibold text-white mb-2">Flexible Workouts</h3>
            <p class="text-gray-300">Customized plans and class scheduling for every level.</p>
          </div>
        </div>

        <!-- Feature Card -->
        <div class="group relative p-1 rounded-xl bg-gradient-to-tr from-pink-700 via-rose-600 to-red-700 hover:via-purple-500 transition-all duration-700 shadow-xl hover:scale-105">
          <div class="bg-gray-800/90 backdrop-blur-xl p-6 rounded-xl h-full transition-transform duration-500 ease-in-out group-hover:-translate-y-2 group-hover:shadow-2xl">
            <div class="text-4xl text-primary mb-4 transform transition-transform duration-500 group-hover:rotate-[12deg] group-hover:scale-125">
              <i class="fas fa-shopping-cart"></i>
            </div>
            <h3 class="text-2xl font-semibold text-white mb-2">Online Store</h3>
            <p class="text-gray-300">Shop high‑quality supplements and gym gear easily.</p>
          </div>
        </div>

      </div>
    </div>
  </section>

  <!-- CTA Section -->
  <section
    class="relative w-full py-10 mt-20 bg-white/10 backdrop-blur-md border-t border-b border-primary/50 shadow-lg shadow-primary/40 overflow-hidden"
    role="region"
    aria-labelledby="cta-heading"
  >
    <!-- Animated Gradient Background -->
    <div
      aria-hidden="true"
      class="absolute inset-0 -z-10 bg-gradient-to-r from-primary to-orange-600 opacity-25 animate-gradient-x"
    ></div>

    <div class="relative z-10 max-w-7xl mx-auto px-6 flex flex-col md:flex-row items-center justify-between gap-6 md:gap-0">
      <div class="flex-1 text-center md:text-left">
        <h2
          id="cta-heading"
          class="text-2xl md:text-3xl font-extrabold tracking-tight text-white drop-shadow-lg mb-1 md:mb-2 leading-tight"
        >
          Ready to transform your fitness journey?
        </h2>
        <p class="text-sm md:text-base text-white/85 leading-snug max-w-xl mx-auto md:mx-0">
          Sign up today and take the first step toward a healthier, stronger you.
        </p>
      </div>

      <a
        href="register.php"
        class="inline-block px-8 py-2 rounded-full bg-gradient-to-r from-orange-500 to-yellow-400 text-white font-semibold shadow-md shadow-yellow-400/50 transition-transform duration-300 hover:scale-105 hover:brightness-110 focus:outline-none focus:ring-4 focus:ring-yellow-400 focus:ring-opacity-60 whitespace-nowrap"
        aria-label="Join Now and start your fitness journey"
        role="button"
        tabindex="0"
      >
        Join Now
      </a>
    </div>
  </section>

  <style>
    /* Animate gradient background horizontally */
    @keyframes gradient-x {
      0%, 100% {
        background-position: 0% center;
      }
      50% {
        background-position: 100% center;
      }
    }
    .animate-gradient-x {
      background-size: 200% 200%;
      animation: gradient-x 8s ease infinite;
    }
  </style>

  <!-- Testimonials -->
  <section class="py-16 bg-gradient-to-b from-gray-900 to-gray-800">
    <div class="max-w-7xl mx-auto px-4">
      <h2 class="text-3xl font-extrabold text-center mb-12 text-white drop-shadow-md">
        What Our Members Say
      </h2>
      <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
        
        <!-- Testimonial Card -->
        <div
          class="p-8 bg-gradient-to-tr from-gray-800 via-gray-900 to-gray-800 rounded-xl shadow-lg hover:shadow-2xl transform hover:-translate-y-1 transition-all duration-300 relative overflow-hidden"
          data-aos="fade-right"
          role="article"
          aria-label="Testimonial from John Doe"
        >
          <p class="text-gray-300 italic text-lg leading-relaxed mb-6">
            "The personal trainers are top-notch! They truly understand your fitness goals and push you to achieve more."
          </p>
          <div>
            <p class="text-white font-semibold">John Doe</p>
            <p class="text-primary text-sm tracking-wide">Member</p>
          </div>
        </div>

        <!-- Testimonial Card -->
        <div
          class="p-8 bg-gradient-to-tr from-gray-800 via-gray-900 to-gray-800 rounded-xl shadow-lg hover:shadow-2xl transform hover:-translate-y-1 transition-all duration-300 relative overflow-hidden"
          data-aos="fade-left"
          role="article"
          aria-label="Testimonial from Jane Smith"
        >
          <p class="text-gray-300 italic text-lg leading-relaxed mb-6">
            "Love the variety of workout plans available. The online portal is smooth and super easy to use."
          </p>
          <div>
            <p class="text-white font-semibold">Jane Smith</p>
            <p class="text-primary text-sm tracking-wide">Member</p>
          </div>
        </div>

        <!-- Testimonial Card -->
        <div
          class="p-8 bg-gradient-to-tr from-gray-800 via-gray-900 to-gray-800 rounded-xl shadow-lg hover:shadow-2xl transform hover:-translate-y-1 transition-all duration-300 relative overflow-hidden"
          data-aos="fade-right"
          role="article"
          aria-label="Testimonial from Carlos Vega"
        >
          <p class="text-gray-300 italic text-lg leading-relaxed mb-6">
            "I've never been more consistent with my fitness. The community and trainers are incredibly supportive."
          </p>
          <div>
            <p class="text-white font-semibold">Carlos Vega</p>
            <p class="text-primary text-sm tracking-wide">Member</p>
          </div>
        </div>

        <!-- Testimonial Card -->
        <div
          class="p-8 bg-gradient-to-tr from-gray-800 via-gray-900 to-gray-800 rounded-xl shadow-lg hover:shadow-2xl transform hover:-translate-y-1 transition-all duration-300 relative overflow-hidden"
          data-aos="fade-left"
          role="article"
          aria-label="Testimonial from Aanya Perera"
        >
          <p class="text-gray-300 italic text-lg leading-relaxed mb-6">
            "Supplements arrived super fast and the quality is unmatched. Definitely sticking with GYM Core!"
          </p>
          <div>
            <p class="text-white font-semibold">Aanya Perera</p>
            <p class="text-primary text-sm tracking-wide">Member</p>
          </div>
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

  <!-- Scripts -->
  <script src="https://cdn.jsdelivr.net/npm/swiper/swiper-bundle.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>
  <script>
    // Mobile menu toggle
    const menuToggle = document.getElementById('menu-toggle');
    const mobileMenu = document.getElementById('mobile-menu');
    menuToggle.addEventListener('click', () => {
      mobileMenu.classList.toggle('hidden');
    });

    // Initialize Swiper slider
    const swiper = new Swiper('.heroSwiper', {
      loop: true,
      autoplay: { delay: 5000, disableOnInteraction: false },
      pagination: { el: '.swiper-pagination', clickable: true },
      navigation: { nextEl: '.swiper-button-next', prevEl: '.swiper-button-prev' },
      effect: 'fade',
      fadeEffect: { crossFade: true }
    });

    // Initialize AOS
    AOS.init({
      duration: 1000,
      once: true
    });
  </script>

  <style>
    /* Animate gradient background horizontally */
    @keyframes gradient-x {
      0%, 100% {
        background-position: 0% center;
      }
      50% {
        background-position: 100% center;
      }
    }
    .animate-gradient-x {
      background-size: 200% 200%;
      animation: gradient-x 8s ease infinite;
    }
    /* Simple fade in animation for hero text */
    @keyframes fadeIn {
      from {opacity: 0; transform: translateY(15px);}
      to {opacity: 1; transform: translateY(0);}
    }
    .animate-fadeIn {
      animation: fadeIn 1s ease forwards;
    }
  </style>

</body>
</html>
