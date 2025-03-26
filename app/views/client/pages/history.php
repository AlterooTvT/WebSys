<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Debug: Print session user ID
echo "<!-- Debug: User ID = " . $_SESSION['user_id'] . " -->";

// Update past events status first
$booking->updatePastEventsStatus();

// Then get the history with debug
$result = $booking->getUserBookings($_SESSION['user_id']);
$booking_history = $result ? $result->fetchAll(PDO::FETCH_ASSOC) : [];

// Debug: Print query result
if ($result === false) {
    echo "<!-- Debug: Query failed -->";
} else {
    echo "<!-- Debug: Found " . count($booking_history) . " records -->";
    echo "<!-- Debug: " . print_r($booking_history, true) . " -->";
}
?>

<div class="history-container">
    <h2>Completed Events</h2>
    
    <div class="history-list">
        <?php if (!empty($booking_history)): ?>
            <table>
                <thead>
                    <tr>
                        <th>Event Date</th>
                        <th>Event Type</th>
                        <th>Location</th>
                        <th>Total Amount</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($booking_history as $booking): ?>
                        <tr>
                            <td><?php echo date('M d, Y', strtotime($booking['event_date'])); ?></td>
                            <td><?php echo ucfirst($booking['event_type']); ?></td>
                            <td><?php echo $booking['location']; ?></td>
                            <td>₱<?php echo number_format($booking['total_amount'], 2); ?></td>
                            <td>
                                <span class="status-badge completed">
                                    Completed
                                </span>
                            </td>
                            <td>
                                <a href="?page=booking&action=view&id=<?php echo $booking['id']; ?>" 
                                   class="view-btn">
                                    <i class="fas fa-eye"></i> View
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="no-history">
                <i class="fas fa-history"></i>
                <p>No completed events found</p>
                <a href="?page=book" class="book-now-btn">Book Now</a>
            </div>
        <?php endif; ?>
    </div>
</div>

<style>
.history-container {
    padding: 20px;
    max-width: 1200px;
    margin: 0 auto;
}

.history-container h2 {
    color: #2c3e50;
    margin-bottom: 20px;
}

.history-list {
    background: white;
    border-radius: 10px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    overflow: hidden;
}

table {
    width: 100%;
    border-collapse: collapse;
}

th, td {
    padding: 12px 15px;
    text-align: left;
    border-bottom: 1px solid #eee;
}

th {
    background-color: #f8f9fa;
    color: #2c3e50;
    font-weight: 600;
}

.status-badge {
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 0.85rem;
    font-weight: 500;
}

.status-badge.pending {
    background-color: #fff3e0;
    color: #f57c00;
}

.status-badge.approved {
    background-color: #e8f5e9;
    color: #388e3c;
}

.status-badge.completed {
    background-color: #e3f2fd;
    color: #1976d2;
}

.status-badge.cancelled {
    background-color: #ffebee;
    color: #d32f2f;
}

.view-btn {
    display: inline-flex;
    align-items: center;
    padding: 6px 12px;
    background-color: #2196f3;
    color: white;
    border-radius: 6px;
    text-decoration: none;
    font-size: 0.9rem;
    transition: background-color 0.2s;
}

.view-btn:hover {
    background-color: #1976d2;
}

.view-btn i {
    margin-right: 5px;
}

.no-history {
    text-align: center;
    padding: 40px 20px;
    color: #666;
}

.no-history i {
    font-size: 3rem;
    color: #ddd;
    margin-bottom: 15px;
}

.book-now-btn {
    display: inline-block;
    padding: 10px 20px;
    background-color: #2196f3;
    color: white;
    border-radius: 6px;
    text-decoration: none;
    transition: background-color 0.2s;
    margin-top: 15px;
}

.book-now-btn:hover {
    background-color: #1976d2;
}

@media (max-width: 768px) {
    .history-container {
        padding: 10px;
    }
    
    th, td {
        padding: 10px;
    }
    
    .view-btn {
        padding: 4px 8px;
    }
}
</style>