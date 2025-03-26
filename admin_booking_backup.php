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
        /* Overall container styling */
        .bookings-container {
            max-width: 1200px;
            margin: 40px auto;
            padding: 20px;
            background-color: #f8f8f8;
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        /* Page Title */
        .bookings-container h2 {
            font-size: 2rem;
            color: #333;
            margin-bottom: 20px;
            text-align: center;
        }
        /* Table Styles */
        .bookings-table {
            width: 100%;
            border-collapse: collapse;
            background: #fff;
            border-radius: 6px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        .bookings-table th,
        .bookings-table td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        .bookings-table th {
            background-color: #f0f0f0;
            font-weight: 600;
        }
        .bookings-table tbody tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .bookings-table tbody tr:hover {
            background-color: #f2f2f2;
        }
        /* Status Badge */
        .status-badge {
            display: inline-block;
            padding: 5px 10px;
            font-size: 0.9rem;
            border-radius: 4px;
            color: #fff;
        }
        .status-badge.pending {
            background-color: #f39c12;
        }
        .status-badge.approved {
            background-color: #3498db;
        }
        .status-badge.declined {
            background-color: #e74c3c;
        }
        /* Action Buttons */
        .action-buttons {
            display: flex;
            justify-content: space-evenly;
            align-items: center;
        }
        .btn-view,
        .btn-status {
            padding: 8px 16px;
            font-size: 0.9rem;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            transition: background 0.2s ease;
        }
        .btn-view {
            background-color: #3498db;
            color: #fff;
            margin-right: 8px;
        }
        .btn-view:hover {
            background-color: #2980b9;
        }
        .btn-status {
            background-color: #2ecc71;
            color: #fff;
        }
        .btn-status:hover {
            background-color: #27ae60;
        }
        /* Responsive Adjustments */
        @media (max-width: 768px) {
            .bookings-container {
                padding: 10px;
            }
            .bookings-table th,
            .bookings-table td {
                padding: 10px;
            }
        }
        /* Modal Styles */
        .modal {
            display: none; /* Hidden by default */
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0,0,0,0.5); /* Semi-transparent background */
        }
        .modal-content {
            background-color: #fff;
            margin: 10% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 300px;
            border-radius: 8px;
            text-align: center;
            position: relative;
        }
        .modal h3 {
            margin-bottom: 20px;
        }
        .modal-btn {
            margin: 10px;
            padding: 10px 20px;
            background-color: #3498db;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .modal-btn:hover {
            background-color: #2980b9;
        }
        .modal-footer {
            margin-top: 20px;
            text-align: right;
        }
        .modal-cancel-button {
            padding: 8px 16px;
            background-color: #e74c3c;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .modal-cancel-button:hover {
            background-color: #c0392b;
        }
        /* Form Styles Within Modal */
        #statusForm input, #statusForm textarea {
            margin-bottom: 10px;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        #statusForm label {
            font-weight: bold;
            margin-bottom: 5px;
            display: block;
            text-align: left;
        }
    </style>
</head>
<body>
    <div class="bookings-container">
        <h2>Booking Requests</h2>
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
                                <a class="btn-view" href="pages/booking_details.php?booking_id=<?php echo $booking['booking_id']; ?>">View Details</a>
                                <!-- Button that opens the modal -->
                                <button class="btn-status" onclick="openStatusModal(<?php echo $booking['booking_id']; ?>)">Update Status</button>
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
    <div id="statusModal" class="modal">
        <div class="modal-content" id="modalContent">
            <!-- Initial state: Choose Action -->
            <div id="actionChoice">
                <h3>Select Action</h3>
                <button class="modal-btn" onclick="selectAction('approved')">Approve</button>
                <button class="modal-btn" onclick="selectAction('declined')">Decline</button>
                <div class="modal-footer">
                    <button class="modal-cancel-button" onclick="closeStatusModal()">Cancel</button>
                </div>
            </div>
            <!-- Form state: Enter email details and final price if applicable -->
            <div id="messageForm" style="display: none;">
                <h3 id="formTitle"></h3>
                <form id="statusForm" onsubmit="submitStatusForm(event)">
                    <div id="subjectDiv">
                        <label for="emailSubject">Email Subject:</label>
                        <input type="text" id="emailSubject" name="emailSubject" style="width:100%;">
                    </div>
                    <div>
                        <label for="emailMessage">Email Message:</label>
                        <textarea id="emailMessage" name="emailMessage" rows="4" style="width:100%;"></textarea>
                    </div>
                    <div id="finalPriceDiv" style="display: none;">
                        <label for="finalPrice">Final Price:</label>
                        <input type="number" id="finalPrice" name="finalPrice" style="width:100%;">
                    </div>
                    <div style="margin-top:15px; text-align: right;">
                        <button type="submit" class="modal-btn">Submit</button>
                        <button type="button" class="modal-cancel-button" onclick="resetModal()">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        var currentBookingId = null;
        var currentAction = null;

        function openStatusModal(bookingId) {
            currentBookingId = bookingId;
            document.getElementById('statusModal').style.display = 'block';
            // Show the action chooser by default
            document.getElementById('actionChoice').style.display = 'block';
            document.getElementById('messageForm').style.display = 'none';
        }

        function closeStatusModal() {
            document.getElementById('statusModal').style.display = 'none';
            currentBookingId = null;
            currentAction = null;
        }

        function resetModal() {
            document.getElementById('statusForm').reset();
            document.getElementById('actionChoice').style.display = 'block';
            document.getElementById('messageForm').style.display = 'none';
            currentAction = null;
        }

        function selectAction(action) {
            currentAction = action; // "approved" or "declined"
            // Hide the action choice and show the form view
            document.getElementById('actionChoice').style.display = 'none';
            document.getElementById('messageForm').style.display = 'block';

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
                document.getElementById('finalPriceDiv').style.display = 'block';
            } else if (action === 'declined') {
                document.getElementById('formTitle').innerText = 'Decline Booking';
                document.getElementById('emailSubject').value = 'Booking Declined';
                document.getElementById('emailMessage').value = "";
                document.getElementById('finalPriceDiv').style.display = 'none';
            }
        }

        function submitStatusForm(event) {
            event.preventDefault();
            
            const formData = new FormData();
            formData.append('bookingId', currentBookingId);
            formData.append('newStatus', currentAction);
            formData.append('emailSubject', document.getElementById('emailSubject').value);
            formData.append('emailMessage', document.getElementById('emailMessage').value);
            
            // Debug log
            console.log('Current Action:', currentAction);
            console.log('Booking ID:', currentBookingId);
            
            if (currentAction === 'approved') {
                const finalPrice = document.getElementById('finalPrice').value;
                console.log('Final Price:', finalPrice); // Debug log
                
                if (!finalPrice || finalPrice <= 0) {
                    alert('Please enter a valid final price');
                    return;
                }
                formData.append('finalPrice', finalPrice);
            }

            // Log all form data
            for (let pair of formData.entries()) {
                console.log(pair[0] + ': ' + pair[1]);
            }

            fetch('/SIA/api/update_status.php', {
                method: 'POST',
                body: formData
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.text().then(text => {
                    try {
                        return JSON.parse(text);
                    } catch (e) {
                        console.error('Parse error:', text);
                        throw new Error('Invalid JSON response from server');
                    }
                });
            })
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

        // Optional: Close modal when clicking outside of the modal content.
        window.onclick = function(event) {
            var modal = document.getElementById('statusModal');
            if (event.target == modal) {
                closeStatusModal();
            }
        }
    </script>

</body>
</html>
