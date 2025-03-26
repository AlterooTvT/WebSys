<?php
class Service {
    private $conn;
    private $table = 'services';

    public $service_id;
    public $name;
    public $description;
    public $price;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Create new service
    public function create() {
        $query = "INSERT INTO " . $this->table . "
                SET
                    name = :name,
                    description = :description,
                    price = :price";

        $stmt = $this->conn->prepare($query);

        // Sanitize inputs
        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->description = htmlspecialchars(strip_tags($this->description));
        $this->price = htmlspecialchars(strip_tags($this->price));

        // Bind values
        $stmt->bindParam(":name", $this->name);
        $stmt->bindParam(":description", $this->description);
        $stmt->bindParam(":price", $this->price);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Get all services
    public function getAllServices() {
        $query = "SELECT * FROM " . $this->table . " ORDER BY name";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // Get service by ID
    public function getServiceById($id) {
        $query = "SELECT * FROM " . $this->table . " WHERE service_id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Update service
    public function update() {
        $query = "UPDATE " . $this->table . "
                SET
                    name = :name,
                    description = :description,
                    price = :price
                WHERE service_id = :service_id";

        $stmt = $this->conn->prepare($query);

        // Sanitize inputs
        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->description = htmlspecialchars(strip_tags($this->description));
        $this->price = htmlspecialchars(strip_tags($this->price));
        $this->service_id = htmlspecialchars(strip_tags($this->service_id));

        // Bind values
        $stmt->bindParam(":name", $this->name);
        $stmt->bindParam(":description", $this->description);
        $stmt->bindParam(":price", $this->price);
        $stmt->bindParam(":service_id", $this->service_id);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Delete service
    public function delete($id) {
        $query = "DELETE FROM " . $this->table . " WHERE service_id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        return $stmt->execute();
    }

    // Get services for a booking
    public function getBookingServices($booking_id) {
        $query = "SELECT s.* FROM " . $this->table . " s
                INNER JOIN booking_services bs ON s.service_id = bs.service_id
                WHERE bs.booking_id = :booking_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":booking_id", $booking_id);
        $stmt->execute();
        return $stmt;
    }
}
?>
