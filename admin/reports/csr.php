<?php
session_start();
require_once('../../db.php');

// Redirect if not logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: ../../login.php');
    exit();
}
$duration = $_GET['duration'] ?? 'Monthly';
$days = match ($duration) {
    'Today' => 1,
    '1 Week' => 7,
    '2 Weeks' => 14,
    '3 Weeks' => 21,
    default => 30,
};

// Fetch loans
$stmt = $conn->prepare("SELECT l.*, c.first_name, c.last_name, g.name AS group_name
    FROM loans l
    JOIN clients c ON l.client_id = c.id
    JOIN groups g ON l.group_id = g.id");
$stmt->execute();
$loans = $stmt->fetchAll();

foreach ($loans as &$loan) {
    $loan['expected_collection'] = round($loan['monthly_payment'] * ($days / 30), 2);
    $dda = $conn->prepare("SELECT balance FROM dda WHERE client_id = ?");
    $dda->execute([$loan['client_id']]);
    $loan['dda_balance'] = $dda->fetchColumn() ?? 0;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Collection Sheet Report (CSR)</title>
    <link rel="stylesheet" href="../../styles.css">
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table th, table td {
            border: 1px solid #ccc;
            padding: 10px;
            text-align: left;
        }

        table th {
            background-color: lightgray;
        }

        .btn {
            padding: 8px 16px;
            background-color: green;
            color: white;
            border: none;
            text-decoration: none;
            margin-bottom: 15px;
            display: inline-block;
        }

        .btn:hover {
            background-color: darkgreen;
        }

        select {
            padding: 6px;
            margin-left: 10px;
        }

        form {
            margin-bottom: 20px;
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
        <h2>Collection Sheet Report (<?= htmlspecialchars($duration) ?>)</h2>

        <form method="get">
            <label>Select Duration:</label>
            <select name="duration" onchange="this.form.submit()">
                <?php foreach (['Today', '1 Week', '2 Weeks', '3 Weeks', 'Monthly'] as $opt): ?>
                    <option value="<?= $opt ?>" <?= $duration == $opt ? 'selected' : '' ?>><?= $opt ?></option>
                <?php endforeach; ?>
            </select>
        </form>

        <a href="export_csr.php?duration=<?= urlencode($duration) ?>" class="btn">Export to Excel</a>

        <table>
            <thead>
                <tr>
                    <th>Client Name</th>
                    <th>Group</th>
                    <th>Monthly Payment (KES)</th>
                    <th>Expected Collection (KES)</th>
                    <th>DDA Balance (KES)</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($loans as $loan): ?>
                    <tr>
                        <td><?= htmlspecialchars($loan['first_name'] . ' ' . $loan['last_name']) ?></td>
                        <td><?= htmlspecialchars($loan['group_name']) ?></td>
                        <td><?= number_format($loan['monthly_payment'], 2) ?></td>
                        <td><?= number_format($loan['expected_collection'], 2) ?></td>
                        <td><?= number_format($loan['dda_balance'], 2) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </main>
</div>
</body>
</html>
