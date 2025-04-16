<?php
session_start();
require_once('../db.php');

// Check login
if (!isset($_SESSION['officer_id'])) {
    header('Location: ../login.php');
    exit();
}

// Set headers for download
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="principal_overdue_report.csv"');

// Open output stream
$output = fopen('php://output', 'w');

// Column headers
fputcsv($output, ['Client Name', 'Loan Amount', 'Overdue Balance']);

// Fetch data
$sql = "SELECT 
            c.first_name, c.last_name, 
            l.principal_amount, 
            COALESCE(SUM(d.loan_payment), 0) AS total_paid
        FROM loans l
        JOIN clients c ON l.client_id = c.id
        LEFT JOIN deposits d ON l.client_id = d.client_id
        WHERE l.status = 'approved'
        GROUP BY l.id";

$stmt = $conn->prepare($sql);
$stmt->execute();
$loans = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Process data
foreach ($loans as $loan) {
    $loanAmount = $loan['principal_amount'];
    $paid = $loan['total_paid'];
    $overdue = $loanAmount - $paid;

    if ($overdue > 0) {
        $clientName = $loan['first_name'] . ' ' . $loan['last_name'];
        fputcsv($output, [$clientName, number_format($loanAmount, 2), number_format($overdue, 2)]);
    }
}

fclose($output);
exit;
