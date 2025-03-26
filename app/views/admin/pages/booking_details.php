<?php
session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . '/SIA/database/database.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/SIA/app/models/Booking.php';

// Check if user is admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../../auth/auth.php");
    exit();
}

if (!isset($_GET['booking_id'])) {
    echo "No booking selected.";
    exit();
}

$booking_id = intval($_GET['booking_id']);

$database = new Database();
$db = $database->getConnection();
$bookingModel = new Booking($db);

// Fetch the booking using the booking ID, including services and packages
$booking = $bookingModel->getBookingDetails($booking_id);

if (!$booking) {
    echo "Booking not found.";
    exit();
}

// Use stored start_time and end_time directly
$startTime = $booking['start_time'] ?? '';
$endTime = $booking['end_time'] ?? '';
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Booking Details</title>
    <style>
        body {
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f8f8;
            margin: 0;
            padding: 20px;
        }
        .details-container {
            max-width: 800px;
            margin: 0 auto;
            background: #fff;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        .details-container h2 {
            text-align: center;
            color: #333;
        }
        .section {
            margin-bottom: 20px;
        }
        .section h3 {
            margin-bottom: 10px;
            color: #444;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
        }
        .detail {
            margin-bottom: 8px;
        }
        .detail span.label {
            font-weight: bold;
            display: inline-block;
            width: 200px;
        }
        .back-btn {
            display: inline-block;
            padding: 10px 20px;
            background: #3498db;
            color: #fff;
            text-decoration: none;
            border-radius: 4px;
            text-align: center;
        }
        .back-btn:hover {
            background: #2980b9;
        }
        img.reference-image {
            max-width: 100%;
            height: auto;
        }
        /* Optionally style the price breakdown table or list */
        .price-breakdown .detail {
            display: flex;
            justify-content: space-between;
            padding: 4px 0;
        }
        </style>
</head>
<body>
<div class="details-container">
    <h2>Booking Details</h2>
    
    <!-- Section 1: General Information -->
    <div class="section">
        <h3>General Information</h3>
        <div class="detail">
            <span class="label">Event Date:</span>
            <?php echo htmlspecialchars($booking['event_date']); ?>
        </div>
        <div class="detail">
            <span class="label">Location:</span>
            <?php echo htmlspecialchars($booking['location']); ?>
        </div>
        <div class="detail">
            <span class="label">Start Time:</span>
            <?php echo htmlspecialchars($startTime); ?>
        </div>
        <div class="detail">
            <span class="label">End Time:</span>
            <?php echo htmlspecialchars($endTime); ?>
        </div>
        <div class="detail">
            <span class="label">Event Type:</span>
            <?php echo htmlspecialchars($booking['event_type']); ?>
        </div>
        <div class="detail">
            <span class="label">Notes:</span>
            <?php echo nl2br(htmlspecialchars($booking['notes'] ?? 'None provided.')); ?>
        </div>
    </div>
    
    
    <!-- Section 3a: Booked Packages -->
    <div class="section">
        <h3>Booked Packages</h3>
        <?php if (!empty($booking['packages']) && is_array($booking['packages'])): ?>
            <ul>
                <?php foreach ($booking['packages'] as $package): ?>
                    <li>
                        <strong><?php echo htmlspecialchars($package['name']); ?></strong>
                        - ₱<?php echo number_format($package['price'], 2); ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p>No packages associated with this booking.</p>
        <?php endif; ?>
    </div>

    <!-- Section 3b: Booked Services -->
    <div class="section">
        <h3>Booked Services</h3>
        <?php if (!empty($booking['services']) && is_array($booking['services'])): ?>
            <ul>
                <?php foreach ($booking['services'] as $service): ?>
                    <li>
                        <strong><?php echo htmlspecialchars($service['name']); ?></strong>
                        - ₱<?php echo number_format($service['price'], 2); ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p>No services associated with this booking.</p>
        <?php endif; ?>
    </div>


    <!-- Section 4: Price Breakdown -->
    <div class="section">
        <h3>Price Breakdown</h3>
        <div class="detail">
            <span class="label">Estimated Price:</span>
            ₱<?php echo number_format($booking['estimated_price'], 2); ?>
        </div>
        <?php if (!empty($booking['final_price'])): ?>
        <div class="detail">
            <span class="label">Final Price:</span>
            ₱<?php echo number_format($booking['final_price'], 2); ?>
        </div>
        <?php endif; ?>
    </div>
    
    <!-- Section 5: Reference Image -->
    <?php if (!empty($booking['reference_image'])): ?>
        <div class="section">
            <h3>Reference Image</h3>
            <img class="reference-image" src="/SIA/public/images/references/<?php echo htmlspecialchars($booking['reference_image']); ?>" alt="Reference Image">
        </div>
    <?php endif; ?>

    <div style="text-align:center;">
            <a class="back-btn" href="../main.php?page=bookings">Back to Bookings</a>
        </div>
</div>
</body>
</html>