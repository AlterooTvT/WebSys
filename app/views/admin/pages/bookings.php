<?php
// Ensure session is started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once $_SERVER['DOCUMENT_ROOT'] . '/SIA/database/database.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/SIA/app/models/Booking.php';

// Check if user is admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: /SIA/auth/login.php');
    exit();
}

$database = new Database();
$db = $database->getConnection();
$bookingModel = new Booking($db);

// Pagination logic
$limit = 10; // Number of bookings per page
$page = isset($_GET['page']) ? intval($_GET['page']) : 1; // Get current page from URL (default to 1)
if ($page < 1) $page = 1;
$offset = ($page - 1) * $limit;

// Fetch paginated bookings
$bookings = $bookingModel->getAllBookings($limit, $offset)->fetchAll(PDO::FETCH_ASSOC);

// Count total bookings for pagination
$totalBookings = $bookingModel->getTotalBookings();
$totalPages = ceil($totalBookings / $limit);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Manage Bookings</title>
    <style>
        /* Dashboard Container */
        .bookings-container {
            max-width: 1200px;
            margin: 20px auto;
            padding: 25px;
            background-color: #fff;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        /* Page Title */
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding-bottom: 15px;
            border-bottom: 2px solid #f0f0f0;
        }
        .page-header h2 {
            font-size: 1.8rem;
            color: #2c3e50;
            margin: 0;
        }
        /* Table Styling */
        .bookings-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            background: #fff;
            border-radius: 8px;
            overflow: hidden;
            margin-bottom: 20px;
            border: none;
        }
        .bookings-table th {
            background-color: #f8f9fa;
            color: #2c3e50;
            font-weight: 600;
            padding: 15px;
            text-align: left;
            border: none;
            border-bottom: 2px solid #e9ecef;
        }
        .bookings-table td {
            padding: 12px 15px;
            border: none;
            border-bottom: 1px solid #e9ecef;
            color: #2d3436;
        }
        .bookings-table tbody tr:last-child td {
            border-bottom: none;
        }
        .bookings-table tbody tr:hover {
            background-color: #f8f9fa;
        }
        /* Status Badge */
        .status-badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 500;
            text-transform: capitalize;
        }
        .status-badge.pending {
            background-color: #ffeaa7;
            color: #b7791f;
        }
        .status-badge.approved {
            background-color: #c6f6d5;
            color: #2f855a;
        }
        .status-badge.declined {
            background-color: #fed7d7;
            color: #c53030;
        }
        /* Action Buttons */
        .action-buttons {
            display: flex;
            gap: 8px;
            align-items: center;
            flex-wrap: wrap;
        }
        .btn-view,
        .btn-status {
            padding: 8px 16px;
            border-radius: 6px;
            font-size: 0.9rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s ease;
            border: none;
            text-decoration: none;
        }
        .btn-view {
            background-color: #4299e1;
            color: white;
        }
        .btn-view:hover {
            background-color: #3182ce;
        }
        .btn-status {
            background-color: #48bb78;
            color: white;
        }
        .btn-status:hover {
            background-color: #38a169;
        }
        /* Pagination */
        .pagination {
            display: flex;
            justify-content: center;
            gap: 8px;
            margin-top: 20px;
        }
        .pagination a {
            padding: 8px 16px;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            color: #4a5568;
            text-decoration: none;
            transition: all 0.2s ease;
        }
        .pagination a:hover {
            background-color: #edf2f7;
        }
        .pagination a.active {
            background-color: #4299e1;
            color: white;
            border-color: #4299e1;
        }
        /* Modal Styling */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(5px);
        }
        .modal.active {
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .modal-content {
            background-color: #fff;
            border-radius: 12px;
            padding: 25px;
            width: 400px;
            position: relative;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            margin: auto;
            animation: modalSlideIn 0.3s ease-out;
        }
        @keyframes modalSlideIn {
            from {
                transform: translateY(-20px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }
        .modal h3 {
            color: #2d3748;
            font-size: 1.5rem;
            margin-bottom: 20px;
        }
        /* Action Buttons in Modal */
        .modal-actions {
            display: flex;
            gap: 10px;
            margin-top: 20px;
        }
        .modal-btn {
            flex: 1;
            padding: 10px 20px;
            border-radius: 6px;
            border: none;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        .modal-btn.approve {
            background-color: #48bb78;
            color: white;
        }
        .modal-btn.approve:hover {
            background-color: #38a169;
        }
        .modal-btn.decline {
            background-color: #f56565;
            color: white;
        }
        .modal-btn.decline:hover {
            background-color: #e53e3e;
        }
        .modal-btn.cancel {
            background-color: #a0aec0;
            color: white;
        }
        .modal-btn.cancel:hover {
            background-color: #718096;
        }
        /* Close button */
        .close {
            position: absolute;
            right: 15px;
            top: 15px;
            font-size: 24px;
            font-weight: bold;
            color: #a0aec0;
            cursor: pointer;
            transition: color 0.2s ease;
        }
        .close:hover {
            color: #4a5568;
        }
        /* Responsive Design */
        @media (max-width: 768px) {
            .bookings-container {
                margin: 10px;
                padding: 15px;
            }
            .bookings-table {
                display: block;
                overflow-x: auto;
            }
            .action-buttons {
                flex-direction: column;
                align-items: stretch;
            }
            .modal-content {
                width: 90%;
                margin: 20px;
            }
            .modal-actions {
                flex-direction: column;
            }
            .modal-btn {
                width: 100%;
            }
            .status-info {
                text-align: center;
            }
        }
        .status-info {
            display: inline-flex;
            align-items: center;
            padding: 8px 16px;
            border-radius: 6px;
            font-size: 0.9rem;
            color: #718096;
            background-color: #f7fafc;
            border: 1px solid #e2e8f0;
        }
        .status-info i {
            margin-right: 8px;
        }
        .status-info .fa-check-circle {
            color: #48bb78;
        }
        .status-info .fa-times-circle {
            color: #f56565;
        }
    </style>
</head>
<body>
    <div class="bookings-container">
        <div class="page-header">
            <h2>Booking Requests</h2>
        </div>
        <table class="bookings-table" border="1" cellpadding="5" cellspacing="0">
            <thead>
                <tr>
                    <th>Booking ID</th>
                    <th>Client</th>
                    <th>Event Date</th>
                    <th>Event Type</th>
                    <th>Location</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($bookings as $booking): ?>
                    <tr>
                        <td><?php echo $booking['booking_id']; ?></td>
                        <td>
                            <?php echo htmlspecialchars($booking['first_name'] . ' ' . $booking['last_name']); ?><br>
                            <small><?php echo htmlspecialchars($booking['email']); ?></small>
                        </td>
                        <td><?php echo date('M d, Y', strtotime($booking['event_date'])); ?></td>
                        <td><?php echo htmlspecialchars($booking['event_type']); ?></td>
                        <td><?php echo htmlspecialchars($booking['location']); ?></td>
                        <td>
                            <span class="status-badge <?php echo $booking['status']; ?>">
                                <?php echo ucfirst($booking['status']); ?>
                            </span>
                        </td>
                        <td>
                            <div class="action-buttons">
                                <a class="btn-view" href="pages/booking_details.php?booking_id=<?php echo $booking['booking_id']; ?>">
                                    <i class="fas fa-eye"></i> View Details
                                </a>
                                <?php if ($booking['status'] === 'pending'): ?>
                                    <button class="btn-status" onclick="openModal(<?php echo $booking['booking_id']; ?>)">
                                        <i class="fas fa-edit"></i> Update Status
                                    </button>
                                <?php else: ?>
                                    <span class="status-info">
                                        <?php if ($booking['status'] === 'approved'): ?>
                                            <i class="fas fa-check-circle"></i> Approved
                                        <?php elseif ($booking['status'] === 'declined'): ?>
                                            <i class="fas fa-times-circle"></i> Declined
                                        <?php endif; ?>
                                    </span>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <!-- Pagination links -->
        <div class="pagination">
            <?php if ($page > 1): ?>
                <a href="?page=<?php echo $page - 1; ?>">Previous</a>
            <?php endif; ?>
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <a href="?page=<?php echo $i; ?>" class="<?php echo $i == $page ? 'active' : ''; ?>"><?php echo $i; ?></a>
            <?php endfor; ?>
            <?php if ($page < $totalPages): ?>
                <a href="?page=<?php echo $page + 1; ?>">Next</a>
            <?php endif; ?>
        </div>
    </div>

    <!-- Modal for Updating Booking Status -->
    <div id="actionModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            
            <!-- Action Choice View -->
            <div id="actionChoice">
                <h3>Select Action</h3>
                <div class="modal-actions">
                    <button class="modal-btn approve" onclick="updateBookingStatus('approved')">
                        <i class="fas fa-check"></i> Approve
                    </button>
                    <button class="modal-btn decline" onclick="updateBookingStatus('declined')">
                        <i class="fas fa-times"></i> Decline
                    </button>
                    <button class="modal-btn cancel" onclick="closeModal()">
                        <i class="fas fa-ban"></i> Cancel
                    </button>
                </div>
            </div>

            <!-- Message Form View -->
            <div id="messageForm" style="display: none;">
                <h3 id="formTitle">Update Booking Status</h3>
                <form id="statusForm" onsubmit="submitStatusForm(event)">
                    <div class="form-group">
                        <label for="emailSubject">Email Subject:</label>
                        <input type="text" id="emailSubject" required>
                    </div>

                    <div class="form-group">
                        <label for="emailMessage">Email Message:</label>
                        <textarea id="emailMessage" required></textarea>
                    </div>

                    <div id="finalPriceDiv" class="form-group" style="display: none;">
                        <label for="finalPrice">Final Price:</label>
                        <input type="number" id="finalPrice" min="0" step="0.01">
                    </div>

                    <div class="modal-actions">
                        <button type="submit" class="modal-btn approve">
                            <i class="fas fa-paper-plane"></i> Send
                        </button>
                        <button type="button" class="modal-btn cancel" onclick="closeModal()">
                            <i class="fas fa-times"></i> Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        var currentBookingId = null;
        var currentAction = null;

        function openModal(bookingId) {
            const modal = document.getElementById('actionModal');
            const actionChoice = document.getElementById('actionChoice');
            const messageForm = document.getElementById('messageForm');
            
            // Reset form views
            actionChoice.style.display = 'block';
            messageForm.style.display = 'none';
            
            // Show modal
            modal.classList.add('active');
            modal.style.display = 'flex';
            currentBookingId = bookingId;
        }

        function closeModal() {
            const modal = document.getElementById('actionModal');
            const actionChoice = document.getElementById('actionChoice');
            const messageForm = document.getElementById('messageForm');
            
            // Reset everything
            modal.classList.remove('active');
            modal.style.display = 'none';
            actionChoice.style.display = 'block';
            messageForm.style.display = 'none';
            currentBookingId = null;
            currentAction = null;
        }

        function updateBookingStatus(action) {
            currentAction = action;
            const actionChoice = document.getElementById('actionChoice');
            const messageForm = document.getElementById('messageForm');
            const finalPriceDiv = document.getElementById('finalPriceDiv');
            
            // Hide action choice and show message form
            actionChoice.style.display = 'none';
            messageForm.style.display = 'block';

            if (action === 'approved') {
                document.getElementById('formTitle').innerText = 'Approve Booking';
                document.getElementById('emailSubject').value = 'Booking Approved';
                document.getElementById('emailMessage').value = 
                    "Dear Client,\n\n" +
                    "Your booking has been approved. Please proceed with the payment as you prefer. " +
                    "If you decide to make a downpayment, you may do so via GCash or bank transfer, and it should be at least 50% of your total booking cost. " +
                    "Please note: If you do not pay the downpayment before the event, your booking will be considered cancelled. " +
                    "If you opt for a downpayment, the remaining balance can be paid in cash on the day of the event.\n\n" +
                    "GCash\n" +
                    "GCash: 0123456789\n\n" +
                    "BDO\n" +
                    "Bank Account: 9876543210\n\n" +
                    "Thank you.";
                finalPriceDiv.style.display = 'block';
            } else {
                document.getElementById('formTitle').innerText = 'Decline Booking';
                document.getElementById('emailSubject').value = 'Booking Declined';
                document.getElementById('emailMessage').value = '';
                finalPriceDiv.style.display = 'none';
            }
        }

        function submitStatusForm(event) {
            event.preventDefault();
            
            const formData = new FormData();
            formData.append('bookingId', currentBookingId);
            formData.append('newStatus', currentAction);
            formData.append('emailSubject', document.getElementById('emailSubject').value);
            formData.append('emailMessage', document.getElementById('emailMessage').value);
            
            if (currentAction === 'approved') {
                const finalPrice = document.getElementById('finalPrice').value;
                if (!finalPrice || finalPrice <= 0) {
                    alert('Please enter a valid final price');
                    return;
                }
                formData.append('finalPrice', finalPrice);
            }

            fetch('/SIA/api/update_status.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    alert('Booking updated successfully');
                    location.reload();
                } else {
                    throw new Error(data.message || 'Failed to update booking');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while updating the booking: ' + error.message);
            });
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            const modal = document.getElementById('actionModal');
            if (event.target == modal) {
                closeModal();
            }
        }

        // Close modal when pressing Escape key
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                closeModal();
            }
        });

        // Close button functionality
        document.querySelector('.close').onclick = closeModal;
    </script>

</body>
</html>
