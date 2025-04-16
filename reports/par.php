<?php
session_start();
require_once('../db.php');

// Ensure the user is logged in
if (!isset($_SESSION['officer_id'])) {
    header('Location: ../login.php');
    exit();
}

// SQL query to fetch data for PAR report
$stmt = $conn->prepare('
    SELECT 
        c.id AS client_id, 
        CONCAT(c.first_name, " ", c.last_name) AS client_name, 
        l.principal_amount, 
        l.monthly_payment, 
        l.total_repayment, 
        (l.principal_amount - l.total_repayment) AS balance
    FROM 
        loans l
    INNER JOIN 
        clients c ON c.id = l.client_id
    WHERE 
        l.status = "active"');

$stmt->execute();
$loans = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Calculate totals
$totalOutstanding = 0;
$totalOverdue = 0;
foreach ($loans as $loan) {
    $totalOutstanding += $loan['outstanding_balance'];
    $totalOverdue += $loan['overdue_balance'];
}
$par = $totalOutstanding > 0 ? ($totalOverdue / $totalOutstanding) * 100 : 0;
?>

<!DOCTYPE html>
<html>
<head>
    <title>Portfolio at Risk (PAR) Report</title>
    <link rel="stylesheet" href="../styles.css">
    <style>
        .content {
            padding: 20px;
        }
        .report-card {
            background-color: #f8f8f8;
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 8px;
            border: 1px solid #ccc;
        }
        .table-container {
            margin-top: 20px;
        }
        .table-container table {
            width: 100%;
            border-collapse: collapse;
        }
        .table-container th, .table-container td {
            padding: 10px;
            border: 1px solid #ccc;
            text-align: left;
        }
        .table-container th {
            background-color: lightgray;
        }
        .btn {
            padding: 10px;
            background-color: #28a745;
            color: white;
            border: none;
            cursor: pointer;
            margin-top: 20px;
        }
        .btn:hover {
            background-color: #218838;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="sidebar">
        <h3>Menu</h3>
        <ul class="menu-items">
            <li><a href="../index.php">Dashboard</a></li>
            <li><a href="../groups/index.php">Groups</a></li>
            <li><a href="../clients/index.php">Clients</a></li>
            <li><a href="../products/index.php">Products</a></li>
            <li><a href="index.php">Reports</a></li>
            <li><a href="../deposit/index.php">Deposits</a></li>
        </ul>
    </div>

    <main class="content">
        <h2>Portfolio at Risk (PAR) Report</h2>

        <div class="report-card">
            <h3>Total Outstanding Loans: KES <?= number_format($totalOutstanding, 2) ?></h3>
            <h3>Total Overdue Loans: KES <?= number_format($totalOverdue, 2) ?></h3>
            <h3>PAR (Portfolio at Risk): <?= number_format($par, 2) ?>%</h3>
        </div>
        <form method="post" action="export_par.php">
        <button class="btn"><a href="export_par.php" class="btn btn-success" style="margin-bottom: 10px; text-decoration:none;">Export to Excel</a>
        </button>
    </form>
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Client Name</th>
                        <th>Loan Amount</th>
                        <th>Outstanding Balance</th>
                        <th>Overdue Balance</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($loans as $loan): ?>
                    <tr>
                        <td><?= htmlspecialchars($loan['client_name']) ?></td>
                        <td><?= number_format($loan['loan_amount'], 2) ?></td>
                        <td><?= number_format($loan['outstanding_balance'], 2) ?></td>
                        <td><?= number_format($loan['overdue_balance'], 2) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </main>
</div>
</body>
</html>
