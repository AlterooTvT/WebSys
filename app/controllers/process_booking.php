<?php
session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . '/SIA/database/database.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/SIA/app/models/Booking.php';

header('Content-Type: application/json');

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'User not logged in']);
    exit;
}

try {
    $database = new Database();
    $db = $database->getConnection();
    $booking = new Booking($db);
    
    // Validate required POST fields
    $requiredFields = ['event_date', 'location', 'start_time', 'end_time', 'event_type', 'estimated_price'];
    foreach ($requiredFields as $field) {
        if (empty($_POST[$field])) {
            throw new Exception("Missing required booking data: $field");
        }
    }
    
    // Prepare booking data
    $bookingData = [
        'user_id'         => $_SESSION['user_id'],
        'event_date'      => $_POST['event_date'],
        'location'        => $_POST['location'],
        'start_time'      => $_POST['start_time'],
        'end_time'        => $_POST['end_time'],
        'event_type'      => $_POST['event_type'],
        'estimated_price' => $_POST['estimated_price'],
        'notes'           => isset($_POST['special_requests']) ? $_POST['special_requests'] : null,
        'status'          => 'pending'
    ];
    
    // Start transaction
    $db->beginTransaction();
    
    // Create the booking record
    $booking_id = $booking->createBooking($bookingData);
    if (!$booking_id) {
        throw new Exception("Failed to create booking.");
    }

    // Process additional services (if any)
    if (isset($_POST['services'])) {
        $services = json_decode($_POST['services'], true);
        if (!empty($services)) {
            foreach ($services as $service_id) {
                $booking->addBookingService($booking_id, $service_id);
            }
        }
    }

    // Process additional packages (if any)
    if (isset($_POST['packages'])) {
        $packages = json_decode($_POST['packages'], true);
        if (!empty($packages)) {
            foreach ($packages as $package_id) {
                $booking->addBookingPackage($booking_id, $package_id);
            }
        }
    }

    // Handle reference image upload
    if (isset($_FILES['reference_image']) && $_FILES['reference_image']['error'] === UPLOAD_ERR_OK) {
        // Validate uploaded file
        $imageInfo = getimagesize($_FILES['reference_image']['tmp_name']);
        if ($imageInfo === false) {
            throw new Exception("Uploaded file is not a valid image.");
        }
        
        // Restrict file size (e.g., max 2MB)
        if ($_FILES['reference_image']['size'] > 2 * 1024 * 1024) {
            throw new Exception("Image file is too large (maximum 2MB).");
        }
        
        // Restrict allowed file types
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
        $file_extension = strtolower(pathinfo($_FILES['reference_image']['name'], PATHINFO_EXTENSION));
        if (!in_array($file_extension, $allowedExtensions)) {
            throw new Exception("Invalid image file type.");
        }
        
        // Set upload directory (ensure proper permissions)
        $upload_dir = $_SERVER['DOCUMENT_ROOT'] . '/SIA/public/images/references/';
        if (!file_exists($upload_dir)) {
            if (!mkdir($upload_dir, 0777, true)) {
                throw new Exception("Failed to create directory for uploaded images.");
            }
        }
        
        // Generate unique filename
        $new_filename = 'ref_' . $booking_id . '_' . time() . '.' . $file_extension;
        $upload_path = $upload_dir . $new_filename;
        
        // Move uploaded file
        if (move_uploaded_file($_FILES['reference_image']['tmp_name'], $upload_path)) {
            // Update booking with reference image filename
            $booking->updateReferenceImage($booking_id, $new_filename);
        } else {
            throw new Exception("Failed to upload the reference image.");
        }
    }
    
    // Commit transaction if all operations succeeded
    $db->commit();
    echo json_encode(['status' => 'success', 'message' => 'Booking created successfully']);
    
} catch (Exception $e) {
    // Rollback transaction on error
    if (isset($db)) {
        $db->rollBack();
    }
    // Log the error
    error_log($e->getMessage());
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?>
