<?php
session_start();

// Access control: Only logged in users with role 'member' can access this page
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'member') {
    header("Location: login.php");
    exit;
}

// DB connection details
$host = "localhost";
$dbname = "gym_db";
$user = "postgres";
$password = "lakindu";

try {
    $conn = new PDO("pgsql:host=$host;dbname=$dbname", $user, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Fetch memberships that are active (available plans)
    $stmt = $conn->query("SELECT * FROM buy_membership WHERE is_active = TRUE ORDER BY id ASC");
    $memberships = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Fetch all purchased memberships for logged in user ordered by purchase_date DESC
    $purchaseStmt = $conn->prepare("
        SELECT mp.id, bm.plan_name, bm.duration_days, bm.price, mp.purchase_date
        FROM membership_purchases mp
        JOIN buy_membership bm ON mp.membership_id = bm.id
        WHERE mp.user_id = ?
        ORDER BY mp.purchase_date DESC
    ");
    $purchaseStmt->execute([$_SESSION['user_id']]);
    $purchased_memberships = $purchaseStmt->fetchAll(PDO::FETCH_ASSOC);

    // Determine the latest purchase date(s) for active memberships
    $active_memberships = [];
    if (count($purchased_memberships) > 0) {
        $latest_date = $purchased_memberships[0]['purchase_date'];
        // Filter all memberships with the same latest purchase date
        foreach ($purchased_memberships as $purchase) {
            if ($purchase['purchase_date'] === $latest_date) {
                $active_memberships[] = $purchase;
            } else {
                break; // Since ordered DESC, once date differs, stop
            }
        }
    }

} catch (PDOException $e) {
    die("DB Error: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Buy Memberships - GYM Core</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link
    rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
  />
  <script>
    // Open payment modal and set membership id
    function openPaymentModal(membershipId, planName, price) {
      document.getElementById('membershipIdInput').value = membershipId;
      document.getElementById('selectedPlanName').textContent = planName;
      document.getElementById('selectedPlanPrice').textContent = price.toFixed(2);
      document.getElementById('paymentModal').classList.remove('hidden');
      document.body.style.overflow = 'hidden'; // prevent scroll while modal open
    }
    // Close payment modal
    function closePaymentModal() {
      document.getElementById('paymentForm').reset();
      document.getElementById('paymentModal').classList.add('hidden');
      document.body.style.overflow = 'auto';
    }
  </script>
</head>
<body class="bg-gray-900 text-white font-sans">

<!-- Sidebar -->
<aside class="fixed top-0 left-0 h-full w-64 bg-gray-800 shadow z-50">
  <div class="px-6 py-4 border-b border-gray-700">
    <h1 class="text-2xl font-bold text-orange-500 flex items-center gap-2">
      <i class="fas fa-dumbbell"></i> GYM Core
    </h1>
  </div>
  <nav class="mt-6 px-4 space-y-4">
    <a href="user_dashboard.php" class="block px-4 py-3 rounded hover:bg-orange-600 transition">
      <i class="fas fa-home mr-2"></i>Dashboard
    </a>
    <a href="profile.php" class="block px-4 py-3 rounded hover:bg-orange-600 transition">
       <i class="fas fa-user mr-3"></i> Profile
    </a>
    <a href="supplements_dashboard.php" class="block px-4 py-3 rounded hover:bg-orange-600 transition">
      <i class="fas fa-capsules mr-3"></i> Supplements
    </a>
    <a href="buy_membership.php" class="block px-4 py-3 rounded bg-orange-600">
      <i class="fas fa-id-card mr-2"></i>Memberships
    </a>
    <a href="orders_dashboard.php" class="block px-4 py-3 rounded hover:bg-orange-600 transition">
      <i class="fas fa-box-open mr-3"></i>Membership Purchases and Order Details
    </a>
    <a href="book_trainer_dashboard.php" class="block px-4 py-3 rounded hover:bg-primary/20">
    <i class="fas fa-dumbbell mr-3"></i> Trainers
</a>

    <a
      href="logout.php"
      class="block px-4 py-3 rounded hover:bg-red-600 text-red-500 hover:text-white transition"
    >
      <i class="fas fa-sign-out-alt mr-2"></i>Logout
    </a>
  </nav>
</aside>

<!-- Main Content -->
<main class="md:ml-64 p-8 min-h-screen max-w-7xl mx-auto">
  <h2 class="text-3xl font-bold mb-8">Available Memberships</h2>

  <!-- Success and Error messages -->
  <?php if (!empty($_SESSION['success_message'])): ?>
    <div
      class="mb-6 p-4 bg-green-600 rounded text-white font-semibold"
      role="alert"
    >
      <?= htmlspecialchars($_SESSION['success_message']) ?>
    </div>
    <?php unset($_SESSION['success_message']); ?>
  <?php endif; ?>

  <?php if (!empty($_SESSION['error_message'])): ?>
    <div
      class="mb-6 p-4 bg-red-600 rounded text-white font-semibold"
      role="alert"
    >
      <?= htmlspecialchars($_SESSION['error_message']) ?>
    </div>
    <?php unset($_SESSION['error_message']); ?>
  <?php endif; ?>

  <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
    <?php foreach ($memberships as $membership): ?>
      <div class="bg-gray-800 rounded-lg shadow-lg p-6 flex flex-col justify-between hover:shadow-xl transition-shadow duration-300">
        <div>
          <h3 class="text-2xl font-bold text-orange-400 mb-3">
            <?= htmlspecialchars($membership['plan_name']) ?>
          </h3>
          <p class="mb-3 text-gray-300 leading-relaxed">
            <?= htmlspecialchars($membership['description']) ?>
          </p>
          <p class="mb-2 text-orange-300 font-semibold flex items-center gap-2">
            <i class="fa-solid fa-hourglass-half"></i>
            Duration: <?= htmlspecialchars($membership['duration_days']) ?> days
          </p>
          <p class="mb-4 text-green-400 font-bold text-lg flex items-center gap-2">
            <i class="fa-solid fa-tag"></i>
            Rs. <?= number_format($membership['price'], 2) ?>
          </p>
          <?php if (!empty($membership['features'])): ?>
            <p class="mb-4 text-sm text-gray-400 italic">
              <strong>Features:</strong> <?= htmlspecialchars($membership['features']) ?>
            </p>
          <?php endif; ?>
        </div>
        <button
          onclick="openPaymentModal(<?= $membership['id'] ?>, '<?= htmlspecialchars(addslashes($membership['plan_name'])) ?>', <?= $membership['price'] ?>)"
          class="bg-orange-500 hover:bg-orange-600 text-white px-6 py-3 rounded font-semibold w-full"
        >
          Buy Now
        </button>
      </div>
    <?php endforeach; ?>
  </div>

  <!-- Active Membership Section -->
  <section class="mt-16">
    <h2 class="text-3xl font-bold mb-6 border-b border-green-500 pb-2">
      Your Active Membership<?= count($active_memberships) > 1 ? 's' : '' ?>
    </h2>

    <?php if (count($active_memberships) === 0): ?>
      <p class="text-gray-400 italic">You currently have no active memberships.</p>
    <?php else: ?>
      <div class="space-y-6">
        <?php foreach ($active_memberships as $active): ?>
          <div class="bg-green-900 rounded-lg p-6 shadow-md">
            <h3 class="text-2xl font-semibold text-green-400 mb-2"><?= htmlspecialchars($active['plan_name']) ?></h3>
            <p class="text-green-300 mb-1">
              Duration: <?= htmlspecialchars($active['duration_days']) ?> days
            </p>
            <p class="text-green-300 mb-1">
              Price: Rs. <?= number_format($active['price'], 2) ?>
            </p>
            <p class="text-green-200 text-sm">
              Purchased on: <?= date('Y-m-d H:i', strtotime($active['purchase_date'])) ?>
            </p>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </section>

  <!-- Purchased Memberships Section -->
  <section class="mt-16">
    <h2 class="text-3xl font-bold mb-6 border-b border-orange-600 pb-2">
      Your Purchased Memberships (History)
    </h2>

    <?php if (count($purchased_memberships) === 0): ?>
      <p class="text-gray-400 italic">You have not purchased any memberships yet.</p>
    <?php else: ?>
      <div class="overflow-x-auto bg-gray-800 rounded shadow p-4">
        <table class="min-w-full table-auto border-collapse text-white">
          <thead>
            <tr class="border-b border-orange-600">
              <th class="text-left px-6 py-3">Purchase ID</th>
              <th class="text-left px-6 py-3">Plan Name</th>
              <th class="text-left px-6 py-3">Duration (Days)</th>
              <th class="text-left px-6 py-3">Price (Rs.)</th>
              <th class="text-left px-6 py-3">Purchase Date</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($purchased_memberships as $purchase): ?>
              <tr class="border-b border-gray-700 hover:bg-orange-900">
                <td class="px-6 py-3"><?= htmlspecialchars($purchase['id']) ?></td>
                <td class="px-6 py-3"><?= htmlspecialchars($purchase['plan_name']) ?></td>
                <td class="px-6 py-3"><?= htmlspecialchars($purchase['duration_days']) ?></td>
                <td class="px-6 py-3"><?= number_format($purchase['price'], 2) ?></td>
                <td class="px-6 py-3"><?= date('Y-m-d H:i', strtotime($purchase['purchase_date'])) ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php endif; ?>
  </section>
</main>

<!-- Payment Modal -->
<div
  id="paymentModal"
  class="hidden fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center z-60"
>
  <div class="bg-gray-800 rounded-lg shadow-lg p-8 w-full max-w-md relative">
    <button
      onclick="closePaymentModal()"
      class="absolute top-3 right-3 text-gray-400 hover:text-white"
      aria-label="Close"
    >
      <i class="fas fa-times text-2xl"></i>
    </button>
    <h3 class="text-xl font-bold text-orange-400 mb-4">
      Pay for Membership: <span id="selectedPlanName"></span>
    </h3>
    <p class="mb-6 text-green-400 font-bold text-lg">
      Price: Rs. <span id="selectedPlanPrice"></span>
    </p>
    <form id="paymentForm" action="process_membership_purchase.php" method="POST" class="space-y-4">
      <input type="hidden" name="membership_id" id="membershipIdInput" />
      <!-- Simple Payment Fields -->
      <div>
        <label class="block mb-1" for="card_name">Name on Card</label>
        <input
          type="text"
          id="card_name"
          name="card_name"
          required
          class="w-full rounded px-3 py-2 text-black"
          placeholder="John Doe"
        />
      </div>
      <div>
        <label class="block mb-1" for="card_number">Card Number</label>
        <input
          type="text"
          id="card_number"
          name="card_number"
          pattern="\d{16}"
          maxlength="16"
          required
          class="w-full rounded px-3 py-2 text-black"
          placeholder="1234 5678 9012 3456"
        />
      </div>
      <div class="flex gap-4">
        <div class="flex-1">
          <label class="block mb-1" for="expiry_date">Expiry Date (MM/YY)</label>
          <input
            type="text"
            id="expiry_date"
            name="expiry_date"
            pattern="(0[1-9]|1[0-2])\/\d{2}"
            placeholder="MM/YY"
            required
            class="w-full rounded px-3 py-2 text-black"
          />
        </div>
        <div class="flex-1">
          <label class="block mb-1" for="cvv">CVV</label>
          <input
            type="text"
            id="cvv"
            name="cvv"
            pattern="\d{3}"
            maxlength="3"
            required
            class="w-full rounded px-3 py-2 text-black"
            placeholder="123"
          />
        </div>
      </div>
      <button
        type="submit"
        class="bg-orange-500 hover:bg-orange-600 text-white px-6 py-3 rounded font-semibold w-full"
      >
        Pay Now
      </button>
    </form>
  </div>
</div>

</body>
</html>
