<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Piesquare Photobooth</title>
  <link id="favicon" rel="icon" type="image/png" href="public/assets/images/logo/logo.png">
  <link rel="stylesheet" href="public/assets/css/home.css">
</head>
<body>
  <script src="public/assets/js/icon.js"></script>
</body>
</html>


<?php
require_once 'config/config.php';
require_once 'config/constants.php';
require_once 'database/database.php';
require_once 'app/models/AuthMiddleware.php';

// Initialize session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (isset($_SESSION['user_id'])) {
    // Get user's role
    $database = new Database();
    $db = $database->getConnection();
    
    $query = "SELECT role FROM users WHERE user_id = ?";
    $stmt = $db->prepare($query);
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Redirect based on role
    if ($user) {
        switch ($user['role']) {
            case 'admin':
                header('Location: app/views/admin/main.php');
                exit();
                break;
            case 'client':
                header('Location: app/views/client/main.php');
                exit();
                break;
            default:
                // If role is not recognized, logout user
                session_destroy();
                header('Location: app/views/auth/auth.php?error=invalid_role');
                exit();
        }
    }
}

// If not logged in, show the public home page
$page = isset($_GET['page']) ? $_GET['page'] : 'home';

// Include header
include 'partials/header.php';
include 'pages/navigation.php';

// Main content routing
switch($page) {
    case 'home':
        include 'pages/home.php';
        break;
        
    case 'services':
        include 'pages/services.php';
        break;
        
    case 'gallery':
        include 'pages/gallery.php';
        break;
    case 'booking':
        include 'pages/booking.php';
        break;
        
    case 'contact':
        include 'pages/contact.php';
        break;
        
    case 'login':
        include 'app/views/auth/auth.php';
        break;
        
    case 'register':
        include 'app/views/auth/auth.php';
        break;
        
    default:
        include 'pages/404.php';
        break;
}

// Include footer
include 'partials/footer.php';
?>
