<?php
// DB connection
$host = 'localhost';
$db = 'planet_victoria';
$user = 'root';
$pass = '';

$pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
session_start();
$officerId = $_SESSION['officer_id'] ?? null;

$cartCount = 0;

if ($officerId) {
    try {
        $stmt = $pdo->prepare("SELECT SUM(quantity) as total_items FROM cart WHERE customer_id = :officerId");
        $stmt->execute(['officerId' => $officerId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $cartCount = $result['total_items'] ?? 0;
    } catch (PDOException $e) {
        // Log or handle error if needed
        $cartCount = 0;
    }
}

// Fetch categories
$selectedCategory = isset($_GET['category']) ? $_GET['category'] : null;
$searchTerm = isset($_GET['search']) ? trim($_GET['search']) : null;

// Fetch all categories for the dropdown
$categoryQuery = "SELECT DISTINCT category_name FROM categories";
$categoryStmt = $pdo->query($categoryQuery);
$categoryList = $categoryStmt->fetchAll(PDO::FETCH_COLUMN);

// Fetch products based on category and search term
$query = "
    SELECT p.*, c.category_name
    FROM products p
    JOIN categories c ON p.category_id = c.id
    WHERE 1=1
";

$params = [];

if ($selectedCategory) {
    $query .= " AND c.category_name = :category";
    $params['category'] = $selectedCategory;
}

if ($searchTerm) {
    $query .= " AND (p.name LIKE :search OR p.description LIKE :search)";
    $params['search'] = "%" . $searchTerm . "%";
}

$query .= " ORDER BY p.created_at DESC";
$productStmt = $pdo->prepare($query);
$productStmt->execute($params);
$products = $productStmt->fetchAll(PDO::FETCH_ASSOC);

// Add item to cart functionality
if (isset($_POST['add_to_cart'])) {
    $productId = $_POST['product_id'];
    $officerId = 1; // Replace with actual officer ID, maybe from session

    // Add to cart query
    $stmt = $pdo->prepare("INSERT INTO cart (customer_id, product_id, quantity, added_at) VALUES (:customer_id, :product_id, :quantity, NOW())");
    $stmt->execute([
      'customer_id' => $officerId,
      'product_id' => $productId,
      'quantity' => 1
    ]);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Planet Victoria - Products</title>
  <link rel="stylesheet" href="../styles.css">
  <style>
    .category-dropdown {
      background: white;
      padding: 6px 10px;
      border-radius: 4px;
      font-size: 14px;
    }

    .product-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
      gap: 20px;
      margin-top: 30px;
    }

    .product-card {
      background: #fff;
      border-radius: 10px;
      padding: 15px;
      box-shadow: 0 3px 8px rgba(0, 0, 0, 0.08);
      text-align: center;
    }

    .product-card img {
      max-width: 100%;
      height: 120px;
      object-fit: cover;
      border-radius: 6px;
    }

    .product-name {
      font-size: 16px;
      font-weight: bold;
      margin: 10px 0 5px;
    }

    .product-price {
      color: green;
      font-weight: bold;
    }

    .add-to-cart {
      margin-top: 10px;
      padding: 5px 12px;
      background: #3498db;
      color: white;
      border: none;
      border-radius: 4px;
      cursor: pointer;
    }

    .add-to-cart:hover {
      background: #2980b9;
    }

    .cart-icon {
  font-size: 18px;
  cursor: pointer;
  color: #333;
  display: flex;
  align-items: center;
  gap: 4px;
  padding: 6px 12px;
  background: #f1f1f1;
  border-radius: 6px;
  text-decoration: none;
}
.cart-icon:hover {
  background: #ddd;
}
.cart-count {
  background-color: red;
  color: white;
  padding: 2px 6px;
  border-radius: 12px;
  font-size: 12px;
  margin-left: 4px;
}


  </style>
</head>
<body>
<div class="container">
  <header class="header">
  <div class="logo">🌍 Planet Victoria</div>
    <nav class="top-nav" style="display: flex; justify-content: space-between; align-items: center;">
  <form method="GET" action="index.php" style="display: flex; gap: 10px; align-items: center;">
    <select name="category" onchange="this.form.submit()" class="category-dropdown">
      <option value="">-- All Categories --</option>
      <?php foreach ($categoryList as $cat): ?>
        <option value="<?= htmlspecialchars($cat) ?>" <?= ($cat === $selectedCategory) ? 'selected' : '' ?>>
          <?= htmlspecialchars($cat) ?>
        </option>
      <?php endforeach; ?>
    </select>

    <input type="text" name="search" placeholder="Search products..." value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>" style="padding: 6px 10px; border-radius: 4px; border: 1px solid #ccc;">

    <button type="submit" class="nav-btn" style="padding: 6px 12px;">Search</button>
  </form>

  <!-- Cart icon inside nav bar -->
  <a href="view_cart.php" class="cart-icon">
    🛒 Cart
    <span class="cart-count"><?= $cartCount ?></span>
  </a>
</nav>

  </header>

  <div class="sidebar">
    <h3>Menu</h3>
    <ul class="menu-items">
      <li><a href="../index.php">Dashboard</a></li>
      <li><a href="../groups/index.php">Groups</a></li>
      <li><a href="../deposit/index.php">Deposit</a></li>
      <li><a href="../clients/index.php">Clients</a></li>
      <li><a href="../reports/index.php">Reports</a></li>
      <li><a href="index.php" class="active">Products</a></li>
    </ul>
  </div>

  <main class="content">
    <h2>Available Products</h2>

    <?php if ($selectedCategory): ?>
      <h3>Showing products in category: <strong><?= htmlspecialchars($selectedCategory) ?></strong></h3>
    <?php endif; ?>

    <div class="product-grid">
      <?php if (!empty($products)): ?>
        <?php foreach ($products as $product): ?>
          <div class="product-card">
            <img src="<?= htmlspecialchars($product['image_url']) ?>" alt="<?= htmlspecialchars($product['name']) ?>">
            <div class="product-name"><?= htmlspecialchars($product['name']) ?></div>
            <div class="product-price">KES <?= number_format($product['price'], 2) ?></div>

            <form method="POST" action="add_to_cart.php">
              <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
              <button type="submit" class="add-to-cart">Add to Cart</button>
            </form>

          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <p>No products found in this category.</p>
      <?php endif; ?>
    </div>
  </main>

  <footer class="footer">
    <p>© 2025 Planet Victoria. All rights reserved.</p>
  </footer>
</div>

</body>
</html>
