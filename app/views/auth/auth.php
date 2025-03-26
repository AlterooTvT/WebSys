<?php
// Include core requirements
require_once $_SERVER['DOCUMENT_ROOT'] . '/SIA/config/config.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/SIA/config/constants.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/SIA/database/database.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/SIA/app/models/User.php';

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$database = new Database();
$db = $database->getConnection();
$user = new User($db);

$message = '';
$action = isset($_GET['action']) ? $_GET['action'] : 'login';

// Store booking parameters in session if they exist
if (isset($_GET['next'])) {
    $_SESSION['redirect_after_login'] = $_GET['next'];
    if (isset($_GET['item_type']) && isset($_GET['item_id'])) {
        $_SESSION['redirect_after_login'] .= '?item_type=' . urlencode($_GET['item_type']) . 
                                           '&item_id=' . urlencode($_GET['item_id']);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['login'])) {
        $email = $_POST['email'];
        $password = $_POST['password'];
        
        $user_data = $user->login($email, $password);
        
        if ($user_data) {
            // Set session variables
            $_SESSION['user_id'] = $user_data['user_id'];
            $_SESSION['email'] = $user_data['email'];
            $_SESSION['role'] = $user_data['role'];
            $_SESSION['name'] = $user_data['first_name'] . ' ' . $user_data['last_name'];
            
            if (isset($_SESSION['redirect_after_login'])) {
                $redirect_url = $_SESSION['redirect_after_login'];
                unset($_SESSION['redirect_after_login']);
                header("Location: " . $redirect_url);
                exit();
            } else {
                // Default redirects based on role
                header("Location: " . ($user_data['role'] === 'admin' ? '../admin/main.php' : '../client/main.php'));
                exit();
            }
        } else {
            $message = "Invalid email or password!";
        }
    } elseif (isset($_POST['register'])) {
        // Validate password match
        if ($_POST['password'] !== $_POST['confirm_password']) {
            $message = "Passwords do not match!";
        } else {
            $user->email = $_POST['email'];
            $user->password = $_POST['password'];
            $user->first_name = $_POST['first_name'];
            $user->last_name = $_POST['last_name'];
            $user->phone = $_POST['phone'];
            $user->role = 'client'; // Default role for new registrations
            
            if (!$user->emailExists()) {
                if ($user->register()) {
                    // Redirect to login with success message
                    header("Location: auth.php?message=Registration successful! Please login.");
                    exit();
                } else {
                    $message = "Registration failed. Please try again.";
                }
            } else {
                $message = "Email already exists!";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login & Register</title>
    <link id="favicon" rel="icon" type="image/png" href="../../../public/assets/images/logo/logo.png">
    <link rel="stylesheet" href="../../../public/assets/css/login.css">
    <link rel="stylesheet" href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css">
</head>
<body>
<script src="../../../public/assets/js/icon.js"></script>

    <div id="container" class="container <?php echo $action === 'register' ? 'sign-up' : 'sign-in'; ?>">
        <div class="row">
            <!-- SIGN UP FORM -->
            <div class="col align-items-center flex-col sign-up">
                <div class="img-wrapper">
                    <img src="../../../public/assets/images/logo/logo.png" alt="Sign Up">
                </div>
                <div class="form-wrapper align-items-center">
                    <form class="form sign-up" method="POST">
                        <div class="input-group">
                            <i class='bx bx-user'></i>
                            <input type="text" name="first_name" placeholder="First Name" required>
                        </div>
                        <div class="input-group">
                            <i class='bx bx-user'></i>
                            <input type="text" name="last_name" placeholder="Last Name" required>
                        </div>
                        <div class="input-group">
                            <i class='bx bx-phone'></i>
                            <input type="tel" name="phone" placeholder="Phone Number" required>
                        </div>
                        <div class="input-group">
                            <i class='bx bx-mail-send'></i>
                            <input type="email" name="email" placeholder="Email" required>
                        </div>
                        <div class="input-group">
                            <i class='bx bxs-lock-alt'></i>
                            <input type="password" name="password" placeholder="Password" required>
                            <i class="fas fa-eye" id="togglePassword"></i>
                        </div>
                        <div class="input-group">
                            <i class='bx bxs-lock-alt'></i>
                            <input type="password" name="confirm_password" placeholder="Confirm password" required>
                            <i class="fas fa-eye" id="togglePassword"></i>
                        </div>
                        <button type="submit" name="register">Sign up</button>
                        <p>
                            <span>Already have an account?</span>
                            <b onclick="toggle()" class="pointer">Sign in here</b>
                        </p>
                    </form>
                </div>
            </div>
            
            <!-- SIGN IN FORM -->
            <div class="col align-items-center flex-col sign-in">
                <div class="img-wrapper">
                    <img src="../../../public/assets/images/logo/logo.png" alt="Login Image">
                </div>
                <div class="form-wrapper align-items-center">
                    <form class="form sign-in" method="POST">
                        <div class="input-group">
                            <i class='bx bx-mail-send'></i>
                            <input type="text" name="email" placeholder="Email/Phone Number" required>
                        </div>
                        <div class="input-group">
                            <i class='bx bxs-lock-alt'></i>
                            <input type="password" name="password" placeholder="Password" required>
                            <i class="fas fa-eye" id="togglePassword"></i>
                        </div>
                        <button type="submit" name="login">Sign in</button>
                        <p><b>Forgot password?</b></p>
                        <p>
                            <span>Don't have an account?</span>
                            <b onclick="toggle()" class="pointer">Sign up here</b>
                        </p>
                    </form>
                </div>
            </div>
        </div>

        <div class="row content-row">
            <div class="col align-items-center flex-col">
                <div class="text sign-in">
                    <h2>Welcome back!</h2>
                </div>
            </div>
            <div class="col align-items-center flex-col">
                <div class="text sign-up">
                    <h2>Join with us</h2>
                </div>
            </div>
        </div>

        <?php if (!empty($message)): ?>
            <div class="message <?php echo strpos($message, 'successful') !== false ? 'success' : 'error'; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const container = document.getElementById('container');
            const urlParams = new URLSearchParams(window.location.search);
            const action = urlParams.get('action');

            // Set initial state based on action parameter
            if (action === 'register') {
                container.classList.add('sign-up');
                container.classList.remove('sign-in');
            } else {
                container.classList.add('sign-in');
                container.classList.remove('sign-up');
            }

            // Update URL without page reload when toggling forms
            window.toggle = () => {
                container.classList.toggle('sign-in');
                container.classList.toggle('sign-up');
                const newAction = container.classList.contains('sign-up') ? 'register' : 'login';
                const newUrl = updateQueryStringParameter(window.location.href, 'action', newAction);
                window.history.pushState({ path: newUrl }, '', newUrl);
            }

            // Helper function to update URL parameters
            function updateQueryStringParameter(uri, key, value) {
                const re = new RegExp("([?&])" + key + "=.*?(&|$)", "i");
                const separator = uri.indexOf('?') !== -1 ? "&" : "?";
                if (uri.match(re)) {
                    return uri.replace(re, '$1' + key + "=" + value + '$2');
                }
                return uri + separator + key + "=" + value;
            }
        });
    </script>
</body>
</html>
