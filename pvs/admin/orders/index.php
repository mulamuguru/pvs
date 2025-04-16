<?php
// Prevent caching
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

require_once '../../db.php';
session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: ../../login.php");
    exit;
}


// Fetch orders
$stmt = $conn->prepare("SELECT * FROM orders ORDER BY placed_at DESC");
$stmt->execute();
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Orders - Planet Victoria</title>
    <link rel="stylesheet" href="../../styles.css">
    <link rel="icon" href="favicon.png" type="image/png">
    <script src="../script.js?v=<?php echo time(); ?>"></script>
    <style>
        .orders-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .orders-table th, .orders-table td {
            padding: 10px;
            text-align: left;
            border: 1px solid #ccc;
        }

        .orders-table th {
            background-color: #3498db;
            color: white;
        }

        .orders-table tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .orders-table tr:hover {
            background-color: #f1f1f1;
        }

      /* Style for buttons container */
.actions-btns {
    display: flex;
    gap: 10px; /* Space between buttons */
    justify-content: flex-start; /* Align buttons to the left */
}

/* Individual button styles */
.btn {
    padding: 6px 12px;
    border-radius: 4px;
    color: white;
    text-decoration: none;
    font-size: 13px;
    transition: all 0.3s ease;
}

.btn-view {
    background-color: #3498db;
}

.btn-view:hover {
    background-color: #2980b9;
}

.btn-approve {
    background-color: #27ae60;
}

.btn-approve:hover {
    background-color: #1e8449;
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
        <h3>Menu</h3>
        <ul class="menu-items">
            <li><a href="../index.php">Dashboard</a></li>
            <li><a href="../groups/index.php">Groups</a></li>
            <li><a href="../clients/index.php">Clients</a></li>
            <li><a href="../deposit/index.php">Deposit</a></li>
            <li><a href="../loans/index.php">Loans</a></li>
            <li><a href="index.php" class="active">Orders</a></li>
            <li><a href="../users/index.php">Users</a></li>
            <li><a href="../reports/index.php">Reports</a></li>
            <li><a href="../products/index.php">Products</a></li>
            <li><a href="../categories/index.php">Categories</a></li>
        </ul>
    </div>

    <button class="toggle-btn" id="toggleBtn">&#9776;</button>

    <main class="content">
        <h2>All Orders</h2>

        <?php if (count($orders) > 0): ?>
            <table class="orders-table">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Customer ID</th>
                    <th>Total Amount</th>
                    <th>Description</th>
                    <th>Status</th>
                    <th>Placed At</th>
                    <th>Total Quantity</th>
                    <th>Actions</th> <!-- Add actions column -->
                </tr>
                </thead>
                <tbody>
                <?php foreach ($orders as $order): ?>
                    <tr>
                        <td><?= htmlspecialchars($order['id']) ?></td>
                        <td><?= htmlspecialchars($order['customer_id']) ?></td>
                        <td>KES <?= number_format($order['total_amount'], 2) ?></td>
                        <td><?= htmlspecialchars($order['description']) ?></td>
                        <td><?= htmlspecialchars($order['status']) ?></td>
                        <td><?= htmlspecialchars($order['placed_at']) ?></td>
                        <td><?= htmlspecialchars($order['total_quantity']) ?></td>
                        <td>
                            <div class="actions-btns">
                                <a href="view.php?id=<?= $order['id']; ?>" class="btn btn-view">View</a>
                                <a href="javascript:void(0);" onclick="confirmApproval(<?= $order['id']; ?>)" class="btn btn-approve">Approve</a>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No orders found.</p>
        <?php endif; ?>
    </main>
</div>
<script>
    function confirmApproval(orderId) {
        if (confirm("Are you sure you want to approve and delete this order?")) {
            window.location.href = "approve.php?id=" + orderId;
        }
    }
</script>
</body>
</html>
