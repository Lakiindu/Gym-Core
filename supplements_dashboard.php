<?php
session_start();

// Database connection (PostgreSQL with PDO)
$dsn = "pgsql:host=localhost;port=5432;dbname=gym_db;";
$db_user = "postgres";
$db_pass = "lakindu";

try {
    $pdo = new PDO($dsn, $db_user, $db_pass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Fetch supplements
$stmt = $pdo->prepare("SELECT id, name, stock, price, image_path FROM supplements ORDER BY id ASC");
$stmt->execute();
$supplements = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Handle Add to Cart POST request and insert into 'cart' table
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['supplement_id'], $_POST['quantity'])) {
    $supplement_id = (int)$_POST['supplement_id'];
    $quantity = (int)$_POST['quantity'];
    $user_id = $_SESSION['user_id'] ?? 1; // Replace 1 with actual session-based user_id

    if ($quantity > 0) {
        // Check available stock
        $stockCheck = $pdo->prepare("SELECT stock FROM supplements WHERE id = :id");
        $stockCheck->execute(['id' => $supplement_id]);
        $stockRow = $stockCheck->fetch(PDO::FETCH_ASSOC);

        if ($stockRow && $stockRow['stock'] >= $quantity) {
            // Insert into cart
            $insertCart = $pdo->prepare("INSERT INTO cart (user_id, supplement_id, quantity, added_at) VALUES (:user_id, :supplement_id, :quantity, NOW())");
            $insertCart->execute([
                'user_id' => $user_id,
                'supplement_id' => $supplement_id,
                'quantity' => $quantity
            ]);

            $message = "Added $quantity item(s) to cart.";
        } else {
            $error = "Not enough stock available.";
        }
    } else {
        $error = "Invalid quantity.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Supplements Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-900 text-white min-h-screen">
    <header class="bg-gray-800 p-4 flex justify-between">
        <h1 class="text-2xl font-bold text-orange-500">GYM Core Supplements</h1>
        <a href="cart.php" class="text-white bg-orange-500 px-4 py-2 rounded hover:bg-orange-600">Cart</a>
    </header>

    <main class="p-6 container mx-auto">
        <?php if (!empty($message)) : ?>
            <div class="bg-green-600 p-4 rounded mb-4"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>

        <?php if (!empty($error)) : ?>
            <div class="bg-red-600 p-4 rounded mb-4"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
            <?php foreach ($supplements as $supp): ?>
                <div class="bg-gray-800 p-4 rounded shadow flex flex-col">
                    <img src="<?= htmlspecialchars($supp['image_path']) ?>" alt="<?= htmlspecialchars($supp['name']) ?>" class="h-48 w-full object-cover rounded">
                    <h2 class="mt-3 text-xl font-bold"><?= htmlspecialchars($supp['name']) ?></h2>
                    <p class="mt-1">Price: <span class="text-orange-400 font-semibold">LKR <?= number_format($supp['price'], 2) ?></span></p>
                    <p class="mt-1">Stock: 
                        <?php
                        if ($supp['stock'] == 0) {
                            echo '<span class="text-red-500 font-bold">Out of Stock</span>';
                        } elseif ($supp['stock'] <= 5) {
                            echo '<span class="text-yellow-400 font-bold">Only ' . $supp['stock'] . ' left!</span>';
                        } else {
                            echo '<span class="text-green-400">' . $supp['stock'] . '</span>';
                        }
                        ?>
                    </p>

                    <?php if ($supp['stock'] > 0): ?>
                        <form method="POST" class="mt-auto">
                            <input type="hidden" name="supplement_id" value="<?= $supp['id'] ?>">
                            <label for="quantity_<?= $supp['id'] ?>" class="block text-sm mt-2">Quantity:</label>
                            <input
                                type="number"
                                id="quantity_<?= $supp['id'] ?>"
                                name="quantity"
                                min="1"
                                max="<?= $supp['stock'] ?>"
                                value="1"
                                class="w-full p-2 rounded bg-gray-700 border border-gray-600 text-white"
                                required
                            >
                            <button type="submit" class="w-full mt-3 bg-orange-500 hover:bg-orange-600 text-white py-2 rounded font-semibold">Add to Cart</button>
                        </form>
                    <?php else: ?>
                        <p class="mt-4 text-red-400 font-semibold">Currently unavailable.</p>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    </main>

    <footer class="text-center p-4 text-gray-400 bg-gray-800 mt-auto">
        &copy; <?= date('Y') ?> GYM Core. All rights reserved.
    </footer>
</body>
</html>
