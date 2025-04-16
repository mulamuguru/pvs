<?php
require_once '../../db.php';
session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../../login.php");
    exit;
}

// Get the order ID from the query string
if (isset($_GET['id'])) {
    $order_id = $_GET['id'];

    // Fetch the order details from the database
    $stmt = $conn->prepare("SELECT * FROM orders WHERE id = :id");
    $stmt->bindParam(':id', $order_id, PDO::PARAM_INT);
    $stmt->execute();
    $order = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$order) {
        echo "Order not found.";
        exit;
    }
} else {
    echo "Invalid order ID.";
    exit;
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Order Details - Planet Victoria</title>
    <link rel="stylesheet" href="../../styles.css">
    <link rel="icon" href="favicon.png" type="image/png">
    <script src="../script.js?v=<?php echo time(); ?>"></script>
    <style>
        .order-details-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            font-size: 16px;
        }

        .order-details-container h2 {
            margin-bottom: 20px;
        }

        .order-details-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .order-details-table th, .order-details-table td {
            padding: 12px;
            text-align: left;
            border: 1px solid #ccc;
        }

        .order-details-table th {
            background-color: #3498db;
            color: white;
        }

        .order-details-table tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .order-details-table tr:hover {
            background-color: #f1f1f1;
        }

        .action-buttons {
            margin-top: 20px;
        }

        .btn-back {
            background-color: #f39c12;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            font-size: 16px;
        }

        .btn-back:hover {
            background-color: #e67e22;
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

    <div class="sidebar" id="sidebar">
        <h3>Menu</h3>
        <ul class="menu-items">
            <li><a href="../index.php">Dashboard</a></li>
            <li><a href="../groups/index.php">Groups</a></li>
            <li><a href="../clients/index.php">Clients</a></li>
            <li><a href="../clients/approvals.php" class="bg-primary text-white">Approve Clients</a></li>
            <li><a href="../deposit/index.php">Deposit</a></li>
            <li><a href="../loans/index.php">Loans</a></li>
            <li><a href="index.php" class="active">Orders</a></li>
            <li><a href="../users/index.php">Users</a></li>
            <li><a href="../reports/index.php">Reports</a></li>
            <li><a href="../products/index.php">Products</a></li>
        </ul>
    </div>

    <button class="toggle-btn" id="toggleBtn">&#9776;</button>

    <main class="content">
        <div class="order-details-container">
            <h2>Order Details (ID: <?= htmlspecialchars($order['id']) ?>)</h2>

            <table class="order-details-table">
                <tr>
                    <th>Customer ID</th>
                    <td><?= htmlspecialchars($order['customer_id']) ?></td>
                </tr>
                <tr>
                    <th>Total Amount</th>
                    <td>KES <?= number_format($order['total_amount'], 2) ?></td>
                </tr>
                <tr>
                    <th>Description</th>
                    <td><?= htmlspecialchars($order['description']) ?></td>
                </tr>
                <tr>
                    <th>Status</th>
                    <td><?= htmlspecialchars($order['status']) ?></td>
                </tr>
                <tr>
                    <th>Placed At</th>
                    <td><?= htmlspecialchars($order['placed_at']) ?></td>
                </tr>
                <tr>
                    <th>Total Quantity</th>
                    <td><?= htmlspecialchars($order['total_quantity']) ?></td>
                </tr>
            </table>

            <div class="action-buttons">
                <a href="index.php" class="btn-back">Back to Orders</a>
            </div>
        </div>
    </main>
</div>
</body>
</html>
