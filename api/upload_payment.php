<?php
// Start session and set headers
session_start();
header('Content-Type: application/json');

// Enable error reporting for debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'User not authenticated']);
    exit();
}

require_once $_SERVER['DOCUMENT_ROOT'] . '/SIA/database/database.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/SIA/app/models/Payment.php';

try {
    // Log received data
    error_log('POST data: ' . print_r($_POST, true));
    error_log('FILES data: ' . print_r($_FILES, true));

    // Validate payment_id
    if (!isset($_POST['payment_id']) || empty($_POST['payment_id'])) {
        throw new Exception('Payment ID is required');
    }

    // Check if file was uploaded
    if (!isset($_FILES['proof_of_payment']) || $_FILES['proof_of_payment']['error'] !== UPLOAD_ERR_OK) {
        $uploadError = isset($_FILES['proof_of_payment']) ? $_FILES['proof_of_payment']['error'] : 'No file uploaded';
        throw new Exception('Upload error: ' . $uploadError);
    }

    $file = $_FILES['proof_of_payment'];
    
    // Validate file type
    $allowedTypes = ['image/jpeg', 'image/png', 'image/jpg'];
    if (!in_array($file['type'], $allowedTypes)) {
        throw new Exception('Invalid file type. Only JPG, JPEG, and PNG files are allowed.');
    }

    // Validate file size (5MB max)
    if ($file['size'] > 5 * 1024 * 1024) {
        throw new Exception('File size too large. Maximum size is 5MB.');
    }

    // Create uploads directory if it doesn't exist
    $uploadDir = $_SERVER['DOCUMENT_ROOT'] . '/SIA/uploads/payments/';
    if (!file_exists($uploadDir)) {
        if (!mkdir($uploadDir, 0777, true)) {
            throw new Exception('Failed to create upload directory');
        }
    }

    // Generate unique filename
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = uniqid('payment_') . '.' . $extension;
    $uploadPath = $uploadDir . $filename;

    // Move uploaded file
    if (!move_uploaded_file($file['tmp_name'], $uploadPath)) {
        throw new Exception('Failed to save the uploaded file. Upload path: ' . $uploadPath);
    }

    // Update payment record in database
    $database = new Database();
    $db = $database->getConnection();
    $payment = new Payment($db);

    $updateResult = $payment->updateProofOfPayment($_POST['payment_id'], $filename);
    
    if (!$updateResult) {
        // Remove uploaded file if database update fails
        unlink($uploadPath);
        throw new Exception('Failed to update payment record in database');
    }

    // Return success response
    echo json_encode([
        'status' => 'success',
        'message' => 'Payment proof uploaded successfully',
        'filename' => $filename
    ]);

} catch (Exception $e) {
    error_log('Payment Upload Error: ' . $e->getMessage());
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}
?> 