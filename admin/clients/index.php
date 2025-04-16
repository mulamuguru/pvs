<?php
require_once '../../db.php';
session_start();

// Ensure the user is logged in as an admin
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../../login.php");
    exit;
}

// Get the filter parameters if set
$group_filter = isset($_GET['group']) ? $_GET['group'] : null;
$branch_filter = isset($_GET['branch']) ? $_GET['branch'] : null;
$officer_filter = isset($_GET['officer']) ? $_GET['officer'] : null;

// Build the query
$sql = "SELECT * FROM clients WHERE 1 AND clients.status = 'approved'"; // Start with basic query

// Apply filters if provided
if ($group_filter) {
    $sql .= " AND group_id = :group";
}
if ($branch_filter) {
    $sql .= " AND branch = :branch";
}
if ($officer_filter) {
    $sql .= " AND officer_id = :officer";
}

// Prepare and execute the query
$stmt = $conn->prepare($sql);

// Bind parameters if filters are set
if ($group_filter) {
    $stmt->bindParam(':group', $group_filter);
}
if ($branch_filter) {
    $stmt->bindParam(':branch', $branch_filter);
}
if ($officer_filter) {
    $stmt->bindParam(':officer', $officer_filter);
}

$stmt->execute();
$clients = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch groups, branches, and officers for dropdowns
$groups = $conn->query("SELECT * FROM groups")->fetchAll(PDO::FETCH_ASSOC);
$branches = $conn->query("SELECT * FROM branches")->fetchAll(PDO::FETCH_ASSOC);
$officers = $conn->query("SELECT * FROM users")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Clients | Admin Panel</title>
    <link rel="stylesheet" href="../../styles.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
<header class="header">
            <div class="logo">🌍 Planet Victoria</div>
            <nav class="top-nav">
                <button class="nav-btn">Home</button>
                <a href="../profile.php" class="profile-button">Profile</a>
                <button class="nav-btn"> <a href="../../logout.php">Logout</a></button>
            </nav>
        </header>
<div class="wrapper d-flex">
    <!-- Sidebar -->
    <div class="sidebar text-white p-3" style="min-height: 100vh; width: 250px;">
        <ul class="nav flex-column">
            <li class="nav-item"><a href="../index.php" class="nav-link text-white"> Dashboard</a></li>
            <li class="nav-item"><a href="../groups/index.php" class="nav-link text-white"> Groups</a></li>
            <li class="nav-item"><a href="index.php" class="nav-link  text-white "> Clients</a></li>
            <li class="nav-item"><a href="../loans/index.php" class="nav-link text-white"> Loans</a></li>
            <li class="nav-item"><a href="../orders/index.php" class="nav-link text-white">Orders</a></li>
            <li class="nav-item"><a href="../users/index.php" class="nav-link text-white">Users</a></li>
            <li class="nav-item"><a href="../deposit/index.php" class="nav-link text-white"> Deposits</a></li>
            <li class="nav-item"><a href="../products/index.php" class="nav-link text-white"> Products</a></li>
            <li class="nav-item"><a href="../categories/index.php" class="nav-link text-white">Categories</a></li>
            <li class="nav-item"><a href="../reports/index.php" class="nav-link text-white"> Reports</a></li>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="content flex-grow-1 d-flex flex-column">
        <!-- Navbar -->
        <nav class="navbar navbar-expand-lg navbar-light bg-light border-bottom">
            <span class="navbar-brand mb-0 h1">Admin Dashboard</span>
            <div class="ml-auto">
                <span class="navbar-text">Welcome, Admin</span>
            </div>
        </nav>

        <!-- Page Content -->
        <div class="container-fluid mt-4">
            <h3>Clients</h3>

            <!-- Create Client Button -->
            <a href="create.php" class="btn btn-success mb-4">➕ Create New Client</a>

            <!-- Filter Form -->
            <form method="GET" class="mb-4">
                <div class="row">
                    <div class="col-md-3">
                        <label for="group">Group</label>
                        <select name="group" id="group" class="form-control">
                            <option value="">Select Group</option>
                            <?php foreach ($groups as $group): ?>
                                <option value="<?= $group['id'] ?>" <?= $group['id'] == $group_filter ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($group['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="branch">Branch</label>
                        <select name="branch" id="branch" class="form-control">
                            <option value="">Select Branch</option>
                            <?php foreach ($branches as $branch): ?>
                                <option value="<?= $branch['id'] ?>" <?= $branch['id'] == $branch_filter ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($branch['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="officer">Officer</label>
                        <select name="officer" id="officer" class="form-control">
                            <option value="">Select Officer</option>
                            <?php foreach ($officers as $officer): ?>
                                <option value="<?= $officer['id'] ?>" <?= $officer['id'] == $officer_filter ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($officer['username']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-primary mt-4">Filter</button>
                    </div>
                </div>
            </form>

            <!-- Client List -->
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>First Name</th>
                        <th>Last Name</th>
                        <th>Phone</th>
                        <th>Branch</th>
                        <th>Group</th>
                        <th>Officer</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($clients as $client): ?>
                        <tr class="<?= ($client['status'] == 'inactive') ? 'table-danger' : 'table-light' ?>">
                            <td><?= $client['id'] ?></td>
                            <td><?= htmlspecialchars($client['first_name']) ?></td>
                            <td><?= htmlspecialchars($client['last_name']) ?></td>
                            <td><?= htmlspecialchars($client['phone']) ?></td>
                            <td><?= htmlspecialchars($client['branch']) ?></td>
                            <td><?= htmlspecialchars($client['group_id']) ?></td>
                            <td><?= htmlspecialchars($client['officer_id']) ?></td>
                            <td>
                                <a href="view.php?id=<?= $client['id'] ?>" class="btn btn-info btn-sm">View</a>
                                <a href="edit.php?id=<?= $client['id'] ?>" class="btn btn-warning btn-sm">Edit</a>
                                <a href="delete.php?id=<?= $client['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this client?')">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Footer -->
        <footer class="bg-light text-center py-3 mt-auto border-top">
            &copy; <?= date('Y') ?> Planet Victoria. All rights reserved.
        </footer>
    </div>
</div>
</body>
</html>
