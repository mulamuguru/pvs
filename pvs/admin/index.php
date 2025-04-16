<?php
// Prevent caching
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");
// Include database connection
require_once '../db.php';

// session to check user logged in
session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: ../login.php");
    exit;
}

// Fetch number of pending clients
$stmt = $conn->prepare("SELECT COUNT(*) FROM clients WHERE status = 'pending'");
$stmt->execute();
$pendingClients = $stmt->fetchColumn();

if (!isset($_SESSION['admin_id'])) {
    header("Location: ../login.php");
    exit;
}

$admin_id = $_SESSION['admin_id'];

// Fetch total groups
$groupsQuery = $conn->prepare("SELECT COUNT(*) AS total_groups FROM groups");
$groupsQuery->execute();
$groupsResult = $groupsQuery->fetch(PDO::FETCH_ASSOC);
$totalGroups = $groupsResult['total_groups'];

// Fetch total clients
$clientsQuery = $conn->prepare("SELECT COUNT(*) AS total_clients FROM clients WHERE clients.status = 'approved'
");
$clientsQuery->execute();
$clientsResult = $clientsQuery->fetch(PDO::FETCH_ASSOC);
$totalClients = $clientsResult['total_clients'];

// Fetch total outstanding loans (Principal Overdues)
$loansQuery = $conn->prepare("SELECT SUM(principal_amount) AS total_outstanding_loans FROM loans WHERE status = 'Active'"); // Assuming "Active" is the status for outstanding loans
$loansQuery->execute();
$loansResult = $loansQuery->fetch(PDO::FETCH_ASSOC);
$totalOutstandingLoans = $loansResult['total_outstanding_loans'];

// Fetch total deposits
$depositsQuery = $conn->prepare("SELECT SUM(savings_amount + loan_payment) AS total_deposits FROM deposits");
$depositsQuery->execute();
$depositsResult = $depositsQuery->fetch(PDO::FETCH_ASSOC);
$totalDeposits = $depositsResult['total_deposits'];

// Fetch total sales this month or within selected range
$currentMonthStart = date('Y-m-01');
$currentMonthEnd = date('Y-m-t');

$start_date = $_GET['start_date'] ?? $currentMonthStart;
$end_date = $_GET['end_date'] ?? $currentMonthEnd;

// Fetch loan sales (Active loans created within selected date range)
$loanSalesQuery = $conn->prepare("SELECT SUM(principal_amount) AS loan_sales FROM loans WHERE status = 'Active' AND DATE(created_at) BETWEEN :start AND :end");
$loanSalesQuery->execute(['start' => $start_date, 'end' => $end_date]);
$loanSalesResult = $loanSalesQuery->fetch(PDO::FETCH_ASSOC);
$loanSales = $loanSalesResult['loan_sales'] ?? 0;

// Fetch product sales (Active orders created within selected date range)
$productSalesQuery = $conn->prepare("SELECT SUM(total_amount) AS product_sales FROM orders WHERE status = 'Active' AND DATE(placed_at) BETWEEN :start AND :end");
$productSalesQuery->execute(['start' => $start_date, 'end' => $end_date]);
$productSalesResult = $productSalesQuery->fetch(PDO::FETCH_ASSOC);
$productSales = $productSalesResult['product_sales'] ?? 0;

// Combine both
$totalSales = $loanSales + $productSales;


// Get data for graphs (Monthly performance & portfolio distribution)
$performanceQuery = $conn->prepare("
    SELECT DATE_FORMAT(placed_at, '%Y-%m') AS month, SUM(total_amount) AS monthly_sales 
    FROM orders 
    WHERE status = 'Active' 
    GROUP BY month
    ORDER BY month ASC
");
$performanceQuery->execute();
$performanceData = $performanceQuery->fetchAll(PDO::FETCH_ASSOC);


// Portfolio at risk (PAR calculation: Example based on outstanding loans and total deposits)
$parQuery = $conn->prepare("SELECT SUM(principal_amount) AS overdue_loans FROM loans WHERE status = 'Overdue'");
$parQuery->execute();
$overdueLoans = $parQuery->fetch(PDO::FETCH_ASSOC)['overdue_loans'];

// Avoid division by zero for Portfolio at Risk (PAR)
$par = 0;  // Default value
if ($totalOutstandingLoans > 0) {
    $par = ($overdueLoans / $totalOutstandingLoans) * 100; // Example calculation for PAR (Portfolio at Risk)
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Planet Victoria</title>
    <link rel="stylesheet" href="../styles.css">
    <link rel="icon" href="favicon.png" type="image/png">
    <script src="script.js?v=<?php echo time(); ?>"></script>
    <style>
        /* Additional styles for new cards */
        .summary-card .card-icon.par-icon {
            background-color: #9b59b6;
            color: white;
        }

        .summary-card .card-icon.sales-icon {
            background-color: #f39c12;
            color: white;
        }

        .card-change {
            font-size: 14px;
            margin-top: 5px;
        }

        .dashboard-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            justify-content: space-between;
        }

        .chart-container {
            width: 100%;
            max-width: 100%;
            margin-bottom: 30px; /* space between charts */
            padding: 0;
        }

        #performance-chart, #portfolio-chart {
            width: 100%;
            height: 400px; /* You can adjust the height as needed */
        }

        .trend-up { color: #e74c3c; }
        .trend-down { color: #2ecc71; }
        /* Styling for Navigation Buttons */
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


        @media (max-width: 900px) {
            .summary-card {
                max-width: 100%;
            }

            .dashboard-grid {
                flex-direction: column;
                align-items: center;
            }

            .sidebar {
                position: absolute;
                left: -250px; /* Initially hidden */
                top: 0;
                height: 100%;
                width: 250px;
                background-color: #2c3e50;
                transition: left 0.3s ease;
                z-index: 999;
            }

            .sidebar.open {
                left: 0; /* Slide in when open */
            }

            .sidebar h3 {
                color: white;
                padding: 20px;
                text-align: center;
                font-size: 20px;
                margin: 0;
            }

            .menu-items {
                list-style: none;
                padding: 0;
                margin: 0;
            }

            .menu-items li {
                padding: 15px;
                text-align: center;
            }

            .menu-items li a {
                color: white;
                text-decoration: none;
                display: block;
            }

            .menu-items li a:hover {
                background-color: #34495e;
            }

            .toggle-btn {
                display: block;
                position: absolute;
                top: 20px;
                left: 20px;
                background-color: #2c3e50;
                color: white;
                border: none;
                padding: 10px;
                cursor: pointer;
                font-size: 20px;
                z-index: 1000;
            }

            .toggle-btn.open {
                left: 250px; /* Adjust for toggle button visibility */
            }

            .content {
                margin-left: 0;
                padding: 20px;
            }

            /* Hide profile and logout buttons in the top navbar on small screens */
            .top-nav {
                display: flex;
                justify-content: space-between;
            }

            .profile-button {
                display: none;
            }

            .nav-btn {
                font-size: 14px;
            }
        }

        /* For smaller devices (like phones) */
        @media (max-width: 600px) {
            .dashboard-grid {
                flex-direction: column;
            }

            .summary-card {
                width: 100%;
                margin-bottom: 10px;
            }
        }

    </style>
</head>
<body>
    <div class="container">
        <header class="header">
            <div class="logo">🌍 Planet Victoria</div>
            <nav class="top-nav">
                <button class="nav-btn">Home</button>
                <a href="profile.php" class="profile-button">Profile</a>
                <button class="nav-btn"> <a href="../logout.php">Logout</a></button>
            </nav>
        </header>

        <!-- Sidebar with Toggle -->
        <div class="sidebar" id="sidebar">
            <ul class="menu-items">
            <ul class="menu-items">
    <li><a href="index.php" class="active">Dashboard</a></li>
    <li><a href="groups/index.php">Groups</a></li>
    <li><a href="clients/index.php">Clients</a></li>
    <li class="nav-item"><a href="clients/approvals.php" class="nav-link  bg-primary text-white"> Approve Clients</a></li>
    <li><a href="deposit/index.php">Deposit</a></li>
    <li><a href="loans/index.php">Loans</a></li>
    <li><a href="orders/index.php">Orders</a></li>
    <li><a href="users/index.php">Users</a></li>
    <li><a href="reports/index.php">Reports</a></li>
    <li><a href="products/index.php">Products</a></li>
    <li><a href="categories/index.php">Categories</a></li>
</ul>

            </ul>
        </div>

        <!-- Toggle Button -->
        <button class="toggle-btn" id="toggleBtn">&#9776;</button>

        <main class="content">
            <h2>Dashboard Overview</h2>
            <div class="dashboard-grid">
                <!-- Summary Cards -->
                <div class="summary-card" id="groups-card">
                    <div class="card-icon">👥</div>
                    <div class="card-content">
                        <h3>Total Groups</h3>
                        <p class="card-value"><?php echo $totalGroups; ?></p>
                    </div>
                </div>

                <div class="summary-card" id="clients-card">
                    <div class="card-icon">👤</div>
                    <div class="card-content">
                        <h3>Total Clients</h3>
                        <p class="card-value"><?php echo $totalClients; ?></p>
                    </div>
                </div>

                <div class="summary-card" id="olb-card">
                    <div class="card-icon">💰</div>
                    <div class="card-content">
                        <h3>Outstanding Loans</h3>
                        <p class="card-value">Ksh. <?php echo number_format($totalOutstandingLoans); ?></p>
                    </div>
                </div>

                <div class="summary-card" id="pod-card">
                    <div class="card-icon">⚠️</div>
                    <div class="card-content">
                        <h3>Principal Overdues</h3>
                        <p class="card-value">Ksh. <?php echo number_format($overdueLoans); ?></p>
                    </div>
                </div>

                <div class="summary-card" id="par-card">
                    <div class="card-icon par-icon">📉</div>
                    <div class="card-content">
                        <h3>Portfolio at Risk</h3>
                        <p class="card-value"><?php echo round($par, 2); ?>%</p>
                    </div>
                </div>

                <div class="summary-card" id="deposits-card">
                    <div class="card-icon">🏦</div>
                    <div class="card-content">
                        <h3>Total Deposits</h3>
                        <p class="card-value">Ksh. <?php echo number_format($totalDeposits); ?></p>
                    </div>
                </div>
                <div class="summary-card" id="sales-card">
                    <div class="card-icon sales-icon"></div>
                    <div class="card-content">
                        <h3>Total Sales</h3>
                        <form method="GET" style="margin-bottom: 20px;">
                            <label for="start_date">From:</label>
                            <input type="date" name="start_date" value="<?php echo $start_date; ?>" required>
                            <label for="end_date">To:</label>
                            <input type="date" name="end_date" value="<?php echo $end_date; ?>" required>
                            <button type="submit">Filter</button>
                        </form>
                        <p class="card-value">Ksh. <?php echo number_format($totalSales); ?></p>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
    <div class="card text-white bg-warning h-100 shadow">
        <div class="card-body">
            <h5 class="card-title">Pending Loans Approvals</h5>
            <p class="card-text display-4"><?; ?></p>
        </div>
        <div class="card-footer bg-transparent border-top-0 text-right">
            <a href="loans/approvals.php" class="btn btn-sm btn-dark">Review Now</a>
        </div>
    </div>
</div>
                                <div class="col-md-4 mb-4">
                    <div class="card text-white bg-warning h-100 shadow">
                        <div class="card-body">
                            <h5 class="card-title">Pending Client Approvals</h5>
                            <p class="card-text display-4"><?= $pendingClients ?></p>
                        </div>
                        <div class="card-footer bg-transparent border-top-0 text-right">
                            <a href="clients/approvals.php" class="btn btn-sm btn-dark">Review Now</a>
                        </div>
                    </div>
                </div>
                <!-- Charts Section -->
                <div class="chart-container" id="performance-chart">
                    <h3>Monthly Performance</h3>
                    <canvas id="performanceCanvas"></canvas>
                </div>

                <div class="chart-container" id="portfolio-chart">
                    <h3>Portfolio Distribution</h3>
                    <canvas id="portfolioCanvas"></canvas>
                </div>
            </div>
        </main>

        <footer class="footer">
            <p>© 2025 Planet Victoria. All rights reserved.</p>
        </footer>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="script.js"></script>
    <script>
        // Initialize charts
        document.addEventListener('DOMContentLoaded', function() {
            // Performance Line Chart
            const perfCtx = document.getElementById('performanceCanvas').getContext('2d');
            new Chart(perfCtx, {
                type: 'line',
                data: {
                    labels: <?php echo json_encode(array_column($performanceData, 'month')); ?>,
                    datasets: [{
                        label: 'Monthly Sales',
                        data: <?php echo json_encode(array_column($performanceData, 'monthly_sales')); ?>,
                        borderColor: '#3498db',
                        backgroundColor: 'rgba(52, 152, 219, 0.1)',
                        tension: 0.3,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: { position: 'top' }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return 'Ksh. ' + value.toLocaleString();
                                }
                            }
                        }
                    }
                }
            });

            // Portfolio Pie Chart
            const portCtx = document.getElementById('portfolioCanvas').getContext('2d');
            new Chart(portCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Current Loans', 'Overdue Loans', 'Savings', 'Fixed Deposits'],
                    datasets: [{
                        data: [<?php echo $totalOutstandingLoans; ?>, <?php echo $overdueLoans; ?>, <?php echo $totalDeposits; ?>, <?php echo $totalDeposits; ?>],
                        backgroundColor: [
                            '#3498db',
                            '#e74c3c',
                            '#2ecc71',
                            '#f39c12'
                        ]
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: { position: 'right' },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return context.label + ': Ksh. ' + context.raw.toLocaleString();
                                }
                            }
                        }
                    }
                }
            });
        });

        // Sidebar Toggle
        const toggleBtn = document.getElementById('toggleBtn');
        const sidebar = document.getElementById('sidebar');
        toggleBtn.addEventListener('click', () => {
            sidebar.classList.toggle('open');
        });
        
    </script>
</body>
</html>
