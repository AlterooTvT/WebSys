<?php
require_once '../../../database/database.php';
require_once '../../../app/models/User.php';
require_once '../../../helpers/auth.php';

$database = new Database();
$db = $database->getConnection();
$user = new User($db);

$message = '';

if($_POST) {
    $email = $_POST['email'];
    $password = $_POST['password'];
    
    $user_data = $user->login($email, $password);
    
    if($user_data) {
        $_SESSION['user_id'] = $user_data['user_id'];
        $_SESSION['email'] = $user_data['email'];
        $_SESSION['role'] = $user_data['role'];
        $_SESSION['name'] = $user_data['first_name'] . ' ' . $user_data['last_name'];
        
        if($user_data['role'] === 'admin') {
            header("Location: ../admin/dashboard.php");
        } else {
            header("Location: dashboard.php");
        }
        exit();
    } else {
        $message = "Invalid email or password!";
    }
}
?>
