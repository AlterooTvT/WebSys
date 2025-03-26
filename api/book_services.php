<?php
require_once '../../database/database.php';
require_once '../../models/Booking.php';

$bookingModel = new Booking($db);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_POST['user_id'];
    $package_id = null; // Optional, if not using packages
    $event_date = $_POST['event_date'];
    $location = $_POST['location'];
    $time_slot = $_POST['time_slot'];
    $event_type = $_POST['event_type'];
    $estimated_price = $_POST['estimated_price'];
    $services = $_POST['services'] ?? []; // Handle service selections

    $booking_id = $bookingModel->createWithServices($user_id, $package_id, $event_date, $location, $time_slot, $event_type, $estimated_price, $services);

    if ($booking_id) {
        echo "Booking created successfully with ID: $booking_id";
    } else {
        echo "Failed to create booking.";
    }
}
?>
