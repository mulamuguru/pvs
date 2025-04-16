<?php
require_once '../../db.php';
session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: ../../login.php");
    exit;
}

if (!isset($_GET['category'])) {
    echo "Category name not specified.";
    exit;
}

$category = $_GET['category'];

// Then modify your query to use category_name instead of id:
$stmt = $conn->prepare("SELECT * FROM categories WHERE category_name = ?");
$stmt->execute([$category]);

$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (!$results) {
    echo "Category not found.";
    exit;
}

$category_name = $results[0]['category_name'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Category - <?= htmlspecialchars($category_name) ?></title>
    <link rel="stylesheet" href="../../styles.css">
    <style>
        .category-box {
            max-width: 600px;
            margin: 30px auto;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 10px;
            background: #f9f9f9;
        }
        .category-box h2 {
            margin-bottom: 10px;
        }
        .subcategory-list {
            margin-top: 15px;
        }
        .subcategory-list li {
            margin: 6px 0;
            padding: 6px 12px;
            background: #eef;
            border-radius: 5px;
            list-style-type: disc;
        }
        a.back-link {
            display: inline-block;
            margin-top: 20px;
            padding: 8px 14px;
            background-color: #3498db;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }

        a.back-link:hover {
            background-color: #2980b9;
        }
    </style>
</head>
<body>
<div class="category-box">
    <h2>Category: <?= htmlspecialchars($category_name) ?></h2>

    <h4>Subcategories:</h4>
    <ul class="subcategory-list">
        <?php foreach ($results as $row): ?>
            <li><?= htmlspecialchars($row['subcategory_name']) ?></li>
        <?php endforeach; ?>
    </ul>

    <a href="index.php" class="back-link">← Back to Categories</a>
</div>
</body>
</html>
