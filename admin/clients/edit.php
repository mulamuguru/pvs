<?php
require_once '../../db.php';
session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: ../../login.php");
    exit;
}

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$client_id = $_GET['id'];

// Fetch client details
$stmt = $conn->prepare("SELECT * FROM clients WHERE id = :id");
$stmt->execute([':id' => $client_id]);
$client = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$client) {
    header("Location: index.php");
    exit;
}

// Fetch guarantor details
$guarantor_stmt = $conn->prepare("SELECT * FROM guarantors WHERE client_id = :client_id");
$guarantor_stmt->execute([':client_id' => $client_id]);
$guarantor = $guarantor_stmt->fetch(PDO::FETCH_ASSOC);

// Dropdown data
$branches = $conn->query("SELECT * FROM branches")->fetchAll(PDO::FETCH_ASSOC);
$officers = $conn->query("SELECT * FROM users")->fetchAll(PDO::FETCH_ASSOC);
$groups = $conn->query("SELECT * FROM groups")->fetchAll(PDO::FETCH_ASSOC);

// On form submit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $phone = $_POST['phone'];
    $branch = $_POST['branch'];
    $group = $_POST['group'];
    $officer = $_POST['officer'];

    // Guarantor data
    $guarantor_data = [
        ':client_id' => $client_id,
        ':guarantor_name' => trim($_POST['guarantor_name']),
        ':id_number' => trim($_POST['guarantor_id_number']),
        ':village' => trim($_POST['guarantor_village']),
        ':town' => trim($_POST['guarantor_town']),
        ':sub_county' => trim($_POST['guarantor_sub_county']),
        ':county' => trim($_POST['guarantor_county']),
        ':country' => trim($_POST['guarantor_country']),
        ':phone' => trim($_POST['guarantor_phone']),
        ':relationship' => trim($_POST['guarantor_relationship']),
    ];

    // Update client
    $update_stmt = $conn->prepare(
        "UPDATE clients SET 
            first_name = :first_name, 
            last_name = :last_name, 
            phone = :phone, 
            branch = :branch, 
            group_id = :group, 
            officer_id = :officer 
         WHERE id = :id"
    );
    $update_stmt->execute([
        ':first_name' => $first_name,
        ':last_name' => $last_name,
        ':phone' => $phone,
        ':branch' => $branch,
        ':group' => $group,
        ':officer' => $officer,
        ':id' => $client_id
    ]);

    // Update or insert guarantor
    if ($guarantor) {
        $update_guarantor = $conn->prepare(
            "UPDATE guarantors SET 
                guarantor_name = :guarantor_name, 
                id_number = :id_number, 
                village = :village, 
                town = :town, 
                sub_county = :sub_county, 
                county = :county, 
                country = :country, 
                phone = :phone, 
                relationship = :relationship 
             WHERE client_id = :client_id"
        );
        $update_guarantor->execute($guarantor_data);
    } else {
        $insert_guarantor = $conn->prepare(
            "INSERT INTO guarantors 
                (client_id, guarantor_name, id_number, village, town, sub_county, county, country, phone, relationship) 
             VALUES 
                (:client_id, :guarantor_name, :id_number, :village, :town, :sub_county, :county, :country, :phone, :relationship)"
        );
        $insert_guarantor->execute($guarantor_data);
    }

    header("Location: index.php?msg=Client and Guarantor updated successfully");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Client | Admin Panel</title>
    <link rel="stylesheet" href="../../styles.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
<div class="wrapper d-flex">
    <!-- Sidebar -->
    <div class="sidebar text-white p-3" style="min-height: 100vh; width: 250px;">
        <h4 class="text-center mb-4">Planet Victoria</h4>
        <ul class="nav flex-column">
            <li class="nav-item"><a href="../index.php" class="nav-link text-white">🏠 Dashboard</a></li>
            <li class="nav-item"><a href="../groups/index.php" class="nav-link text-white">👥 Groups</a></li>
            <li class="nav-item"><a href="index.php" class="nav-link text-white">👤 Clients</a></li>
            <li class="nav-item"><a href="../loans/index.php" class="nav-link text-white">💰 Loans</a></li>
            <li class="nav-item"><a href="../deposit/index.php" class="nav-link text-white">💳 Deposits</a></li>
            <li class="nav-item"><a href="../products/index.php" class="nav-link text-white">📦 Products</a></li>
            <li class="nav-item"><a href="../reports/index.php" class="nav-link text-white">📊 Reports</a></li>
            <li class="nav-item mt-3"><a href="../../logout.php" class="nav-link text-danger">🚪 Logout</a></li>
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
            <h3>Edit Client</h3>

            <form method="POST">
                <div class="form-group">
                    <label for="first_name">First Name</label>
                    <input type="text" name="first_name" id="first_name" class="form-control" value="<?= htmlspecialchars($client['first_name']) ?>" required>
                </div>
                <div class="form-group">
                    <label for="last_name">Last Name</label>
                    <input type="text" name="last_name" id="last_name" class="form-control" value="<?= htmlspecialchars($client['last_name']) ?>" required>
                </div>
                <div class="form-group">
                    <label for="phone">Phone</label>
                    <input type="text" name="phone" id="phone" class="form-control" value="<?= htmlspecialchars($client['phone']) ?>" required>
                </div>
                <div class="form-group">
                    <label for="branch">Branch</label>
                    <select name="branch" id="branch" class="form-control" required>
                        <?php foreach ($branches as $b): ?>
                            <option value="<?= $b['id'] ?>" <?= $client['branch'] == $b['id'] ? 'selected' : '' ?>><?= htmlspecialchars($b['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="group">Group</label>
                    <select name="group" id="group" class="form-control" required>
                        <?php foreach ($groups as $g): ?>
                            <option value="<?= $g['id'] ?>" <?= $client['group_id'] == $g['id'] ? 'selected' : '' ?>><?= htmlspecialchars($g['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="officer">Officer</label>
                    <select name="officer" id="officer" class="form-control" required>
                        <?php foreach ($officers as $o): ?>
                            <option value="<?= $o['id'] ?>" <?= $client['officer_id'] == $o['id'] ? 'selected' : '' ?>><?= htmlspecialchars($o['username']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <hr>
                <h4>Guarantor Details</h4>
                <div class="form-group">
                    <label for="guarantor_name">Full Name</label>
                    <input type="text" name="guarantor_name" id="guarantor_name" class="form-control" value="<?= htmlspecialchars($guarantor['guarantor_name'] ?? '') ?>" required>
                </div>
                <div class="form-group">
                    <label for="guarantor_id_number">ID Number</label>
                    <input type="text" name="guarantor_id_number" id="guarantor_id_number" class="form-control" value="<?= htmlspecialchars($guarantor['id_number'] ?? '') ?>" required>
                </div>
                <div class="form-group">
                    <label for="guarantor_village">Village</label>
                    <input type="text" name="guarantor_village" id="guarantor_village" class="form-control" value="<?= htmlspecialchars($guarantor['village'] ?? '') ?>">
                </div>
                <div class="form-group">
                    <label for="guarantor_town">Town</label>
                    <input type="text" name="guarantor_town" id="guarantor_town" class="form-control" value="<?= htmlspecialchars($guarantor['town'] ?? '') ?>">
                </div>
                <div class="form-group">
                    <label for="guarantor_sub_county">Sub-County</label>
                    <input type="text" name="guarantor_sub_county" id="guarantor_sub_county" class="form-control" value="<?= htmlspecialchars($guarantor['sub_county'] ?? '') ?>">
                </div>
                <div class="form-group">
                    <label for="guarantor_county">County</label>
                    <input type="text" name="guarantor_county" id="guarantor_county" class="form-control" value="<?= htmlspecialchars($guarantor['county'] ?? '') ?>">
                </div>
                <div class="form-group">
                    <label for="guarantor_country">Country</label>
                    <input type="text" name="guarantor_country" id="guarantor_country" class="form-control" value="<?= htmlspecialchars($guarantor['country'] ?? '') ?>">
                </div>
                <div class="form-group">
                    <label for="guarantor_phone">Phone</label>
                    <input type="text" name="guarantor_phone" id="guarantor_phone" class="form-control" value="<?= htmlspecialchars($guarantor['phone'] ?? '') ?>">
                </div>
                <div class="form-group">
                    <label for="guarantor_relationship">Relationship</label>
                    <input type="text" name="guarantor_relationship" id="guarantor_relationship" class="form-control" value="<?= htmlspecialchars($guarantor['relationship'] ?? '') ?>">
                </div>
                <button type="submit" class="btn btn-primary">Update Client</button>
            </form>
        </div>
        <!-- End Page Content -->
         <br>
        <!-- Footer -->
        <footer class="bg-light text-center py-3 mt-auto border-top">
            &copy; <?= date('Y') ?> Planet Victoria. All rights reserved.
        </footer>
    </div>
</div>
</body>
</html>
