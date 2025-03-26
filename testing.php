<?php
require_once 'database/database.php';
$database = new Database();
$db = $database->getConnection();
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$bookingId = 1; // Use an actual booking_id from your database
$newStatus = 'approved';
$finalPrice = 500.00;

$query = "UPDATE bookings SET status = :newStatus, final_price = :finalPrice WHERE booking_id = :bookingId";
$stmt = $db->prepare($query);
$stmt->bindValue(':newStatus', $newStatus);
$stmt->bindValue(':finalPrice', $finalPrice);
$stmt->bindValue(':bookingId', $bookingId);

if ($stmt->execute()) {
    echo "Rows affected: " . $stmt->rowCount();
} else {
    echo "Error: " . print_r($stmt->errorInfo(), true);
}
