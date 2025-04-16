<?php
require_once '../../db.php';
session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: ../../login.php");
    exit;
}

if (!isset($_GET['id'])) {
    echo "Product ID is missing.";
    exit;
}

$id = $_GET['id'];

// Fetch product details
$stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
    echo "Product not found.";
    exit;
}

// Fetch all category-subcategory pairs
$categoryStmt = $conn->prepare("SELECT id, category_name, subcategory_name FROM categories");
$categoryStmt->execute();
$categoryData = $categoryStmt->fetchAll(PDO::FETCH_ASSOC);

// Group subcategories by category_name
$grouped = [];
foreach ($categoryData as $cat) {
    $grouped[$cat['category_name']][] = ['id' => $cat['id'], 'subcategory_name' => $cat['subcategory_name']];
}

// Extract current category and subcategory
$currentCat = '';
$currentSub = '';
foreach ($categoryData as $cat) {
    if ($cat['id'] == $product['category_id']) {
        $currentCat = $cat['category_name'];
        $currentSub = $cat['subcategory_name'];
        break;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $desc = $_POST['description'];
    $price = $_POST['price'];
    $category_id = $_POST['category_id'];

    $image_url = $product['image_url'];

    if (!empty($_FILES['image']['name'])) {
        $targetDir = "../../uploads/";
        $imageFileName = time() . '_' . basename($_FILES["image"]["name"]);
        $targetFile = $targetDir . $imageFileName;

        if (move_uploaded_file($_FILES["image"]["tmp_name"], $targetFile)) {
            $image_url = "uploads/" . $imageFileName;
        }
    }

    $updateStmt = $conn->prepare("UPDATE products SET name = ?, description = ?, price = ?, image_url = ?, category_id = ?, updated_at = NOW() WHERE id = ?");
    $updateStmt->execute([$name, $desc, $price, $image_url, $category_id, $id]);

    header("Location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Update Product - Planet Victoria</title>
    <link rel="stylesheet" href="../../styles.css">
    <style>
        .form-container {
            max-width: 600px;
            margin: 30px auto;
            background: #fff;
            padding: 20px;
            border-radius: 10px;
        }

        .form-container label {
            display: block;
            margin-top: 15px;
        }

        .form-container input,
        .form-container textarea,
        .form-container select {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border: 1px solid #ddd;
            border-radius: 6px;
        }

        .form-container button {
            margin-top: 20px;
            padding: 10px 20px;
            background: #3498db;
            border: none;
            color: white;
            border-radius: 6px;
            cursor: pointer;
        }

        .form-container img {
            max-width: 100%;
            margin-top: 10px;
            border-radius: 8px;
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
<div class="form-container">
    <h2>Update Product</h2>
    <form action="" method="POST" enctype="multipart/form-data">
        <label for="name">Product Name:</label>
        <input type="text" name="name" value="<?= htmlspecialchars($product['name']) ?>" required>

        <label for="description">Description:</label>
        <textarea name="description" rows="4" required><?= htmlspecialchars($product['description']) ?></textarea>

        <label for="price">Price (KES):</label>
        <input type="number" name="price" value="<?= htmlspecialchars($product['price']) ?>" required>

        <label for="image">Product Image:</label>
        <input type="file" name="image">
        <?php if ($product['image_url']): ?>
            <img src="../../<?= htmlspecialchars($product['image_url']) ?>" alt="Current Image">
        <?php endif; ?>

        <label for="categorySelect">Category:</label>
        <select id="categorySelect" name="category" required>
            <option value="">-- Select Category --</option>
            <?php foreach (array_keys($grouped) as $catName): ?>
                <option value="<?= htmlspecialchars($catName) ?>" <?= $catName == $currentCat ? 'selected' : '' ?>>
                    <?= htmlspecialchars($catName) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label for="subcategorySelect">Subcategory:</label>
        <select id="subcategorySelect" name="category_id" required>
            <option value="">-- Select Subcategory --</option>
        </select>

        <button type="submit">Update Product</button> <br>
        <a href="index.php" class="back-btn">← Back to Products</a>
    </form>
</div>

<script>
    const grouped = <?= json_encode($grouped) ?>;
    const subSelect = document.getElementById("subcategorySelect");
    const catSelect = document.getElementById("categorySelect");

    function populateSubcategories(selectedCategory, selectedId = null) {
        subSelect.innerHTML = '<option value="">-- Select Subcategory --</option>';
        if (selectedCategory && grouped[selectedCategory]) {
            grouped[selectedCategory].forEach(sub => {
                const opt = document.createElement('option');
                opt.value = sub.id;
                opt.textContent = sub.subcategory_name;
                if (selectedId && selectedId == sub.id) {
                    opt.selected = true;
                }
                subSelect.appendChild(opt);
            });
        }
    }

    // Initial load
    populateSubcategories("<?= $currentCat ?>", <?= $product['category_id'] ?>);

    // On category change
    catSelect.addEventListener("change", function () {
        populateSubcategories(this.value);
    });
</script>
</body>
</html>
