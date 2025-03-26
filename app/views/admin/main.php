<?php
session_start();
require_once '../../../config/config.php';
require_once '../../../config/constants.php';
require_once '../../../database/database.php';
require_once '../../../app/models/Booking.php';
require_once '../../../app/models/Payment.php';
require_once '../../../app/models/AuthMiddleware.php';
require_once '../../../app/models/User.php';
require_once '../../../app/models/Message.php';

use App\Middleware\AuthMiddleware;

// Authentication checks
if (!AuthMiddleware::isLoggedIn()) {
    header("Location: " . SITE_URL . "/app/views/auth/auth.php");
    exit();
}

if (!AuthMiddleware::isAdmin()) {
    header("Location: " . SITE_URL . "/app/views/client/main.php");
    exit();
}
$adminName = isset($_SESSION['first_name']) ? $_SESSION['first_name'] : 'Admin';

$database = new Database();
$db = $database->getConnection();

$booking = new Booking($db);
$payment = new Payment($db);
$user = new User($db);
$message = new Message($db);

// Get the current page from URL parameter
$page = isset($_GET['page']) ? $_GET['page'] : 'dashboard';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pie Square Photobooth</title>
    <link rel="stylesheet" href="../../../public/assets/css/admin.css">
    <link id="favicon" rel="icon" type="image/png" href="../../../public/assets/images/logo/logo.png">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
      <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

</head>
<body>
<script src="../../../public/assets/js/icon.js"></script>
    <div class="dashboard-container">
        <!-- Sidebar -->
        <div class="sidebar">
        <a href="main.php">
            <div class="logo">
                <img src="../../../public/assets/images/logo/logo_360.jpg" alt="Logo">
            </div>
            </a>
            <nav class="nav-menu">
                <a href="?page=dashboard" class="nav-link <?php echo $page === 'dashboard' ? 'active' : ''; ?>">
                    <i class="fas fa-home"></i>
                    <span>Dashboard</span>
                </a>
                <a href="?page=bookings" class="nav-link <?php echo $page === 'bookings' ? 'active' : ''; ?>">
                    <i class="fas fa-calendar-alt"></i>
                    <span>Bookings</span>
                </a>
                <a href="?page=clients" class="nav-link <?php echo $page === 'clients' ? 'active' : ''; ?>">
                    <i class="fas fa-users"></i>
                    <span>Clients</span>
                </a>
                <a href="?page=services_packages" class="nav-link <?php echo $page === 'services_packages' ? 'active' : ''; ?>">
                    <i class="fas fa-box"></i>
                    <span>Services & Packages</span>
                </a>
                <a href="?page=gallery" class="nav-link <?php echo $page === 'gallery' ? 'active' : ''; ?>">
                    <i class="fas fa-images"></i>
                    <span>Gallery</span>
                </a>
                <a href="?page=payments" class="nav-link <?php echo $page === 'payments' ? 'active' : ''; ?>">
                    <i class="fas fa-credit-card"></i>
                    <span>Payments</span>
                </a>
                <a href="?page=reports" class="nav-link <?php echo $page === 'reports' ? 'active' : ''; ?>">
                    <i class="fas fa-chart-bar"></i>
                    <span>Reports</span>
                </a>
                <a href="?page=settings" class="nav-link <?php echo $page === 'settings' ? 'active' : ''; ?>">
                    <i class="fas fa-cog"></i>
                    <span>Settings</span>
                </a>
                <a href="?page=chat" class="nav-link <?php echo $page === 'chat' ? 'active' : ''; ?>">
                    <i class="fas fa-comments"></i>
                    <span>Chat</span>
                    <?php 
                    // Show unread message count if any
                    $unreadCount = $message->getUnreadCount($_SESSION['user_id']);
                    if ($unreadCount > 0): 
                    ?>
                        <span class="badge"><?php echo $unreadCount; ?></span>
                    <?php endif; ?>
                </a>
                <a href="../../../partials/logout.php" class="nav-link <?php echo $page === 'Logout' ? 'active' : ''; ?>">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Logout</span>
                </a>
            </nav>
        </div>

        <!-- Main Content Area -->
        <div class="main-content">
            <!-- Header -->
            <div class="header">
                <div class="welcome"><b>Welcome, <?php echo $_SESSION['name']; ?>!</b></div> 
                <div class="header-right">
                    <div class="search-bar">
                        <input type="text" placeholder="Search...">
                        <button><i class="fas fa-search"></i></button>
                    </div>
                    <div class="notification">
                        <i class="fas fa-bell"></i>
                    </div>
                </div>
            </div>

            <!-- Content Area -->
            <div class="content">
                <?php
                switch ($page) {
                    case 'dashboard':
                        include 'pages/dashboard.php';
                        break;
                    case 'bookings':
                        include 'pages/bookings.php';
                        break;
                    case 'clients':
                        include 'pages/clients.php';
                        break;
                    case 'services_packages':
                        include 'pages/services_packages.php';
                        break;
                    case 'gallery':
                        include 'pages/gallery.php';
                        break;
                    case 'payments':
                        include 'pages/payments.php';
                        break;
                    case 'reports':
                        include 'pages/reports.php';
                        break;
                    case 'chat':
                        include 'pages/chat.php';
                        break;
                    case 'settings':
                        include 'pages/settings.php';
                        break;
                    default:
                        include 'pages/dashboard.php';
                        break;
                }
                ?>
            </div>
        </div>
    </div>
</body>
</html>