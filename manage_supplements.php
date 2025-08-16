<?php
// manage_supplements.php
session_start();

// DB connection
$host = "localhost";
$dbname = "gym_db";
$user = "postgres";
$password = "lakindu";

try {
    $pdo = new PDO("pgsql:host=$host;dbname=$dbname", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Handle insert/update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_supplement'])) {
    $name = $_POST['name'];
    $stock = $_POST['stock'];
    $price = $_POST['price'];
    $id = $_POST['supplement_id'] ?? null;
    $imagePath = $_POST['existing_image'] ?? '';

    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        $imageName = basename($_FILES['image']['name']);
        $targetDir = "uploads/";
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0777, true);
        }
        $targetPath = $targetDir . time() . "_" . $imageName;
        if (move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {
            $imagePath = $targetPath;
        }
    }

    if ($id) {
        // Update
        $stmt = $pdo->prepare("UPDATE supplements SET name=?, stock=?, price=?, image_path=? WHERE id=?");
        $stmt->execute([$name, $stock, $price, $imagePath, $id]);
    } else {
        // Insert
        $stmt = $pdo->prepare("INSERT INTO supplements (name, stock, price, image_path) VALUES (?, ?, ?, ?)");
        $stmt->execute([$name, $stock, $price, $imagePath]);
    }
    header("Location: manage_supplements.php");
    exit;
}

// Handle delete
if (isset($_GET['delete_id'])) {
    $id = $_GET['delete_id'];
    $stmt = $pdo->prepare("DELETE FROM supplements WHERE id = ?");
    $stmt->execute([$id]);
    header("Location: manage_supplements.php");
    exit;
}

// Handle search
$searchTerm = $_GET['search'] ?? '';
if ($searchTerm) {
    $stmt = $pdo->prepare("SELECT * FROM supplements WHERE name ILIKE ? ORDER BY id ASC");
    $stmt->execute(['%' . $searchTerm . '%']);
} else {
    $stmt = $pdo->query("SELECT * FROM supplements ORDER BY id ASC");
}
$supplements = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Manage Supplements</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
</head>
<body class="min-h-screen bg-orange-50 bg-gray-100 text-gray-800 font-sans">


  <!-- Navbar -->
  <header class="bg-white shadow fixed w-full top-0 z-50">
    <div class="max-w-7xl mx-auto px-6 py-4 flex justify-between items-center">
      <div class="text-2xl font-bold text-orange-500 flex items-center gap-2">
        <i class="fa-solid fa-dumbbell"></i> GYM Core Admin
      </div>
      <div class="flex items-center gap-4">
        <span class="text-sm font-medium">Welcome, <?php echo htmlspecialchars($_SESSION['username'] ?? 'Admin'); ?></span>
        <a href="logout.php" class="text-red-500 hover:text-red-700 font-bold flex items-center gap-1">
          <i class="fa-solid fa-right-from-bracket"></i> Logout
        </a>
      </div>
    </div>
  </header>

  <div class="flex pt-16 min-h-screen">

    <!-- Sidebar -->
    <aside class="w-64 bg-gray-800 text-white p-6 space-y-6 sticky top-16 h-screen">
      <nav class="space-y-4">
        <a href="admin_dashboard.php#users" class="block px-4 py-2 rounded hover:bg-orange-500 transition text-gray-300 hover:text-white flex items-center gap-2">
          <i class="fa-solid fa-users"></i> Manage Users
        </a>
        <a href="manage_supplements.php" class="block px-4 py-2 rounded bg-orange-500 text-white flex items-center gap-2" aria-current="page">
          <i class="fa-solid fa-capsules"></i> Manage Supplements
        </a>
        <a href="manage_memberships.php" class="block px-4 py-2 rounded hover:bg-gray-700 transition text-gray-300 hover:text-white flex items-center gap-2">
          <i class="fa-solid fa-id-card"></i> Manage Memberships
        </a>
        <a href="manage_orders.php" class="block px-4 py-2 rounded hover:bg-gray-700 transition text-gray-300 hover:text-white flex items-center gap-2">
          <i class="fa-solid fa-cart-shopping"></i> Manage Orders
        </a>
        <a href="manage_payments.php" class="block px-4 py-2 rounded hover:bg-gray-700 transition text-gray-300 hover:text-white flex items-center gap-2">
          <i class="fa-solid fa-credit-card"></i> Manage Payments
        </a>
        <a href="manage_delivery.php" class="block px-4 py-2 rounded hover:bg-gray-700 transition text-gray-300 hover:text-white flex items-center gap-2">
          <i class="fa-solid fa-truck"></i> Delivery Details
        </a>
      </nav>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 max-w-6xl mx-auto p-6 space-y-6">

      <!-- Page Header -->
      <h1 class="text-3xl font-bold text-orange-500 flex items-center gap-2">
        <i class="fa-solid fa-capsules"></i> Manage Supplements
      </h1>

      <!-- Search Form -->
      <form method="GET" class="flex gap-3 max-w-md mb-6">
        <input
          type="text"
          name="search"
          placeholder="Search supplement name..."
          value="<?php echo htmlspecialchars($searchTerm); ?>"
          class="flex-grow p-3 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-orange-400"
        />
        <button type="submit" class="bg-orange-500 text-white px-5 py-2 rounded hover:bg-orange-600 transition">
          Search
        </button>
      </form>

      <!-- Add/Edit Supplement Form -->
      <form action="" method="POST" enctype="multipart/form-data" class="bg-white p-6 rounded shadow max-w-6xl grid grid-cols-1 md:grid-cols-5 gap-4">
        <input type="hidden" name="supplement_id" id="supplement_id" />
        <input type="hidden" name="existing_image" id="existing_image" />

        <input
          type="text"
          name="name"
          id="name"
          placeholder="Supplement Name"
          required
          class="p-3 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-orange-400"
        />
        <input
          type="number"
          name="stock"
          id="stock"
          placeholder="Stock"
          required
          class="p-3 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-orange-400"
        />
        <input
          type="number"
          step="0.01"
          name="price"
          id="price"
          placeholder="Price"
          required
          class="p-3 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-orange-400"
        />
        <input
          type="file"
          name="image"
          id="image"
          accept="image/*"
          class="p-3 border border-gray-300 rounded"
        />
        <button
          type="submit"
          name="save_supplement"
          class="bg-orange-500 text-white font-semibold rounded hover:bg-orange-600 transition md:col-span-1 col-span-full"
        >
          Save Supplement
        </button>
      </form>

      <!-- Supplement Table -->
      <div class="overflow-x-auto bg-white rounded shadow max-w-6xl">
        <table class="min-w-full table-auto border-collapse">
          <thead>
            <tr class="bg-orange-100 text-orange-700">
              <th class="text-left px-6 py-3">ID</th>
              <th class="text-left px-6 py-3">Image</th>
              <th class="text-left px-6 py-3">Name</th>
              <th class="text-left px-6 py-3">Stock</th>
              <th class="text-left px-6 py-3">Price</th>
              <th class="text-left px-6 py-3">Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($supplements as $supp): ?>
              <tr class="border-b hover:bg-orange-50">
                <td class="px-6 py-3"><?php echo htmlspecialchars($supp['id']); ?></td>
                <td class="px-6 py-3">
                  <?php if (!empty($supp['image_path'])): ?>
                    <img src="<?php echo htmlspecialchars($supp['image_path']); ?>" alt="Supplement Image" class="w-16 h-16 object-cover rounded" />
                  <?php else: ?>
                    <span class="text-gray-400 italic">No image</span>
                  <?php endif; ?>
                </td>
                <td class="px-6 py-3"><?php echo htmlspecialchars($supp['name']); ?></td>
                <td class="px-6 py-3"><?php echo htmlspecialchars($supp['stock']); ?></td>
                <td class="px-6 py-3 text-orange-600 font-semibold">
                  <?php echo "Rs. " . number_format((float)$supp['price'], 0, '', ''); ?>
                </td>
                <td class="px-6 py-3 flex gap-4">
                  <button onclick='editSupplement(<?php echo json_encode($supp); ?>)' class="bg-yellow-400 hover:bg-yellow-500 text-white px-3 py-1 rounded">
                    Edit
                  </button>
                  <a href="?delete_id=<?php echo $supp['id']; ?>" onclick="return confirm('Delete this supplement?')" class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded">
                    Delete
                  </a>
                </td>
              </tr>
            <?php endforeach; ?>
            <?php if (count($supplements) === 0): ?>
              <tr>
                <td colspan="6" class="text-center py-6 italic text-gray-500">No supplements found.</td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>

    </main>
  </div>

  <script>
    function editSupplement(supp) {
      document.getElementById('supplement_id').value = supp.id;
      document.getElementById('name').value = supp.name;
      document.getElementById('stock').value = supp.stock;
      document.getElementById('price').value = supp.price;
      document.getElementById('existing_image').value = supp.image_path || '';
      window.scrollTo({ top: 0, behavior: 'smooth' });
    }
  </script>
</body>
</html>
