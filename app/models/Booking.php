<?php
class Booking {
    private $conn;
    private $table_name = "bookings";

    // Properties
    public $booking_id;
    public $user_id;
    public $event_date;
    public $location;
    public $start_time;
    public $end_time;
    public $event_type;
    public $estimated_price;
    public $final_price;
    public $status;
    public $reference_image;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Create a new booking
    public function createBooking($data) {
        $query = "INSERT INTO " . $this->table_name . "
                  (user_id, event_date, location, start_time, end_time, event_type, estimated_price, notes, status)
                  VALUES
                  (:user_id, :event_date, :location, :start_time, :end_time, :event_type, :estimated_price, :notes, :status)";
                  
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id',         $data['user_id']);
        $stmt->bindParam(':event_date',      $data['event_date']);
        $stmt->bindParam(':location',        $data['location']);
        $stmt->bindParam(':start_time',      $data['start_time']);
        $stmt->bindParam(':end_time',        $data['end_time']);
        $stmt->bindParam(':event_type',      $data['event_type']);
        $stmt->bindParam(':estimated_price', $data['estimated_price']);
        $stmt->bindParam(':notes',           $data['notes']);
        $stmt->bindParam(':status',          $data['status']);
    
        if($stmt->execute()) {
            return $this->conn->lastInsertId();
        }
        return false;
    }
    
    // Add a package to a booking
    public function addBookingPackage($booking_id, $package_id) {
        $query = "INSERT INTO booking_packages (booking_id, package_id) VALUES (:booking_id, :package_id)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":booking_id", $booking_id);
        $stmt->bindParam(":package_id", $package_id);
        return $stmt->execute();
    }

    // Add a service to a booking
    public function addBookingService($booking_id, $service_id) {
        $query = "INSERT INTO booking_services (booking_id, service_id) VALUES (:booking_id, :service_id)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':booking_id', $booking_id);
        $stmt->bindParam(':service_id', $service_id);
        return $stmt->execute();
    }

    public function getBookingById($bookingId) {
        $query = "SELECT b.*, u.email 
                  FROM bookings b 
                  JOIN users u ON b.user_id = u.user_id 
                  WHERE b.booking_id = :bookingId";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':bookingId', $bookingId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt;
    }
    
    // Update booking service (for single service selection)
    public function updateBookingService($booking_id, $service_id) {
        $query = "UPDATE " . $this->table_name . " SET service_id = :service_id WHERE booking_id = :booking_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':service_id', $service_id);
        $stmt->bindParam(':booking_id', $booking_id);
        return $stmt->execute();
    }

    // Update booking package (update primary package in booking record)
    public function updateBookingPackage($booking_id, $package_id) {
        $query = "UPDATE " . $this->table_name . " SET package_id = :package_id WHERE booking_id = :booking_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':package_id', $package_id);
        $stmt->bindParam(':booking_id', $booking_id);
        return $stmt->execute();
    }
    
    // Update the reference image field for a given booking
    public function updateReferenceImage($booking_id, $image_name) {
        $query = "UPDATE " . $this->table_name . " SET reference_image = :image_name WHERE booking_id = :booking_id";
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(':booking_id', $booking_id);
        $stmt->bindParam(':image_name', $image_name);
        
        return $stmt->execute();
    }

    // Get detailed booking information including associated services
    // Get booking details including associated services and packages
    public function getBookingDetails($booking_id) {
        $query = "SELECT b.*, 
                         u.first_name, u.last_name, u.email, u.phone
                  FROM bookings b
                  JOIN users u ON b.user_id = u.user_id
                  WHERE b.booking_id = :booking_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':booking_id', $booking_id);
        $stmt->execute();
        $booking = $stmt->fetch(PDO::FETCH_ASSOC);
    
        if ($booking) {
            // Get associated services
            $servicesQuery = "SELECT s.* 
                              FROM services s
                              JOIN booking_services bs ON s.service_id = bs.service_id
                              WHERE bs.booking_id = :booking_id";
            $servicesStmt = $this->conn->prepare($servicesQuery);
            $servicesStmt->bindParam(':booking_id', $booking_id);
            $servicesStmt->execute();
            $booking['services'] = $servicesStmt->fetchAll(PDO::FETCH_ASSOC);
    
            // Get associated packages
            $packagesQuery = "SELECT p.* 
                              FROM packages p
                              JOIN booking_packages bp ON p.package_id = bp.package_id
                              WHERE bp.booking_id = :booking_id";
            $packagesStmt = $this->conn->prepare($packagesQuery);
            $packagesStmt->bindParam(':booking_id', $booking_id);
            $packagesStmt->execute();
            $booking['packages'] = $packagesStmt->fetchAll(PDO::FETCH_ASSOC);
            
            return $booking;
        }
    
        return false;
    }
    
    public function updateFinalPrice($bookingId, $finalPrice) {
        error_log("updateFinalPrice called with ID: $bookingId, Price: $finalPrice");
        
        if (!is_numeric($bookingId) || !is_numeric($finalPrice)) {
            error_log("Invalid parameters in updateFinalPrice");
            return false;
        }

        $query = "UPDATE " . $this->table_name . " 
                  SET final_price = :finalPrice 
                  WHERE booking_id = :bookingId";
                  
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':finalPrice', $finalPrice, PDO::PARAM_STR);
        $stmt->bindValue(':bookingId', $bookingId, PDO::PARAM_INT);

        try {
            $result = $stmt->execute();
            error_log("Update result: " . ($result ? "Success" : "Failed"));
            error_log("Affected rows: " . $stmt->rowCount());
            return $result && $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            error_log("Database error in updateFinalPrice: " . $e->getMessage());
            return false;
        }
    }
    
    
    // Get recent bookings with client information (for admin)
    public function getRecentBookings($userId) {
        try {
            $query = "SELECT 
                        b.booking_id, 
                        b.event_date, 
                        b.event_type, 
                        b.status,
                        s.name as service_name,
                        p.amount as payment_amount,
                        p.status as payment_status
                    FROM " . $this->table_name . " b
                    LEFT JOIN services s ON b.service_id = s.service_id
                    LEFT JOIN payments p ON b.booking_id = p.booking_id
                    WHERE b.user_id = :user_id 
                    ORDER BY b.created_at DESC
                    LIMIT 5";

            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
            $stmt->execute();

            // Debug: Print the query and user ID
            error_log("Recent Bookings Query: " . $query);
            error_log("User ID: " . $userId);
            
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Debug: Print the results count
            error_log("Results found: " . count($results));
            
            return $results;
        } catch(PDOException $e) {
            error_log("Error getting recent bookings: " . $e->getMessage());
            return [];
        }
    }

    // Get the total number of bookings
    public function getTotalBookings() {
        $query = "SELECT COUNT(*) as total FROM bookings";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'];
    }


    // Get upcoming events coun

    // Get booking status breakdown over the next 30 days
    public function getBookingStatus() {
        try {
            // Get dates that are fully booked (have verified payments)
            $bookedQuery = "SELECT COUNT(DISTINCT b.event_date) as booked
                           FROM " . $this->table_name . " b
                           JOIN payments p ON b.booking_id = p.booking_id
                           WHERE b.event_date >= CURRENT_DATE
                           AND p.status = 'verified'";

            // Get available dates (next 30 days excluding booked dates)
            $availableQuery = "SELECT COUNT(*) as available
                              FROM (
                                  SELECT DATE_ADD(CURRENT_DATE, INTERVAL n DAY) as date
                                  FROM (
                                      SELECT a.N + b.N * 10 as n
                                      FROM (SELECT 0 as N UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9) a,
                                           (SELECT 0 as N UNION SELECT 1 UNION SELECT 2 UNION SELECT 3) b
                                      ORDER BY n LIMIT 30
                                  ) numbers
                              ) dates
                              WHERE date NOT IN (
                                  SELECT b.event_date
                                  FROM " . $this->table_name . " b
                                  JOIN payments p ON b.booking_id = p.booking_id
                                  WHERE b.event_date >= CURRENT_DATE
                                  AND p.status = 'verified'
                              )";

            $stmt = $this->conn->prepare($bookedQuery);
            $stmt->execute();
            $booked = $stmt->fetch(PDO::FETCH_ASSOC)['booked'] ?? 0;

            $stmt = $this->conn->prepare($availableQuery);
            $stmt->execute();
            $available = $stmt->fetch(PDO::FETCH_ASSOC)['available'] ?? 0;

            return [
                'booked' => $booked,
                'available' => $available
            ];
        } catch(PDOException $e) {
            error_log("Error getting booking status: " . $e->getMessage());
            return [
                'booked' => 0,
                'available' => 0
            ];
        }
    }

    // Get count of pending bookings
    public function getPendingBookings() {
        $query = "SELECT COUNT(*) as pending FROM " . $this->table_name . " 
                  WHERE status = 'pending'";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['pending'];
    }

    public function checkAvailability($date, $start_time) {
        $query = "SELECT COUNT(*) as count FROM " . $this->table_name . "
                  WHERE event_date = :date 
                  AND start_time = :start_time 
                  AND status NOT IN ('rejected', 'completed')";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":date", $date);
        $stmt->bindParam(":start_time", $start_time);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return ($row['count'] == 0);
    }

    // Get bookings for a specific user
    public function getUserBookings($userId) {
        try {
            $query = "SELECT 
                        booking_id as id,
                        event_date,
                        event_type,
                        status,
                        final_price as total_amount,
                        location
                    FROM bookings 
                    WHERE user_id = :user_id 
                    AND event_date < CURRENT_DATE()
                    AND status = 'paid'
                    ORDER BY event_date DESC";

            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt;
        } catch(PDOException $e) {
            error_log("Error in getUserBookings: " . $e->getMessage());
            return false;
        }
    }

    // Get all bookings (for admin)
    public function getAllBookings($limit, $offset) {
        $query = "SELECT b.*, u.first_name, u.last_name, u.email 
                FROM bookings b
                JOIN users u ON b.user_id = u.user_id
                ORDER BY b.created_at DESC
                LIMIT :limit OFFSET :offset";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt;
    }

    public function updateBookingStatus($booking_id, $status, $final_price) {
        $query = "UPDATE bookings SET status = :status, final_price = :final_price WHERE booking_id = :booking_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':final_price', $final_price);
        $stmt->bindParam(':booking_id', $booking_id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function createPayment($booking_id, $amount, $payment_type) {
        $query = "INSERT INTO payments (booking_id, amount, payment_type) VALUES (:booking_id, :amount, :payment_type)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':booking_id', $booking_id, PDO::PARAM_INT);
        $stmt->bindParam(':amount', $amount);
        $stmt->bindParam(':payment_type', $payment_type);
        return $stmt->execute();
    }
    
    // Update the booking status
    public function updateStatus($bookingId, $newStatus) {
        error_log("Attempting to update status for booking: $bookingId to $newStatus");
        $query = "UPDATE bookings SET status = :newStatus WHERE booking_id = :bookingId";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':newStatus', $newStatus);
        $stmt->bindValue(':bookingId', $bookingId);

        if (!$stmt->execute()) {
            error_log("updateStatus error: " . print_r($stmt->errorInfo(), true));
            return false;
        }
        error_log("updateStatus affected rows: " . $stmt->rowCount());
        return $stmt->rowCount() > 0;
    }
    
    
    
    // Get all bookings ordered by event date (ascending)
    public function getAllBookingsOrderedByDate() {
        $query = "SELECT b.*, u.first_name, u.last_name, u.email,
                         GROUP_CONCAT(DISTINCT p.name SEPARATOR ', ') AS packages,
                         GROUP_CONCAT(DISTINCT s.name SEPARATOR ', ') AS services
                  FROM " . $this->table_name . " b
                  LEFT JOIN users u ON b.user_id = u.user_id
                  LEFT JOIN booking_packages bp ON b.booking_id = bp.booking_id
                  LEFT JOIN packages p ON bp.package_id = p.package_id
                  LEFT JOIN booking_services bs ON b.booking_id = bs.booking_id
                  LEFT JOIN services s ON bs.service_id = s.service_id
                  GROUP BY b.booking_id
                  ORDER BY b.event_date ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }
    
    // Get bookings by status (for admin)
    public function getBookingsByStatus($status) {
        $query = "SELECT b.*, u.first_name, u.last_name,
                         GROUP_CONCAT(DISTINCT p.name SEPARATOR ', ') AS packages,
                         GROUP_CONCAT(DISTINCT s.name SEPARATOR ', ') AS services
                  FROM " . $this->table_name . " b
                  JOIN users u ON b.user_id = u.user_id
                  LEFT JOIN booking_packages bp ON b.booking_id = bp.booking_id
                  LEFT JOIN packages p ON bp.package_id = p.package_id
                  LEFT JOIN booking_services bs ON b.booking_id = bs.booking_id
                  LEFT JOIN services s ON bs.service_id = s.service_id
                  WHERE b.status = :status
                  GROUP BY b.booking_id
                  ORDER BY b.created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":status", $status);
        $stmt->execute();
        return $stmt;
    }

    // Get upcoming event count for a specific user
    public function getUpcomingEventCount($user_id) {
        $query = "SELECT COUNT(*) as count FROM " . $this->table_name . " 
                  WHERE user_id = ? AND event_date >= CURDATE() AND status != 'cancelled'";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$user_id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['count'];
    }

    // Get completed event count for a specific user
    public function getCompletedEventCount($userId) {
        try {
            $query = "SELECT COUNT(*) as count 
                     FROM bookings b
                     WHERE b.user_id = :user_id 
                     AND b.event_date < CURRENT_DATE()
                     AND b.status = 'paid'";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
            $stmt->execute();
            
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['count'] ?? 0;
        } catch(PDOException $e) {
            error_log("Error in getCompletedEventCount: " . $e->getMessage());
            return 0;
        }
    }

    // Get bookings by month
    public function getBookingsByMonth($month, $year) {
        $query = "SELECT event_date FROM " . $this->table_name . " 
                  WHERE MONTH(event_date) = :month 
                  AND YEAR(event_date) = :year";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":month", $month);
        $stmt->bindParam(":year", $year);
        try {
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            return [];
        }
    }
    public function addPayment($bookingId, $amount, $paymentType) {
        error_log("Attempting to add payment for booking: $bookingId, amount: $amount, type: $paymentType");
        $query = "INSERT INTO payments (booking_id, amount, payment_type, status) 
                  VALUES (:bookingId, :amount, :paymentType, 'pending')";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':bookingId', $bookingId);
        $stmt->bindValue(':amount', $amount);
        $stmt->bindValue(':paymentType', $paymentType);

        if (!$stmt->execute()) {
            error_log("addPayment error: " . print_r($stmt->errorInfo(), true));
            return false;
        }
        error_log("addPayment inserted row with id: " . $this->conn->lastInsertId());
        return true;
    }

    // Modified to make userId optional for admin view
    public function getUpcomingEvents($userId = null) {
        try {
            $query = "SELECT 
                        COUNT(b.booking_id) as upcoming_count
                    FROM bookings b
                    WHERE b.event_date >= CURRENT_DATE()
                    AND b.status NOT IN ('completed', 'cancelled')";

            // Add user filter only if userId is provided (for client view)
            $params = [];
            if ($userId !== null) {
                $query .= " AND b.user_id = :user_id";
                $params[':user_id'] = $userId;
            }

            $stmt = $this->conn->prepare($query);
            
            // Bind parameters if any exist
            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value, PDO::PARAM_INT);
            }
            
            $stmt->execute();
            
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['upcoming_count'] ?? 0;
            
        } catch(PDOException $e) {
            error_log("Error in getUpcomingEvents: " . $e->getMessage());
            return 0;
        }
    }

    // For detailed upcoming events (used in client view)
    public function getDetailedUpcomingEvents($userId) {
        try {
            $query = "SELECT 
                        b.booking_id as id,
                        b.event_date,
                        b.event_type,
                        b.status,
                        b.total_amount,
                        s.name as service_name
                    FROM bookings b
                    LEFT JOIN services s ON b.service_id = s.service_id
                    WHERE b.user_id = :user_id 
                    AND b.event_date >= CURRENT_DATE()
                    AND b.status NOT IN ('completed', 'cancelled')
                    ORDER BY b.event_date ASC";

            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt;
        } catch(PDOException $e) {
            error_log("Error in getDetailedUpcomingEvents: " . $e->getMessage());
            return false;
        }
    }

    // Add a method to update status of past events
    public function updatePastEventsStatus() {
        try {
            $query = "UPDATE " . $this->table_name . "
                     SET status = 'completed'
                     WHERE DATE(event_date) < CURDATE()
                     AND status NOT IN ('completed', 'cancelled')";
                     
            $stmt = $this->conn->prepare($query);
            return $stmt->execute();
            
        } catch(PDOException $e) {
            error_log("Error in updatePastEventsStatus: " . $e->getMessage());
            return false;
        }
    }

    // Add method to get service name
    public function getServiceName($serviceId) {
        try {
            $query = "SELECT name FROM services WHERE service_id = :service_id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':service_id', $serviceId, PDO::PARAM_INT);
            $stmt->execute();
            
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['name'] ?? 'N/A';
        } catch(PDOException $e) {
            error_log("Error getting service name: " . $e->getMessage());
            return 'N/A';
        }
    }
}
?>
