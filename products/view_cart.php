<?php
session_start();
$pdo = new PDO("mysql:host=localhost;dbname=planet_victoria", "root", "");
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$officerId = $_SESSION['officer_id'] ?? 1; // fallback for now

// Handle order placement
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['place_order'])) {
    $description = $_POST['description'] ?? '';

    // Calculate total
    $stmt = $pdo->prepare("
        SELECT c.quantity, p.price 
        FROM cart c 
        JOIN products p ON c.product_id = p.id 
        WHERE c.customer_id = :officerId
    ");
    $stmt->execute(['officerId' => $officerId]);
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $totalAmount = 0;
    $totalQuantity = 0;
    foreach ($items as $item) {
        $totalAmount += $item['price'] * $item['quantity'];
        $totalQuantity += $item['quantity'];
    }

    $insertOrder = $pdo->prepare("
        INSERT INTO orders (customer_id, total_amount, total_quantity, description, status, placed_at)
        VALUES (:customer_id, :total_amount, :total_quantity, :description, 'Pending', NOW())
    ");
    $insertOrder->execute([
        'customer_id' => $officerId,
        'total_amount' => $totalAmount,
        'total_quantity' => $totalQuantity,
        'description' => $description
    ]);

    // Clear cart
    $pdo->prepare("DELETE FROM cart WHERE customer_id = :officerId")->execute(['officerId' => $officerId]);
    
    header("Location: order_success.php");
    exit;
}

// Fetch cart items
$stmt = $pdo->prepare("
    SELECT c.*, p.name, p.price, p.image_url 
    FROM cart c 
    JOIN products p ON c.product_id = p.id 
    WHERE c.customer_id = :officerId
");
$stmt->execute(['officerId' => $officerId]);
$cartItems = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>View Cart - Planet Victoria</title>
  <link rel="stylesheet" href="../styles.css">
  <style>
    .cart-item {
      display: flex;
      align-items: center;
      margin-bottom: 15px;
      border-bottom: 1px solid #ddd;
      padding-bottom: 10px;
    }
    .cart-item img {
      width: 80px;
      height: 80px;
      object-fit: cover;
      border-radius: 6px;
      margin-right: 15px;
    }
    .cart-item-details {
      flex: 1;
    }
    .cart-item-actions {
      text-align: right;
    }
    .remove-btn {
      background: crimson;
      color: white;
      border: none;
      padding: 4px 10px;
      border-radius: 4px;
      cursor: pointer;
    }
    .place-order {
      margin-top: 20px;
      padding: 10px 20px;
      background: #27ae60;
      color: white;
      border: none;
      border-radius: 6px;
      cursor: pointer;
    }
    .place-order:hover {
      background: #219150;
    }
    textarea.description {
      width: 100%;
      padding: 8px 10px;
      border-radius: 6px;
      border: 1px solid #ccc;
      resize: vertical;
      margin-top: 10px;
    }
  </style>
</head>
<body>

<div class="container">
  <!-- Sidebar -->
  <div class="sidebar">
    <h3>Menu</h3>
    <ul class="menu-items">
      <li><a href="../index.php">Dashboard</a></li>
      <li><a href="../groups/index.php">Groups</a></li>
      <li><a href="../clients/index.php">Clients</a></li>
      <li><a href="../reports/index.php">Reports</a></li>
      <li><a href="index.php">Products</a></li>
    </ul>
  </div>

  <!-- Main Content -->
  <main class="content">
    <h2>🛒 Your Cart</h2>

    <?php if (count($cartItems) > 0): ?>
      <?php $total = 0; ?>
      <?php foreach ($cartItems as $item): ?>
        <div class="cart-item">
          <img src="<?= htmlspecialchars($item['image_url']) ?>" alt="<?= htmlspecialchars($item['name']) ?>">
          <div class="cart-item-details">
            <div><strong><?= htmlspecialchars($item['name']) ?></strong></div>
            <div>KES <?= number_format($item['price'], 2) ?> x <?= $item['quantity'] ?></div>
          </div>
          <div class="cart-item-actions">
            <form method="POST" action="remove_from_cart.php" style="display:inline;">
              <input type="hidden" name="cart_id" value="<?= $item['id'] ?>">
              <button class="remove-btn" type="submit">Remove</button>
            </form>
          </div>
        </div>
        <?php $total += $item['price'] * $item['quantity']; ?>
      <?php endforeach; ?>

      <h3>Total: <span style="color: green;">KES <?= number_format($total, 2) ?></span></h3>

      <form method="POST">
        <label for="description">Description (optional):</label>
        <textarea name="description" class="description" rows="3" placeholder="Order notes or delivery details..."></textarea>
        <button type="submit" name="place_order" class="place-order">Place Order</button>
      </form>

    <?php else: ?>
      <p>Your cart is empty.</p>
    <?php endif; ?>
  </main>
</div>

</body>
</html>
