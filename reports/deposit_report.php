<?php
session_start();
require_once('../db.php');

if (!isset($_SESSION['officer_id'])) {
    header('Location: ../login.php');
    exit();
}

$officerId = $_SESSION['officer_id'];

$stmt = $conn->prepare("
    SELECT d.*, c.first_name, c.last_name, g.name AS group_name
    FROM deposits d
    JOIN clients c ON d.client_id = c.id
    JOIN groups g ON c.group_id = g.id
    WHERE c.officer_id = ?
    ORDER BY d.deposit_date DESC
");
$stmt->execute([$officerId]);
$deposits = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Calculate totals
$totalSavings = 0;
$totalLoanPayments = 0;

foreach ($deposits as $d) {
    $totalSavings += $d['savings_amount'];
    $totalLoanPayments += $d['loan_payment'];
}

$grandTotal = $totalSavings + $totalLoanPayments;
?>

<!DOCTYPE html>
<html>
<head>
    <title>Deposit Report</title>
    <link rel="stylesheet" href="../styles.css">
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
            <li><a href="index.php" class="active">Reports</a></li>
            <li><a href="../deposit/index.php">Deposits</a></li>
        </ul>
    </div>

    <main class="content">
        <h2>Deposit Report</h2>
        <form method="post" action="export_deposit_report.php">
            <button type="submit" class="btn btn-export"><a href="export_deposit_report.php" style="margin-bottom: 10px;
            text-decoration:none;">Export to Excel</a>
            </button>
        </form>

        <!-- Total savings, loan payments, and grand total -->
        <div class="totals">
            <p><strong>Total Savings: </strong>KES <?= number_format($totalSavings, 2) ?></p>
            <p><strong>Total Loan Payments: </strong>KES <?= number_format($totalLoanPayments, 2) ?></p>
            <p><strong>Grand Total (Savings + Loan Payments): </strong>KES <?= number_format($grandTotal, 2) ?></p>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Client Name</th>
                    <th>Group</th>
                    <th>Savings Amount (KES)</th>
                    <th>Loan Payment (KES)</th>
                    <th>Deposit Date</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($deposits as $d): ?>
                    <tr>
                        <td><?= htmlspecialchars($d['first_name'] . ' ' . $d['last_name']) ?></td>
                        <td><?= htmlspecialchars($d['group_name']) ?></td>
                        <td><?= number_format($d['savings_amount'], 2) ?></td>
                        <td><?= number_format($d['loan_payment'], 2) ?></td>
                        <td><?= date('d M Y', strtotime($d['deposit_date'])) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </main>
</div>
</body>
</html>
