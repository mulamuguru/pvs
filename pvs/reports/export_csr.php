<?php
session_start();
require_once('../db.php');

if (!isset($_SESSION['officer_id'])) {
    header('Location: ../login.php');
    exit();
}

$officerId = $_SESSION['officer_id'];
$duration = $_GET['duration'] ?? 'Monthly';
$days = match ($duration) {
    'Today' => 1,
    '1 Week' => 7,
    '2 Weeks' => 14,
    '3 Weeks' => 21,
    default => 30,
};

// Fetch loan data
$stmt = $conn->prepare("SELECT l.*, c.first_name, c.last_name, g.name AS group_name
    FROM loans l
    JOIN clients c ON l.client_id = c.id
    JOIN groups g ON l.group_id = g.id
    WHERE l.officer_id = ?");
$stmt->execute([$officerId]);
$loans = $stmt->fetchAll();

foreach ($loans as &$loan) {
    $loan['expected_collection'] = round($loan['monthly_payment'] * ($days / 30), 2);
    $dda = $conn->prepare("SELECT balance FROM dda WHERE client_id = ?");
    $dda->execute([$loan['client_id']]);
    $loan['dda_balance'] = $dda->fetchColumn() ?? 0;
}

// Set headers for Excel export
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=collection_sheet_report.xls");

echo "Client Name\tGroup\tMonthly Payment (KES)\tExpected Collection (KES)\tDDA Balance (KES)\n";

foreach ($loans as $loan) {
    echo "{$loan['first_name']} {$loan['last_name']}\t";
    echo "{$loan['group_name']}\t";
    echo number_format($loan['monthly_payment'], 2) . "\t";
    echo number_format($loan['expected_collection'], 2) . "\t";
    echo number_format($loan['dda_balance'], 2) . "\n";
}
?>
