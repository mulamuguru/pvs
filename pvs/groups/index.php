<?php
require_once '../db.php';
session_start();

if (!isset($_SESSION['officer_id'])) {
    header("Location: ../login.php");
    exit;
}

$officer_id = $_SESSION['officer_id'];

// Fetch groups assigned to this officer
$stmt = $conn->prepare("SELECT * FROM groups WHERE officer_id = ?");
$stmt->execute([$officer_id]);
$groups = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Groups | Planet Victoria</title>
    <link rel="stylesheet" href="../styles.css">
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
            }
            
            .sidebar.active {
                left: 0;
            }
            
            .menu-toggle {
                display: block;
            }
            
            .content {
                padding-top: 70px;
            }
            
            table {
                width: 100%;
                display: block;
                overflow-x: auto;
            }
        }
        
        @media (max-width: 480px) {
            .header {
                padding: 15px 50px;
            }
            
            .btn {
                padding: 5px 10px;
                font-size: 14px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <header class="header">
            <button class="menu-toggle">☰</button>
            <div class="logo">🌍 Planet Victoria</div>
        </header>

        <div class="sidebar">
            <h3>Menu</h3>
            <ul class="menu-items">
                <li><a href="../index.php">Dashboard</a></li>
                <li><a href="index.php" class="active">Groups</a></li>
                <li><a href="../clients/index.php">Clients</a></li>
                <li><a href="../deposit/index.php">Deposit</a></li>
                <li><a href="../reports/index.php">Reports</a></li>
                <li><a href="../products/index.php">Products</a></li>
            </ul>
        </div>

        <main class="content">
            <h2>My Groups</h2>

            <?php if (count($groups) > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Group Name</th>
                            <th>Created</th>
                            <th>View</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($groups as $index => $group): ?>
                            <tr>
                                <td><?php echo $index + 1; ?></td>
                                <td><?php echo htmlspecialchars($group['name']); ?></td>
                                <td><?php echo htmlspecialchars($group['created_at']); ?></td>
                                <td><a class="btn" href="view.php?id=<?php echo $group['id']; ?>">View</a></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>You don't have any groups assigned yet.</p>
            <?php endif; ?>
        </main>

        <footer class="footer">
            <p>© 2025 Planet Victoria. All rights reserved.</p>
        </footer>
    </div>

    <script>
        // Add only this minimal JavaScript for the toggle
        document.querySelector('.menu-toggle').addEventListener('click', function() {
            document.querySelector('.sidebar').classList.toggle('active');
        });
    </script>
</body>
</html>