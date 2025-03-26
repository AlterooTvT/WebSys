<?php
class Calendar {
    private $conn;
    private $table_name = "bookings";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getMonthEvents($year, $month) {
        $query = "SELECT 
                    booking_id, 
                    event_date, 
                    time_slot, 
                    service_type,
                    status
                FROM " . $this->table_name . "
                WHERE YEAR(event_date) = :year 
                AND MONTH(event_date) = :month
                AND status != 'cancelled'";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":year", $year);
        $stmt->bindParam(":month", $month);
        $stmt->execute();

        return $stmt;
    }

    public function checkAvailability($date, $time_slot) {
        $query = "SELECT COUNT(*) as count 
                FROM " . $this->table_name . "
                WHERE event_date = :date 
                AND time_slot = :time_slot 
                AND status != 'cancelled'";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":date", $date);
        $stmt->bindParam(":time_slot", $time_slot);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['count'] == 0;
    }
}