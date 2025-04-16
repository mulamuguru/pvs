<?php
include 'db.php';
session_start();

if (!isset($_SESSION['officer_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['officer_id'];
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Profile - Planet Victoria</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
    margin: 0;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background: linear-gradient(to bottom, #3498db, #2ecc71);
    min-height: 100vh;
    color: #333;
}
    .main-container {
    display: flex;
    height: 100vh;
   
}

.sidebar {
    width: 220px;
    padding: 20px;
}

.menu-items {
    list-style: none;
    padding: 0;
}

.menu-items li a {
    display: block;
    padding: 10px;
    color: #ecf0f1;
    text-decoration: none;
    border-radius: 4px;
    margin-bottom: 5px;
    transition: background 0.3s;
}

.menu-items li a:hover,
.menu-items li a.active {
    background: #1abc9c;
    color: #fff;
}

.content {
    flex-grow: 1;
    padding: 20px;
    overflow-y: auto;
}

.card {
    background: #fff;
    padding: 20px;
    border-radius: 8px;
}

.btn-back {
    display: inline-block;
    margin-top: 20px;
    background: #3498db;
    color: #fff;
    padding: 10px 15px;
    border-radius: 4px;
    text-decoration: none;
    transition: background 0.3s;
}

.btn-back:hover {
    background: #2980b9;
}
</style>
</head>
<body>
    <div class="main-container">
        <!-- Sidebar -->
        <div class="sidebar">
            <h3>Menu</h3>
            <ul class="menu-items">
                <li><a href="index.php">Dashboard</a></li>
                <li><a href="groups/index.php">Groups</a></li>
                <li><a href="clients/index.php">Clients</a></li>
                <li><a href="deposit/index.php">Deposit</a></li>
                <li><a href="reports/index.php">Reports</a></li>
                <li><a href="products/index.php">Products</a></li>
        
            </ul>
        </div>

        <!-- Content Area -->
        <div class="content">
            <div class="top-bar">
                <h2>Officer Profile</h2>
            </div>

            <div class="card" style="max-width: 600px; margin-top: 20px;">
                <h3>Profile Information</h3>
                <p><strong>Full Name:</strong> <?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?></p>
                <p><strong>Username:</strong> <?= htmlspecialchars($user['username']) ?></p>
                <p><strong>Email:</strong> <?= htmlspecialchars($user['email']) ?></p>
                <p><strong>Phone:</strong> <?= htmlspecialchars($user['phone']) ?></p>
                <p><strong>Role:</strong> <?= ucfirst($user['role']) ?></p>
                <p><strong>Account Created:</strong> <?= htmlspecialchars($user['created_at']) ?></p>
            </div>
        </div>
    </div>
</body>
</html>
