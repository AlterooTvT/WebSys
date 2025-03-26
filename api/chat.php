<?php
header('Content-Type: application/json');
require_once '../../database/database.php';
require_once '../../helpers/auth.php';
require_once '../../app/models/Message.php';

$database = new Database();
$db = $database->getConnection();
$message = new Message($db);

$action = isset($_GET['action']) ? $_GET['action'] : '';

switch($action) {
    case 'send_message':
        $data = json_decode(file_get_contents('php://input'), true);
        
        $message->sender_id = $_SESSION['user_id'];
        $message->receiver_id = $data['receiver_id'];
        $message->message = $data['message'];
        
        $response = [
            'success' => $message->send()
        ];
        break;
        
    case 'get_messages':
        $user_id = $_GET['user_id'];
        $conversation = $message->getConversation($user_id, 1); // 1 is admin ID
        
        $response = [
            'messages' => $conversation->fetchAll(PDO::FETCH_ASSOC)
        ];
        break;
        
    default:
        http_response_code(400);
        $response = ['error' => 'Invalid action'];
}

echo json_encode($response);