<?php
session_start();
require_once('../../db.php');

// Check if officer is logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: ../../login.php');
    exit();
}

// Fetch overdue principal data
$sql = "SELECT 
            l.id AS loan_id,
            c.first_name, c.last_name,
            l.principal_amount,
            l.loan_term,
            l.created_at AS disbursement_date
        FROM loans l
        JOIN clients c ON l.client_id = c.id
        WHERE l.status = 'active'";

$stmt = $conn->prepare($sql);
$stmt->execute();
$loans = $stmt->fetchAll();

$today = new DateTime();
$overdueLoans = [];
$totalPrincipalOverdue = 0;

foreach ($loans as $loan) {
    $disbursement = new DateTime($loan['disbursement_date']);
    $loanTermMonths = (int) $loan['loan_term'];
    $dueDate = (clone $disbursement)->modify("+{$loanTermMonths} months");

    if ($today > $dueDate) {
        $daysOverdue = $today->diff($dueDate)->days;
        $totalPrincipalOverdue += $loan['principal_amount'];
        $loan['due_date'] = $dueDate->format("Y-m-d");
        $loan['days_overdue'] = $daysOverdue;
        $overdueLoans[] = $loan;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Principal Overdue (POD) Report</title>
    <link rel="stylesheet" href="../../styles.css">
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
        <h2>Principal Overdue (POD) Report</h2>

        <div class="report-summary">
            <p><strong>Total Principal Overdue:</strong> KES <?= number_format($totalPrincipalOverdue, 2) ?></p>
        </div>

        <form method="post" action="export_pod.php">
            <button type="submit" class="btn btn-export"><a href="export_pod.php" style="margin-bottom: 10px;
            text-decoration:none;">Export to Excel</a>
            </button>
        </form>

        <table class="table-container">
            <thead>
                <tr>
                    <th>Client Name</th>
                    <th>Principal Amount</th>
                    <th>Loan Term (Months)</th>
                    <th>Disbursement Date</th>
                    <th>Due Date</th>
                    <th>Days Overdue</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($overdueLoans) > 0): ?>
                    <?php foreach ($overdueLoans as $loan): ?>
                        <tr>
                            <td><?= htmlspecialchars($loan['first_name'] . ' ' . $loan['last_name']) ?></td>
                            <td><?= number_format($loan['principal_amount'], 2) ?></td>
                            <td><?= htmlspecialchars($loan['loan_term']) ?></td>
                            <td><?= htmlspecialchars($loan['disbursement_date']) ?></td>
                            <td><?= $loan['due_date'] ?></td>
                            <td><?= $loan['days_overdue'] ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6">No overdue principal loans found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </main>
</div>
</body>
</html>
