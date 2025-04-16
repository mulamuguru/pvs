<?php
session_start();
require_once('../db.php');

// Ensure the user is logged in
if (!isset($_SESSION['officer_id'])) {
    header('Location: ../login.php');
    exit();
}

// Check if deposit data is passed
if (!isset($_SESSION['deposit_data'])) {
    header('Location: index.php'); // Redirect if no data is passed
    exit();
}

$depositData = $_SESSION['deposit_data'];

// Totals
$totalSavings = 0;
$totalLoans = 0;

// Handle the deposit confirmation
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($depositData as $client) {
        // 1. Insert into deposits table
        $stmt = $conn->prepare('INSERT INTO deposits (client_id, savings_amount, loan_payment, deposit_date) 
                                VALUES (:client_id, :savings_amount, :loan_payment, NOW())');
        $stmt->execute([
            ':client_id' => $client['client_id'],
            ':savings_amount' => $client['savings_amount'],
            ':loan_payment' => $client['loan_payment'],
        ]);
    
        // 2. Check if the client already has a dda record
        $checkDda = $conn->prepare('SELECT id FROM dda WHERE client_id = :client_id');
        $checkDda->execute([':client_id' => $client['client_id']]);
        $dda = $checkDda->fetch();
    
        if ($dda) {
            // 3. Update existing dda record
            $updateDda = $conn->prepare('UPDATE dda 
                                         SET deposit_amount = deposit_amount + :loan_payment, 
                                             balance = balance + :loan_payment 
                                         WHERE client_id = :client_id');
            $updateDda->execute([
                ':loan_payment' => $client['loan_payment'],
                ':client_id' => $client['client_id'],
            ]);
        } else {
            // 4. Insert new dda record if not found
            $insertDda = $conn->prepare('INSERT INTO dda (client_id, deposit_amount, balance) 
                                         VALUES (:client_id, :loan_payment, :loan_payment)');
            $insertDda->execute([
                ':client_id' => $client['client_id'],
                ':loan_payment' => $client['loan_payment'],
            ]);
        }
    }
    
    unset($_SESSION['deposit_data']);
    header('Location: deposit_success.php');
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Confirm Deposits</title>
    <link rel="stylesheet" href="../styles.css">
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 25px;
        }
        th, td {
            padding: 10px;
            border: 1px solid #ccc;
        }
        th {
            background: lightgray;
        }
        .btn {
            padding: 10px 16px;
            margin-top: 15px;
            cursor: pointer;
        }
        .btn-success {
            background: green;
            color: white;
            border: none;
        }
        .btn-danger {
            background: red;
            color: white;
            border: none;
            margin-left: 10px;
        }
    </style>
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
            <li><a href="../reports/index.php">Reports</a></li>
            <li><a href="index.php" class="active">Deposits</a></li>
        </ul>
    </div>

    <main class="content">
        <h2>Confirm Deposits</h2>

        <form method="POST" action="confirm_deposit.php">
            <table>
                <thead>
                    <tr>
                        <th>Client ID</th>
                        <th>Savings Amount (KES)</th>
                        <th>Loan Payment (KES)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($depositData as $client): 
                        $totalSavings += $client['savings_amount'];
                        $totalLoans += $client['loan_payment'];
                    ?>
                        <tr>
                            <td><?= htmlspecialchars($client['client_id']) ?></td>
                            <td><?= number_format($client['savings_amount'], 2) ?></td>
                            <td><?= number_format($client['loan_payment'], 2) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <th>Total</th>
                        <th><?= number_format($totalSavings, 2) ?></th>
                        <th><?= number_format($totalLoans, 2) ?></th>
                    </tr>
                    <tr>
                        <th colspan="2">Grand Total</th>
                        <th><?= number_format($totalSavings + $totalLoans, 2) ?></th>
                    </tr>
                </tfoot>
            </table>

            <button type="submit" class="btn btn-success">Confirm Deposits</button>
            <a href="index.php" class="btn btn-danger">Cancel</a>
        </form>
    </main>
</div>
</body>
</html>
