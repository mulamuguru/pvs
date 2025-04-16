<?php
session_start();
require_once('../../db.php');

if (!isset($_SESSION['admin_id'])) {
    header('Location: ../../login.php');
    exit();
}

$arrearsData = [
    '30_days' => [],
    '60_days' => [],
    '90_days' => [],
];

$query = "
    SELECT 
        l.id AS loan_id,
        l.client_id,
        l.created_at,
        l.monthly_payment,
        c.first_name,
        c.last_name,
        COALESCE(d.balance, 0) AS balance
    FROM loans l
    JOIN clients c ON l.client_id = c.id
    LEFT JOIN dda d ON l.client_id = d.client_id
    WHERE l.status = 'active'
";

$stmt = $conn->prepare($query);
$stmt->execute();
$loans = $stmt->fetchAll(PDO::FETCH_ASSOC);

$currentDate = new DateTime();

foreach ($loans as $loan) {
    $loanStart = new DateTime($loan['created_at']);
    $interval = $loanStart->diff($currentDate);
    $monthsElapsed = $interval->m + ($interval->y * 12);

    for ($i = 1; $i <= 3; $i++) {
        $dueMonth = clone $loanStart;
        $dueMonth->modify("+".($monthsElapsed + $i)." months");
        $daysUntilDue = (int)$currentDate->diff($dueMonth)->format('%a');

        if ($loan['balance'] < $loan['monthly_payment']) {
            if ($daysUntilDue <= 30) $arrearsData['30_days'][] = $loan;
            elseif ($daysUntilDue <= 60) $arrearsData['60_days'][] = $loan;
            elseif ($daysUntilDue <= 90) $arrearsData['90_days'][] = $loan;
        }
    }
}

function renderTable($title, $data) {
    ob_start(); ?>
    <h3><?= $title ?></h3>
    <?php if (!empty($data)): ?>
    <table>
        <thead>
            <tr>
                <th>Client Name</th>
                <th>Monthly Payment</th>
                <th>DDA Balance</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($data as $item): ?>
            <tr>
                <td><?= htmlspecialchars($item['first_name'] . ' ' . $item['last_name']) ?></td>
                <td>KES <?= number_format($item['monthly_payment'], 2) ?></td>
                <td>KES <?= number_format($item['balance'], 2) ?></td>
                <td>Expected Arrears</td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <form method="POST" action="export_forecast.php">
        <input type="hidden" name="data" value="<?= base64_encode(serialize($data)) ?>">
        <input type="hidden" name="title" value="<?= $title ?>">
        <button type="submit">Export <?= $title ?></button>
    </form>
    <?php else: ?>
        <p>No upcoming arrears in <?= $title ?>.</p>
    <?php endif;
    return ob_get_clean();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Forecasting Report</title>
    <link rel="stylesheet" href="../../styles.css">
    <style>
        .card {
            background-color: #fff;
            padding: 20px;
            margin-top: 20px;
            border-radius: 6px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        table, th, td {
            border: 1px solid #c5c5c5;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
        h3 {
            margin-top: 30px;
            color: #2c3e50;
        }
        form button {
            margin-top: 10px;
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
        <h2>Forecasting Report</h2>
        <div class="card">
            <?= renderTable("Arrears Expected in 30 Days", $arrearsData['30_days']) ?>
            <?= renderTable("Arrears Expected in 60 Days", $arrearsData['60_days']) ?>
            <?= renderTable("Arrears Expected in 90 Days", $arrearsData['90_days']) ?>
        </div>
    </main>
</div>
</body>
</html>
