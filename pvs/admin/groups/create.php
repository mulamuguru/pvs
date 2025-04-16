<?php
session_start();
require_once '../../db.php';

// Redirect if not logged in as admin
if (!isset($_SESSION['admin_id'])) {
    header('Location: ../../login.php');
    exit();
}

// Fetch all officers and branches for selection
$officersQuery = $conn->query("SELECT id, CONCAT(first_name, ' ', last_name) AS full_name FROM users");
$officers = $officersQuery->fetchAll(PDO::FETCH_ASSOC);

$branchesQuery = $conn->query("SELECT id, name FROM branches");
$branches = $branchesQuery->fetchAll(PDO::FETCH_ASSOC);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $location = $_POST['location'];
    $formation_date = $_POST['formation_date'];
    $type = $_POST['type'];
    $officer_id = $_POST['officer_id'];
    $branch_id = $_POST['branch_id'];

    // Insert the new group into the database
    $stmt = $conn->prepare("INSERT INTO groups (name, location, formation_date, type, created_at, officer_id, branch_id) VALUES (?, ?, ?, ?, NOW(), ?, ?)");
    $stmt->execute([$name, $location, $formation_date, $type, $officer_id, $branch_id]);

    // Redirect to groups page after successful creation
    header('Location: index.php');
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Create Group - Admin</title>
    <link rel="stylesheet" href="../styles.css">
    <link rel="icon" href="../favicon.png" type="image/png">
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f8fb;
        }

        .header {
            background: linear-gradient(to right, #2ecc71, #2980b9);
            color: white;
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            font-size: 22px;
            font-weight: bold;
        }

        .nav-btn {
            background: white;
            border: none;
            color: #2980b9;
            padding: 8px 16px;
            border-radius: 5px;
            font-weight: bold;
            cursor: pointer;
            margin-left: 10px;
        }

        .nav-btn a {
            color: #2980b9;
            text-decoration: none;
        }

        .main-area {
            display: flex;
            min-height: 100vh;
        }

        .sidebar {
            background-color: #34495e;
            color: white;
            padding: 20px;
            width: 220px;
            min-height: 100vh;
        }

        .menu-items {
            list-style: none;
            padding: 0;
        }

        .menu-items li {
            margin: 15px 0;
        }

        .menu-items a {
            color: white;
            text-decoration: none;
        }

        .menu-items a.active {
            color: #2ecc71;
        }

        .content {
            flex: 1;
            padding: 30px;
            margin-left: 240px; /* Ensures content is not under sidebar */
        }

        .form-card {
            background-color: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.05);
            max-width: 600px;
            margin: 0 auto;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            font-weight: bold;
            display: block;
            margin-bottom: 6px;
        }

        input[type="text"],
        input[type="date"],
        select {
            width: 100%;
            padding: 10px;
            border-radius: 6px;
            border: 1px solid #ccc;
        }

        .btn-submit {
            background-color: #27ae60;
            color: white;
            padding: 10px 20px;
            font-weight: bold;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            width: 100%;
        }

        .footer {
            background-color: #eee;
            text-align: center;
            padding: 15px;
            font-size: 14px;
            color: #555;
            position: relative;
            bottom: 0;
            width: 100%;
        }

        .alert {
            background-color: #f8d7da;
            color: #721c24;
            padding: 12px;
            border-radius: 6px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="logo">🌍 Planet Victoria</div>
        <nav class="top-nav">
            <button class="nav-btn">Home</button>
            <button class="nav-btn">Profile</button>
            <button class="nav-btn"><a href="../logout.php">Logout</a></button>
        </nav>
    </header>

    <div class="main-area">
        <div class="sidebar">
            <h3>Menu</h3>
            <ul class="menu-items">
                <li><a href="../index.php">Dashboard</a></li>
                <li><a href="../groups/index.php" class="active">Groups</a></li>
                <li><a href="../clients/index.php">Clients</a></li>
                <li><a href="../deposit/index.php">Deposit</a></li>
                <li><a href="../reports/index.php">Reports</a></li>
                <li><a href="../products/index.php">Products</a></li>
                <li><a href="../categories/index.php">Categories</a></li>
            </ul>
        </div>

        <main class="content">
            <h2>Create New Group</h2>

            <!-- Form starts here -->
            <form action="" method="POST" class="form-card">
                <div class="form-group">
                    <label for="name">Group Name</label>
                    <input type="text" id="name" name="name" required>
                </div>

                <div class="form-group">
                    <label for="location">Location</label>
                    <input type="text" id="location" name="location" required>
                </div>

                <div class="form-group">
                    <label for="formation_date">Formation Date</label>
                    <input type="date" id="formation_date" name="formation_date" required>
                </div>

                <div class="form-group">
                    <label for="type">Group Type</label>
                    <select id="type" name="type" required>
                        <option value="Savings">Savings</option>
                        <option value="Loan">Loan</option>
                        <option value="Savings and Loan">Savings and Loan</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="officer_id">Select Officer</label>
                    <select id="officer_id" name="officer_id" required>
                        <option value="">--Select Officer--</option>
                        <?php foreach ($officers as $officer): ?>
                            <option value="<?php echo $officer['id']; ?>"><?php echo htmlspecialchars($officer['full_name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="branch_id">Select Branch</label>
                    <select id="branch_id" name="branch_id" required>
                        <option value="">--Select Branch--</option>
                        <?php foreach ($branches as $branch): ?>
                            <option value="<?php echo $branch['id']; ?>"><?php echo htmlspecialchars($branch['name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <button type="submit" class="btn-submit">Create Group</button>
            </form>
        </main>
    </div>

    <footer class="footer">
        <p>© 2025 Planet Victoria. All rights reserved.</p>
    </footer>
</body>
</html>
