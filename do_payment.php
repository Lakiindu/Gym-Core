<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$host = "localhost";
$dbname = "gym_db";
$user = "postgres";
$password = "lakindu";

try {
    $pdo = new PDO("pgsql:host=$host;dbname=$dbname", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("DB connection failed: " . $e->getMessage());
}

$user_id = $_SESSION['user_id'];

// Fetch username for orders
$userStmt = $pdo->prepare("SELECT username FROM users WHERE id = :user_id");
$userStmt->execute(['user_id' => $user_id]);
$userData = $userStmt->fetch(PDO::FETCH_ASSOC);
$customer_name = $userData ? $userData['username'] : 'Unknown Customer';

// Mock payment gateway function (pretends to charge card)
function processPaymentGateway($user_id, $amount, $payment_method, $cardData = []) {
    // Normally you'd send $cardData to payment API
    sleep(1);
    if (random_int(1, 100) <= 80) {
        return [
            'success' => true,
            'transaction_id' => uniqid('tx_', true),
            'message' => 'Payment approved',
        ];
    } else {
        return [
            'success' => false,
            'message' => 'Payment declined by gateway',
        ];
    }
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $payment_method = $_POST['payment_method'] ?? '';
    $delivery_option = $_POST['delivery_option'] ?? 'Visit Gym';
    $delivery_address = trim($_POST['delivery_address'] ?? '');

    // Card details from modal
    $card_number = preg_replace('/\s+/', '', $_POST['card_number'] ?? '');
    $card_expiry = $_POST['card_expiry'] ?? '';
    $card_cvv = $_POST['card_cvv'] ?? '';

    // Get cart items
    $cartStmt = $pdo->prepare("SELECT supplement_id, quantity FROM cart WHERE user_id = :user_id");
    $cartStmt->execute(['user_id' => $user_id]);
    $cartItems = $cartStmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($cartItems)) {
        $_SESSION['error'] = "Your cart is empty. Please add supplements before making a payment.";
    } elseif (!$payment_method) {
        $_SESSION['error'] = "Please select a payment method.";
    } elseif ($delivery_option === 'Delivery' && empty($delivery_address)) {
        $_SESSION['error'] = "Please enter a delivery address.";
    } elseif ($payment_method === 'Credit Card') {
        // Validate card details server-side
        if (strlen($card_number) < 13 || strlen($card_number) > 19 || !ctype_digit($card_number)) {
            $_SESSION['error'] = "Invalid card number.";
        } elseif (!preg_match('/^\d{2}\/\d{2}$/', $card_expiry)) {
            $_SESSION['error'] = "Invalid expiry date format. Use MM/YY.";
        } elseif (!preg_match('/^\d{3,4}$/', $card_cvv)) {
            $_SESSION['error'] = "Invalid CVV.";
        }
    }

    if (!empty($_SESSION['error'])) {
        // do nothing, error will be shown below
    } else {
        try {
            // Calculate total amount
            $totalAmount = 0;
            foreach ($cartItems as $item) {
                $supplementStmt = $pdo->prepare("SELECT price FROM supplements WHERE id = :id");
                $supplementStmt->execute(['id' => $item['supplement_id']]);
                $supplement = $supplementStmt->fetch(PDO::FETCH_ASSOC);
                if ($supplement) {
                    $totalAmount += $supplement['price'] * $item['quantity'];
                }
            }

            // Process payment via mock gateway
            $cardData = [
                'number' => $card_number,
                'expiry' => $card_expiry,
                'cvv' => $card_cvv
            ];
            $paymentResult = processPaymentGateway($user_id, $totalAmount, $payment_method, $cardData);

            if (!$paymentResult['success']) {
                throw new Exception("Payment failed: " . $paymentResult['message']);
            }

            $pdo->beginTransaction();

            // Insert payment record with membership_id NULL
            $stmt = $pdo->prepare("
                INSERT INTO payments (user_id, membership_id, amount, payment_method, status, paid_at)
                VALUES (:user_id, NULL, :amount, :payment_method, 'Paid', NOW())
                RETURNING id
            ");
            $stmt->execute([
                ':user_id' => $user_id,
                ':amount' => $totalAmount,
                ':payment_method' => $payment_method
            ]);
            $payment_id = $stmt->fetchColumn();

            // Prepare statements for orders, stock update, deliveries
            $insertOrderStmt = $pdo->prepare("
                INSERT INTO orders (customer_name, description, quantity, price, status, user_id)
                VALUES (:customer_name, :description, :quantity, :price, 'pending', :user_id)
                RETURNING id
            ");
            $updateStockStmt = $pdo->prepare("UPDATE supplements SET stock = stock - :qty WHERE id = :supplement_id");
            $insertDeliveryStmt = $pdo->prepare("
                INSERT INTO deliveries (order_id, delivery_address, delivery_status, delivery_date)
                VALUES (:order_id, :delivery_address, 'In Transit', NOW())
            ");

            foreach ($cartItems as $item) {
                $supplementStmt = $pdo->prepare("SELECT name, price FROM supplements WHERE id = :id");
                $supplementStmt->execute(['id' => $item['supplement_id']]);
                $supplement = $supplementStmt->fetch(PDO::FETCH_ASSOC);

                if (!$supplement) continue;

                $totalPrice = $supplement['price'] * $item['quantity'];

                // Update stock
                $updateStockStmt->execute([
                    ':qty' => $item['quantity'],
                    ':supplement_id' => $item['supplement_id']
                ]);

                // Insert order
                $insertOrderStmt->execute([
                    ':customer_name' => $customer_name,
                    ':description' => $supplement['name'],
                    ':quantity' => $item['quantity'],
                    ':price' => $totalPrice,
                    ':user_id' => $user_id
                ]);
                $order_id = $insertOrderStmt->fetchColumn();

                // Insert delivery if needed
                if ($delivery_option === 'Delivery' && !empty($delivery_address)) {
                    $insertDeliveryStmt->execute([
                        ':order_id' => $order_id,
                        ':delivery_address' => $delivery_address
                    ]);
                }
            }

            // Clear cart
            $clearCartStmt = $pdo->prepare("DELETE FROM cart WHERE user_id = :user_id");
            $clearCartStmt->execute(['user_id' => $user_id]);

            $pdo->commit();

            $_SESSION['message'] = "Payment successful! Your orders have been placed.";
            header("Location: user_dashboard.php");  // <-- Redirect changed here
            exit;

        } catch (Exception $e) {
            $pdo->rollBack();
            $_SESSION['error'] = $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Do Payment - GYM Core</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    /* Modal background */
    #mockGatewayModal {
      display: none;
      position: fixed;
      z-index: 50;
      left: 0; top: 0;
      width: 100%; height: 100%;
      background: rgba(0,0,0,0.7);
      backdrop-filter: blur(3px);
      justify-content: center;
      align-items: center;
    }
    /* Modal content */
    #mockGatewayModal .modal-content {
      background: #1f2937;
      padding: 2rem;
      border-radius: 0.5rem;
      max-width: 400px;
      width: 90%;
      color: white;
      box-shadow: 0 0 15px #f97316;
    }
    #mockGatewayModal h2 {
      text-align: center;
      margin-bottom: 1rem;
    }
    #mockGatewayModal label {
      display: block;
      margin-top: 1rem;
      font-weight: 600;
    }
    #mockGatewayModal input {
      width: 100%;
      padding: 0.5rem;
      margin-top: 0.25rem;
      border-radius: 0.375rem;
      border: none;
      font-size: 1rem;
      color: black;       /* typed text color */
      background-color: #f9fafb; /* light input background */
    }
    #mockGatewayModal .buttons {
      margin-top: 1.5rem;
      display: flex;
      justify-content: center;
      gap: 1rem;
    }
    #mockGatewayModal button {
      padding: 0.5rem 1.5rem;
      border-radius: 0.375rem;
      border: none;
      font-weight: bold;
      cursor: pointer;
      font-size: 1rem;
    }
    #mockGatewayModal button.confirm {
      background-color: #f97316;
      color: white;
    }
    #mockGatewayModal button.cancel {
      background-color: #374151;
      color: #d1d5db;
    }
    /* Error text inside modal */
    #modalError {
      color: #f87171;
      font-weight: 600;
      margin-top: 0.5rem;
      text-align: center;
      min-height: 1.25rem;
    }
  </style>
  <script>
    function toggleAddressInput() {
      const delivery = document.getElementById('delivery');
      const addressInput = document.getElementById('addressDiv');
      addressInput.style.display = delivery.checked ? 'block' : 'none';
    }

    function openMockGateway(e) {
      e.preventDefault();

      // Validate payment method and delivery address client-side before showing modal
      const paymentMethods = document.getElementsByName('payment_method');
      let paymentMethodSelected = false;
      let selectedPaymentMethod = null;
      for (const pm of paymentMethods) {
        if (pm.checked) {
          paymentMethodSelected = true;
          selectedPaymentMethod = pm.value;
          break;
        }
      }
      if (!paymentMethodSelected) {
        alert("Please select a payment method.");
        return;
      }

      const deliveryDelivery = document.querySelector('input[name="delivery_option"][value="Delivery"]');
      const addressInput = document.querySelector('input[name="delivery_address"]');

      if (deliveryDelivery.checked && !addressInput.value.trim()) {
        alert("Please enter a delivery address.");
        return;
      }

      // Reset modal errors and inputs if opening fresh
      document.getElementById('modalError').textContent = '';
      if(selectedPaymentMethod !== 'Credit Card') {
        document.getElementById('cardDetailsSection').style.display = 'none';
      } else {
        document.getElementById('cardDetailsSection').style.display = 'block';
      }

      // Clear previous inputs
      document.getElementById('modal_card_number').value = '';
      document.getElementById('modal_card_expiry').value = '';
      document.getElementById('modal_card_cvv').value = '';

      document.getElementById('mockGatewayModal').style.display = 'flex';
    }

    function validateCardDetails() {
      const cardNumber = document.getElementById('modal_card_number').value.replace(/\s+/g, '');
      const cardExpiry = document.getElementById('modal_card_expiry').value.trim();
      const cardCVV = document.getElementById('modal_card_cvv').value.trim();

      if (!/^\d{13,19}$/.test(cardNumber)) {
        return "Card number must be 13 to 19 digits.";
      }
      if (!/^\d{2}\/\d{2}$/.test(cardExpiry)) {
        return "Expiry date must be in MM/YY format.";
      }
      if (!/^\d{3,4}$/.test(cardCVV)) {
        return "CVV must be 3 or 4 digits.";
      }

      return ""; // no error
    }

    function transferCardDataAndConfirm() {
      const cardNumberInput = document.getElementById('modal_card_number');
      const cardExpiryInput = document.getElementById('modal_card_expiry');
      const cardCvvInput = document.getElementById('modal_card_cvv');
      const modalError = document.getElementById('modalError');

      const cardNumber = cardNumberInput.value.replace(/\s+/g, '');
      const cardExpiry = cardExpiryInput.value.trim();
      const cardCVV = cardCvvInput.value.trim();

      const errorText = validateCardDetails();
      if (errorText) {
        modalError.textContent = errorText;
        return;
      }

      // Copy modal inputs to hidden form inputs
      document.getElementById('card_number').value = cardNumber;
      document.getElementById('card_expiry').value = cardExpiry;
      document.getElementById('card_cvv').value = cardCVV;

      // Close modal and submit form
      document.getElementById('mockGatewayModal').style.display = 'none';
      document.getElementById('paymentForm').submit();
    }

    function cancelPayment() {
      document.getElementById('mockGatewayModal').style.display = 'none';
    }
  </script>
</head>
<body class="bg-gray-900 text-white min-h-screen flex flex-col">
  <header class="bg-gray-800 p-4 flex justify-between items-center">
    <h1 class="text-xl font-bold text-orange-500">💳 Make Payment</h1>
    <a href="user_dashboard.php" class="bg-orange-600 hover:bg-orange-700 px-4 py-2 rounded">← Back</a>
  </header>

  <main class="flex-grow container mx-auto p-6">
    <?php if (!empty($_SESSION['message'])): ?>
      <div class="bg-green-600 p-4 mb-4 rounded">
        <?= htmlspecialchars($_SESSION['message']) ?>
      </div>
      <?php unset($_SESSION['message']); ?>
    <?php elseif (!empty($_SESSION['error'])): ?>
      <div class="bg-red-600 p-4 mb-4 rounded">
        <?= htmlspecialchars($_SESSION['error']) ?>
      </div>
      <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <div class="bg-gray-800 p-6 rounded shadow-lg max-w-xl mx-auto">
      <form method="POST" id="paymentForm" novalidate>
        <div class="mb-4">
          <label class="block mb-2 font-semibold">Payment Method</label>
          <div class="flex gap-4">
            <?php foreach (['Credit Card', 'PayPal', 'Cash'] as $method): ?>
              <label class="flex items-center">
                <input type="radio" name="payment_method" value="<?= $method ?>" required class="mr-2" />
                <?= $method ?>
              </label>
            <?php endforeach; ?>
          </div>
        </div>

        <div class="mb-4">
          <label class="block mb-2 font-semibold">How would you like to receive your supplements?</label>
          <div class="flex gap-4">
            <label class="flex items-center">
              <input type="radio" name="delivery_option" value="Visit Gym" onclick="toggleAddressInput()" checked class="mr-2" />
              Visit Gym
            </label>
            <label class="flex items-center">
              <input type="radio" id="delivery" name="delivery_option" value="Delivery" onclick="toggleAddressInput()" class="mr-2" />
              Delivery
            </label>
          </div>
        </div>

        <div class="mb-4 hidden" id="addressDiv">
          <label class="block mb-2 font-semibold">Delivery Address</label>
          <input type="text" name="delivery_address" class="w-full px-3 py-2 rounded text-black" placeholder="Enter your address" />
        </div>

        <button onclick="openMockGateway(event)" class="w-full bg-orange-600 hover:bg-orange-700 p-3 rounded font-bold text-white" type="button">
          Pay Now
        </button>

        <!-- Hidden inputs for card details will be filled by modal -->
        <input type="hidden" id="card_number" name="card_number" />
        <input type="hidden" id="card_expiry" name="card_expiry" />
        <input type="hidden" id="card_cvv" name="card_cvv" />
      </form>
    </div>
  </main>

  <footer class="bg-gray-800 p-4 text-center text-gray-400">
    &copy; <?= date('Y') ?> GYM Core | Secure Payment Portal
  </footer>

  <!-- Mock Payment Gateway Modal -->
  <div id="mockGatewayModal" class="flex">
    <div class="modal-content">
      <h2>Mock Payment Gateway</h2>
      <p>Please enter your card details to proceed.</p>

      <div id="cardDetailsSection">
        <label for="modal_card_number">Card Number</label>
        <input id="modal_card_number" type="text" maxlength="19" placeholder="1234 5678 9012 3456" autocomplete="off" />

        <label for="modal_card_expiry">Expiry Date (MM/YY)</label>
        <input id="modal_card_expiry" type="text" maxlength="5" placeholder="MM/YY" autocomplete="off" />

        <label for="modal_card_cvv">CVV</label>
        <input id="modal_card_cvv" type="password" maxlength="4" placeholder="123" autocomplete="off" />
      </div>

      <div id="modalError"></div>

      <div class="buttons">
        <button class="confirm" onclick="transferCardDataAndConfirm()">Confirm Payment</button>
        <button class="cancel" onclick="cancelPayment()">Cancel</button>
      </div>
    </div>
  </div>

</body>
</html>
