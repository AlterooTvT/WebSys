<nav class="navbar">
    <div class="logo">
        <a href="?page=home">
        <img src="public/assets/images/logo/logo.png" alt="Pie Square Photobooth">
        </a>
    </div>
    <div class="nav-links">
        <!-- Use just ?page= for the routing -->
        <a href="?page=home" class="<?php echo $page === 'home' ? 'active' : ''; ?>">Home</a>
        <a href="?page=services" class="<?php echo $page === 'services' ? 'active' : ''; ?>">Services</a>
        <a href="?page=gallery" class="<?php echo $page === 'gallery' ? 'active' : ''; ?>">Gallery</a>
        <a href="?page=booking" class="<?php echo $page === 'booking' ? 'active' : ''; ?>">Booking</a>
        <a href="?page=contact" class="<?php echo $page === 'contact' ? 'active' : ''; ?>">Contact Us</a>
        
        <?php if ($user): ?>
            <div class="user-menu">
                <button class="user-menu-btn">
                    Welcome, <?php echo htmlspecialchars($user['first_name']); ?> ▼
                </button>
                <div class="user-menu-content">
                    <?php if ($user['role'] === 'admin'): ?>
                        <a href="?page=admin">Admin Dashboard</a>
                    <?php else: ?>
                        <a href="?page=profile">My Profile</a>
                        <a href="?page=bookings">My Bookings</a>
                    <?php endif; ?>
                    <a href="../partials/logout.php">Logout</a>
                </div>
            </div>
        <?php else: ?>
            <a href="app/views/auth/auth.php" class="login-btn">Log In</a>
        <?php endif; ?>
    </div>
</nav>
<link rel="stylesheet" href="public/assets/css/nav.css">