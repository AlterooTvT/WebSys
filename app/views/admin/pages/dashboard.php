<?php
// Get statistics
$totalBookings = $booking->getTotalBookings();
$totalRevenue = $payment->getTotalRevenue('all');
$upcomingEvents = $booking->getUpcomingEvents();
$bookingStatus = $booking->getBookingStatus();
?>

<div class="stats-container">
    <div class="stat-card blue">
        <div class="stat-info">
            <h2><?php echo $totalBookings; ?></h2>
            <p>Total Bookings</p>
            <small>Verified bookings only</small>
        </div>
        <a href="?page=bookings" class="more-info">
            More info <i class="fas fa-arrow-circle-right"></i>
        </a>
    </div>

    <div class="stat-card purple">
        <div class="stat-info">
            <h2>₱<?php echo number_format($totalRevenue, 2); ?></h2>
            <p>Total Revenue</p>
            <small>All verified payments</small>
        </div>
        <a href="?page=payments" class="more-info">
            More info <i class="fas fa-arrow-circle-right"></i>
        </a>
    </div>

    <div class="stat-card green">
        <div class="stat-info">
            <h2><?php echo $upcomingEvents; ?></h2>
            <p>Upcoming Events</p>
            <small>Verified bookings only</small>
        </div>
        <a href="?page=bookings" class="more-info">
            More info <i class="fas fa-arrow-circle-right"></i>
        </a>
    </div>

    <div class="stat-card red">
        <div class="stat-info">
            <h2>Booking Status</h2>
            <p>Fully booked: <?php echo $bookingStatus['booked']; ?> days</p>
            <p>Available: <?php echo $bookingStatus['available']; ?> days</p>
            <small>Next 30 days</small>
        </div>
        <a href="?page=bookings" class="more-info">
            More info <i class="fas fa-arrow-circle-right"></i>
        </a>
    </div>
</div>

<style>
.stats-container {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
    padding: 20px;
}

.stat-card small {
    display: block;
    color: #666;
    font-size: 0.8em;
    margin-top: 5px;
}
</style>