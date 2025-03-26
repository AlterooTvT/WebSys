<?php
// Get statistics
$upcomingEvents = $booking->getUpcomingEventCount($_SESSION['user_id']);
$pendingPayments = $payment->getPendingPaymentCount($_SESSION['user_id']);
$completedEvents = $booking->getCompletedEventCount($_SESSION['user_id']);
?>

<!-- Dashboard Overview -->
<div class="dashboard-container">
    <!-- Stats Section -->
    <div class="dashboard-stats">
        <div class="stat-card upcoming" onclick="showUpcomingEvents()" role="button" tabindex="0">
            <div class="stat-icon">
                <i class="fas fa-calendar-alt"></i>
            </div>
            <div class="stat-info">
                <h3>Upcoming Events</h3>
                <p class="stat-number"><?php echo $upcomingEvents; ?></p>
                <small>Your scheduled events</small>
            </div>
        </div>
        <div class="stat-card pending" onclick="window.location.href='?page=payments'" role="button" tabindex="0">
            <div class="stat-icon">
                <i class="fas fa-clock"></i>
            </div>
            <div class="stat-info">
                <h3>Pending Payments</h3>
                <p class="stat-number"><?php echo $pendingPayments; ?></p>
                <small>Awaiting payment confirmation</small>
            </div>
        </div>
        <div class="stat-card completed" onclick="window.location.href='?page=history'" role="button" tabindex="0">
            <div class="stat-icon">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="stat-info">
                <h3>Completed Events</h3>
                <p class="stat-number"><?php echo $completedEvents; ?></p>
                <small>Successfully finished events</small>
            </div>
        </div>
    </div>

    <!-- Recent Bookings Section -->
    <div class="recent-bookings" id="upcomingEventsTable" style="display: none;">
        <div class="section-header">
            <h2><i class="fas fa-list"></i> Upcoming Events</h2>
        </div>
        <div class="table-responsive" id="bookingsTable">
            <!-- Table will be loaded here -->
        </div>
    </div>
</div>

<style>
/* Dashboard Container */
.dashboard-container {
    margin: 0 auto;
    display: flex;
    flex-direction: column;
    gap: 30px;
}

/* Stats Section */
.dashboard-stats {
    display: flex;
    justify-content: space-between;
    gap: 20px;
    width: 100%;
}

.stat-card {
    flex: 1;
    background: white;
    border-radius: 10px;
    padding: 20px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    display: flex;
    align-items: center;
    transition: transform 0.2s;
    position: relative;
    overflow: hidden;
    min-width: 250px;
    cursor: pointer;
    user-select: none;
}

.stat-card:hover {
    transform: translateY(-5px);
}

.stat-card::after {
    content: '';
    position: absolute;
    top: 0;
    right: 0;
    width: 5px;
    height: 100%;
    transition: all 0.3s ease;
}

.upcoming::after { background-color: #1976d2; }
.pending::after { background-color: #f57c00; }
.completed::after { background-color: #388e3c; }

.stat-card:hover::after {
    width: 7px;
}

.stat-card.active {
    border: 2px solid currentColor;
}

.stat-card:focus {
    outline: none;
    box-shadow: 0 0 0 3px rgba(33, 150, 243, 0.3);
}

.stat-icon {
    font-size: 2.5rem;
    margin-right: 15px;
    width: 60px;
    height: 60px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
}

.upcoming .stat-icon {
    background: #e3f2fd;
    color: #1976d2;
}

.pending .stat-icon {
    background: #fff3e0;
    color: #f57c00;
}

.completed .stat-icon {
    background: #e8f5e9;
    color: #388e3c;
}

.stat-info h3 {
    margin: 0;
    font-size: 1rem;
    color: #666;
}

.stat-number {
    font-size: 2rem;
    font-weight: bold;
    margin: 5px 0 0;
    color: #2c3e50;
}

/* Recent Bookings Section */
.recent-bookings {
    width: 100%;
    background: white;
    border-radius: 10px;
    padding: 20px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    margin-top: 20px; /* Added spacing from stats cards */
}

.section-header {
    display: flex;
    align-items: center;
    margin-bottom: 20px;
}

.section-header h2 {
    font-size: 1.5rem;
    color: #2c3e50;
    margin: 0;
}

.section-header i {
    margin-right: 10px;
}

/* Table Styles */
.table-responsive {
    overflow-x: auto;
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    margin: 0;
}

table {
    width: 100%;
    border-collapse: collapse;
}

th {
    background-color: #f8f9fa;
    color: #2c3e50;
    font-weight: 600;
    padding: 12px;
    text-align: left;
}

td {
    padding: 12px;
    border-bottom: 1px solid #eee;
}

tbody tr:last-child td {
    border-bottom: none;
}

/* Status Badge */
.status-badge {
    position: relative;
    padding: 6px 12px 6px 24px;
}

.status-badge::before {
    content: '';
    position: absolute;
    left: 10px;
    top: 50%;
    transform: translateY(-50%);
    width: 6px;
    height: 6px;
    border-radius: 50%;
    background-color: currentColor;
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

/* View Button */
.view-btn {
    position: relative;
    overflow: hidden;
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

.view-btn::after {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    width: 100%;
    height: 100%;
    background: rgba(255,255,255,0.1);
    transform: translate(-50%, -50%) scale(0);
    border-radius: 50%;
    transition: transform 0.3s ease;
}

.view-btn:hover::after {
    transform: translate(-50%, -50%) scale(2);
}

/* Responsive Design */
@media (max-width: 768px) {
    .dashboard-container {
        padding: 10px;
        gap: 20px;
    }
    
    .dashboard-stats {
        flex-direction: column;
    }
    
    .recent-bookings {
        margin-top: 10px;
    }
    
    .stat-card {
        width: 100%;
        margin-bottom: 15px;
    }
    
    .stat-icon {
        width: 50px;
        height: 50px;
        font-size: 2rem;
    }
    
    .section-header h2 {
        font-size: 1.3rem;
    }

    td, th {
        padding: 10px;
    }
}

/* Add styles for empty state */
.no-bookings {
    text-align: center;
    padding: 40px 20px;
    color: #666;
}

.no-bookings i {
    font-size: 3rem;
    color: #ddd;
    margin-bottom: 15px;
    display: block;
}

.no-bookings p {
    margin-bottom: 20px;
    font-size: 1.1rem;
}

.book-now-btn {
    display: inline-block;
    padding: 10px 20px;
    background-color: #2196f3;
    color: white;
    border-radius: 6px;
    text-decoration: none;
    transition: background-color 0.2s;
}

.book-now-btn:hover {
    background-color: #1976d2;
}

/* Loading animation */
.loading {
    text-align: center;
    padding: 20px;
}

.loading i {
    font-size: 2rem;
    color: #2196f3;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}
</style>

<script>
function showUpcomingEvents() {
    const tableContainer = document.getElementById('upcomingEventsTable');
    const tableContent = document.getElementById('bookingsTable');
    
    // Show the table container
    tableContainer.style.display = 'block';
    
    // Show loading state
    tableContent.innerHTML = `
        <div class="loading">
            <i class="fas fa-spinner"></i>
        </div>`;

    // Fetch upcoming events
    fetch(`/SIA/api/get_bookings.php?type=upcoming&user_id=<?php echo $_SESSION['user_id']; ?>`)
        .then(response => response.json())
        .then(data => {
            if (data.length === 0) {
                showEmptyState();
                return;
            }
            updateTable(data);
        })
        .catch(error => {
            console.error('Error:', error);
            showError();
        });
}

function updateTable(data) {
    const tableHTML = `
        <table>
            <thead>
                <tr>
                    <th>Event Date</th>
                    <th>Event Type</th>
                    <th>Service</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                ${data.map(booking => `
                    <tr>
                        <td>${formatDate(booking.event_date)}</td>
                        <td>${escapeHtml(booking.event_type)}</td>
                        <td>${escapeHtml(booking.service_name || 'N/A')}</td>
                        <td>
                            <span class="status-badge ${booking.status}">
                                ${capitalizeFirst(booking.status)}
                            </span>
                        </td>
                        <td>
                            <a href="?page=booking&action=view&id=${booking.booking_id}" 
                               class="view-btn">
                                <i class="fas fa-eye"></i> View
                            </a>
                        </td>
                    </tr>
                `).join('')}
            </tbody>
        </table>
    `;
    document.getElementById('bookingsTable').innerHTML = tableHTML;
}

function showEmptyState() {
    document.getElementById('bookingsTable').innerHTML = `
        <div class="no-bookings">
            <i class="fas fa-calendar-times"></i>
            <p>No upcoming events scheduled</p>
            <a href="?page=book" class="book-now-btn">Book Now</a>
        </div>
    `;
}

function showError() {
    document.getElementById('bookingsTable').innerHTML = `
        <div class="no-bookings">
            <i class="fas fa-exclamation-circle"></i>
            <p>Something went wrong. Please try again later.</p>
        </div>
    `;
}

// Helper functions
function formatDate(dateString) {
    return new Date(dateString).toLocaleDateString('en-US', {
        month: 'short',
        day: 'numeric',
        year: 'numeric'
    });
}

function escapeHtml(str) {
    if (!str) return '';
    return str
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&#039;");
}

function capitalizeFirst(string) {
    return string.charAt(0).toUpperCase() + string.slice(1);
}
</script>
