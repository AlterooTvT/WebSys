<?php
require_once '../../../../database/database.php';
require_once '../../../../config/config.php';
require_once '../../../../app/models/Payment.php';

$database = new Database();
$db = $database->getConnection();
$paymentModel = new Payment($db);

// Ensure a payment ID is provided
if (!isset($_GET['id'])) {
    die("No Payment ID provided.");
}

$payment_id = $_GET['id'];

// Query payment details along with booking info (modify field names as needed)
$query = "SELECT p.*, b.event_date, b.event_type, b.final_price 
          FROM payments p 
          LEFT JOIN bookings b ON p.booking_id = b.booking_id 
          WHERE p.payment_id = ?";
$stmt = $db->prepare($query);
$stmt->execute([$payment_id]);
$paymentDetails = $stmt->fetch(PDO::FETCH_ASSOC);

if(!$paymentDetails) {
    die("Payment not found.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Payment Details</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
    <style>
        .payment-details-container {
            max-width: 800px;
            margin: 40px auto;
            background: #fff;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }
        .payment-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 2px solid #e0e0e0;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        .payment-header h1 {
            font-size: 1.5em;
            margin: 0;
        }
        .payment-header .payment-amount {
            font-size: 1.2em;
            color: #333;
        }
        .payment-status {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 4px;
            background-color: #ffc107;
            color: #fff;
            font-weight: bold;
        }
        .payment-details {
            margin: 20px 0;
        }
        .payment-details p {
            margin: 10px 0;
            font-size: 1.1em;
            color: #555;
        }
        .btn-primary {
            background: #3498db;
            color: #fff;
            padding: 10px 20px;
            border-radius: 4px;
            text-decoration: none;
            display: inline-block;
            margin-top: 20px;
        }
        .btn-primary:hover {
            background: #2980b9;
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <?php include 'includes/sidebar.php'; ?>
        <div class="main-content">
            <div class="payment-details-container">
                <div class="payment-header">
                    <h1>Payment Details</h1>
                    <div class="payment-amount">₱<?php echo number_format($paymentDetails['amount'], 2); ?></div>
                </div>
                <div class="payment-status">Pending...</div>
                <div class="payment-details">
                    <p><strong>Name:</strong> <?php echo htmlspecialchars($paymentDetails['client_name']); ?></p>
                    <p><strong>Event:</strong> <?php echo htmlspecialchars($paymentDetails['event_type']); ?></p>
                    <p><strong>Booking ID:</strong> <?php echo $paymentDetails['booking_id']; ?></p>
                    <p><strong>Event Date:</strong> <?php echo date('M d, Y', strtotime($paymentDetails['event_date'])); ?></p>
                    <p><strong>Email:</strong> <?php echo htmlspecialchars($paymentDetails['client_email']); ?></p>
                    <p><strong>Payment Method:</strong> <?php echo ucfirst($paymentDetails['payment_type']); ?></p>
                    <p><strong>Contact #:</strong> <?php echo htmlspecialchars($paymentDetails['client_contact']); ?></p>
                    <p><strong>Status:</strong> <?php echo ucfirst($paymentDetails['status']); ?></p>
                </div>
                <a href="?page=payments" class="btn-primary">Back to Payments</a>
            </div>
        </div>
    </div>
</body>
</html>
