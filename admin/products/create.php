<?php
require_once '../../db.php';
session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: ../../login.php");
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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $desc = $_POST['description'];
    $price = $_POST['price'];
    $category_id = $_POST['category_id'];

    // Handle image upload
    $image_url = '';
    if (!empty($_FILES['image']['name'])) {
        $targetDir = "../../uploads/";
        $imageFileName = time() . '_' . basename($_FILES["image"]["name"]);
        $targetFile = $targetDir . $imageFileName;

        if (move_uploaded_file($_FILES["image"]["tmp_name"], $targetFile)) {
            $image_url = "uploads/" . $imageFileName;
        }
    }

    // Insert into database
    $insertStmt = $conn->prepare("INSERT INTO products (name, description, price, image_url, category_id, created_at, updated_at) 
                                  VALUES (?, ?, ?, ?, ?, NOW(), NOW())");
    $insertStmt->execute([$name, $desc, $price, $image_url, $category_id]);

    header("Location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Create Product - Planet Victoria</title>
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
    </style>
</head>
<body>
<div class="form-container">
    <h2>Create New Product</h2>
    <form action="" method="POST" enctype="multipart/form-data">
        <label for="name">Product Name:</label>
        <input type="text" name="name" required>

        <label for="description">Description:</label>
        <textarea name="description" rows="4" required></textarea>

        <label for="price">Price (KES):</label>
        <input type="number" name="price" required>

        <label for="image">Product Image:</label>
        <input type="file" name="image">

        <label for="categorySelect">Category:</label>
        <select id="categorySelect" name="category" required>
            <option value="">-- Select Category --</option>
            <?php foreach (array_keys($grouped) as $catName): ?>
                <option value="<?= htmlspecialchars($catName) ?>"><?= htmlspecialchars($catName) ?></option>
            <?php endforeach; ?>
        </select>

        <label for="subcategorySelect">Subcategory:</label>
        <select id="subcategorySelect" name="category_id" required>
            <option value="">-- Select Subcategory --</option>
        </select>

        <button type="submit">Create Product</button>
    </form>
</div>

<script>
    const grouped = <?= json_encode($grouped) ?>;
    const subSelect = document.getElementById("subcategorySelect");
    const catSelect = document.getElementById("categorySelect");

    function populateSubcategories(selectedCategory) {
        subSelect.innerHTML = '<option value="">-- Select Subcategory --</option>';
        if (selectedCategory && grouped[selectedCategory]) {
            grouped[selectedCategory].forEach(sub => {
                const opt = document.createElement('option');
                opt.value = sub.id;
                opt.textContent = sub.subcategory_name;
                subSelect.appendChild(opt);
            });
        }
    }

    // On category change
    catSelect.addEventListener("change", function () {
        populateSubcategories(this.value);
    });
</script>
</body>
</html>
