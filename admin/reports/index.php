<?php
session_start();
require_once('../../db.php');

// Make sure the officer is logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: ../../login.php');
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Reports Dashboard</title>
    <link rel="stylesheet" href="../../styles.css">
    <style>
        .card-container {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
        }

        .report-card {
            flex: 1 1 250px;
            background-color: #f8f8f8;
            border: 1px solid #ccc;
            border-radius: 8px;
            padding: 20px;
            text-align: center;
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .report-card:hover {
            background-color: #e1f7e1;
            transform: translateY(-3px);
        }

        .report-card h3 {
            margin: 0;
            font-size: 18px;
        }

        .content {
            padding: 20px;
        }
    </style>
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
        <h2>Reports</h2>
        <div class="card-container">
            <div class="report-card" onclick="window.location.href='par.php'">
                <h3>Portfolio at Risk (PAR)</h3>
            </div>
            <div class="report-card" onclick="window.location.href='olb.php'">
                <h3>Outstanding Loan Balance (OLB)</h3>
            </div>
            <div class="report-card" onclick="window.location.href='pod.php'">
                <h3>Principal Overdue (POD)</h3>
            </div>
            <div class="report-card" onclick="window.location.href='forecast.php'">
                <h3>Forecasting Report</h3>
            </div>
            <div class="report-card" onclick="window.location.href='csr.php'">
                <h3>Collection Sheet Report (CSR)</h3>
            </div>
            <div class="report-card" onclick="window.location.href='deposit_report.php'">
                <h3>Deposit Report</h3>
            </div>
            <div class="report-card" onclick="window.location.href='sales_report.php'">
                <h3>Sales Report</h3>
            </div>
        </div>
    </main>
</div>
</body>
</html>
