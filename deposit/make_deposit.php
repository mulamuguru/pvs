<?php
session_start();
require_once('../db.php');

// Ensure the user is logged in
if (!isset($_SESSION['officer_id'])) {
    header('Location: ../login.php');
    exit();
}

if (!isset($_GET['group_id'])) {
    header('Location: index.php');
    exit();
}

$groupId = $_GET['group_id'];

$stmt = $conn->prepare("SELECT name AS group_name FROM groups WHERE id = :group_id");
$stmt->execute([':group_id' => $groupId]);
$group = $stmt->fetch(PDO::FETCH_ASSOC);

$stmtClients = $conn->prepare("SELECT * FROM clients WHERE group_id = :group_id");
$stmtClients->execute([':group_id' => $groupId]);
$clients = $stmtClients->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $depositData = [];

    foreach ($_POST['client_id'] as $key => $clientId) {
        $depositData[] = [
            'client_id' => $clientId,
            'savings_amount' => $_POST['savings_amount'][$key],
            'loan_payment' => $_POST['loan_payment'][$key]
        ];
    }

    $_SESSION['deposit_data'] = $depositData;
    $_SESSION['selected_group_name'] = $group['group_name'] ?? '';
    header('Location: confirm_deposit.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Make Deposit - <?= htmlspecialchars($group['group_name']) ?></title>
    <link rel="stylesheet" href="../styles.css">
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            padding: 10px;
            border: 1px solid #ccc;
        }

        th {
            background-color: lightgray;
            text-align: left;
        }

        input[type="number"] {
            width: 100%;
            padding: 5px;
            box-sizing: border-box;
        }

        .btn-primary {
            margin-top: 20px;
            padding: 10px 16px;
            background-color: #28a745;
            color: white;
            border: none;
            cursor: pointer;
            font-weight: bold;
        }

        .btn-primary:hover {
            background-color: #218838;
        }

        tfoot td {
            font-weight: bold;
            background-color: #eef7ee;
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
        <h2>Make Deposit for Group: <?= htmlspecialchars($group['group_name']) ?></h2>

        <form action="make_deposit.php?group_id=<?= $groupId ?>" method="POST">
            <table>
                <thead>
                    <tr>
                        <th>Client Name</th>
                        <th>Savings Amount (KES)</th>
                        <th>Loan Payment (KES)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($clients as $client): ?>
                        <tr>
                            <td><?= htmlspecialchars($client['first_name'] . ' ' . $client['last_name']) ?></td>
                            <td><input type="number" name="savings_amount[]" step="0.01" min="0" required class="savings"></td>
                            <td><input type="number" name="loan_payment[]" step="0.01" min="0" required class="loan"></td>
                            <input type="hidden" name="client_id[]" value="<?= $client['id'] ?>">
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td>Total</td>
                        <td id="total-savings">0.00</td>
                        <td id="total-loans">0.00</td>
                    </tr>
                    <tr>
                        <td colspan="2">Grand Total</td>
                        <td id="grand-total">0.00</td>
                    </tr>
                </tfoot>
            </table>

            <button type="submit" class="btn-primary">Submit Deposits</button>
        </form>
    </main>
</div>

<script>
    const savingsInputs = document.querySelectorAll('.savings');
    const loanInputs = document.querySelectorAll('.loan');

    function calculateTotals() {
        let savingsTotal = 0;
        let loansTotal = 0;

        savingsInputs.forEach(input => {
            savingsTotal += parseFloat(input.value) || 0;
        });

        loanInputs.forEach(input => {
            loansTotal += parseFloat(input.value) || 0;
        });

        document.getElementById('total-savings').textContent = savingsTotal.toFixed(2);
        document.getElementById('total-loans').textContent = loansTotal.toFixed(2);
        document.getElementById('grand-total').textContent = (savingsTotal + loansTotal).toFixed(2);
    }

    savingsInputs.forEach(input => input.addEventListener('input', calculateTotals));
    loanInputs.forEach(input => input.addEventListener('input', calculateTotals));
</script>

</body>
</html>
