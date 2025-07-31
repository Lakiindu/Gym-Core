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
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Manage Supplements</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-6">

  <!-- Back Button -->
  <div class="mb-4">
    <a href="admin_dashboard.php" class="bg-gray-700 text-white px-4 py-2 rounded hover:bg-gray-800">&larr; Back to Admin Dashboard</a>
  </div>

  <section class="bg-white p-6 rounded-lg shadow animate-fadeIn">
    <h2 class="text-2xl font-semibold mb-4 text-orange-500">Supplement Management</h2>

    <!-- Search Form -->
    <form method="GET" class="mb-4">
      <input type="text" name="search" placeholder="Search supplement name..." value="<?php echo htmlspecialchars($searchTerm); ?>" class="p-2 border rounded w-1/2" />
      <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Search</button>
    </form>

    <!-- Add/Edit Supplement Form -->
    <form action="" method="POST" enctype="multipart/form-data" class="mb-6 grid grid-cols-1 md:grid-cols-5 gap-4">
      <input type="hidden" name="supplement_id" id="supplement_id" />
      <input type="text" name="name" id="name" required placeholder="Supplement Name" class="p-2 border rounded" />
      <input type="number" name="stock" id="stock" required placeholder="Stock" class="p-2 border rounded" />
      <input type="number" step="0.01" name="price" id="price" required placeholder="Price" class="p-2 border rounded" />
      <input type="file" name="image" id="image" accept="image/*" class="p-2 border rounded" />
      <input type="hidden" name="existing_image" id="existing_image" />
      <button type="submit" name="save_supplement" class="bg-orange-500 text-white px-4 py-2 rounded hover:bg-orange-600 col-span-1 md:col-span-5">Save Supplement</button>
    </form>

    <!-- Supplement Table -->
    <div class="overflow-x-auto">
      <table class="min-w-full border">
        <thead class="bg-gray-200">
          <tr>
            <th class="py-2 px-4 text-left">ID</th>
            <th class="py-2 px-4 text-left">Image</th>
            <th class="py-2 px-4 text-left">Name</th>
            <th class="py-2 px-4 text-left">Stock</th>
            <th class="py-2 px-4 text-left">Price</th>
            <th class="py-2 px-4 text-left">Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($supplements as $supp): ?>
            <tr class="border-t hover:bg-gray-50">
              <td class="py-2 px-4"><?php echo htmlspecialchars($supp['id']); ?></td>
              <td class="py-2 px-4">
                <?php if (!empty($supp['image_path'])): ?>
                  <img src="<?php echo htmlspecialchars($supp['image_path']); ?>" alt="Supplement Image" class="w-16 h-16 object-cover rounded" />
                <?php else: ?>
                  <span class="text-gray-400 italic">No image</span>
                <?php endif; ?>
              </td>
              <td class="py-2 px-4"><?php echo htmlspecialchars($supp['name']); ?></td>
              <td class="py-2 px-4"><?php echo htmlspecialchars($supp['stock']); ?></td>
              <td class="py-2 px-4">$<?php echo htmlspecialchars($supp['price']); ?></td>
              <td class="py-2 px-4 space-x-2">
                <button onclick='editSupplement(<?php echo json_encode($supp); ?>)' class="bg-yellow-400 text-white px-3 py-1 rounded hover:bg-yellow-500">Edit</button>
                <a href="?delete_id=<?php echo $supp['id']; ?>" onclick="return confirm('Delete this supplement?')" class="bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600">Delete</a>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </section>

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
