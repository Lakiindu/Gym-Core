<!-- header.php -->
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>GYM Core - Instructors</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: { primary: "#ff6600" }
        }
      }
    };
  </script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css" />
  <script defer>
    document.addEventListener("DOMContentLoaded", () => {
      document.getElementById("menu-toggle").addEventListener("click", () => {
        document.getElementById("mobile-menu").classList.toggle("hidden");
      });
    });
  </script>
  <style>
    /* Modal backdrop */
    #modal {
      position: fixed;
      inset: 0;
      background: rgba(0,0,0,0.7);
      display: none;
      justify-content: center;
      align-items: center;
      z-index: 9999;
      overflow-y: auto;
      padding: 1.5rem;
    }
    #modal.show {
      display: flex;
    }

    /* Modal box */
    #modal > div {
      background: #1f1f1f;
      border-radius: 1rem;
      max-width: 900px;
      width: 100%;
      max-height: 80vh;
      display: flex;
      flex-direction: row;
      overflow: hidden;
      box-shadow: 0 10px 30px rgba(0,0,0,0.7);
      position: relative;
    }

    /* Modal image */
    #modal-image {
      width: 40%;
      object-fit: cover;
      border-top-left-radius: 1rem;
      border-bottom-left-radius: 1rem;
      height: auto;
    }

    /* Modal content */
    #modal-content {
      padding: 2rem;
      flex: 1;
      color: #fff;
      overflow-y: auto;
    }

    /* Modal headings */
    #modal-title {
      font-size: 2rem;
      font-weight: 800;
      color: #ff6600;
      margin-bottom: 0.25rem;
    }
    #modal-title2 {
      font-size: 1.25rem;
      font-weight: 600;
      color: #b0b0b0;
      margin-bottom: 1rem;
    }
    #modal-desc {
      font-size: 1rem;
      line-height: 1.5;
      color: #ddd;
    }

    /* Close button */
    #modal-close {
      position: absolute;
      top: 0.75rem;
      right: 1rem;
      background: none;
      border: none;
      font-size: 2.5rem;
      color: #ff6600;
      cursor: pointer;
      font-weight: 900;
      transition: color 0.3s ease;
    }
    #modal-close:hover {
      color: #ffa64d;
    }

    /* Quick View button styling */
    .quick-view-btn {
      position: absolute;
      bottom: 0.75rem;
      right: 0.75rem;
      background-color: rgba(0,0,0,0.6);
      color: white;
      font-weight: 600;
      padding: 0.3rem 0.7rem;
      border-radius: 9999px;
      font-size: 0.75rem;
      letter-spacing: 0.05em;
      text-transform: uppercase;
      opacity: 0;
      transition: opacity 0.3s ease;
      cursor: pointer;
      user-select: none;
    }
    .group:hover .quick-view-btn,
    .group:focus-within .quick-view-btn {
      opacity: 1;
    }

    /* Position relative on cards for button */
    .instructor-card {
      position: relative;
      cursor: pointer;
    }

    /* Responsive modal */
    @media (max-width: 768px) {
      #modal > div {
        flex-direction: column;
        max-height: 90vh;
      }
      #modal-image {
        width: 100%;
        height: 250px;
        border-radius: 1rem 1rem 0 0;
      }
      #modal-content {
        padding: 1.5rem 1rem 2rem 1rem;
      }
    }
  </style>
</head>
<body class="bg-gray-900 text-white font-sans transition-colors duration-300">

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
      <a href="instructors.php" class="text-primary font-bold" aria-current="page">Instructors</a>
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
      <a href="index.php" class="text-primary hover:text-white">Home</a>
      <a href="about.php" class="hover:text-primary">About Us</a>
      <a href="supplements.php" class="hover:text-primary">Supplements</a>
      <a href="instructors.php" class="text-primary font-bold" aria-current="page">Instructors</a>
      <a href="contact.php" class="hover:text-primary">Contact</a>
      <a href="login.php" class="px-4 py-2 border border-primary text-primary rounded w-5/6 text-center">Login</a>
      <a href="register.php" class="px-4 py-2 bg-primary text-white rounded w-5/6 text-center">Register</a>
    </nav>
  </div>
</header>

  <!-- Hero Section -->
  <section class="pt-32 pb-16 bg-gradient-to-r from-primary/90 via-transparent to-primary/60 text-center max-w-7xl mx-auto px-4 rounded-b-3xl shadow-lg">
    <h1 class="text-5xl font-extrabold mb-4 drop-shadow-lg">Meet Our Expert Trainers</h1>
    <p class="text-gray-200 max-w-xl mx-auto text-lg mb-6">Trained .Trsuted . Certified . Dedicated to your transformation.</p>
  </section>

<!-- Instructors Grid -->
<section class="py-16 px-6 bg-gray-900">
  <div class="max-w-7xl mx-auto grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-10">

    <!-- Instructor Card 1 -->
    <div tabindex="0" role="button" class="instructor-card bg-gray-800 rounded-3xl overflow-hidden shadow-xl hover:shadow-2xl transition-all transform hover:-translate-y-1 group relative" data-aos="zoom-in" data-aos-delay="200"
      data-name="Alex Johnson" data-title="Strength Coach" data-desc="10+ years building champions. Expert in powerlifting & hypertrophy programs." data-image="images/Instructors/instructor1.jpg">
      <div class="w-full aspect-w-1 aspect-h-1 overflow-hidden relative">
        <img src="images/Instructors/instructor1.jpg" alt="Alex Johnson"
             class="object-cover w-full h-full transition-transform duration-500 ease-in-out transform group-hover:scale-105 group-hover:brightness-110" />
        <button class="quick-view-btn" aria-label="Quick view Alex Johnson">Quick View</button>
      </div>
      <div class="p-6 text-center">
        <h3 class="text-2xl font-bold text-primary mb-1">Alex Johnson</h3>
        <p class="text-sm text-gray-400 mb-2">Strength Coach</p>
        <p class="text-sm text-gray-300">10+ years building champions. Expert in powerlifting & hypertrophy programs.</p>
      </div>
    </div>

    <!-- Instructor Card 2 -->
    <div tabindex="0" role="button" class="instructor-card bg-gray-800 rounded-3xl overflow-hidden shadow-xl hover:shadow-2xl transition-all transform hover:-translate-y-1 group relative" data-aos="zoom-in" data-aos-delay="200"
      data-name="Emily Carter" data-title="Personal Trainer" data-desc="Certified in functional training, yoga & recovery. Holistic health advocate." data-image="images/Instructors/instructor5.jpg">
      <div class="w-full aspect-w-1 aspect-h-1 overflow-hidden relative">
        <img src="images/Instructors/instructor5.jpg" alt="Emily Carter"
             class="object-cover w-full h-full transition-transform duration-500 ease-in-out transform group-hover:scale-105 group-hover:brightness-110" />
        <button class="quick-view-btn" aria-label="Quick view Emily Carter">Quick View</button>
      </div>
      <div class="p-6 text-center">
        <h3 class="text-2xl font-bold text-primary mb-1">Emily Carter</h3>
        <p class="text-sm text-gray-400 mb-2">Personal Trainer</p>
        <p class="text-sm text-gray-300">Certified in functional training, yoga & recovery. Holistic health advocate.</p>
      </div>
    </div>

    <!-- Instructor Card 3 -->
    <div tabindex="0" role="button" class="instructor-card bg-gray-800 rounded-3xl overflow-hidden shadow-xl hover:shadow-2xl transition-all transform hover:-translate-y-1 group relative" data-aos="zoom-in" data-aos-delay="200"
      data-name="Marcus Lee" data-title="HIIT Specialist" data-desc="Expert in HIIT & conditioning. High-energy workouts tailored for fat burn." data-image="images/Instructors/instructor3.jpg">
      <div class="w-full aspect-w-1 aspect-h-1 overflow-hidden relative">
        <img src="images/Instructors/instructor3.jpg" alt="Marcus Lee"
             class="object-cover w-full h-full transition-transform duration-500 ease-in-out transform group-hover:scale-105 group-hover:brightness-110" />
        <button class="quick-view-btn" aria-label="Quick view Marcus Lee">Quick View</button>
      </div>
      <div class="p-6 text-center">
        <h3 class="text-2xl font-bold text-primary mb-1">Marcus Lee</h3>
        <p class="text-sm text-gray-400 mb-2">HIIT Specialist</p>
        <p class="text-sm text-gray-300">Expert in HIIT & conditioning. High-energy workouts tailored for fat burn.</p>
      </div>
    </div>

     <!-- Instructor Card 4 -->
    <div tabindex="0" role="button" class="instructor-card bg-gray-800 rounded-3xl overflow-hidden shadow-xl hover:shadow-2xl transition-all transform hover:-translate-y-1 group relative" data-aos="zoom-in" data-aos-delay="200"
      data-name="Lakindu Ransika" data-title="HIIT Specialist" data-desc="Expert in HIIT & conditioning. High-energy workouts tailored for fat burn." data-image="images/Instructors/instructor2.jpg">
      <div class="w-full aspect-w-1 aspect-h-1 overflow-hidden relative">
        <img src="images/Instructors/instructor2.jpg" alt="Lakindu Ransika"
             class="object-cover w-full h-full transition-transform duration-500 ease-in-out transform group-hover:scale-105 group-hover:brightness-110" />
        <button class="quick-view-btn" aria-label="Quick view Lakindu Ransika">Quick View</button>
      </div>
      <div class="p-6 text-center">
        <h3 class="text-2xl font-bold text-primary mb-1">Lakindu Ransika</h3>
        <p class="text-sm text-gray-400 mb-2">HIIT Specialist</p>
        <p class="text-sm text-gray-300">Expert in HIIT & conditioning. High-energy workouts tailored for fat burn.</p>
      </div>
    </div>

  </div>
</section>

<!-- Quick View Modal -->
<div id="modal" tabindex="-1" aria-hidden="true" role="dialog" aria-modal="true" aria-labelledby="modal-title" aria-describedby="modal-desc">
  <div>
    <button id="modal-close" aria-label="Close modal">&times;</button>
    <img id="modal-image" src="" alt="" />
    <div id="modal-content">
      <h2 id="modal-title"></h2>
      <p id="modal-title2"></p>
      <p id="modal-desc"></p>
    </div>
  </div>
</div>

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

<script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>
<script>
  AOS.init({ duration: 1000, once: true });

  // Quick View Modal JS
  document.addEventListener("DOMContentLoaded", () => {
    const modal = document.getElementById("modal");
    const modalImage = document.getElementById("modal-image");
    const modalTitle = document.getElementById("modal-title");
    const modalTitle2 = document.getElementById("modal-title2");
    const modalDesc = document.getElementById("modal-desc");
    const closeBtn = document.getElementById("modal-close");

    // Show modal with data from clicked card
    function showModal(e) {
      const card = e.currentTarget;
      const name = card.getAttribute("data-name");
      const title = card.getAttribute("data-title");
      const desc = card.getAttribute("data-desc");
      const image = card.getAttribute("data-image");

      modalImage.src = image;
      modalImage.alt = name;
      modalTitle.textContent = name;
      modalTitle2.textContent = title;
      modalDesc.textContent = desc;

      modal.classList.add("show");
      modal.classList.remove("hidden");
      modal.focus();
      document.body.style.overflow = "hidden"; // Prevent background scroll
    }

    // Close modal function
    function closeModal() {
      modal.classList.remove("show");
      modal.classList.add("hidden");
      document.body.style.overflow = ""; // Restore scroll
    }

    // Attach click event on all instructor cards
    const cards = document.querySelectorAll(".instructor-card");
    cards.forEach(card => {
      card.addEventListener("click", showModal);
      card.addEventListener("keypress", (e) => {
        if (e.key === "Enter" || e.key === " ") {
          e.preventDefault();
          showModal(e);
        }
      });
    });

    closeBtn.addEventListener("click", closeModal);

    // Close modal on outside click
    modal.addEventListener("click", (e) => {
      if (e.target === modal) {
        closeModal();
      }
    });

    // Close modal on Escape key press
    document.addEventListener("keydown", (e) => {
      if (e.key === "Escape" && modal.classList.contains("show")) {
        closeModal();
      }
    });
  });
</script>
</body>
</html>
