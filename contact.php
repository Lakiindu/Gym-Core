<?php
// Database connection
$host = "localhost";
$port = "5432"; // default PostgreSQL port
$dbname = "gym_db";
$user = "postgres"; // change to your PostgreSQL username
$pass = "lakindu"; // change to your PostgreSQL password

$conn = pg_connect("host=$host port=$port dbname=$dbname user=$user password=$pass");

if (!$conn) {
    die("Database connection failed: " . pg_last_error());
}

$successMsg = $errorMsg = "";

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name    = trim($_POST['name']);
    $email   = trim($_POST['email']);
    $message = trim($_POST['message']);

    if (!empty($name) && !empty($email) && !empty($message)) {
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $query = "INSERT INTO contact_messages (name, email, message) VALUES ($1, $2, $3)";
            $result = pg_query_params($conn, $query, [$name, $email, $message]);
            if ($result) {
                // Store success message in session and redirect
                session_start();
                $_SESSION['successMsg'] = "✅ Your message has been sent successfully!";
                header("Location: contact.php");
                exit;
            } else {
                $errorMsg = "❌ Error: " . pg_last_error($conn);
            }
        } else {
            $errorMsg = "❌ Invalid email address.";
        }
    } else {
        $errorMsg = "❌ All fields are required.";
    }
}

// Get success message from session if exists
session_start();
if (isset($_SESSION['successMsg'])) {
    $successMsg = $_SESSION['successMsg'];
    unset($_SESSION['successMsg']);
}
?>
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
          colors: { primary: "#ff6600" },
          keyframes: {
            fadeIn: { '0%': { opacity: 0 }, '100%': { opacity: 1 } },
          },
          animation: { fadeIn: "fadeIn 1.5s ease-out" },
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
        <a href="instructors.php" class="hover:text-primary">Instructors</a>
        <a href="contact.php" class="text-primary">Contact</a>
      </nav>
      <div class="flex items-center space-x-3">
        <a href="login.php" class="hidden md:inline-block text-sm px-4 py-2 border border-primary text-primary rounded hover:bg-primary hover:text-white transition">Login</a>
        <a href="register.php" class="hidden md:inline-block text-sm px-4 py-2 bg-primary text-white rounded hover:bg-primary/90 transition">Register</a>
        <button id="menu-toggle" class="md:hidden text-white text-xl focus:outline-none"><i class="fas fa-bars"></i></button>
      </div>
    </div>
    <div id="mobile-menu" class="hidden bg-gray-800 md:hidden">
      <nav class="flex flex-col items-center py-4 space-y-3">
        <a href="index.php" class="hover:text-primary">Home</a>
        <a href="about.php" class="hover:text-primary">About Us</a>
        <a href="supplements.php" class="hover:text-primary">Supplements</a>
        <a href="instructors.php" class="hover:text-primary">Instructors</a>
        <a href="contact.php" class="text-primary">Contact</a>
        <a href="login.php" class="px-4 py-2 border border-primary text-primary rounded w-5/6 text-center">Login</a>
        <a href="register.php" class="px-4 py-2 bg-primary text-white rounded w-5/6 text-center">Register</a>
      </nav>
    </div>
  </header>

  <!-- Hero Section -->
  <section class="relative h-[60vh] bg-cover bg-center flex items-center justify-center px-6" style="background-image: url('images/Contact/contact.jpg'); margin-top: 64px;">
    <div class="bg-black/70 p-10 md:p-16 rounded-xl text-center animate-fadeIn">
      <h1 class="text-4xl md:text-6xl font-extrabold">CONTACT <span class="text-primary">US</span></h1>
      <p class="mt-4 text-gray-300 max-w-2xl mx-auto">We’d love to hear from you! Fill out the form below or reach us directly.</p>
    </div>
  </section>

  <!-- Contact Form Section -->
  <section class="py-16 bg-gray-900">
    <div class="max-w-4xl mx-auto px-4">
      <div class="bg-gray-800 p-8 rounded-lg shadow-lg">

        <!-- Success/Error Messages -->
        <?php if (!empty($successMsg)): ?>
          <div class="mb-4 p-3 bg-green-600 text-white rounded"><?php echo $successMsg; ?></div>
        <?php endif; ?>
        <?php if (!empty($errorMsg)): ?>
          <div class="mb-4 p-3 bg-red-600 text-white rounded"><?php echo $errorMsg; ?></div>
        <?php endif; ?>

        <form action="" method="POST" class="space-y-6">
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
          <button type="submit" class="bg-primary hover:bg-primary/90 text-white py-2 rounded font-bold transition">Subscribe</button>
        </form>
      </div>
    </div>
    <div class="text-center text-sm text-gray-500 pt-6 border-t-4 border-primary mt-6">
      &copy; <?php echo date("Y"); ?> GYM Core. All rights reserved.
    </div>
  </footer>

  <!-- JavaScript -->
  <script>
    document.getElementById("menu-toggle").addEventListener("click", function () {
      document.getElementById("mobile-menu").classList.toggle("hidden");
    });
  </script>
</body>
</html>
