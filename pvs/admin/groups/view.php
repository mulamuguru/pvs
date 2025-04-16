<?php
require_once '../../db.php';

// Get group ID
if (!isset($_GET['id'])) {
    echo "<p>No group selected.</p>";
    exit;
}
$group_id = $_GET['id'];

// Fetch group details
$stmt = $conn->prepare("
    SELECT g.*, u.username AS officer_name, b.name AS branch_name
    FROM groups g
    JOIN branches b ON g.branch_id = b.id
    JOIN users u ON g.officer_id = u.id AND u.role = 'officer'
    WHERE g.id = ?
");
$stmt->execute([$group_id]);
$group = $stmt->fetch();



if (!$group) {
    echo "<p>Group not found.</p>";
    exit;
}

// Fetch group members
$stmt = $conn->prepare("SELECT * FROM clients WHERE group_id = ? AND clients.status = 'approved'");
$stmt->execute([$group_id]);
$members = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Group Details - Planet Victoria</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../../styles.css" rel="stylesheet">
    <style>
        body {
            display: flex;
            min-height: 100vh;
            margin: 0;
        }

        #sidebar {
            width: 250px;
            background: #2c3e50;
            color: white;
            padding: 20px;
        }

        #sidebar a {
            color: white;
            display: block;
            margin: 10px 0;
            text-decoration: none;
        }

        #sidebar a:hover {
            text-decoration: underline;
        }

        #main-content {
            flex-grow: 1;
            background: #f8f9fa;
        }

        .top-nav {
            background: #27ae60;
            color: white;
            padding: 10px 20px;
        }

        .container-fluid {
            padding: 20px;
        }

        .btn-primary {
            background: #007bff;
            border: none;
        }

        .btn-primary:hover {
            background: #0056b3;
        }

        .table thead {
            background: #2980b9;
            color: white;
        }
        body {
    margin: 0;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background: #f1f4f6;
    display: flex;
    min-height: 100vh;
}

#sidebar {
    width: 240px;
    background: #0f5132; /* Deep green (Planet Victoria theme) */
    color: #fff;
    padding: 20px;
    position: fixed;
    top: 0;
    bottom: 0;
    left: 0;
}

#sidebar h4 {
    font-size: 20px;
    margin-bottom: 20px;
    color: #fff;
}

#sidebar a {
    display: block;
    padding: 10px 15px;
    margin-bottom: 8px;
    color: #fff;
    text-decoration: none;
    border-radius: 4px;
    transition: background 0.3s ease;
}

#sidebar a:hover,
#sidebar a.active {
    background: #198754; /* Bootstrap green */
}

#main-content {
    margin-left: 240px;
    flex-grow: 1;
    background: #f8f9fa;
    padding-bottom: 60px;
}

.top-nav {
    background: #198754;
    color: white;
    padding: 15px 20px;
    font-size: 16px;
}

.container-fluid {
    padding: 20px;
}

.card {
    border: 1px solid #ddd;
    border-radius: 5px;
}

.btn-primary {
    background: #0d6efd;
    border: none;
}

.btn-primary:hover {
    background: #0b5ed7;
}

.table thead {
    background: #0d6efd;
    color: white;
}

    </style>
</head>
<body>

    <!-- Sidebar -->
    <div id="sidebar">
        <h4>Planet Victoria</h4>
        <a href="../index.php">Dashboard</a>
        <a href="../groups/index.php">Groups</a>
        <a href="../clients/index.php">Clients</a>
        <a href="../loans/index.php">Loans</a>
        <a href="../deposit/index.php">Deposits</a>
        <a href="../products/index.php">Products</a>
        <a href="../reports/index.php">Reports</a>
        <<a href="../categories/index.php">Categories</a>
    </div>

    <!-- Main Content -->
    <div id="main-content">
        <!-- Top Nav -->
        <div class="top-nav">
            <span>Welcome back, Admin</span>
        </div>

        <div class="container-fluid">
            <h2 class="mb-3"><?= htmlspecialchars($group['name']) ?> - Group Details</h2>

            <div class="card mb-4">
                <div class="card-body">
                    <p><strong>Branch:</strong> <?= htmlspecialchars($group['branch_name']) ?></p>
                    <p><strong>Officer:</strong> <?= htmlspecialchars($group['officer_name']) ?></p>
                    <p><strong>Created At:</strong> <?= htmlspecialchars($group['created_at']) ?></p>
                </div>
            </div>

            <h4 class="mt-4">Group Members</h4>

            <?php if (count($members) > 0): ?>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Phone</th>
                                <th>Joined</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($members as $member): ?>
                                <tr>
                                    <td><?= htmlspecialchars($member['first_name']) .''. htmlspecialchars($member['last_name'])?></td>
                                    <td><?= htmlspecialchars($member['phone']) ?></td>
                                    <td><?= htmlspecialchars($member['created_at']) ?></td>
                                    <td>
                                        <a href="../clients/view.php?id=<?= $member['id'] ?>" class="btn btn-sm btn-primary">View</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p class="text-muted">No members found in this group.</p>
            <?php endif; ?>

            <a href="index.php" class="btn btn-secondary mt-4">← Back to Groups</a>
        </div>
    </div>

</body>
</html>
