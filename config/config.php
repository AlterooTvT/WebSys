<?php
// Database Configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'photobooth_system');
define('DB_USER', 'root');
define('DB_PASS', '');

// Application Configuration
define('SITE_NAME', 'Photobooth System');
define('SITE_URL', 'http://localhost/SIA');
define('UPLOAD_PATH', $_SERVER['DOCUMENT_ROOT'] . '/SIA-1ST/assets/images/uploads/');
define('GALLERY_PATH', UPLOAD_PATH . 'gallery/');
define('PAYMENT_PROOFS_PATH', UPLOAD_PATH . 'payments/');

// Email Configuration
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USER', 'piesquarephoto@gmail.com');
define('SMTP_PASS', 'your-app-password');