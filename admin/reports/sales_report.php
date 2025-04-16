<?php
session_start();
require_once('../../db.php');

if (!isset($_SESSION['admin_id'])) {
    header("Location: ../../login.php");
    exit();
}


$startDate = $_GET['start_date'] ?? date('Y-m-01');
$endDate = $_GET['end_date'] ?? date('Y-m-t');
$customerId = $_GET['customer_id'] ?? null;

// Fetch loans
$loanStmt = $conn->prepare("SELECT principal_amount, created_at FROM loans WHERE status = 'active'  AND DATE(created_at) BETWEEN ? AND ?");
$loanStmt->execute([$startDate, $endDate]);
$loans = $loanStmt->fetchAll(PDO::FETCH_ASSOC);
$totalLoanSales = array_sum(array_column($loans, 'principal_amount'));

// Fetch orders
$orderStmt = $conn->prepare("SELECT total_amount, placed_at FROM orders WHERE status = 'active' AND customer_id = ? AND DATE(placed_at) BETWEEN ? AND ?");
$orderStmt->execute([$customerId,$startDate, $endDate]);
$orders = $orderStmt->fetchAll(PDO::FETCH_ASSOC);
$totalProductSales = array_sum(array_column($orders, 'total_amount'));

$grandTotal = $totalLoanSales + $totalProductSales;
?>

<!DOCTYPE html>
<html>
<head>
    <title>Sales Report</title>
    <link rel="stylesheet" href="../../styles.css">
</head>
<body>
<div class="container">
    <div class="sidebar">
        <h3>Menu</h3>
        <ul class="menu-items">
        <li><a href="../index.php" >Dashboard</a></li>
    <li><a href="../index.php">Groups</a></li>
    <li><a href="../clients/index.php">Clients</a></li>
    <li><a href="../deposit/index.php">Deposit</a></li>
    <li><a href="../loans/index.php">Loans</a></li>
    <li><a href="../orders/index.php">Orders</a></li>
    <li><a href="../users/index.php">Users</a></li>
    <li><a href="index.php" class="active">Reports</a></li>
    <li><a href="../products/index.php">Products</a></li>
        </ul>
    </div>

    <main class="content">
        <h2>Sales Report</h2>

        <form method="get" style="margin-bottom: 20px;">
            <label>Start Date:
                <input type="date" name="start_date" value="<?= $startDate ?>">
            </label>
            <label>End Date:
                <input type="date" name="end_date" value="<?= $endDate ?>">
            </label>
            <button type="submit" class="btn">Filter</button>
        </form> 

        <p><strong>Total Loan Sales:</strong> KES <?= number_format($totalLoanSales, 2) ?></p>
        <p><strong>Total Product Sales:</strong> KES <?= number_format($totalProductSales, 2) ?></p>
        <p><strong>Grand Total:</strong> KES <?= number_format($grandTotal, 2) ?></p>

        <h3>Loan Sales</h3>
        <table>
            <tr>
                <th>Amount (KES)</th>
                <th>Date</th>
            </tr>
            <?php foreach ($loans as $loan): ?>
                <tr>
                    <td><?= number_format($loan['principal_amount'], 2) ?></td>
                    <td><?= date('d M Y', strtotime($loan['created_at'])) ?></td>
                </tr>
            <?php endforeach; ?>
        </table>

        <h3 style="margin-top: 30px;">Product Sales</h3>
        <table>
            <tr>
                <th>Amount (KES)</th>
                <th>Date</th>
            </tr>
            <?php foreach ($orders as $order): ?>
                <tr>
                    <td><?= number_format($order['total_amount'], 2) ?></td>
                    <td><?= date('d M Y', strtotime($order['placed_at'])) ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    </main>
</div>
</body>
</html>
