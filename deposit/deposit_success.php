<?php
session_start();
if (!isset($_SESSION['officer_id'])) {
    header('Location: ../login.php');
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Deposit Success</title>
    <link rel="stylesheet" href="../styles.css">
    <style>
        .success-box {
            background: #e6ffe6;
            border: 1px solid #b2d8b2;
            padding: 25px;
            text-align: center;
            margin-top: 40px;
            border-radius: 8px;
        }
        .success-box h2 {
            color: green;
        }
        .btn {
            padding: 10px 16px;
            margin: 10px;
            border: none;
            cursor: pointer;
            border-radius: 5px;
        }
        .btn-primary {
            background: #2e86de;
            color: white;
        }
        .btn-secondary {
            background: #28a745;
            color: white;
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
            <li><a href="../loans/loans.php">Loans</a></li>
            <li><a href="../products/index.php">Products</a></li>
            <li><a href="../reports/index.php">Reports</a></li>
            <li><a href="index.php" class="active">Deposits</a></li>
        </ul>
    </div>

    <main class="content">
        <div class="success-box">
            <h2>✅ Deposit Recorded Successfully!</h2>
            <p>Your deposits have been saved and updated in the system.</p>

            <a href="../index.php" class="btn btn-primary">Return to Dashboard</a>
            <a href="index.php" class="btn btn-secondary">Make Another Deposit</a>
        </div>
    </main>
</div>
</body>
</html>
