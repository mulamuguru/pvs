<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = unserialize(base64_decode($_POST['data']));
    $title = $_POST['title'] ?? 'Forecast_Report';

    header("Content-Type: application/vnd.ms-excel");
    header("Content-Disposition: attachment; filename={$title}.xls");

    echo "<table border='1'>";
    echo "<tr><th>Client Name</th><th>Monthly Payment</th><th>Available in DDA</th><th>Status</th></tr>";
    foreach ($data as $row) {
        echo "<tr>";
        echo "<td>{$row['first_name']} {$row['last_name']}</td>";
        echo "<td>KES " . number_format($row['monthly_payment'], 2) . "</td>";
        echo "<td>KES " . number_format($row['balance'], 2) . "</td>";
        echo "<td>Expected Arrears</td>";
        echo "</tr>";
    }
    echo "</table>";
}
?>
