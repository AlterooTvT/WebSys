<?php
require_once '../../../database/database.php';
require_once '../../../config/config.php';
require_once '../../../app/models/User.php';

$database = new Database();
$db = $database->getConnection();
$user = new User($db);

$message_profile = '';
$message_password = '';

// Get current admin data
$current_admin = $user->getUserById($_SESSION['user_id']);

if ($_POST) {
    // Update profile submission (from inside the modal)
    if (isset($_POST['update_profile'])) {
        $first_name = $_POST['first_name'];
        $last_name  = $_POST['last_name'];
        $email      = $_POST['email'];
        $phone      = $_POST['phone'];
        
        $query = "UPDATE users 
                  SET first_name = :first_name, last_name = :last_name, email = :email, phone = :phone, updated_at = NOW()
                  WHERE user_id = :user_id";
        $stmt = $db->prepare($query);
        $stmt->bindParam(":first_name", $first_name);
        $stmt->bindParam(":last_name", $last_name);
        $stmt->bindParam(":email", $email);
        $stmt->bindParam(":phone", $phone);
        $stmt->bindParam(":user_id", $_SESSION['user_id']);
        
        if($stmt->execute()) {
            $message_profile = "Profile updated successfully!";
            $current_admin = $user->getUserById($_SESSION['user_id']);
        } else {
            $message_profile = "Profile update failed.";
        }
    }
    
    // Change password submission (from inside the modal)
    if (isset($_POST['change_password'])) {
        $current_password = $_POST['current_password'];
        $new_password     = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];
        
        if (!password_verify($current_password, $current_admin['password'])) {
            $message_password = "Current password is incorrect.";
        } elseif ($new_password !== $confirm_password) {
            $message_password = "Password confirmation does not match.";
        } else {
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $query = "UPDATE users 
                      SET password = :password, updated_at = NOW()
                      WHERE user_id = :user_id";
            $stmt = $db->prepare($query);
            $stmt->bindParam(":password", $hashed_password);
            $stmt->bindParam(":user_id", $_SESSION['user_id']);
            if($stmt->execute()) {
                $message_password = "Password updated successfully!";
            } else {
                $message_password = "Password update failed.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Settings - Admin</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
    <style>
        /* Global page styling */
        .admin-container {
            max-width: 1000px;
            margin: 0 auto;
        }
        h1 {
            text-align: center;
            color: #333;
            margin-bottom: 30px;
        }
        .cards-container {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            justify-content: center;
        }
        /* Card style */
        .card {
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
            padding: 20px;
            width: 300px;
            text-align: center;
        }
        .card h2 {
            color: #3498db;
            margin-bottom: 15px;
        }
        .card p {
            color: #555;
            margin-bottom: 20px;
        }
        .btn-primary {
            background: #3498db;
            color: #fff;
            border: none;
            padding: 12px 25px;
            border-radius: 4px;
            cursor: pointer;
            transition: background 0.3s ease;
            font-size: 1em;
        }
        .btn-primary:hover {
            background: #2980b9;
        }
        /* Modal overlay */
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            display: none;
            align-items: center; /* vertically center content */
            justify-content: center; /* horizontally center content */
            z-index: 1000;
        }
        /* Modal content */
        .modal-content {
            background: #fff;
            padding: 30px;
            border-radius: 8px;
            width: 90%;
            max-width: 500px;
            position: relative;
            text-align: left;
        }
        .modal-content h3 {
            margin-top: 0;
            color: #333;
        }
        .close-modal {
            position: absolute;
            top: 15px;
            right: 20px;
            font-size: 1.5em;
            color: #aaa;
            cursor: pointer;
        }
        .close-modal:hover {
            color: #333;
        }
        form {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }
        form .form-group {
            display: flex;
            flex-direction: column;
        }
        form .form-group label {
            margin-bottom: 5px;
            font-weight: bold;
            color: #555;
        }
        form .form-group input {
            padding: 10px;
            border: 1px solid #ccd1d9;
            border-radius: 4px;
        }
        .alert {
            background: #dff0d8;
            color: #3c763d;
            padding: 10px 15px;
            border-radius: 4px;
            margin-bottom: 15px;
            text-align: center;
        }
    </style>
</head>
<body>
<div class="admin-container">
    <h1>Settings</h1>
    
    <!-- Cards Container -->
    <div class="cards-container">
        <!-- Edit Profile Card -->
        <div class="card">
            <h2>Edit Profile</h2>
            <p>Update your name, email and phone number.</p>
            <button class="btn-primary" onclick="showModal('profileModal')">Edit Profile</button>
        </div>
        <!-- Change Password Card -->
        <div class="card">
            <h2>Change Password</h2>
            <p>Update your account password.</p>
            <button class="btn-primary" onclick="showModal('passwordModal')">Change Password</button>
        </div>
    </div>
</div>

<!-- Soft Modal for Edit Profile -->
<div id="profileModal" class="modal-overlay">
    <div class="modal-content">
        <span class="close-modal" onclick="closeModal('profileModal')">&times;</span>
        <h3>Edit Profile</h3>
        <?php if($message_profile): ?>
            <div class="alert"><?php echo $message_profile; ?></div>
        <?php endif; ?>
        <form method="POST">
            <div class="form-group">
                <label>First Name</label>
                <input type="text" name="first_name" value="<?php echo htmlspecialchars($current_admin['first_name']); ?>" required>
            </div>
            <div class="form-group">
                <label>Last Name</label>
                <input type="text" name="last_name" value="<?php echo htmlspecialchars($current_admin['last_name']); ?>" required>
            </div>
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" value="<?php echo htmlspecialchars($current_admin['email']); ?>" required>
            </div>
            <div class="form-group">
                <label>Phone</label>
                <input type="tel" name="phone" value="<?php echo htmlspecialchars($current_admin['phone']); ?>" required>
            </div>
            <button type="submit" name="update_profile" class="btn-primary">Save Changes</button>
        </form>
    </div>
</div>

<!-- Soft Modal for Change Password -->
<div id="passwordModal" class="modal-overlay">
    <div class="modal-content">
        <span class="close-modal" onclick="closeModal('passwordModal')">&times;</span>
        <h3>Change Password</h3>
        <?php if($message_password): ?>
            <div class="alert"><?php echo $message_password; ?></div>
        <?php endif; ?>
        <form method="POST">
            <div class="form-group">
                <label>Current Password</label>
                <input type="password" name="current_password" required>
            </div>
            <div class="form-group">
                <label>New Password</label>
                <input type="password" name="new_password" required>
            </div>
            <div class="form-group">
                <label>Confirm New Password</label>
                <input type="password" name="confirm_password" required>
            </div>
            <button type="submit" name="change_password" class="btn-primary">Update Password</button>
        </form>
    </div>
</div>

<script>
    // Simple functions to display or hide the modal overlay
    function showModal(modalId) {
        document.getElementById(modalId).style.display = "flex";
    }
    function closeModal(modalId) {
        document.getElementById(modalId).style.display = "none";
    }
    
    // Optionally, close the modal if the user clicks outside the modal-content
    window.onclick = function(e) {
        const modals = document.getElementsByClassName("modal-overlay");
        for (let i = 0; i < modals.length; i++) {
            if (e.target == modals[i]) {
                modals[i].style.display = "none";
            }
        }
    }
</script>
</body>
</html>
