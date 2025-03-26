<?php
class Package {
    private $conn;
    private $table = 'packages';

    public $package_id;
    public $name;
    public $service_type;
    public $description;
    public $price;
    public $duration;
    public $features;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Create new package
    public function create() {
        $query = "INSERT INTO " . $this->table . "
                SET
                    name = :name,
                    service_type = :service_type,
                    description = :description,
                    price = :price,
                    duration = :duration,
                    features = :features";

        $stmt = $this->conn->prepare($query);

        // Sanitize inputs
        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->service_type = htmlspecialchars(strip_tags($this->service_type));
        $this->description = htmlspecialchars(strip_tags($this->description));
        $this->price = htmlspecialchars(strip_tags($this->price));
        $this->duration = htmlspecialchars(strip_tags($this->duration));
        $this->features = htmlspecialchars(strip_tags($this->features));

        // Bind values
        $stmt->bindParam(":name", $this->name);
        $stmt->bindParam(":service_type", $this->service_type);
        $stmt->bindParam(":description", $this->description);
        $stmt->bindParam(":price", $this->price);
        $stmt->bindParam(":duration", $this->duration);
        $stmt->bindParam(":features", $this->features);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Get all packages
    public function getAllPackages() {
        $query = "SELECT * FROM " . $this->table . " ORDER BY service_type, price";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // Get package by ID
    public function getPackageById($id) {
        $query = "SELECT * FROM " . $this->table . " WHERE package_id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Update package
    public function update() {
        $query = "UPDATE " . $this->table . "
                SET
                    name = :name,
                    service_type = :service_type,
                    description = :description,
                    price = :price,
                    duration = :duration,
                    features = :features
                WHERE package_id = :package_id";

        $stmt = $this->conn->prepare($query);

        // Sanitize inputs
        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->service_type = htmlspecialchars(strip_tags($this->service_type));
        $this->description = htmlspecialchars(strip_tags($this->description));
        $this->price = htmlspecialchars(strip_tags($this->price));
        $this->duration = htmlspecialchars(strip_tags($this->duration));
        $this->features = htmlspecialchars(strip_tags($this->features));
        $this->package_id = htmlspecialchars(strip_tags($this->package_id));

        // Bind values
        $stmt->bindParam(":name", $this->name);
        $stmt->bindParam(":service_type", $this->service_type);
        $stmt->bindParam(":description", $this->description);
        $stmt->bindParam(":price", $this->price);
        $stmt->bindParam(":duration", $this->duration);
        $stmt->bindParam(":features", $this->features);
        $stmt->bindParam(":package_id", $this->package_id);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Delete package
    public function delete($id) {
        $query = "DELETE FROM " . $this->table . " WHERE package_id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        return $stmt->execute();
    }

    // Get packages by service type
    public function getPackagesByType($service_type) {
        $query = "SELECT * FROM " . $this->table . " 
                WHERE service_type = :service_type 
                ORDER BY price";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":service_type", $service_type);
        $stmt->execute();
        return $stmt;
    }
}
?> 