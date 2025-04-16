<?php
require_once '../../db.php';
session_start();

// Redirect if not logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../../login.php");
    exit;
}

// Fetch all products
$stmt = $conn->prepare("SELECT * FROM products ORDER BY created_at DESC");
$stmt->execute();
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Products - Planet Victoria</title>
    <link rel="stylesheet" href="../../styles.css">
    <style>
        .products-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .products-table th, .products-table td {
            padding: 10px;
            text-align: left;
            border: 1px solid #ccc;
        }

        .products-table th {
            background-color: #3498db;
            color: white;
        }

        .products-table tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .products-table tr:hover {
            background-color: #f1f1f1;
        }

        .actions button {
            margin-right: 5px;
        }

        .actions {
            display: flex;
            gap: 5px;
            flex-wrap: nowrap;
        }
        .actions a button {
            white-space: nowrap;
        }

        .top-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .create-btn {
            background-color: #2ecc71;
            color: white;
            padding: 8px 15px;
            border: none;
            border-radius: 4px;
            text-decoration: none;
            font-weight: bold;
        }

        .create-btn:hover {
            background-color: #27ae60;
        }
    </style>
</head>
<body>
<div class="container">
    <!-- Header and Sidebar (same as before) -->
    <header class="header">
            <div class="logo">🌍 Planet Victoria</div>
            <nav class="top-nav">
                <button class="nav-btn">Home</button>
                <a href="../profile.php" class="profile-button">Profile</a>
                <button class="nav-btn"> <a href="../../logout.php">Logout</a></button>
            </nav>
        </header>

        <!-- Sidebar with Toggle -->
        <div class="sidebar" id="sidebar">
            <ul class="menu-items">
            <ul class="menu-items">
    <li><a href="../index.php" >Dashboard</a></li>
    <li><a href="../index.php" >Groups</a></li>
    <li><a href="../clients/index.php">Clients</a></li>
    <li><a href="../deposits/index.php">Deposit</a></li>
    <li><a href="../loans/index.php">Loans</a></li>
    <li><a href="../orders/index.php">Orders</a></li>
    <li><a href="../users/index.php">Users</a></li>
    <li><a href="../reports/index.php">Reports</a></li>
    <li><a href=".index.php" class="active">Products</a></li>
    <li><a href="../categories/index.php">Categories</a></li>
</ul>

            </ul>
        </div>

    <main class="content">
        <div class="top-bar">
            <h2>All Products</h2>
            <a href="create.php" class="create-btn">+ Create New Product</a>
        </div>

        <?php if (count($products) > 0): ?>
            <table class="products-table">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Description</th>
                    <th>Price (KES)</th>
                    <th>Image</th>
                    <th>Category</th>
                    <th>Created At</th>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($products as $product): ?>
                    <tr>
                        <td><?= htmlspecialchars($product['id']) ?></td>
                        <td><?= htmlspecialchars($product['name']) ?></td>
                        <td><?= htmlspecialchars($product['description']) ?></td>
                        <td><?= number_format($product['price'], 2) ?></td>
                        <td>
                            <?php if (!empty($product['image_url'])): ?>
                                <img src="<?= htmlspecialchars($product['image_url']) ?>" alt="Product" width="50">
                            <?php else: ?>
                                No image
                            <?php endif; ?>
                        </td>
                        <td><?= htmlspecialchars($product['category_id']) ?></td>
                        <td><?= htmlspecialchars($product['created_at']) ?></td>
                        <td class="actions">
                            <a href="view.php?id=<?= $product['id'] ?>"><button>View</button></a>
                            <a href="update.php?id=<?= $product['id'] ?>"><button>Update</button></a>
                            <a href="delete.php?id=<?= $product['id'] ?>" onclick="return confirm('Are you sure you want to delete this product?')">
                                <button style="background-color: crimson; color: white;">Delete</button>
                            </a>
                        </td>

                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No products available.</p>
        <?php endif; ?>
    </main>
</div>
<footer class="footer">
        <p>© 2025 Planet Victoria. All rights reserved.</p>
    </footer>
</body>
</html>
