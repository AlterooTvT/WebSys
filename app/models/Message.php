<?php
class Message {
    private $conn;
    private $table = 'messages';

    public $message_id;
    public $sender_id;
    public $receiver_id;
    public $message;
    public $is_read;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Send message
    public function send() {
        $query = "INSERT INTO " . $this->table . "
                SET
                    sender_id = :sender_id,
                    receiver_id = :receiver_id,
                    message = :message";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":sender_id", $this->sender_id);
        $stmt->bindParam(":receiver_id", $this->receiver_id);
        $stmt->bindParam(":message", $this->message);

        return $stmt->execute();
    }

    // Get conversation
    public function getConversation($user_id, $admin_id) {
        $query = "SELECT * FROM " . $this->table . " 
                 WHERE (sender_id = ? AND receiver_id = ?) 
                 OR (sender_id = ? AND receiver_id = ?)
                 ORDER BY created_at ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$user_id, $admin_id, $admin_id, $user_id]);
        return $stmt;
    }

    public function sendMessage($sender_id, $receiver_id, $message) {
        $query = "INSERT INTO " . $this->table . " 
                 (sender_id, receiver_id, message) 
                 VALUES (?, ?, ?)";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$sender_id, $receiver_id, $message]);
    }

    public function hasUnreadMessages($user_id, $admin_id) {
        $query = "SELECT COUNT(*) as count FROM " . $this->table . " 
                 WHERE receiver_id = ? AND sender_id = ? AND is_read = 0";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$user_id, $admin_id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'] > 0;
    }

    public function markAsRead($sender_id, $receiver_id) {
        $query = "UPDATE " . $this->table . " 
                 SET is_read = 1 
                 WHERE sender_id = ? AND receiver_id = ? AND is_read = 0";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$sender_id, $receiver_id]);
    }

    public function getUnreadCount($user_id) {
        $query = "SELECT COUNT(*) as count FROM " . $this->table . " 
                 WHERE receiver_id = ? AND is_read = 0";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$user_id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'];
    }
}
?>