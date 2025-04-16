<?php
require_once '../../db.php';
session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: ../../login.php");
    exit;
}

if (!isset($_GET['id'])) {
    echo "No product selected.";
    exit;
}

$product_id = $_GET['id'];
$stmt = $conn->prepare("
    SELECT p.*, c.category_name, c.subcategory_name 
    FROM products p 
    LEFT JOIN categories c ON p.category_id = c.id 
    WHERE p.id = ?
");

$stmt->execute([$product_id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
    echo "Product not found.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Product - <?= htmlspecialchars($product['name']) ?></title>
    <link rel="stylesheet" href="../../styles.css">
    <style>
        .product-view {
            max-width: 800px;
            margin: 40px auto;
            padding: 20px;
            background: #f9f9f9;
            border-radius: 10px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
        }

        .product-view img {
            max-width: 100%;
            height: auto;
            border-radius: 10px;
            margin-bottom: 20px;
        }

        .product-details {
            font-size: 16px;
        }

        .product-details h2 {
            margin-bottom: 10px;
        }

        .product-details p {
            margin: 8px 0;
        }

        .back-btn {
            display: inline-block;
            margin-top: 20px;
            padding: 8px 16px;
            background: #3498db;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }

        .back-btn:hover {
            background: #2980b9;
        }
    </style>
</head>
<body>

<div class="product-view">
    <img src="<?= htmlspecialchars($product['image_url']) ?>" alt="<?= htmlspecialchars($product['name']) ?>">

    <div class="product-details">
        <h2><?= htmlspecialchars($product['name']) ?></h2>
        <p><strong>Description:</strong><br><?= nl2br(htmlspecialchars($product['description'])) ?></p>
        <p><strong>Price:</strong> KES <?= number_format($product['price'], 2) ?></p>
        <p><strong>Category:</strong> <?= htmlspecialchars($product['category_name']) ?></p>
        <p><strong>Created At:</strong> <?= $product['created_at'] ?></p>
        <p><strong>Last Updated:</strong> <?= $product['updated_at'] ?></p>
    </div>

    <a href="index.php" class="back-btn">← Back to Products</a>
</div>

</body>
</html>
