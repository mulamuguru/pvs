<?php
session_start();
require_once('../db.php');

if (!isset($_SESSION['officer_id'])) {
    header('Location: ../login.php');
    exit();
}

header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="outstanding_loan_balance.csv"');

$output = fopen('php://output', 'w');
fputcsv($output, ['Client Name', 'Loan Amount', 'Outstanding Balance']);

$sql = "SELECT 
            c.first_name, c.last_name, 
            l.principal_amount, 
            COALESCE(SUM(d.loan_payment), 0) AS total_paid
        FROM loans l
        JOIN clients c ON l.client_id = c.id
        LEFT JOIN deposits d ON l.client_id = d.client_id
        WHERE l.status = 'approved'
        GROUP BY l.id";

$stmt = $pdo->prepare($sql);
$stmt->execute();
$loans = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($loans as $loan) {
    $outstanding = $loan['principal_amount'] - $loan['total_paid'];
    if ($outstanding > 0) {
        fputcsv($output, [
            $loan['first_name'] . ' ' . $loan['last_name'],
            number_format($loan['principal_amount'], 2),
            number_format($outstanding, 2)
        ]);
    }
}

fclose($output);
exit;
