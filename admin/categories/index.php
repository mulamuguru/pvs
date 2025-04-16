<?php
require_once '../../db.php';
session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: ../../login.php");
    exit;
}

// Fetch and group categories with subcategories
$stmt = $conn->prepare("SELECT category_name, subcategory_name FROM categories ORDER BY category_name, subcategory_name");
$stmt->execute();
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Group subcategories under each category
$grouped = [];
foreach ($rows as $row) {
    $cat = $row['category_name'];
    $sub = $row['subcategory_name'];
    if (!isset($grouped[$cat])) {
        $grouped[$cat] = [];
    }
    $grouped[$cat][] = $sub;
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Categories - Planet Victoria</title>
    <link rel="stylesheet" href="../../styles.css">
    <link rel="icon" href="favicon.png" type="image/png">
    <script src="../script.js?v=<?php echo time(); ?>"></script>
    <style>
        .categories-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .categories-table th, .categories-table td {
            padding: 10px;
            text-align: left;
            border: 1px solid #ccc;
        }

        .categories-table th {
            background-color: #3498db;
            color: white;
        }

        .categories-table tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .categories-table tr:hover {
            background-color: #f1f1f1;
        }

        h2 {
            margin-bottom: 10px;
        }

        /* css for buttons view,update ,delete */
    .action-buttons {
        display: flex;
        gap: 10px;
    }

    .action-buttons a {
        padding: 6px 12px;
        text-decoration: none;
        color: white;
        border-radius: 5px;
        font-size: 14px;
        font-weight: 500;
        transition: background-color 0.3s ease;
    }

    .btn-info {
        background-color: #3498db;
    }

    .btn-warning {
        background-color: #f39c12;
    }

    .btn-danger {
        background-color: #e74c3c;
    }

    .btn-info:hover {
        background-color: #2980b9;
    }

    .btn-warning:hover {
        background-color: #d68910;
    }

    .btn-danger:hover {
        background-color: #c0392b;
    }
    /* create new category */
    .btn-success {
        background-color: #2ecc71;
        color: white;
        padding: 8px 14px;
        border-radius: 5px;
        text-decoration: none;
        font-weight: bold;
        transition: background-color 0.3s ease;
    }

    .btn-success:hover {
        background-color: #27ae60;
    }
    </style>
</head>
<body>
<div class="container">
    <header class="header">
        <div class="logo">🌍 Planet Victoria</div>
        <nav class="top-nav">
            <button class="nav-btn">Home</button>
            <a href="../profile.php" class="profile-button">Profile</a>
            <button class="nav-btn"><a href="../logout.php">Logout</a></button>
        </nav>
    </header>

    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <ul class="menu-items">
            <li><a href="../index.php">Dashboard</a></li>
            <li><a href="../groups/index.php">Groups</a></li>
            <li><a href="../clients/index.php">Clients</a></li>
            <li><a href="../clients/approvals.php">Approve Clients</a></li>
            <li><a href="../deposit/index.php">Deposit</a></li>
            <li><a href="../loans/index.php">Loans</a></li>
            <li><a href="../orders/index.php">Orders</a></li>
            <li><a href="index.php" class="active">Categories</a></li>
            <li><a href="../users/index.php">Users</a></li>
            <li><a href="../reports/index.php">Reports</a></li>
            <li><a href="../products/index.php">Products</a></li>
        </ul>
    </div>

    <button class="toggle-btn" id="toggleBtn">&#9776;</button>

    <main class="content">
    <h2>Categories</h2>
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
    <a href="create.php" class="btn btn-success">➕ Create Category</a>
</div>
<?php if (!empty($grouped)): ?>
    <table class="categories-table">
        <thead>
            <tr>
                <th>Category Name</th>
                <th>Subcategories</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($grouped as $category => $subcategories): ?>
                <tr>
                    <td><?= htmlspecialchars($category) ?></td>
                    <td><?= htmlspecialchars(implode(', ', $subcategories)) ?></td>
                    <td>
                    <div class="action-buttons">
                        <a href="view.php?category=<?= urlencode($category) ?>" class="btn btn-info"> View</a>
                        <a href="update.php?category=<?= urlencode($category) ?>" class="btn btn-warning">Update</a>
                        <a href="delete.php?category=<?= urlencode($category) ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this category and all its subcategories?')">Delete</a>
                    </div>
                </td>

                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php else: ?>
    <p>No categories found.</p>
<?php endif; ?>

    </main>
</div>
</body>
</html>
