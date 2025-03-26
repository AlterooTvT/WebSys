<?php
require_once '../config/database.php';
require_once '../includes/session.php';
require_once '../includes/Booking.php';

requireLogin();

$database = new Database();
$db = $database->getConnection();
$booking = new Booking($db);

$message = '';
$step = isset($_GET['step']) ? $_GET['step'] : 1;

if($_POST) {
    switch($step) {
        case 1:
            $_SESSION['booking'] = [
                'service_type' => $_POST['service_type'],
                'event_type' => $_POST['event_type']
            ];
            header('Location: booking.php?step=2');
            break;
            
        case 2:
            $_SESSION['booking']['event_date'] = $_POST['event_date'];
            $_SESSION['booking']['time_slot'] = $_POST['time_slot'];
            
            if($booking->checkAvailability($_POST['event_date'], $_POST['time_slot'])) {
                header('Location: booking.php?step=3');
            } else {
                $message = "Selected date and time is not available!";
            }
            break;
            
        case 3:
            $_SESSION['booking']['location'] = $_POST['location'];
            $_SESSION['booking']['estimated_price'] = calculatePrice(
                $_SESSION['booking']['service_type'],
                $_SESSION['booking']['event_type']
            );
            header('Location: booking.php?step=4');
            break;
            
        case 4:
            // Create booking
            $booking->user_id = $_SESSION['user_id'];
            $booking->event_date = $_SESSION['booking']['event_date'];
            $booking->location = $_SESSION['booking']['location'];
            $booking->service_type = $_SESSION['booking']['service_type'];
            $booking->time_slot = $_SESSION['booking']['time_slot'];
            $booking->event_type = $_SESSION['booking']['event_type'];
            $booking->estimated_price = $_SESSION['booking']['estimated_price'];
            
            if($booking_id = $booking->create()) {
                unset($_SESSION['booking']);
                header('Location: payment.php?booking_id=' . $booking_id);
            } else {
                $message = "Error creating booking. Please try again.";
            }
            break;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Now - Photobooth System</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="booking-container">
        <!-- Progress Bar -->
        <div class="progress-bar">
            <div class="step <?php echo $step >= 1 ? 'active' : ''; ?>">1. Select Service</div>
            <div class="step <?php echo $step >= 2 ? 'active' : ''; ?>">2. Choose Date & Time</div>
            <div class="step <?php echo $step >= 3 ? 'active' : ''; ?>">3. Location Details</div>
            <div class="step <?php echo $step >= 4 ? 'active' : ''; ?>">4. Review & Pay</div>
        </div>

        <?php if($message): ?>
            <div class="alert"><?php echo $message; ?></div>
        <?php endif; ?>

        <form method="POST" class="booking-form">
            <?php switch($step): 
                case 1: ?>
                    <!-- Service Selection -->
                    <h2>Select Your Service</h2>
                    <div class="service-grid">
                        <div class="service-option">
                            <input type="radio" name="service_type" value="photobooth" required>
                            <label>Photobooth</label>
                        </div>
                        <!-- Add other service options -->
                    </div>
                    
                    <div class="form-group">
                        <label>Event Type</label>
                        <select name="event_type" required>
                            <option value="">Select Event Type</option>
                            <option value="wedding">Wedding</option>
                            <option value="birthday">Birthday</option>
                            <option value="corporate">Corporate Event</option>
                        </select>
                    </div>
                    <?php break; ?>

                <?php case 2: ?>
                    <!-- Date & Time Selection -->
                    <h2>Choose Date & Time</h2>
                    <div class="form-group">
                        <label>Event Date</label>
                        <input type="date" name="event_date" required min="<?php echo date('Y-m-d'); ?>">
                    </div>
                    
                    <div class="form-group">
                        <label>Time Slot</label>
                        <select name="time_slot" required>
                            <option value="">Select Time</option>
                            <option value="09:00:00">9:00 AM</option>
                            <option value="13:00:00">1:00 PM</option>
                            <option value="17:00:00">5:00 PM</option>
                        </select>
                    </div>
                    <?php break; ?>

                <?php case 3: ?>
                    <!-- Location Details -->
                    <h2>Event Location</h2>
                    <div class="form-group">
                        <label>Complete Address</label>
                        <textarea name="location" required></textarea>
                    </div>
                    <?php break; ?>

                <?php case 4: ?>
                    <!-- Review & Confirm -->
                    <h2>Review Your Booking</h2>
                    <div class="booking-summary">
                        <!-- Display booking summary -->
                    </div>
                    <?php break; ?>
            <?php endswitch; ?>

            <button type="submit" class="btn-primary">
                <?php echo $step < 4 ? 'Continue' : 'Confirm Booking'; ?>
            </button>
        </form>
    </div>

    <script src="../assets/js/booking.js"></script>
</body>
</html>