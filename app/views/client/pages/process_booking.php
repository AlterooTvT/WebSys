<?php
session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . '/SIA/database/database.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/SIA/app/models/Booking.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/SIA/app/models/Notification.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $database = new Database();
    $db = $database->getConnection();
    $booking = new Booking($db);
    $notification = new Notification($db);

    // Get form data
    $user_id = $_SESSION['user_id'];
    $event_date = $_POST['event_date'];
    $location = $_POST['street'] . ', ' . $_POST['barangay'] . ', ' . $_POST['city'] . ', ' . $_POST['province'];
    $time_slot = $_POST['start_time'];
    $event_type = $_POST['event_type'];
    $special_requests = $_POST['special_requests'];
    
    // Calculate estimated price from selected services/packages
    $estimated_price = 0;
    
    try {
        $db->beginTransaction();

        // Create the booking
        $booking_data = [
            'user_id' => $user_id,
            'event_date' => $event_date,
            'location' => $location,
            'time_slot' => $time_slot,
            'event_type' => $event_type,
            'estimated_price' => $estimated_price,
            'status' => 'pending'
        ];

        $booking_id = $booking->createBooking($booking_data);

        // Handle services if any are selected
        if (isset($_POST['service_ids']) && is_array($_POST['service_ids'])) {
            foreach ($_POST['service_ids'] as $service_id) {
                $booking->addBookingService($booking_id, $service_id);
            }
        }

        // Handle packages if any are selected
        if (isset($_POST['package_ids']) && is_array($_POST['package_ids'])) {
            foreach ($_POST['package_ids'] as $package_id) {
                $booking->updateBookingPackage($booking_id, $package_id);
            }
        }

        // Handle image upload if any
        if (isset($_FILES['reference_image']) && $_FILES['reference_image']['error'] === UPLOAD_ERR_OK) {
            $upload_dir = $_SERVER['DOCUMENT_ROOT'] . '/SIA/public/uploads/references/';
            $file_extension = pathinfo($_FILES['reference_image']['name'], PATHINFO_EXTENSION);
            $file_name = 'booking_' . $booking_id . '_' . time() . '.' . $file_extension;
            
            if (move_uploaded_file($_FILES['reference_image']['tmp_name'], $upload_dir . $file_name)) {
                $booking->updateReferenceImage($booking_id, $file_name);
            }
        }

        // Create notification for admin
        $notification->createNotification([
            'user_id' => 1, // Assuming admin has user_id 1
            'type' => 'new_booking',
            'message' => "New booking request received for " . $event_type . " on " . $event_date,
            'is_read' => false
        ]);

        $db->commit();

        // Send success response
        echo json_encode([
            'status' => 'success',
            'message' => 'Booking submitted successfully',
            'booking_id' => $booking_id
        ]);

    } catch (Exception $e) {
        $db->rollBack();
        echo json_encode([
            'status' => 'error',
            'message' => 'An error occurred while processing your booking'
        ]);
    }
} 