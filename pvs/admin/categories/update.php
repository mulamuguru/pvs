<?php
require_once '../../db.php';
session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: ../../login.php");
    exit;
}

if (!isset($_GET['category'])) {
    echo "Category not specified.";
    exit;
}

$category_name = $_GET['category'];

// Fetch all subcategories under this category
$stmt = $conn->prepare("SELECT * FROM categories WHERE category_name = ?");
$stmt->execute([$category_name]);
$subcategories = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (!$subcategories) {
    echo "Category not found.";
    exit;
}

// Update on form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_category_name = $_POST['category_name'];

    // Update the category name in all subcategories
    $update_stmt = $conn->prepare("UPDATE categories SET category_name = ? WHERE category_name = ?");
    $update_stmt->execute([$new_category_name, $category_name]);

    // Delete selected subcategories
    if (!empty($_POST['delete_subcategories'])) {
        foreach ($_POST['delete_subcategories'] as $id) {
            $delete_stmt = $conn->prepare("DELETE FROM categories WHERE id = ?");
            $delete_stmt->execute([$id]);
        }
    }

    // Update subcategory names
    foreach ($_POST['subcategories'] as $id => $subcat_name) {
        if (!empty($subcat_name)) {
            $update_sub = $conn->prepare("UPDATE categories SET subcategory_name = ? WHERE id = ?");
            $update_sub->execute([$subcat_name, $id]);
        }
    }

    header("Location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Update Category - <?= htmlspecialchars($category_name) ?></title>
    <link rel="stylesheet" href="../../styles.css">
    <style>
        /* Container for the entire page */
        .category-box {
            max-width: 700px;
            margin: 30px auto;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 10px;
            background: #f9f9f9;
        }

        .category-box h2 {
            margin-bottom: 10px;
        }

        /* Subcategory list styling */
        .subcategory-group {
            margin-top: 15px;
        }

        .subcategory-group input[type="text"] {
            width: 100%;
            padding: 8px;
            margin-bottom: 10px;
        }

        .delete-checkbox {
            margin-left: 10px;
            color: red;
        }

        button {
            padding: 10px 20px;
            background-color: #3498db;
            color: white;
            border: none;
            cursor: pointer;
        }

        button:hover {
            background-color: #2980b9;
        }

        /* Back link styling */
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
    <h2>Update Category: <?= htmlspecialchars($category_name) ?></h2>

    <form method="post">
        <label>Category Name</label>
        <input type="text" name="category_name" value="<?= htmlspecialchars($category_name) ?>" required>

        <h4>Subcategories</h4>
        <?php foreach ($subcategories as $sub): ?>
            <div class="subcategory-group">
                <input type="text" name="subcategories[<?= $sub['id'] ?>]" value="<?= htmlspecialchars($sub['subcategory_name']) ?>" required>
                <label class="delete-checkbox">
                    <input type="checkbox" name="delete_subcategories[]" value="<?= $sub['id'] ?>"> Delete
                </label>
            </div>
        <?php endforeach; ?> <br>

        <button type="submit">Update</button>
    </form>

    <a href="index.php" class="back-link">← Back to Categories</a>
</div>

</body>
</html>
