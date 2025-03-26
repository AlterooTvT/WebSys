<?php
require_once '../../../database/database.php';


requireAdmin();

$database = new Database();
$db = $database->getConnection();
$reports = new Reports($db);

// Get date range from request
$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-01');
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-t');

// Get reports data
$bookings_summary = $reports->getBookingsSummary($start_date, $end_date);
$revenue_summary = $reports->getRevenueSummary($start_date, $end_date);
$service_stats = $reports->getServiceStatistics($start_date, $end_date);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports & Analytics - Admin</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <div class="admin-container">
        <?php include 'includes/sidebar.php'; ?>
        
        <div class="main-content">
            <h1>Reports & Analytics</h1>
            
            <!-- Date Range Filter -->
            <div class="filter-section">
                <form method="GET" class="date-range-form">
                    <input type="date" name="start_date" value="<?php echo $start_date; ?>">
                    <input type="date" name="end_date" value="<?php echo $end_date; ?>">
                    <button type="submit">Filter</button>
                </form>
            </div>

            <!-- Summary Cards -->
            <div class="summary-cards">
                <div class="card">
                    <h3>Total Bookings</h3>
                    <p class="number"><?php echo $bookings_summary['total']; ?></p>
                </div>
                
                <div class="card">
                    <h3>Total Revenue</h3>
                    <p class="number">₱<?php echo number_format($revenue_summary['total'], 2); ?></p>
                </div>
                
                <div class="card">
                    <h3>Average Booking Value</h3>
                    <p class="number">₱<?php echo number_format($revenue_summary['average'], 2); ?></p>
                </div>
            </div>

            <!-- Charts -->
            <div class="charts-container">
                <div class="chart-box">
                    <h3>Bookings by Service</h3>
                    <canvas id="serviceChart"></canvas>
                </div>
                
                <div class="chart-box">
                    <h3>Monthly Revenue Trend</h3>
                    <canvas id="revenueChart"></canvas>
                </div>
            </div>

            <!-- Detailed Tables -->
            <div class="report-tables">
                <h3>Service Performance</h3>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Service</th>
                            <th>Bookings</th>
                            <th>Revenue</th>
                            <th>Average Value</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($service_stats as $stat): ?>
                            <tr>
                                <td><?php echo $stat['service_type']; ?></td>
                                <td><?php echo $stat['bookings']; ?></td>
                                <td>₱<?php echo number_format($stat['revenue'], 2); ?></td>
                                <td>₱<?php echo number_format($stat['average'], 2); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        // Initialize charts using Chart.js
        const serviceCtx = document.getElementById('serviceChart').getContext('2d');
        const revenueCtx = document.getElementById('revenueChart').getContext('2d');

        // Service Chart
        new Chart(serviceCtx, {
            type: 'pie',
            data: {
                labels: <?php echo json_encode(array_column($service_stats, 'service_type')); ?>,
                datasets: [{
                    data: <?php echo json_encode(array_column($service_stats, 'bookings')); ?>,
                    backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0']
                }]
            }
        });

        // Revenue Chart
        new Chart(revenueCtx, {
            type: 'line',
            data: {
                labels: <?php echo json_encode($revenue_summary['months']); ?>,
                datasets: [{
                    label: 'Monthly Revenue',
                    data: <?php echo json_encode($revenue_summary['values']); ?>,
                    borderColor: '#36A2EB',
                    tension: 0.1
                }]
            }
        });
    </script>
</body>
</html>