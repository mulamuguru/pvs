<?php
require_once '../../db.php';
session_start();

$selectedBranch = isset($_GET['branch']) ? $_GET['branch'] : null;
$officerFilter = isset($_GET['officer_id']) ? $_GET['officer_id'] : null;

// Prepare main query with filters
$sql = "SELECT g.*, b.name AS branch_name, u.username AS officer_name
        FROM groups g
        LEFT JOIN branches b ON g.branch_id = b.id
        LEFT JOIN users u ON g.officer_id = u.id";
$conditions = [];
$params = [];

if (!empty($selectedBranch)) {
    $conditions[] = "g.branch_id = ?";
    $params[] = $selectedBranch;
}
if (!empty($officerFilter)) {
    $conditions[] = "g.officer_id = ?";
    $params[] = $officerFilter;
}

if (!empty($conditions)) {
    $sql .= " WHERE " . implode(" AND ", $conditions);
}

$stmt = $conn->prepare($sql);
$stmt->execute($params);
$groups = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch branches for filter
$branchStmt = $conn->query("SELECT id, name FROM branches");
$branches = $branchStmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch officers for dropdown
$officerStmt = $conn->query("SELECT id, username FROM users WHERE role = 'officer'");
$officers = $officerStmt->fetchAll(PDO::FETCH_ASSOC);
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>All Groups | Admin - Planet Victoria</title>
    <link rel="stylesheet" href="../../styles.css">
    <style>
        /* Centering Modal */
    /* Centering Modal */
        .modal {
            display: none; 
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.7);
            z-index: 1000;
            justify-content: center;
            align-items: center;
        }

        .modal-content {
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            width: 450px;
            max-width: 90%;
            box-shadow: 0px 4px 20px rgba(0, 0, 0, 0.1);
            position: relative;
            opacity: 0;
            transform: translateY(-20px);
            animation: modalFadeIn 0.4s forwards;
        }

        @keyframes modalFadeIn {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .close-btn {
            position: absolute;
            top: 10px;
            right: 10px;
            font-size: 18px;
            cursor: pointer;
            color: #888;
        }

        select, input {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        select:focus, input:focus {
            border-color: #4CAF50;
            outline: none;
        }

        button.btn {
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            width: 100%;
        }

        button.btn:hover {
            background-color: #45a049;
        }

        .modal.show {
            display: flex;
            opacity: 1;
        }

        .modal-content h3 {
            margin-bottom: 20px;
        }

        .view-btn, .assign-btn {
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            margin-right: 10px;
        }

        .view-btn {
            background-color: #4CAF50;
            color: white;
        }

        .view-btn:hover {
            background-color: #45a049;
        }

        .assign-btn {
            background-color: #007BFF;
            color: white;
        }

        .assign-btn:hover {
            background-color: #0056b3;
        }
    
        /* Container for buttons */
        .btn-container {
            display: flex;                  /* Aligns buttons horizontally */
            justify-content: flex-start;    /* Align buttons to the left */
            align-items: center;            /* Aligns items in the center vertically */
        }

        /* Style for each button in the container */
        .btn-container a {
            padding: 10px 20px;             /* Keeps padding for the buttons */
            margin-right: 10px;             /* Add space between the buttons */
        }

        /* Remove the margin for the last button (Assign button) */
        .btn-container a:last-child {
            margin-right: 0;                /* No margin on the right for the last button */
        }



        /* Table header style */
        th {
            background-color: #f2f2f2; /* Light gray background */
            color: #333; /* Dark text color for contrast */
            padding: 10px;
            text-align: left;
            border-bottom: 2px solid #ddd;
        }

        th:hover {
            background-color: #e0e0e0; /* Darker gray on hover */
        }

        /* Table style */
        table {
            width: 100%;
            border-collapse: collapse;
        }

        td, th {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        table tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        table tr:hover {
            background-color: #f1f1f1;
        }
        /* Table row spacing */
        table tr {
            margin-bottom: 10px; /* Add space between rows */
        }

        table tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        table tr:hover {
            background-color: #f1f1f1;
        }


        /* nav buttons css */
        .nav-btn, .profile-button {
        padding: 10px 20px;
        background-color: #3498db;
        color: white;
        border: none;
        border-radius: 3px;
        font-size: 10px;
        cursor: pointer;
        text-decoration: none;
        display: inline-block;
        margin: 2px;
        transition: background-color 0.3s, transform 0.3s;
        }

    .nav-btn:hover, .profile-button:hover {
        background-color: #2980b9;
        transform: scale(1.05);
    }

    .nav-btn a, .profile-button a {
        color: white;
        text-decoration: none;
    }

    /* Specific style for logout button */
    .nav-btn:last-child {
        background-color: #e74c3c;
    }

    .nav-btn:last-child:hover {
        background-color: #c0392b;
    }
    .btn-create-group {
    display: inline-block;
    padding: 10px 20px;
    background-color: #2ecc71;
    color: white;
    text-decoration: none;
    font-weight: bold;
    border-radius: 5px;
    margin-bottom: 20px;
    transition: background-color 0.3s ease;
}

.btn-create-group:hover {
    background-color: #27ae60;
}

    </style>
</head>
<body>
<div class="container">
    <!-- Header and Sidebar (same as before) -->
    <header class="header">
            <div class="logo">🌍 Planet Victoria</div>
            <nav class="top-nav">
                <button class="nav-btn">Home</button>
                <a href="../profile.php" class="profile-button">Profile</a>
                <button class="nav-btn"> <a href="../../logout.php">Logout</a></button>
            </nav>
        </header>

        <!-- Sidebar with Toggle -->
        <div class="sidebar" id="sidebar">
            <h3>Menu</h3>
            <ul class="menu-items">
            <ul class="menu-items">
    <li><a href="../index.php" >Dashboard</a></li>
    <li><a href="index.php" class="active">Groups</a></li>
    <li><a href="../clients/index.php">Clients</a></li>
    <li><a href="../deposits/index.php">Deposit</a></li>
    <li><a href="../loans/index.php">Loans</a></li>
    <li><a href="../orders/index.php">Orders</a></li>
    <li><a href="../users/index.php">Users</a></li>
    <li><a href="../reports/index.php">Reports</a></li>
    <li><a href="../products/index.php">Products</a></li>
    <li><a href="../categories/index.php">Categories</a></li>
</ul>

            </ul>
        </div>

    <main class="content">
        <div class="page-header">
            <h2>All Groups</h2>
            <a href="create.php" class="btn-create-group">➕ Create Group</a>
        </div>

        <!-- Branch Filter -->
        <form method="GET" style="margin: 10px 0;">
            <label for="branch">Filter by Branch:</label>
            <select name="branch" id="branch" onchange="this.form.submit()">
                <option value="">-- All Branches --</option>
                <?php foreach ($branches as $branch): ?>
                    <option value="<?= $branch['id'] ?>" <?= ($selectedBranch == $branch['id']) ? 'selected' : '' ?>><?= htmlspecialchars($branch['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </form>
        <?php
        $officerFilter = isset($_GET['officer_id']) ? $_GET['officer_id'] : '';
        ?>
                <!-- Officer Filter Dropdown -->
                <form method="GET" style="margin-bottom: 20px;">
            <label for="officer_id">Filter by Officer:</label>
            <select name="officer_id" id="officer_id" onchange="this.form.submit()">
                <option value="">-- All Officers --</option>
                <?php foreach ($officers as $officer): ?>
                    <option value="<?= $officer['id']; ?>" <?= ($officerFilter == $officer['id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($officer['username']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </form>
        <?php if (count($groups) > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Group Name</th>
                        <th>Branch</th>
                        <th>Officer</th>
                        <th>Created At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($groups as $index => $group): ?>
                        <tr class="group-list">
                            <td><?= $index + 1 ?></td>
                            <td><?= htmlspecialchars($group['name']) ?></td>
                            <td><?= htmlspecialchars($group['branch_name'] ?? 'Unassigned') ?></td>
                            <td><?= htmlspecialchars($group['officer_name'] ?? 'Unassigned') ?></td>
                            <td><?= htmlspecialchars($group['created_at']) ?></td>
                            <td class="btn-container">
                                <a class="btn view-btn" href="view.php?id=<?= $group['id']; ?>">View</a>
                                <a class="btn assign-btn" href="javascript:void(0);" data-group-id="<?= $group['id']; ?>" data-group-name="<?= htmlspecialchars($group['name']); ?>" onclick="openAssignModal(this)">Assign</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No groups found for the selected branch.</p>
        <?php endif; ?>

        <!-- Assign Modal -->
        <div id="assignModal" class="modal">
            <div class="modal-content">
                <span class="close-btn">&times;</span>
                <h3>Assign Group: <span id="modalGroupName"></span></h3>
                <form id="assignForm" method="POST" action="assign_process.php">
                    <input type="hidden" name="group_id" id="groupIdInput">
                    <label for="branch_id">Select Branch:</label>
                    <select name="branch_id" id="branchSelect" required>
                        <option value="">-- Choose Branch --</option>
                        <?php
                        $branches = $conn->query("SELECT id, name FROM branches")->fetchAll(PDO::FETCH_ASSOC);
                        foreach ($branches as $branch):
                        ?>
                            <option value="<?= $branch['id']; ?>"><?= htmlspecialchars($branch['name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                    <label for="officer_id">Assign Officer:</label>
                    <select name="officer_id" id="officerSelect" required>
                        <option value="">-- Choose Officer --</option>
                        <?php
                        $officers = $conn->query("SELECT id, username FROM users WHERE role = 'officer'")->fetchAll(PDO::FETCH_ASSOC);
                        foreach ($officers as $officer):
                        ?>
                            <option value="<?= $officer['id']; ?>"><?= htmlspecialchars($officer['username']); ?></option>
                        <?php endforeach; ?>
                    </select>
                    <button type="submit" class="btn">Confirm Assignment</button>
                </form>
            </div>
        </div>
    </main>

    <footer class="footer">
        <p>© 2025 Planet Victoria. All rights reserved.</p>
    </footer>
</div>

<script>
// Open modal function
function openAssignModal(button) {
    var groupId = button.getAttribute('data-group-id');
    var groupName = button.getAttribute('data-group-name');
    
    document.getElementById('groupIdInput').value = groupId;
    document.getElementById('modalGroupName').textContent = groupName;

    var modal = document.getElementById('assignModal');
    modal.style.display = 'flex';
    setTimeout(() => modal.classList.add('show'), 10);
}

// Close modal function
document.querySelector('.close-btn').onclick = function() {
    const modal = document.getElementById('assignModal');
    modal.classList.remove('show');
    setTimeout(() => modal.style.display = 'none', 300);
};

window.onclick = function(e) {
    const modal = document.getElementById('assignModal');
    if (e.target == modal) {
        modal.classList.remove('show');
        setTimeout(() => modal.style.display = 'none', 300);
    }
};


function filterByOfficer() {
    const officerId = document.getElementById('officer-select').value;
    if (officerId) {
        window.location.href = `index.php?officer_id=${officerId}`;
    } else {
        window.location.href = `index.php`;
    }
}
</script>

</body>
</html>
