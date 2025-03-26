<?php
class Gallery {
    private $conn;
    private $table_name = "gallery";

    public function __construct($db) {
        $this->conn = $db;
    }

    // Upload image (already implemented in your admin gallery)
    public function uploadImage($tmp_file, $filename, $category) {
        // Calculate the absolute path to the gallery directory.
        // From SIA/app/models/ go up two levels to SIA, then into public/assets/images/gallery/
        $uploadDir = dirname(__DIR__, 2) . '/public/assets/images/gallery/';
    
        // Ensure the directory exists - if not, create it.
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
    
        $destination = $uploadDir . $filename;
        
        if(move_uploaded_file($tmp_file, $destination)) {
            $query = "INSERT INTO " . $this->table_name . " (filename, service_type) 
                      VALUES (:filename, :category)";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":filename", $filename);
            $stmt->bindParam(":category", $category);
            return $stmt->execute();
        }
        return false;
    }
    
    
    // Retrieve all images (used by admin page)
    public function getAllImages() {
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY uploaded_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // Get images by category (for client landing page)
    public function getImagesByCategory($category) {
        $query = "SELECT * FROM " . $this->table_name . " 
                  WHERE service_type = :category 
                  ORDER BY uploaded_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":category", $category);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


public function deleteImage($image_id) {
    // Get the filename first so we can delete the image file from disk.
    $query = "SELECT filename FROM " . $this->table_name . " WHERE image_id = :image_id";
    $stmt = $this->conn->prepare($query);
    $stmt->bindParam(":image_id", $image_id, PDO::PARAM_INT);
    if (!$stmt->execute()) {
        return false;
    }
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$row) {
        return false;
    }
    $filename = $row['filename'];

    // Delete the record from the database.
    $query = "DELETE FROM " . $this->table_name . " WHERE image_id = :image_id";
    $stmt = $this->conn->prepare($query);
    $stmt->bindParam(":image_id", $image_id, PDO::PARAM_INT);
    if ($stmt->execute()) {
        // Delete the image file from disk.
        $filePath = dirname(__DIR__, 2) . '/public/assets/images/gallery/' . $filename;
        if (file_exists($filePath)) {
            unlink($filePath);
        }
        return true;
    }
    return false;
}
}
?>
