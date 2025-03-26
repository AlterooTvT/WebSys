<?php
class Payment {
    private $conn;
    private $table_name = "payments";

    public $payment_id;
    public $booking_id;
    public $amount;
    public $payment_type;
    public $proof_of_payment;
    public $status;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Create new payment
    public function create() {
        $query = "INSERT INTO " . $this->table_name . "
                SET
                    booking_id = :booking_id,
                    amount = :amount,
                    payment_type = :payment_type,
                    proof_of_payment = :proof_of_payment,
                    status = 'pending',
                    created_at = CURRENT_TIMESTAMP,
                    updated_at = CURRENT_TIMESTAMP";

        try {
            $stmt = $this->conn->prepare($query);

            $stmt->bindParam(":booking_id", $this->booking_id);
            $stmt->bindParam(":amount", $this->amount);
            $stmt->bindParam(":payment_type", $this->payment_type);
            $stmt->bindParam(":proof_of_payment", $this->proof_of_payment);

            return $stmt->execute();
        } catch(PDOException $e) {
            error_log("Error in create payment: " . $e->getMessage());
            return false;
        }
    }

    // Get payment details
    public function getPaymentDetails($payment_id) {
        try {
            $query = "SELECT p.*, b.event_date, b.user_id 
                     FROM payments p
                     JOIN bookings b ON p.booking_id = b.booking_id
                     WHERE p.payment_id = :payment_id";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':payment_id', $payment_id, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            error_log("Error in getPaymentDetails: " . $e->getMessage());
            return false;
        }
    }

    public function getUserPayments($userId) {
        $query = "SELECT p.*, b.event_date 
                 FROM " . $this->table_name . " p
                 JOIN bookings b ON p.booking_id = b.booking_id
                 WHERE b.user_id = :user_id
                 ORDER BY p.created_at DESC";

        try {
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':user_id', $userId);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            error_log("Error in getUserPayments: " . $e->getMessage());
            return [];
        }
    }

    // Get total revenue
    public function getTotalRevenue($period = 'month') {
        try {
            $query = "SELECT COALESCE(SUM(p.amount), 0) as total 
                     FROM " . $this->table_name . " p
                     JOIN bookings b ON p.booking_id = b.booking_id
                     WHERE p.status = 'verified' ";

            // Add date filtering
            switch($period) {
                case 'month':
                    $query .= "AND MONTH(p.created_at) = MONTH(CURRENT_DATE()) 
                              AND YEAR(p.created_at) = YEAR(CURRENT_DATE())";
                    break;
                case 'year':
                    $query .= "AND YEAR(p.created_at) = YEAR(CURRENT_DATE())";
                    break;
                case 'week':
                    $query .= "AND WEEK(p.created_at) = WEEK(CURRENT_DATE())
                              AND YEAR(p.created_at) = YEAR(CURRENT_DATE())";
                    break;
            }

            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
            return floatval($row['total']) ?? 0;
        } catch(PDOException $e) {
            error_log("Error calculating revenue: " . $e->getMessage());
            return 0;
        }
    }

    // Get pending payments
    public function getPendingPayments() {
        $query = "SELECT COUNT(*) as pending FROM " . $this->table_name . " 
                WHERE status = 'pending'";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $row['pending'];
    }

    // Update payment status
    public function updateStatus($paymentId, $status) {
        try {
            // Start transaction
            $this->conn->beginTransaction();

            // First verify the payment exists
            $checkQuery = "SELECT * FROM " . $this->table_name . " 
                          WHERE payment_id = :payment_id";
            
            $stmt = $this->conn->prepare($checkQuery);
            $stmt->bindParam(':payment_id', $paymentId);
            $stmt->execute();
            
            if ($stmt->rowCount() === 0) {
                throw new Exception('Payment not found');
            }

            // Update the payment status
            $updateQuery = "UPDATE " . $this->table_name . " 
                           SET status = :status,
                               updated_at = CURRENT_TIMESTAMP 
                           WHERE payment_id = :payment_id";

            $stmt = $this->conn->prepare($updateQuery);
            $stmt->bindParam(':status', $status);
            $stmt->bindParam(':payment_id', $paymentId);
            
            if (!$stmt->execute()) {
                throw new Exception('Failed to update payment status');
            }

            // Commit transaction
            $this->conn->commit();
            return true;

        } catch (Exception $e) {
            // Rollback transaction on error
            if ($this->conn->inTransaction()) {
                $this->conn->rollBack();
            }
            error_log("Payment Status Update Error: " . $e->getMessage());
            return false;
        }
    }

    public function getPendingPaymentCount($user_id) {
        $query = "SELECT COUNT(*) as count 
                  FROM " . $this->table_name . " p
                  JOIN bookings b ON p.booking_id = b.booking_id
                  WHERE b.user_id = ? AND p.status = 'pending'";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$user_id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['count'];
    }

    // Get filtered payments for admin
    public function getFilteredPayments($status = 'all', $dateRange = 'all') {
        $query = "SELECT p.*, b.event_date, 
                         CONCAT(u.first_name, ' ', u.last_name) as client_name
                  FROM " . $this->table_name . " p
                  JOIN bookings b ON p.booking_id = b.booking_id
                  JOIN users u ON b.user_id = u.user_id
                  WHERE 1=1";

        // Add status filter
        if ($status !== 'all') {
            $query .= " AND p.status = :status";
        }

        // Add date range filter
        switch ($dateRange) {
            case 'today':
                $query .= " AND DATE(b.event_date) = CURDATE()";
                break;
            case 'week':
                $query .= " AND YEARWEEK(b.event_date, 1) = YEARWEEK(CURDATE(), 1)";
                break;
            case 'month':
                $query .= " AND MONTH(b.event_date) = MONTH(CURDATE()) 
                           AND YEAR(b.event_date) = YEAR(CURDATE())";
                break;
        }

        $query .= " ORDER BY p.created_at DESC";

        try {
            $stmt = $this->conn->prepare($query);
            
            // Bind status parameter if needed
            if ($status !== 'all') {
                $stmt->bindParam(':status', $status);
            }

            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            error_log("Error in getFilteredPayments: " . $e->getMessage());
            return [];
        }
    }

    // Update proof of payment
    public function updateProofOfPayment($paymentId, $proofFilename) {
        try {
            // Start transaction
            $this->conn->beginTransaction();

            // First, verify the payment exists
            $checkQuery = "SELECT p.*, b.user_id 
                          FROM " . $this->table_name . " p 
                          JOIN bookings b ON p.booking_id = b.booking_id 
                          WHERE p.payment_id = :payment_id";
            
            $stmt = $this->conn->prepare($checkQuery);
            $stmt->bindParam(':payment_id', $paymentId);
            $stmt->execute();
            
            $payment = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$payment) {
                throw new Exception('Payment not found');
            }

            if ($payment['user_id'] != $_SESSION['user_id']) {
                throw new Exception('Unauthorized access');
            }

            // Update the payment record
            $updateQuery = "UPDATE " . $this->table_name . " 
                           SET proof_of_payment = :proof,
                               status = 'pending',
                               updated_at = CURRENT_TIMESTAMP 
                           WHERE payment_id = :payment_id";

            $stmt = $this->conn->prepare($updateQuery);
            
            $stmt->bindParam(':proof', $proofFilename);
            $stmt->bindParam(':payment_id', $paymentId);
            
            $result = $stmt->execute();

            if (!$result) {
                throw new Exception('Failed to execute update query');
            }

            if ($stmt->rowCount() === 0) {
                throw new Exception('No payment record was updated');
            }

            // Commit transaction
            $this->conn->commit();
            return true;

        } catch (Exception $e) {
            // Rollback transaction on error
            if ($this->conn->inTransaction()) {
                $this->conn->rollBack();
            }
            error_log("Payment Update Error: " . $e->getMessage());
            error_log("Payment ID: " . $paymentId);
            error_log("Filename: " . $proofFilename);
            error_log("User ID: " . $_SESSION['user_id']);
            return false;
        }
    }

    // Get payment details by ID
    public function getPaymentById($paymentId) {
        $query = "SELECT p.*, b.event_date,
                         CONCAT(u.first_name, ' ', u.last_name) as client_name
                 FROM " . $this->table_name . " p
                 JOIN bookings b ON p.booking_id = b.booking_id
                 JOIN users u ON b.user_id = u.user_id
                 WHERE p.payment_id = :payment_id";

        try {
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':payment_id', $paymentId);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            error_log("Error in getPaymentById: " . $e->getMessage());
            return false;
        }
    }
}
?>