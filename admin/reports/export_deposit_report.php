<?php
session_start();
require_once('../../db.php');

if (!isset($_SESSION['admin_id'])) {
    header('Location: ../../login.php');
    exit();
}

header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=deposit_report.xls");
header("Pragma: no-cache");
header("Expires: 0");

$stmt = $conn->prepare("
    SELECT d.*, c.first_name, c.last_name, g.name AS group_name
    FROM deposits d
    JOIN clients c ON d.client_id = c.id
    JOIN groups g ON c.group_id = g.id
    ORDER BY d.deposit_date DESC
");
$stmt->execute();
$deposits = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Initialize totals
$totalSavings = 0;
$totalLoanPayments = 0;

echo "<table border='1'>";
echo "<tr>
        <th>Client Name</th>
        <th>Group</th>
        <th>Savings Amount (KES)</th>
        <th>Loan Payment (KES)</th>
        <th>Deposit Date</th>
      </tr>";

foreach ($deposits as $d) {
    $clientName = htmlspecialchars($d['first_name'] . ' ' . $d['last_name']);
    $groupName = htmlspecialchars($d['group_name']);
    $savings = number_format($d['savings_amount'], 2);
    $loan = number_format($d['loan_payment'], 2);
    $date = date('d M Y', strtotime($d['deposit_date']));

    $totalSavings += $d['savings_amount'];
    $totalLoanPayments += $d['loan_payment'];

    echo "<tr>
            <td>{$clientName}</td>
            <td>{$groupName}</td>
            <td>{$savings}</td>
            <td>{$loan}</td>
            <td>{$date}</td>
          </tr>";
}

$grandTotal = $totalSavings + $totalLoanPayments;

echo "<tr>
        <td colspan='2'><strong>Totals</strong></td>
        <td><strong>KES " . number_format($totalSavings, 2) . "</strong></td>
        <td><strong>KES " . number_format($totalLoanPayments, 2) . "</strong></td>
        <td><strong>KES " . number_format($grandTotal, 2) . "</strong></td>
      </tr>";
echo "</table>";
