<?php
session_start();
header('Content-Type: application/json');

// Check admin access
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized access']);
    exit();
}

require_once $_SERVER['DOCUMENT_ROOT'] . '/SIA/database/database.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/SIA/app/models/Payment.php';

try {
    // Get and validate POST data
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($data['payment_id']) || !isset($data['status'])) {
        throw new Exception('Missing required parameters');
    }

    $paymentId = $data['payment_id'];
    $status = $data['status'];

    // Validate status
    $allowedStatuses = ['pending', 'verified', 'rejected'];
    if (!in_array($status, $allowedStatuses)) {
        throw new Exception('Invalid status value');
    }

    // Update payment status
    $database = new Database();
    $db = $database->getConnection();
    $payment = new Payment($db);

    if (!$payment->updateStatus($paymentId, $status)) {
        throw new Exception('Failed to update payment status');
    }

    echo json_encode([
        'status' => 'success',
        'message' => 'Payment status updated successfully'
    ]);

} catch (Exception $e) {
    error_log('Payment Status Update Error: ' . $e->getMessage());
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}
?> 