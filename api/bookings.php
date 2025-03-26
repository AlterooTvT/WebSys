<?php
header('Content-Type: application/json');
require_once '../../database/database.php';
require_once '../../helpers/auth.php';
require_once '../../app/models/Booking.php';

$database = new Database();
$db = $database->getConnection();
$booking = new Booking($db);

$action = isset($_GET['action']) ? $_GET['action'] : '';

switch($action) {
    case 'check_availability':
        $date = $_GET['date'];
        $time_slot = $_GET['time_slot'];
        
        $response = [
            'available' => $booking->checkAvailability($date, $time_slot)
        ];
        break;
        
    case 'get_calendar_events':
        $year = $_GET['year'];
        $month = $_GET['month'];
        
        $events = $booking->getMonthEvents($year, $month);
        $response = [
            'events' => $events->fetchAll(PDO::FETCH_ASSOC)
        ];
        break;
        
    default:
        http_response_code(400);
        $response = ['error' => 'Invalid action'];
}

echo json_encode($response);