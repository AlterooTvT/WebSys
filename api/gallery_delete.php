<?php
require_once '../../config/config.php';
require_once '../../database/database.php';
require_once '../../app/models/Gallery.php';

// Check request method
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit();
}

// Ensure image_id is provided
if (!isset($_POST['image_id'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Image ID is required']);
    exit();
}

$image_id = intval($_POST['image_id']);

// Setup database connection and Gallery model
$database = new Database();
$db = $database->getConnection();
$gallery = new Gallery($db);

// Attempt to delete the image
if ($gallery->deleteImage($image_id)) {
    echo json_encode(['success' => true]);
} else {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to delete image']);
}
?>
