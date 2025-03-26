<?php
// Prevent any output before JSON response
ob_clean();
header('Content-Type: application/json');

// Ensure no HTML errors are output
ini_set('display_errors', 0);
error_reporting(E_ALL);

session_start();

// Log errors instead of displaying them
ini_set('log_errors', 1);
error_log("POST Data: " . print_r($_POST, true));

try {
    // Check admin access
    if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
        throw new Exception('Unauthorized access');
    }

    // Validate request method
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Method Not Allowed');
    }

    // Required parameters check
    if (!isset($_POST['bookingId'], $_POST['newStatus'], $_POST['emailSubject'], $_POST['emailMessage'])) {
        throw new Exception('Missing required parameters');
    }

    require_once $_SERVER['DOCUMENT_ROOT'] . '/SIA/database/database.php';
    require_once $_SERVER['DOCUMENT_ROOT'] . '/SIA/app/models/Booking.php';

    $database = new Database();
    $db = $database->getConnection();
    $booking = new Booking($db);

    // Sanitize inputs
    $bookingId = filter_var($_POST['bookingId'], FILTER_SANITIZE_NUMBER_INT);
    $newStatus = filter_var($_POST['newStatus'], FILTER_SANITIZE_STRING);
    $finalPrice = isset($_POST['finalPrice']) ? filter_var($_POST['finalPrice'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION) : null;

    // Begin transaction
    $db->beginTransaction();

    // Update status
    if (!$booking->updateStatus($bookingId, $newStatus)) {
        throw new Exception('Failed to update booking status');
    }

    // Handle approval with final price
    if ($newStatus === 'approved' && $finalPrice !== null) {
        if (!$booking->updateFinalPrice($bookingId, $finalPrice)) {
            throw new Exception('Failed to update final price');
        }
        
        if (!$booking->addPayment($bookingId, $finalPrice, 'full')) {
            throw new Exception('Failed to create payment record');
        }
    }

    // Commit transaction
    $db->commit();

    // Send success response
    echo json_encode([
        'status' => 'success',
        'message' => 'Booking updated successfully'
    ]);

} catch (Exception $e) {
    // Rollback transaction if active
    if (isset($db) && $db->inTransaction()) {
        $db->rollBack();
    }

    // Log error
    error_log('Update Status Error: ' . $e->getMessage());

    // Send error response
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}
exit;
?>
