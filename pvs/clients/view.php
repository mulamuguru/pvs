<?php
require_once '../db.php';

// Get client ID
if (!isset($_GET['id'])) {
    echo "<p>No client selected.</p>";
    exit;
}
$client_id = $_GET['id'];

// Fetch client details
$stmt = $conn->prepare("
    SELECT c.*, g.name AS group_name, b.name AS branch_name, u.username AS officer_name 
    FROM clients c
    JOIN groups g ON c.group_id = g.id
    JOIN branches b ON g.branch_id = b.id
    JOIN users u ON g.officer_id = u.id
    WHERE c.id = ?
");
$stmt->execute([$client_id]);
$client = $stmt->fetch();

if (!$client) {
    echo "<p>Client not found.</p>";
    exit;
}

// DDA balance
$stmt = $conn->prepare("SELECT balance FROM dda WHERE client_id = ?");
$stmt->execute([$client_id]);
$dda = $stmt->fetch();
$dda_balance = $dda ? $dda['balance'] : 0;

// Total savings
$stmt = $conn->prepare("SELECT SUM(savings_amount) AS total_savings FROM deposits WHERE client_id = ?");
$stmt->execute([$client_id]);
$total_savings = $stmt->fetchColumn();

// Total loans (active + closed)
$stmt = $conn->prepare("SELECT COUNT(*) FROM loans WHERE client_id = ?");
$stmt->execute([$client_id]);
$total_loans = $stmt->fetchColumn();

// Loan history
$stmt = $conn->prepare("SELECT * FROM loans WHERE client_id = ? ORDER BY created_at DESC");
$stmt->execute([$client_id]);
$loan_history = $stmt->fetchAll();

// Savings history
$stmt = $conn->prepare("SELECT * FROM deposits WHERE client_id = ? ORDER BY deposit_date DESC");
$stmt->execute([$client_id]);
$savings_history = $stmt->fetchAll();

// Fetch guarantor details
$stmt = $conn->prepare("SELECT * FROM guarantors WHERE client_id = ?");
$stmt->execute([$client_id]);
$guarantor = $stmt->fetch();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Client Details - Planet Victoria</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../styles.css">
    <style>
        /* Original styles remain unchanged */
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
            transition: transform 0.3s ease;
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
            position: relative;
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

        /* New responsive additions */
        .menu-toggle {
            display: none;
            background: none;
            border: none;
            color: white;
            font-size: 24px;
            cursor: pointer;
            position: absolute;
            left: 10px;
            top: 10px;
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
            .table-responsive table {
                font-size: 14px;
            }
        }

        @media (max-width: 768px) {
            body {
                flex-direction: column;
            }
            
            #sidebar {
                position: fixed;
                left: -250px;
                top: 0;
                bottom: 0;
                z-index: 100;
                width: 250px;
            }
            
            #sidebar.active {
                left: 0;
            }
            
            .menu-toggle {
                display: block;
            }
            
            .overlay.active {
                display: block;
            }
            
            #main-content {
                margin-left: 0;
            }
            
            .top-nav {
                padding-left: 60px;
            }
        }

        @media (max-width: 576px) {
            .card-body p {
                margin-bottom: 0.5rem;
            }
            
            .table-responsive table {
                font-size: 12px;
            }
            
            .btn {
                padding: 0.25rem 0.5rem;
                font-size: 0.875rem;
            }
            
            h2, h4, h5 {
                font-size: 1.25rem;
            }
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
        <a href="../deposit/index.php">Deposits</a>
        <a href="../products/index.php">Products</a>
        <a href="../reports/index.php">Reports</a>
    </div>
    
    <div class="overlay"></div>

    <!-- Main Content -->
    <div id="main-content">
        
        <!-- Top Nav -->
        <div class="top-nav">
            <button class="menu-toggle">☰</button>
            <span>Welcome back, Officer</span>
        </div>

        <div class="container-fluid">
            <div class="d-flex justify-content-end mb-3">
                <button class="btn btn-success me-2" onclick="window.print()">Print</button>
                <button class="btn btn-primary">Export PDF</button>
            </div>
            <h2 class="mb-3"><?= htmlspecialchars($client['first_name'] . ' ' . $client['last_name']) ?> - Client Details</h2>

            <div class="card mb-4">
                <div class="card-body">
                    <p><strong>Phone:</strong> <?= htmlspecialchars($client['phone']) ?></p>
                    <p><strong>Group:</strong> <?= htmlspecialchars($client['group_name']) ?></p>
                    <p><strong>Branch:</strong> <?= htmlspecialchars($client['branch_name']) ?></p>
                    <p><strong>Officer:</strong> <?= htmlspecialchars($client['officer_name']) ?></p>
                    <p><strong>Created At:</strong> <?= htmlspecialchars($client['created_at']) ?></p>
                    <p><strong>DDA Balance:</strong> KES <?= number_format($dda_balance, 2) ?></p>
                    <p><strong>Total Savings:</strong> KES <?= number_format($total_savings, 2) ?></p>
                    <p><strong>Total Loans:</strong> <?= $total_loans ?></p>
                </div>
            </div>

            <!-- Guarantor Information -->
            <?php if ($guarantor): ?>
                <div class="card mb-4">
                    <div class="card-body">
                        <h5>Guarantor Information</h5>
                        <p><strong>Name:</strong> <?= htmlspecialchars($guarantor['guarantor_name']) ?></p>
                        <p><strong>ID Number:</strong> <?= htmlspecialchars($guarantor['id_number']) ?></p>
                        <p><strong>Village:</strong> <?= htmlspecialchars($guarantor['village']) ?></p>
                        <p><strong>Town:</strong> <?= htmlspecialchars($guarantor['town']) ?></p>
                        <p><strong>Sub-County:</strong> <?= htmlspecialchars($guarantor['sub_county']) ?></p>
                        <p><strong>County:</strong> <?= htmlspecialchars($guarantor['county']) ?></p>
                        <p><strong>Country:</strong> <?= htmlspecialchars($guarantor['country']) ?></p>
                        <p><strong>Phone:</strong> <?= htmlspecialchars($guarantor['phone']) ?></p>
                        <p><strong>Relationship:</strong> <?= htmlspecialchars($guarantor['relationship']) ?></p>
                    </div>
                </div>
            <?php else: ?>
                <p class="text-muted">No guarantor information available.</p>
            <?php endif; ?>

            <h4 class="mt-4">Savings History</h4>
            <?php if ($savings_history): ?>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Amount</th>
                                <th>Date</th>
                                <th>Reference</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($savings_history as $row): ?>
                                <tr>
                                    <td><?= number_format($row['savings_amount'], 2) ?></td>
                                    <td><?= $row['deposit_date'] ?></td>
                                    <td><?= $row['reference'] ?? 'N/A' ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p class="text-muted">No savings records found.</p>
            <?php endif; ?>

            <h4 class="mt-5">Loan History</h4>
            <?php if ($loan_history): ?>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Loan Amount</th>
                                <th>Product</th>
                                <th>Status</th>
                                <th>Start Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($loan_history as $loan): ?>
                                <tr>
                                    <td><?= number_format($loan['amount'], 2) ?></td>
                                    <td><?= htmlspecialchars($loan['product'] ?? 'N/A') ?></td>
                                    <td><?= ucfirst($loan['status']) ?></td>
                                    <td><?= $loan['created_at'] ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p class="text-muted">No loan records found.</p>
            <?php endif; ?>

            <a href="index.php" class="btn btn-secondary mt-4">← Back to Clients</a>
        </div>
    </div>

    <script>
        // Enhanced toggle functionality
        const menuToggle = document.querySelector('.menu-toggle');
        const sidebar = document.querySelector('#sidebar');
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
        const menuItems = document.querySelectorAll('#sidebar a');
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