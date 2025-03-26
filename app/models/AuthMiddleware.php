<?php
namespace App\Middleware;

class AuthMiddleware {
    // Add constants for roles
    const ROLE_ADMIN = 'admin';
    const ROLE_CLIENT = 'client';

    /**
     * Check if user is logged in
     * @return bool
     */
    public static function isLoggedIn() {
        // Add session start check
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
    }

    /**
     * Check if user is admin
     * @return bool
     */
    public static function isAdmin() {
        return self::isLoggedIn() && 
               isset($_SESSION['role']) && 
               $_SESSION['role'] === self::ROLE_ADMIN;
    }

    /**
     * Check if user is client
     * @return bool
     */
    public static function isClient() {
        return self::isLoggedIn() && 
               isset($_SESSION['role']) && 
               $_SESSION['role'] === self::ROLE_CLIENT;
    }

    /**
     * Require login to access page
     * @return void
     */
    public static function requireLogin() {
        if (!self::isLoggedIn()) {
            $_SESSION['redirect_url'] = $_SERVER['REQUEST_URI'];
            $_SESSION['error'] = 'Please login to access this page.';
            header("Location: " . SITE_URL . "/app/views/auth/auth.php");
            exit();
        }
    }

    /**
     * Require admin role to access page
     * @return void
     */
    public static function requireAdmin() {
        self::requireLogin();
        if (!self::isAdmin()) {
            $_SESSION['error'] = 'Access denied. Admin privileges required.';
            header("Location: " . SITE_URL . "/app/views/client/dashboard.php");
            exit();
        }
    }

    /**
     * Redirect logged in users based on role
     * @return void
     */
    public static function redirectIfLoggedIn() {
        if (self::isLoggedIn()) {
            $redirectUrl = self::isAdmin() 
                ? SITE_URL . "/app/views/admin/dashboard.php"
                : SITE_URL . "/app/views/client/dashboard.php";
            
            header("Location: " . $redirectUrl);
            exit();
        }
    }

    /**
     * Get current user data
     * @return array|null
     */
    public static function getUser() {
        if (self::isLoggedIn()) {
            return [
                'user_id' => $_SESSION['user_id'] ?? null,
                'email' => $_SESSION['email'] ?? null,
                'name' => $_SESSION['name'] ?? null,
                'role' => $_SESSION['role'] ?? null,
                'created_at' => $_SESSION['created_at'] ?? null
            ];
        }
        return null;
    }

    /**
     * Clear all session data
     * @return void
     */
    public static function logout() {
        session_start();
        session_unset();
        session_destroy();
        header("Location: " . SITE_URL . "/app/views/auth/auth.php");
        exit();
    }

    /**
     * Validate CSRF token
     * @return bool
     */
    public static function validateCSRF() {
        if (!isset($_POST['csrf_token']) || !isset($_SESSION['csrf_token'])) {
            return false;
        }
        return hash_equals($_SESSION['csrf_token'], $_POST['csrf_token']);
    }
}