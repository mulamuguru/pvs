
<?php
include '../../db.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $category_name = $_POST['category_name'];
    $subcategories = $_POST['subcategories']; // Array of subcategories

    // Insert the category
    $stmt = $conn->prepare("INSERT INTO categories (category_name) VALUES (?)");
    $stmt->execute([$category_name]);
    $category_id = $conn->lastInsertId();

    // Insert each subcategory
    foreach ($subcategories as $subcategory_name) {
        if (!empty($subcategory_name)) {
            $stmt = $conn->prepare("INSERT INTO subcategories (category_id, subcategory_name) VALUES (?, ?)");
            $stmt->execute([$category_id, $subcategory_name]);
        }
    }

    $_SESSION['success'] = "Category and subcategories added successfully!";
    header("Location: index.php");
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Category</title>
    <link rel="stylesheet" href="../../styles.css">
    <style>
        /* General layout for the page */
.container {
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 30px;
    margin-top: 50px;
}

/* Header styling */
.header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    width: 100%;
    background-color: #4CAF50; /* Green background */
    padding: 15px;
    color: white;
}

.header .logo {
    font-size: 24px;
    font-weight: bold;
}

.top-nav .nav-btn, .profile-button {
    background-color: #5cb85c;
    padding: 8px 20px;
    color: white;
    text-decoration: none;
    border-radius: 5px;
    margin: 0 10px;
}

.top-nav .nav-btn:hover, .profile-button:hover {
    background-color: #4cae4c;
}

/* Remove sidebar for this page */
.sidebar {
    display: none; /* No sidebar for create category */
}

/* Form container */
form {
    width: 100%;
    max-width: 700px;
    background-color: #ffffff;
    padding: 30px;
    border-radius: 8px;
    box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
    margin-top: 20px;
    font-family: Arial, sans-serif;
}

h2 {
    color: #4CAF50;
    font-size: 24px;
    text-align: center;
    margin-bottom: 20px;
}

/* Form styling */
.form-group {
    margin-bottom: 20px;
}

label {
    font-size: 16px;
    color: #333;
    font-weight: bold;
    margin-bottom: 8px;
    display: block;
}

input.form-control, select.form-control {
    width: 100%;
    padding: 10px;
    font-size: 16px;
    border: 1px solid #ccc;
    border-radius: 5px;
    box-sizing: border-box;
}

input.form-control:focus, select.form-control:focus {
    border-color: #4CAF50;
    outline: none;
}

/* Add subcategories section */
#subcategories-wrapper {
    margin-top: 20px;
}

#subcategories-wrapper .form-group {
    display: flex;
    flex-direction: row;
    justify-content: space-between;
    align-items: center;
}

#subcategories-wrapper .form-group input {
    width: 80%;
}

#subcategories-wrapper button {
    background-color: #4CAF50;
    color: white;
    border: none;
    border-radius: 5px;
    font-size: 16px;
    padding: 10px 20px;
    cursor: pointer;
    margin-top: 10px;
}

#subcategories-wrapper button:hover {
    background-color: #45a049;
}

/* Submit button */
button.btn {
    background-color: #4CAF50;
    color: white;
    padding: 12px 20px;
    border: none;
    border-radius: 5px;
    font-size: 16px;
    cursor: pointer;
    width: 100%;
    margin-top: 20px;
}

button.btn:hover {
    background-color: #45a049;
}

/* Hover effects for input fields and buttons */
input.form-control:hover, button.btn:hover {
    background-color: #f1f1f1;
}

/* Responsiveness */
@media (max-width: 768px) {
    .container {
        padding: 20px;
    }

    input.form-control, select.form-control, button.btn {
        font-size: 14px;
    }

    #subcategories-wrapper .form-group input {
        width: 70%;
    }
}

    </style>
</head>
<body>

<div class="container">
    <div class="header">
        <div class="logo">Planet Victoria</div>
        <div class="top-nav">
            <a href="index.php" class="nav-btn">Back to Dashboard</a>
        </div>
    </div>

    <form method="POST" action="create.php">
        <h2>Create New Category</h2>

        <!-- Category Name -->
        <div class="form-group">
            <label for="category_name">Category Name</label>
            <input type="text" name="category_name" id="category_name" class="form-control" required>
        </div>

        <!-- Subcategories -->
        <div id="subcategories-wrapper">
            <div class="form-group">
                <label for="subcategory1">Subcategory 1</label>
                <input type="text" name="subcategories[]" id="subcategory1" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="subcategory2">Subcategory 2</label>
                <input type="text" name="subcategories[]" id="subcategory2" class="form-control">
            </div>
            <div class="form-group">
                <label for="subcategory3">Subcategory 3</label>
                <input type="text" name="subcategories[]" id="subcategory3" class="form-control">
            </div>
        </div>

        <button type="button" id="add-more-subcats" class="btn">Add More Subcategories</button>
        
        <button type="submit" class="btn">Create Category</button>
    </form>
</div>

<script>
    document.getElementById('add-more-subcats').addEventListener('click', function() {
        const subcategoriesWrapper = document.getElementById('subcategories-wrapper');
        const newSubcategory = document.createElement('div');
        newSubcategory.classList.add('form-group');
        newSubcategory.innerHTML = `
            <label for="subcategory">Subcategory</label>
            <input type="text" name="subcategories[]" class="form-control">
        `;
        subcategoriesWrapper.appendChild(newSubcategory);
    });
</script>

</body>
</html>
