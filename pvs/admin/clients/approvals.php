<?php
require_once '../../db.php';
session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: ../../login.php");
    exit;
}

$query = "SELECT clients.*, u.username AS officer_name, g.name AS group_name, b.name AS branch_name
          FROM clients
          LEFT JOIN users u ON clients.officer_id = u.id
          LEFT JOIN groups g ON clients.group_id = g.id
          LEFT JOIN branches b ON clients.branch = b.id
          WHERE clients.status = 'pending'
          ORDER BY clients.created_at DESC";

$stmt = $conn->prepare($query);
$stmt->execute();
$clients = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Approve Clients | Admin Panel</title>
    <link rel="stylesheet" href="../../styles.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
<div class="wrapper d-flex">
    <!-- Sidebar -->
    <div class="sidebar text-white p-3" style="min-height: 100vh; width: 250px; background: linear-gradient(to bottom, #006400, #004080);">
        <h4 class="text-center mb-4">Planet Victoria</h4>
        <ul class="nav flex-column">
            <li class="nav-item"><a href="../index.php" class="nav-link text-white">🏠 Dashboard</a></li>
            <li class="nav-item"><a href="index.php" class="nav-link text-white">👤 All Clients</a></li>
            <li class="nav-item"><a href="approvals.php" class="nav-link active bg-primary text-white">🛂 Approve Clients</a></li>
            <li class="nav-item"><a href="../../logout.php" class="nav-link text-danger">🚪 Logout</a></li>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="content flex-grow-1 d-flex flex-column">
        <nav class="navbar navbar-light bg-light border-bottom">
            <span class="navbar-brand mb-0 h1">Admin Dashboard</span>
        </nav>

        <div class="container-fluid mt-4">
            <h3 class="mb-3">Pending Client Approvals</h3>

            <div class="table-responsive">
                <table class="table table-bordered table-striped table-light">
                    <thead class="thead-dark">
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Phone</th>
                            <th>Group</th>
                            <th>Branch</th>
                            <th>Officer</th>
                            <th>Date Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($clients): ?>
                            <?php foreach ($clients as $index => $client): ?>
                                <tr>
                                    <td><?= $index + 1 ?></td>
                                    <td><?= htmlspecialchars($client['first_name'] . ' ' . $client['last_name']) ?></td>
                                    <td><?= htmlspecialchars($client['phone']) ?></td>
                                    <td><?= htmlspecialchars($client['group_name']) ?></td>
                                    <td><?= htmlspecialchars($client['branch_name']) ?></td>
                                    <td><?= htmlspecialchars($client['officer_name']) ?></td>
                                    <td><?= date('Y-m-d', strtotime($client['created_at'])) ?></td>
                                    <td>
                                        <a href="approve.php?id=<?= $client['id'] ?>&status=approved" class="btn btn-success btn-sm">✅ Approve</a>
                                        <a href="approve.php?id=<?= $client['id'] ?>&status=declined" class="btn btn-danger btn-sm">❌ Decline</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="8" class="text-center">No pending clients.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <footer class="bg-light text-center py-3 mt-auto border-top">
            &copy; <?= date('Y') ?> Planet Victoria. All rights reserved.
        </footer>
    </div>
</div>
</body>
</html>
