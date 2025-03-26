<?php
class Notification {
    private $conn;
    private $table_name = "notifications";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create($user_id, $message, $type) {
        $query = "INSERT INTO " . $this->table_name . "
                SET
                    user_id = :user_id,
                    message = :message,
                    type = :type";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":user_id", $user_id);
        $stmt->bindParam(":message", $message);
        $stmt->bindParam(":type", $type);

        return $stmt->execute();
    }

    public function getUserNotifications($user_id) {
        $query = "SELECT * FROM " . $this->table_name . "
                WHERE user_id = :user_id
                ORDER BY created_at DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":user_id", $user_id);
        $stmt->execute();

        return $stmt;
    }

    public function markAsRead($notification_id) {
        $query = "UPDATE " . $this->table_name . "
                SET is_read = 1
                WHERE id = :id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $notification_id);

        return $stmt->execute();
    }
}