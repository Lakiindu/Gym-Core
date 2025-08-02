<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Subscribe – GYM CORE</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: { primary: "#ff6600" },
          fontFamily: { sans: ["Inter", "ui-sans-serif", "system-ui"] },
        },
      },
    };
  </script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet" />
  <style>
    .text-gradient {
      background-clip: text;
      -webkit-background-clip: text;
      color: transparent;
    }
    .animation-delay-1000 {
      animation-delay: 1s;
    }
  </style>
</head>
<body class="bg-gradient-to-b from-gray-100 to-gray-200 text-gray-900 font-sans">

  <!-- Top Left Logo Button -->
  <div class="fixed top-4 left-6 z-50">
    <a href="index.php" class="flex items-center space-x-2 px-4 py-2 bg-gray-800 hover:bg-primary text-white rounded-full shadow-lg transition duration-300">
      <span class="text-2xl font-bold text-primary">GYM CORE</span>
      <i class="fas fa-dumbbell text-white text-lg"></i>
    </a>
  </div>

  <!-- Header -->
  <header class="bg-[#1f1f1f] text-white py-16 shadow-lg relative overflow-hidden">
    <div class="max-w-4xl mx-auto px-6 text-center relative z-10">
      <div class="inline-flex items-center justify-center mb-4">
        <i class="fas fa-dumbbell text-primary text-4xl mr-3 animate-bounce"></i>
        <a href="index.php" class="text-5xl font-extrabold tracking-tight leading-tight hover:text-primary transition duration-300">
          Subscribe to <span class="text-gradient bg-gradient-to-r from-primary via-yellow-400 to-primary bg-clip-text text-transparent">GYM CORE</span>
        </a>
      </div>
      <p class="text-gray-300 text-xl max-w-2xl mx-auto tracking-wide font-medium">
        Get exclusive fitness tips, deals &amp; updates delivered straight to your inbox.
      </p>
      <div class="mt-8 flex justify-center">
        <span class="block w-28 h-1 rounded-full bg-primary/80 shadow-lg"></span>
      </div>
    </div>
    <div aria-hidden="true" class="absolute -top-16 -left-16 w-72 h-72 bg-primary/30 rounded-full filter blur-3xl animate-pulse"></div>
    <div aria-hidden="true" class="absolute -bottom-20 -right-20 w-96 h-96 bg-yellow-400/20 rounded-full filter blur-3xl animate-pulse animation-delay-1000"></div>
  </header>

  <!-- Main Content -->
  <main class="max-w-xl mx-auto mt-14 px-6">

    <?php if (isset($_SESSION['success'])): ?>
      <div class="bg-green-50 border border-green-400 text-green-700 px-5 py-4 rounded-lg mb-8 shadow-md flex items-center space-x-3">
        <i class="fas fa-check-circle text-green-600 text-xl"></i>
        <p class="font-semibold text-green-700"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></p>
      </div>
    <?php elseif (isset($_SESSION['error'])): ?>
      <div class="bg-red-50 border border-red-400 text-red-700 px-5 py-4 rounded-lg mb-8 shadow-md flex items-center space-x-3">
        <i class="fas fa-exclamation-circle text-red-600 text-xl"></i>
        <p class="font-semibold text-red-700"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></p>
      </div>
    <?php endif; ?>

    <form action="subscribe_process.php" method="POST" class="bg-white rounded-2xl shadow-lg p-8 transition hover:shadow-xl duration-300">
      <label for="email" class="block text-gray-800 font-semibold mb-3 text-lg">Email Address</label>
      <input 
        type="email" 
        name="email" 
        id="email" 
        placeholder="your@email.com" 
        required 
        class="w-full px-5 py-3 border border-gray-300 rounded-xl text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-4 focus:ring-primary focus:border-primary transition" 
        autocomplete="email"
        autofocus
      />
      <button 
        type="submit" 
        class="mt-6 w-full bg-primary hover:bg-primary/90 text-white font-extrabold py-3 rounded-xl shadow-md hover:shadow-lg transition duration-300 flex justify-center items-center space-x-2"
        aria-label="Subscribe"
      >
        <span>Subscribe</span>
        <i class="fas fa-paper-plane"></i>
      </button>
    </form>

    <p class="mt-6 text-center text-gray-600 text-sm max-w-md mx-auto">
      💪 By subscribing, you’ll receive weekly fitness tips, special discounts, and exclusive early access to new supplements.
    </p>
  </main>

  <!-- Footer -->
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

</body>
</html>
