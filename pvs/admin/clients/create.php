<?php
require_once '../../db.php';
session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: ../../login.php");
    exit;
}

// Fetch branches, officers, and groups for dropdown options
$branches = $conn->query("SELECT * FROM branches")->fetchAll(PDO::FETCH_ASSOC);
$officers = $conn->query("SELECT * FROM users")->fetchAll(PDO::FETCH_ASSOC);
$groups = $conn->query("SELECT * FROM groups")->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
   // Insert client
$stmt = $conn->prepare("INSERT INTO clients (
    first_name, last_name, gender, dob, id_number, phone, email, address, city,
    occupation, income, education, notes, group_id, branch, officer_id, status
) VALUES (
    :first_name, :last_name, :gender, :dob, :id_number, :phone, :email, :address, :city,
    :occupation, :income, :education, :notes, :group_id, :branch, :officer_id, :status
)");

$stmt->execute([
    ':first_name' => $_POST['first_name'],
    ':last_name' => $_POST['last_name'],
    ':gender' => $_POST['gender'],
    ':dob' => $_POST['dob'],
    ':id_number' => $_POST['id_number'],
    ':phone' => $_POST['phone'],
    ':email' => $_POST['email'],
    ':address' => $_POST['address'],
    ':city' => $_POST['city'],
    ':occupation' => $_POST['occupation'],
    ':income' => $_POST['income'],
    ':education' => $_POST['education'],
    ':notes' => $_POST['notes'],
    ':group_id' => $_POST['group'],
    ':branch' => $_POST['branch'],
    ':officer_id' => $_POST['officer'],
    ':status' => 'pending'
]);

$client_id = $conn->lastInsertId();

// Insert guarantor details
$stmt2 = $conn->prepare("INSERT INTO guarantors (
    client_id, guarantor_name, id_number, village, town,
    sub_county, county, country, phone, relationship
) VALUES (
    :client_id, :guarantor_name, :id_number, :village, :town,
    :sub_county, :county, :country, :phone, :relationship
)");

$stmt2->execute([
    ':client_id' => $client_id,
    ':guarantor_name' => trim($_POST['guarantor_name']),
    ':id_number' => trim($_POST['guarantor_id_number']),
    ':village' => trim($_POST['guarantor_village']),
    ':town' => trim($_POST['guarantor_town']),
    ':sub_county' => trim($_POST['guarantor_sub_county']),
    ':county' => trim($_POST['guarantor_county']),
    ':country' => trim($_POST['guarantor_country']),
    ':phone' => trim($_POST['guarantor_phone']),
    ':relationship' => trim($_POST['guarantor_relationship'])
]);

echo "<script>alert('Client created successfully and is pending approval. Guarantor details added.'); window.location.href='index.php';</script>";
exit;

}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Create New Client | Admin Panel</title>
    <link rel="stylesheet" href="../../styles.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
<div class="wrapper d-flex">
    <!-- Sidebar -->
    <div class="sidebar text-white p-3" style="min-height: 100vh; width: 250px;">
        <h4 class="text-center mb-4">Planet Victoria</h4>
        <ul class="nav flex-column">
            <li class="nav-item"><a href="../index.php" class="nav-link text-white"> Dashboard</a></li>
            <li class="nav-item"><a href="../groups/index.php" class="nav-link text-white"> Groups</a></li>
            <li class="nav-item"><a href="index.php" class="nav-link text-white"> Clients</a></li>
            <li class="nav-item"><a href="../deposit/index.php" class="nav-link text-white">Deposits</a></li>
            <li class="nav-item"><a href="../products/index.php" class="nav-link text-white"> Products</a></li>
            <li class="nav-item"><a href="../reports/index.php" class="nav-link text-white"> Reports</a></li>
            <li class="nav-item mt-3"><a href="../../logout.php" class="nav-link text-danger"> Logout</a></li>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="content flex-grow-1 d-flex flex-column">
        <nav class="navbar navbar-expand-lg navbar-light bg-light border-bottom">
            <span class="navbar-brand mb-0 h1">Admin Dashboard</span>
            <div class="ml-auto">
                <span class="navbar-text">Welcome, Admin</span>
            </div>
        </nav>

        <div class="container-fluid mt-4">
            <h3>Create New Client</h3>
            <form method="POST">
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>First Name</label>
                        <input type="text" name="first_name" class="form-control" required>
                    </div>
                    <div class="form-group col-md-6">
                        <label>Last Name</label>
                        <input type="text" name="last_name" class="form-control" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group col-md-4">
                        <label>Gender</label>
                        <select name="gender" class="form-control" required>
                            <option value="">Select Gender</option>
                            <option>Male</option>
                            <option>Female</option>
                            <option>Other</option>
                        </select>
                    </div>
                    <div class="form-group col-md-4">
                        <label>Date of Birth</label>
                        <input type="date" name="dob" class="form-control" required>
                    </div>
                    <div class="form-group col-md-4">
                        <label>ID Number</label>
                        <input type="text" name="id_number" class="form-control" required>
                    </div>
                </div>

                <div class="form-group">
                    <label>Phone</label>
                    <input type="text" name="phone" class="form-control" required>
                </div>

                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" class="form-control">
                </div>

                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>Address</label>
                        <input type="text" name="address" class="form-control">
                    </div>
                    <div class="form-group col-md-6">
                        <label>City</label>
                        <input type="text" name="city" class="form-control">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>Occupation</label>
                        <input type="text" name="occupation" class="form-control">
                    </div>
                    <div class="form-group col-md-6">
                        <label>Income</label>
                        <input type="number" name="income" class="form-control" step="0.01">
                    </div>
                </div>

                <div class="form-group">
                    <label>Education Level</label>
                    <input type="text" name="education" class="form-control">
                </div>

                <div class="form-group">
                    <label>Notes</label>
                    <textarea name="notes" class="form-control" rows="2"></textarea>
                </div>

                <div class="form-group">
                    <label>Branch</label>
                    <select name="branch" class="form-control" required>
                        <?php foreach ($branches as $b): ?>
                            <option value="<?= $b['id'] ?>"><?= htmlspecialchars($b['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>Group</label>
                    <select name="group" class="form-control" required>
                        <?php foreach ($groups as $g): ?>
                            <option value="<?= $g['id'] ?>"><?= htmlspecialchars($g['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>Officer</label>
                    <select name="officer" class="form-control" required>
                        <?php foreach ($officers as $o): ?>
                            <option value="<?= $o['id'] ?>"><?= htmlspecialchars($o['username']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <input type="hidden" name="status" value="pending">
    <hr>
    <h5 class="mt-4">Guarantor Details</h5>

    <div class="form-row">
        <div class="form-group col-md-6">
            <label>Guarantor Full Name</label>
            <input type="text" name="guarantor_name" class="form-control" required>
        </div>
        <div class="form-group col-md-6">
            <label>Guarantor ID Number</label>
            <input type="text" name="guarantor_id_number" class="form-control" required>
        </div>
    </div>

    <div class="form-row">
        <div class="form-group col-md-6">
            <label>Guarantor Village</label>
            <input type="text" name="guarantor_village" class="form-control">
        </div>
        <div class="form-group col-md-6">
            <label>Guarantor Town</label>
            <input type="text" name="guarantor_town" class="form-control">
        </div>
    </div>

    <div class="form-row">
        <div class="form-group col-md-6">
            <label>Sub County</label>
            <input type="text" name="guarantor_sub_county" class="form-control">
        </div>
        <div class="form-group col-md-6">
            <label>County</label>
            <input type="text" name="guarantor_county" class="form-control">
        </div>
    </div>

    <div class="form-row">
        <div class="form-group col-md-6">
            <label>Country</label>
            <input type="text" name="guarantor_country" class="form-control" value="Kenya">
        </div>
        <div class="form-group col-md-6">
            <label>Phone</label>
            <input type="text" name="guarantor_phone" class="form-control" required>
        </div>
    </div>

    <div class="form-group">
        <label>Relationship to Client</label>
        <input type="text" name="guarantor_relationship" class="form-control" required>
    </div>

    <input type="hidden" name="status" value="pending">
    <button type="submit" class="btn btn-primary">Create Client</button>
</form>
     </div> <br>
    <footer class="bg-light text-center py-3 mt-auto border-top">
            &copy; <?= date('Y') ?> Planet Victoria. All rights reserved.
        </footer>

</body>
</html>
