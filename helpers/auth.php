<?php
@session_start();

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function isAdmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

function requireLogin() {
    if(!isLoggedIn()) {
        header("Location: " . SITE_URL . "/app/views/auth/auth.php");
        exit();
    }
}

function requireAdmin() {
    if(!isAdmin()) {
        header("Location: ./index.php");
        exit();
    }
}
?>