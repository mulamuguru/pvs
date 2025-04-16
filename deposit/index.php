<?php
session_start();
require_once '../db.php';

$officerId = $_SESSION['officer_id'] ?? null;

$search = $_GET['search'] ?? '';
$officerId = $_SESSION['officer_id'];

$stmt = $conn->prepare("SELECT id, name  AS group_name FROM groups WHERE officer_id = :officer_id");
$stmt->execute(['officer_id' => $officerId]);
$groups = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Deposit - Select Group</title>
    <link rel="stylesheet" href="../styles.css">
    <style>
        .group-box {
            padding: 12px;
            border: 1px solid #ccc;
            border-radius: 6px;
            margin-bottom: 10px;
            background: #f9f9f9;
            cursor: pointer;
        }
        .group-box:hover {
            background: #e1f5e1;
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
        <h2>Select a Group for Deposits</h2>
        <form method="GET" style="margin-bottom: 20px;">
            <input type="text" name="search" placeholder="Search group name..." value="<?= htmlspecialchars($search) ?>" />
            <button type="submit">Search</button>
        </form>

        <?php if (count($groups) > 0): ?>
            <?php foreach ($groups as $group): ?>
                <div class="group-box" onclick="window.location.href='make_deposit.php?group_id=<?= $group['id'] ?>'">
                <strong><?= isset($group['group_name']) ? htmlspecialchars($group['group_name']) : 'Unnamed Group' ?></strong>

                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No groups found.</p>
        <?php endif; ?>
    </main>
</div>
</body>
</html>
