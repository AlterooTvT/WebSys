<?php
session_start();
require_once '../../../config/config.php';
require_once '../../../config/constants.php';
require_once '../../../database/database.php';
require_once '../../../app/models/Booking.php';
require_once '../../../app/models/Payment.php';
require_once '../../../app/models/User.php';
require_once '../../../app/models/Message.php';
require_once '../../../app/models/AuthMiddleware.php';

use App\Middleware\AuthMiddleware;

// Authentication checks
if (!AuthMiddleware::isLoggedIn()) {
    header("Location: " . SITE_URL . "/app/views/auth/auth.php");
    exit();
}

if (!AuthMiddleware::isClient()) {
    header("Location: " . SITE_URL . "/app/views/admin/main.php");
    exit();
}

$database = new Database();
$db = $database->getConnection();

$booking = new Booking($db);
$payment = new Payment($db);
$user = new User($db);
$message = new Message($db);

// Get the current page from URL parameter
$page = isset($_GET['page']) ? $_GET['page'] : 'dashboard';
$userName = isset($_SESSION['first_name']) ? $_SESSION['first_name'] : 'User';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pie Square Photobooth</title>
    <link rel="stylesheet" href="../../../public/assets/css/client.css"> 
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
                <a href="?page=booking" class="nav-link <?php echo $page === 'booking' ? 'active' : ''; ?>">
                    <i class="fas fa-calendar-plus"></i>
                    <span>New Booking</span>
                </a>
                <a href="?page=payments" class="nav-link <?php echo $page === 'payments' ? 'active' : ''; ?>">
                    <i class="fas fa-credit-card"></i>
                    <span>Payments</span>
                </a>
                <a href="?page=gallery" class="nav-link <?php echo $page === 'gallery' ? 'active' : ''; ?>">
                    <i class="fas fa-images"></i>
                    <span>Gallery</span>
                <a href="?page=history" class="nav-link <?php echo $page === 'history' ? 'active' : ''; ?>">
                    <i class="fas fa-history"></i>
                    <span>History</span>
                </a>
                <a href="?page=chat" class="nav-link <?php echo $page === 'chat' ? 'active' : ''; ?>">
                    <i class="fas fa-comments"></i>
                    <span>Chat</span>
                </a>
                <a href="?page=profile" class="nav-link <?php echo $page === 'profile' ? 'active' : ''; ?>">
                    <i class="fas fa-user"></i>
                    <span>Profile</span>
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
                        <i class="fas fa-envelope"></i>
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
                    case 'booking':
                        include 'pages/booking.php';
                        break;
                    case 'payments':
                        include 'pages/payments.php';
                        break;
                    case 'payments_details':
                        include 'pages/payment_details.php';
                        break;
                    case 'gallery':
                        include 'pages/gallery.php';
                        break;
                    case 'history':
                        include 'pages/history.php';
                        break;
                    case 'chat':
                        include 'pages/chat.php';
                        break;
                    case 'profile':
                        include 'pages/profile.php';
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