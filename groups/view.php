<?php
session_start();
require_once '../db.php';

// Redirect if not logged in
if (!isset($_SESSION['officer_id'])) {
    header('Location: ../login.php');
    exit();
}

// Get group ID
if (!isset($_GET['id'])) {
    echo "Group ID missing.";
    exit();
}
$groupId = $_GET['id'];

// Fetch group details
$stmt = $conn->prepare("SELECT * FROM groups WHERE id = ? ");
$stmt->execute([$groupId]);
$group = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$group) {
    echo "Group not found.";
    exit();
}

// Fetch clients in this group
$stmt = $conn->prepare("SELECT * FROM clients WHERE group_id = ? AND clients.status = 'approved'");
$stmt->execute([$groupId]);
$clients = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($group['name']); ?> - Group Members</title>
    <link rel="stylesheet" href="../styles.css">
    <link rel="icon" href="../favicon.png" type="image/png">
    <style>
        /* Add only these new styles - existing CSS remains unchanged */
        .menu-toggle {
            display: none;
            background: none;
            border: none;
            color: white;
            font-size: 24px;
            cursor: pointer;
            position: absolute;
            left: 10px;
            top: 15px;
            z-index: 101;
        }
        
        .overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(0,0,0,0.5);
            z-index: 99;
        }
        
        @media (max-width: 992px) {
            .styled-table th:nth-child(4),
            .styled-table td:nth-child(4) {
                display: none;
            }
        }
        
        @media (max-width: 768px) {
            .container {
                grid-template-columns: 1fr;
            }
            
            .sidebar {
                position: fixed;
                left: -250px;
                top: 0;
                bottom: 0;
                z-index: 100;
                transition: left 0.3s ease;
                width: 250px;
            }
            
            .sidebar.active {
                left: 0;
            }
            
            .menu-toggle {
                display: block;
            }
            
            .overlay.active {
                display: block;
            }
            
            .content {
                padding-top: 70px;
            }
            
            .styled-table {
                width: 100%;
                display: block;
                overflow-x: auto;
                white-space: nowrap;
            }
            
            .top-nav {
                position: absolute;
                right: 10px;
                top: 15px;
            }
            
            .nav-btn {
                padding: 5px 8px;
                font-size: 14px;
            }
        }
        
        @media (max-width: 576px) {
            .header {
                padding: 15px 50px;
            }
            
            .btn-small {
                padding: 4px 6px;
                font-size: 12px;
            }
            
            .styled-table th,
            .styled-table td {
                padding: 6px 4px;
                font-size: 13px;
            }
            
            .styled-table th:nth-child(3),
            .styled-table td:nth-child(3) {
                display: none;
            }
            
            h2 {
                font-size: 1.3rem;
            }
        }
    </style>
</head>
<body>
<div class="container">
    <header class="header">
        <button class="menu-toggle">☰</button>
        <div class="logo">🌍 Planet Victoria</div>
        <nav class="top-nav">
            <button class="nav-btn">Home</button>
            <button class="nav-btn">Profile</button>
            <button class="nav-btn"><a href="../logout.php">Logout</a></button>
        </nav>
    </header>

    <div class="sidebar">
        <h3>Menu</h3>
        <ul class="menu-items">
            <li><a href="../index.php">Dashboard</a></li>
            <li><a href="../groups/index.php" class="active">Groups</a></li>
            <li><a href="../clients/index.php">Clients</a></li>
            <li><a href="../deposit/index.php">Deposit</a></li>
            <li><a href="../reports/index.php">Reports</a></li>
            <li><a href="../products/index.php">Products</a></li>
        </ul>
    </div>
    
    <div class="overlay"></div>

    <main class="content">
        <h2><?php echo htmlspecialchars($group['name']); ?> - Members</h2>

        <?php if (count($clients) > 0): ?>
            <table class="styled-table">
                <thead>
                    <tr>
                        <th>Full Name</th>
                        <th>Phone</th>
                        <th>Gender</th>
                        <th>ID Number</th>
                        <th>View</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($clients as $client): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($client['first_name'] . ' ' . $client['last_name']); ?></td>
                        <td><?php echo htmlspecialchars($client['phone']); ?></td>
                        <td><?php echo htmlspecialchars($client['gender']); ?></td>
                        <td><?php echo htmlspecialchars($client['id_number']); ?></td>
                        <td><a class="btn-small" href="../clients/view.php?id=<?php echo $client['id']; ?>">View</a></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No members found in this group.</p>
        <?php endif; ?>
    </main>

    <footer class="footer">
        <p>© 2025 Planet Victoria. All rights reserved.</p>
    </footer>
</div>

<script>
    // Enhanced toggle functionality
    const menuToggle = document.querySelector('.menu-toggle');
    const sidebar = document.querySelector('.sidebar');
    const overlay = document.querySelector('.overlay');
    
    menuToggle.addEventListener('click', function() {
        sidebar.classList.toggle('active');
        overlay.classList.toggle('active');
    });
    
    overlay.addEventListener('click', function() {
        sidebar.classList.remove('active');
        overlay.classList.remove('active');
    });
    
    // Close sidebar when clicking on menu items (for mobile)
    const menuItems = document.querySelectorAll('.menu-items a');
    menuItems.forEach(item => {
        item.addEventListener('click', function() {
            if (window.innerWidth <= 768) {
                sidebar.classList.remove('active');
                overlay.classList.remove('active');
            }
        });
    });
</script>
</body>
</html>