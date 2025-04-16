<?php
session_start();
require_once('../../db.php');

// Make sure the officer is logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: ../../login.php');
    exit();
}

// Get Outstanding Loan Balance (OLB) data
$stmt = $conn->prepare('
    SELECT 
        c.id AS client_id, 
        CONCAT(c.first_name, " ", c.last_name) AS client_name, 
        l.principal_amount, 
        l.total_repayment, 
        (l.principal_amount - l.total_repayment) AS balance
    FROM 
        loans l
    INNER JOIN 
        clients c ON c.id = l.client_id
    WHERE 
        l.status = "active" 
');
$stmt->execute();
$loans = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Calculate totals for OLB
$totalOutstanding = 0;
$totalOverdue = 0;
foreach ($loans as $loan) {
    $totalOutstanding += $loan['principal_amount'];
    $totalOverdue += ($loan['principal_amount'] - $loan['total_repayment']);
}

// Function to export data to Excel
function exportToExcel($loans)
{
    header("Content-Type: application/vnd.ms-excel");
    header("Content-Disposition: attachment; filename=OLB_report.xls");

    echo "Client Name\tOutstanding Loan Amount\tOutstanding Balance\n";
    foreach ($loans as $loan) {
        echo $loan['client_name'] . "\t" . $loan['principal_amount'] . "\t" . ($loan['principal_amount'] - $loan['total_repayment']) . "\n";
    }
    exit();
}

if (isset($_GET['export'])) {
    exportToExcel($loans);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Outstanding Loan Balance (OLB) Report</title>
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

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table, th, td {
            border: 1px solid #ddd;
        }

        th, td {
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: lightgray;
        }

        .btn {
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            display: inline-block;
            margin-top: 10px;
        }

        .btn-danger {
            background-color: red;
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
        <h2>Outstanding Loan Balance (OLB) Report</h2>
        <p><strong>Total Outstanding Loans:</strong> KES <?= number_format($totalOutstanding, 2) ?></p>
        <p><strong>Total Overdue Loans:</strong> KES <?= number_format($totalOverdue, 2) ?></p>
        <p><strong>OLB (Outstanding Loan Balance):</strong> <?= $totalOutstanding > 0 ? number_format(($totalOverdue / $totalOutstanding) * 100, 2) : 0 ?>%</p>

        <a href="?export=true" class="btn">Export to Excel</a>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Client Name</th>
                        <th>Outstanding Loan Amount</th>
                        <th>Outstanding Balance</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($loans as $loan): ?>
                        <tr>
                            <td><?= htmlspecialchars($loan['client_name']) ?></td>
                            <td><?= number_format($loan['principal_amount'], 2) ?></td>
                            <td><?= number_format($loan['principal_amount'] - $loan['total_repayment'], 2) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </main>
</div>
</body>
</html>
