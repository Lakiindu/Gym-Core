<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
  <meta charset="UTF-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>GYM Core - Supplements</title>

  <!-- Tailwind CSS CDN -->
  <script src="https://cdn.tailwindcss.com"></script>

  <!-- Tailwind Custom Config -->
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

  <!-- FontAwesome for icons -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet" />

  <!-- AOS CSS -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css" rel="stylesheet" />

  <style>
    html {
      scroll-behavior: smooth;
    }

    /* Modal overlay and visibility */
    #modal {
      opacity: 0;
      pointer-events: none;
      transition: opacity 0.3s ease;
      backdrop-filter: blur(4px);
      background-color: rgba(0, 0, 0, 0.7);
    }
    #modal.show {
      opacity: 1;
      pointer-events: auto;
    }

    /* Modal container animation */
    #modal > div {
      transform: translateY(-20px);
      transition: transform 0.3s ease;
    }
    #modal.show > div {
      transform: translateY(0);
    }

    /* Custom scrollbar for modal desc */
    #modal-desc {
      max-height: 100px;
      overflow-y: auto;
      scrollbar-width: thin;
      scrollbar-color: #ff6600 #1f1f1f;
    }
    #modal-desc::-webkit-scrollbar {
      width: 6px;
    }
    #modal-desc::-webkit-scrollbar-track {
      background: #1f1f1f;
      border-radius: 3px;
    }
    #modal-desc::-webkit-scrollbar-thumb {
      background-color: #ff6600;
      border-radius: 3px;
    }

    /* Quick View button */
    .quick-view-btn {
      background-color: rgba(0,0,0,0.55);
      font-weight: 600;
      letter-spacing: 0.05em;
      text-transform: uppercase;
    }

    /* Focus styles */
    button:focus, a:focus {
      outline: 2px solid #ff6600;
      outline-offset: 2px;
    }
  </style>
</head>
<body class="bg-gray-900 text-white font-sans transition-colors duration-500 relative">

  <!-- Navbar Section -->
  <header class="bg-gray-800 fixed w-full top-0 z-50 shadow">
    <div class="max-w-7xl mx-auto px-4 py-4 flex items-center justify-between">
      
      <!-- Logo and Home Link -->
      <a href="index.php" class="flex items-center" aria-label="Go to homepage">
        <span class="text-3xl font-bold text-primary">GYM CORE</span>
        <i class="fas fa-dumbbell ml-2 text-white text-xl"></i>
      </a>

      <!-- Desktop Navigation Links -->
      <nav class="hidden md:flex space-x-6 text-sm uppercase tracking-wide" role="navigation" aria-label="Primary Navigation">
        <a href="index.php" class="hover:text-primary focus:text-primary focus:outline-none">Home</a>
        <a href="about.php" class="hover:text-primary focus:text-primary focus:outline-none">About Us</a>
        <a href="supplements.php" class="text-primary font-bold" aria-current="page">Supplements</a>
        <a href="instructors.php" class="hover:text-primary focus:text-primary focus:outline-none">Instructors</a>
        <a href="contact.php" class="hover:text-primary focus:text-primary focus:outline-none">Contact</a>
      </nav>

      <!-- Right side: Login/Register buttons and toggles -->
      <div class="flex items-center space-x-3">
        <a href="login.php" class="hidden md:inline-block text-sm px-4 py-2 border border-primary text-primary rounded hover:bg-primary hover:text-white transition focus:outline-none focus:ring-2 focus:ring-primary">Login</a>
        <a href="register.php" class="hidden md:inline-block text-sm px-4 py-2 bg-primary text-white rounded hover:bg-primary/90 transition focus:outline-none focus:ring-2 focus:ring-primary">Register</a>

        <!-- Mobile Menu Toggle Button -->
        <button id="menu-toggle" class="md:hidden text-white text-xl focus:outline-none" aria-label="Toggle mobile menu">
          <i class="fas fa-bars"></i>
        </button>
      </div>
    </div>

    <!-- Mobile Navigation Menu -->
    <div id="mobile-menu" class="hidden bg-gray-800 md:hidden">
      <nav class="flex flex-col items-center py-4 space-y-3">
        <a href="index.php" class="hover:text-primary focus:text-primary focus:outline-none">Home</a>
        <a href="about.php" class="hover:text-primary focus:text-primary focus:outline-none">About Us</a>
        <a href="supplements.php" class="text-primary font-bold" aria-current="page">Supplements</a>
        <a href="instructors.php" class="hover:text-primary focus:text-primary focus:outline-none">Instructors</a>
        <a href="contact.php" class="hover:text-primary focus:text-primary focus:outline-none">Contact</a>
        <a href="login.php" class="px-4 py-2 border border-primary text-primary rounded w-5/6 text-center hover:bg-primary hover:text-white transition focus:outline-none">Login</a>
        <a href="register.php" class="px-4 py-2 bg-primary text-white rounded w-5/6 text-center hover:bg-primary/90 transition focus:outline-none">Register</a>
      </nav>
    </div>
  </header>

  <!-- Hero Section -->
  <section class="pt-32 pb-16 bg-gradient-to-r from-primary/90 via-transparent to-primary/60 text-center max-w-7xl mx-auto px-4 rounded-b-3xl shadow-lg">
    <h1 class="text-5xl font-extrabold mb-4 drop-shadow-lg">Premium Supplements</h1>
    <p class="text-gray-200 max-w-xl mx-auto text-lg mb-6">Discover our top-quality supplements crafted to power your fitness journey and maximize your gains.</p>
  </section>

  <!-- Main Content: Supplement Grid -->
  <main id="supplement-grid" class="pt-16 max-w-7xl mx-auto px-4">
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-10 mb-24">

      <?php
        $animations = ['fade-up', 'fade-right', 'fade-left', 'zoom-in'];

        $supplements = [
          ["name" => "Whey Protein", "desc" => "Supports muscle growth and recovery.", "price" => "RS.25000", "image" => "Supplement Images/whey.jpg", "rating" => 5],
          ["name" => "ISO dymatize", "desc" => "Remove excess lactose,carbs,fat,sugar for maximum gains.", "price" => "RS.28000", "image" => "Supplement Images/Dymatize.jpg", "rating" => 4],
          ["name" => "Carnivore Beef Protein", "desc" => "Muscle-building power of beef with greater amino acid levels ", "price" => "RS.28000", "image" => "Supplement Images/Carnivor.jpg", "rating" => 5],
          ["name" => "Gold Creatine", "desc" => "Boosts performance in high-intensity training.", "price" => "RS.8500", "image" => "Supplement Images/creatine.jpg", "rating" => 4],
          ["name" => "Applied Nutrition Creatine", "desc" => "Boosts performance in high-intensity training.", "price" => "RS.9500", "image" => "Supplement Images/AN Creatine.jpg", "rating" => 4],
          ["name" => "BCAA Capsules", "desc" => "Improves recovery and reduces fatigue.", "price" => "R.7500", "image" => "Supplement Images/bcaa.jpg", "rating" => 4],
          ["name" => "Fish Oil", "desc" => "Rich in omega-3 fatty acids like EPA and DHA.", "price" => "RS.8500", "image" => "Supplement Images/Fishoil.jpg", "rating" => 3],
          ["name" => "Dymatize Super Mass ", "desc" => "The ideal protein powder for your muscle gains.", "price" => "RS.27000", "image" => "Supplement Images/Dymatize Mass.jpg", "rating" => 5],
          ["name" => "Serious Mass Gainer", "desc" => "Helps you bulk and gain lean muscle.", "price" => "RS.31000", "image" => "Supplement Images/mass.jpg", "rating" => 5],
          ["name" => "Gold Pre-Workout", "desc" => "Enhances focus and energy before workouts.", "price" => "RS.9500", "image" => "Supplement Images/goldpre.jpg", "rating" => 4],
          ["name" => "Curse Pre-Workout", "desc" => "Enhances focus and energy before workouts.", "price" => "RS.9000", "image" => "Supplement Images/cursepre.jpg", "rating" => 4],
          ["name" => "L-Glutamine", "desc" => "Boosts recovery and immunity.", "price" => "RS.8500", "image" => "Supplement Images/glutamine.jpg", "rating" => 3],
          ["name" => "ZMA Recovery", "desc" => "Supports endurance and deep sleep.", "price" => "RS.10000", "image" => "Supplement Images/zma.jpg", "rating" => 4],
          ["name" => "Protein Bar", "desc" => "Gives Energy.", "price" => "RS.1000", "image" => "Supplement Images/Protein Bar.jpg", "rating" => 4],
          ["name" => "Multi vitamin", "desc" => "Daily vitamins for athletes.", "price" => "RS.12000", "image" => "Supplement Images/multi.jpg", "rating" => 5],
          ["name" => "Hydroxycut Elite", "desc" => "Provides a thermogenic and energy experience with unrivaled intensity", "price" => "RS.7000", "image" => "Supplement Images/Hydroxycut.jpg", "rating" => 4],
          ["name" => "Fat Burner", "desc" => "Increases metabolism & promotes fat loss.", "price" => "RS.13500", "image" => "Supplement Images/burner.jpg", "rating" => 3],
          ["name" => "Monster Energy Drink", "desc" => "Gives Caffene and energy.", "price" => "RS.900", "image" => "Supplement Images/monster.jpg", "rating" => 5]
        ];

        foreach ($supplements as $index => $supp) {
          $animation = $animations[$index % count($animations)];
          $delay = $index * 120;

          $stars = str_repeat('<i class="fas fa-star text-yellow-400" aria-hidden="true"></i>', $supp['rating']) .
                   str_repeat('<i class="far fa-star text-yellow-600" aria-hidden="true"></i>', 5 - $supp['rating']);

          $descEscaped = htmlspecialchars($supp['desc'], ENT_QUOTES);
          $nameEscaped = htmlspecialchars($supp['name'], ENT_QUOTES);
          $priceEscaped = htmlspecialchars($supp['price'], ENT_QUOTES);
          $imageEscaped = htmlspecialchars($supp['image'], ENT_QUOTES);

          echo '
          <article role="article" aria-label="' . $nameEscaped . ' supplement card" data-aos="' . $animation . '" data-aos-delay="' . $delay . '" data-aos-duration="900" class="bg-gray-800/60 backdrop-blur-md rounded-2xl overflow-hidden shadow-lg hover:shadow-2xl hover:scale-[1.04] transition-transform duration-300 relative group cursor-pointer">
            <div class="aspect-[4/3] overflow-hidden relative rounded-t-2xl">
              <img src="images/' . $imageEscaped . '" alt="' . $nameEscaped . ' supplement image" loading="lazy" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110 group-hover:brightness-110 rounded-t-2xl" onerror="this.src=\'images/default.jpg\'" />
              <button
                aria-label="Quick view ' . $nameEscaped . '"
                class="quick-view-btn absolute inset-0 bg-black bg-opacity-60 opacity-0 group-hover:opacity-100 flex items-center justify-center text-white font-semibold tracking-widest text-sm rounded-t-2xl transition-opacity duration-300"
                data-name="' . $nameEscaped . '"
                data-desc="' . $descEscaped . '"
                data-price="' . $priceEscaped . '"
                data-rating="' . $supp["rating"] . '"
                data-image="images/' . $imageEscaped . '"
                type="button"
              >
                QUICK VIEW
              </button>
            </div>
            <div class="p-6">
              <h2 class="text-xl font-bold mb-1 text-center">' . $nameEscaped . '</h2>
              <p class="text-gray-300 text-sm mb-3 text-center truncate" title="' . $descEscaped . '">' . $descEscaped . '</p>
              <div class="flex justify-center mb-3" aria-label="Rating: ' . $supp["rating"] . ' out of 5 stars">' . $stars . '</div>
              <div class="flex justify-between items-center px-3 mt-4">
                <span class="text-primary font-bold text-lg" aria-label="Price ' . $priceEscaped . '">' . $priceEscaped . '</span>
                <a href="login.php" class="bg-primary px-6 py-2 rounded-full shadow-md hover:bg-primary/90 transition inline-flex items-center gap-2 focus:outline-none focus:ring-2 focus:ring-primary" aria-label="Buy ' . $nameEscaped . '">
                  <i class="fas fa-shopping-cart"></i> Buy
                </a>
              </div>
            </div>
          </article>';
        }
      ?>
    </div>
  </main>

  <!-- Polished Quick View Modal -->
  <div id="modal" tabindex="-1" aria-hidden="true" class="fixed inset-0 flex items-center justify-center p-6 z-50 hidden">
    <div role="dialog" aria-modal="true" aria-labelledby="modal-title" aria-describedby="modal-desc" class="bg-gray-800 rounded-xl max-w-4xl w-full shadow-xl overflow-hidden flex flex-col md:flex-row animate-fadeIn">
      
      <!-- Left: Image -->
      <div class="md:w-1/2 h-64 md:h-auto relative overflow-hidden">
        <img id="modal-image" src="" alt="" class="w-full h-full object-cover" />
      </div>

      <!-- Right: Details -->
      <div class="md:w-1/2 p-8 flex flex-col">
        <button id="modal-close" aria-label="Close modal" class="self-end text-gray-300 hover:text-white text-3xl focus:outline-none focus:ring-2 focus:ring-primary">
          <i class="fas fa-times"></i>
        </button>
        <h2 id="modal-title" class="text-3xl font-extrabold mb-4 text-primary"></h2>
        <p id="modal-desc" class="flex-grow text-gray-300 mb-6 overflow-y-auto max-h-36"></p>
        <div id="modal-rating" class="flex mb-6" aria-label="Supplement rating"></div>
        <span id="modal-price" class="text-primary font-bold text-2xl"></span>
        <a href="login.php" class="mt-auto bg-primary hover:bg-primary/90 text-white py-3 rounded-lg text-center shadow-md focus:outline-none focus:ring-2 focus:ring-primary transition" aria-label="Buy supplement">
          <i class="fas fa-shopping-cart mr-2"></i> Buy Now
        </a>
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

  <!-- AOS JS -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>

  <script>
    AOS.init({
      once: true,
      easing: "ease-in-out",
      duration: 800,
    });
  </script>

  <!-- Mobile menu scripts -->
  <script>
    const menuToggle = document.getElementById('menu-toggle');
    const mobileMenu = document.getElementById('mobile-menu');

    menuToggle.addEventListener('click', () => {
      mobileMenu.classList.toggle('hidden');
    });
  </script>

  <!-- Quick View Modal Script -->
  <script>
    const modal = document.getElementById('modal');
    const modalClose = document.getElementById('modal-close');
    const modalTitle = document.getElementById('modal-title');
    const modalDesc = document.getElementById('modal-desc');
    const modalPrice = document.getElementById('modal-price');
    const modalRating = document.getElementById('modal-rating');
    const modalImage = document.getElementById('modal-image');

    function openModalWithData(data) {
      modalTitle.textContent = data.name;
      modalDesc.textContent = data.desc;
      modalPrice.textContent = data.price;
      modalImage.src = data.image;
      modalImage.alt = data.name + " image";

      // Clear previous stars
      modalRating.innerHTML = '';

      for(let i = 0; i < data.rating; i++) {
        const star = document.createElement('i');
        star.className = 'fas fa-star text-yellow-400';
        star.setAttribute('aria-hidden', 'true');
        modalRating.appendChild(star);
      }
      for(let i = data.rating; i < 5; i++) {
        const star = document.createElement('i');
        star.className = 'far fa-star text-yellow-600';
        star.setAttribute('aria-hidden', 'true');
        modalRating.appendChild(star);
      }

      modal.classList.remove('hidden');
      modal.classList.add('show');
      modal.setAttribute('aria-hidden', 'false');
      modal.focus();
      document.body.style.overflow = 'hidden'; // disable background scroll
    }

    // Delegate click event on quick view buttons
    document.addEventListener('click', function(event) {
      if(event.target.closest('.quick-view-btn')) {
        const btn = event.target.closest('.quick-view-btn');
        const data = {
          name: btn.getAttribute('data-name'),
          desc: btn.getAttribute('data-desc'),
          price: btn.getAttribute('data-price'),
          rating: parseInt(btn.getAttribute('data-rating')),
          image: btn.getAttribute('data-image')
        };
        openModalWithData(data);
      }
    });

    function closeModal() {
      modal.classList.remove('show');
      modal.classList.add('hidden');
      modal.setAttribute('aria-hidden', 'true');
      document.body.style.overflow = ''; // re-enable scroll
    }

    modalClose.addEventListener('click', closeModal);

    modal.addEventListener('click', (e) => {
      if (e.target === modal) closeModal();
    });

    document.addEventListener('keydown', (e) => {
      if(e.key === "Escape" && modal.classList.contains('show')) {
        closeModal();
      }
    });
  </script>

</body>
</html>
