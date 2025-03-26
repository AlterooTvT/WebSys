<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../app/models/AuthMiddleware.php';

use App\Middleware\AuthMiddleware;
$user = AuthMiddleware::getUser();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? $pageTitle . ' - ' . SITE_NAME : SITE_NAME; ?></title>
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>/assets/css/style.css">
    <?php if (isset($extraCSS)) echo $extraCSS; ?>
</head>
<body>
            <?php if ($user): ?>
                <div class="dropdown">
                    <button class="dropbtn"><?php echo htmlspecialchars($user['name']); ?></button>
                    <div class="dropdown-content">
                        <?php if ($user['role'] === ROLE_ADMIN): ?>
                            <a href="<?php echo SITE_URL; ?>/app/views/admin/dashboard.php">Dashboard</a>
                        <?php else: ?>
                            <a href="<?php echo SITE_URL; ?>/app/views/client/profile.php">My Profile</a>
                            <a href="<?php echo SITE_URL; ?>/app/views/client/bookings.php">My Bookings</a>
                        <?php endif; ?>
                        <a href="<?php echo SITE_URL; ?>/app/views/auth/logout.php">Logout</a>
                    </div>
                </div>
            <?php else: ?>
            <?php endif; ?>
        </div>
    </nav>
