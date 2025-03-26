<?php
class User {
    private $conn;
    private $table = 'users';

    public $user_id;
    public $email;
    public $password;
    public $first_name;
    public $last_name;
    public $phone;
    public $role;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Register new user
    public function register() {
        $query = "INSERT INTO " . $this->table . "
                (email, password, first_name, last_name, phone, role)
                VALUES
                (:email, :password, :first_name, :last_name, :phone, :role)";

        $stmt = $this->conn->prepare($query);

        // Hash password
        $hashed_password = password_hash($this->password, PASSWORD_DEFAULT);

        // Bind values
        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":password", $hashed_password);
        $stmt->bindParam(":first_name", $this->first_name);
        $stmt->bindParam(":last_name", $this->last_name);
        $stmt->bindParam(":phone", $this->phone);
        $stmt->bindParam(":role", $this->role);

        return $stmt->execute();
    }

    // Login user
    public function login($email, $password) {
        $query = "SELECT * FROM " . $this->table . " WHERE email = :email";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":email", $email);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if($row && password_verify($password, $row['password'])) {
            return [
                'user_id' => $row['user_id'],
                'email' => $row['email'],
                'first_name' => $row['first_name'],
                'last_name' => $row['last_name'],
                'role' => $row['role']
            ];
        }
        return false;
    }

    // Check if email exists
    public function emailExists() {
        $query = "SELECT email FROM " . $this->table . " WHERE email = :email";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":email", $this->email);
        $stmt->execute();

        return $stmt->rowCount() > 0;
    }

    public function getUserById($user_id) {
        $query = "SELECT * FROM " . $this->table . " WHERE user_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$user_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getAllAdmins() {
        $query = "SELECT user_id, first_name, last_name, role 
                 FROM " . $this->table . " 
                 WHERE role = 'admin'";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAllClients() {
        $query = "SELECT user_id, email, first_name, last_name, phone, role 
                  FROM " . $this->table . " 
                  WHERE role = 'client'";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
}
?>