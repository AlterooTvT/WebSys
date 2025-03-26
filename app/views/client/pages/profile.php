<?php
$message = '';
$user_data = $user->getUserById($_SESSION['user_id']);

if ($_POST) {
    if (isset($_POST['update_profile'])) {
        // Handle profile update
    }
    if (isset($_POST['change_password'])) {
        // Handle password change
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>User Profile</title>
  <style>
    /* Container styling */
    .profile-container {
      max-width: 800px;
      margin: 50px auto;
      background: #ffffff;
      padding: 30px;
      border-radius: 8px;
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
      font-family: 'Arial', sans-serif;
    }

    /* Alert message styling */
    .alert {
      background-color: #f8d7da;
      color: #842029;
      padding: 12px 20px;
      border-radius: 5px;
      margin-bottom: 15px;
      border: 1px solid #f5c2c7;
    }

    /* Section headings */
    .profile-section h3 {
      border-bottom: 2px solid #3498db;
      color: #333;
      padding-bottom: 10px;
      margin-bottom: 20px;
    }

    /* Form styles */
    .profile-form,
    .password-form {
      display: flex;
      flex-wrap: wrap;
      gap: 20px;
    }
    .form-group {
      flex: 1 1 45%;
      display: flex;
      flex-direction: column;
    }
    .form-group label {
      font-weight: bold;
      margin-bottom: 8px;
      color: #555;
    }
    .form-group input {
      padding: 10px;
      border: 1px solid #ccc;
      border-radius: 4px;
      font-size: 1em;
    }
    .form-group input[disabled] {
      background-color: #eaeaea;
    }

    /* Button styles */
    .btn-primary {
      background-color: #3498db;
      color: #fff;
      padding: 12px 25px;
      border: none;
      border-radius: 4px;
      cursor: pointer;
      margin-top: 20px;
      transition: background 0.3s ease;
    }
    .btn-primary:hover {
      background-color: #2980b9;
    }
    
    /* Responsive adjustments */
    @media (max-width: 600px) {
        .form-group {
            flex: 1 1 100%;
        }
    }
  </style>
</head>
<body>
<div class="profile-container">
    <?php if($message): ?>
        <div class="alert"><?php echo $message; ?></div>
    <?php endif; ?>

    <div class="profile-section">
        <h3>Personal Information</h3>
        <form method="POST" class="profile-form">
            <div class="form-group">
                <label>First Name</label>
                <input type="text" name="first_name" value="<?php echo $user_data['first_name']; ?>" required>
            </div>
            
            <div class="form-group">
                <label>Last Name</label>
                <input type="text" name="last_name" value="<?php echo $user_data['last_name']; ?>" required>
            </div>
            
            <div class="form-group">
                <label>Email</label>
                <input type="email" value="<?php echo $user_data['email']; ?>" disabled>
            </div>
            
            <div class="form-group">
                <label>Phone</label>
                <input type="tel" name="phone" value="<?php echo $user_data['phone']; ?>" required>
            </div>
            
            <button type="submit" name="update_profile" class="btn-primary">Update Profile</button>
        </form>
    </div>

    <div class="profile-section">
        <h3>Change Password</h3>
        <form method="POST" class="password-form">
            <!-- Password change form fields -->
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
            <button type="submit" name="change_password" class="btn-primary">Change Password</button>
        </form>
    </div>
</div>
</body>
</html>
