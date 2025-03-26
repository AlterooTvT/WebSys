<?php
// Ensure session is started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check admin access
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: /SIA/auth/login.php');
    exit();
}

require_once $_SERVER['DOCUMENT_ROOT'] . '/SIA/database/database.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/SIA/app/models/Payment.php';

$database = new Database();
$db = $database->getConnection();
$payment = new Payment($db);

// Get filter parameters
$status = isset($_GET['status']) ? $_GET['status'] : 'all';
$dateRange = isset($_GET['date_range']) ? $_GET['date_range'] : 'all';

// Get payments based on filters
$allPayments = $payment->getFilteredPayments($status, $dateRange);

// Calculate summary statistics
$totalAmount = array_sum(array_column($allPayments, 'amount'));
$pendingCount = count(array_filter($allPayments, fn($p) => $p['status'] == 'pending'));
$verifiedCount = count(array_filter($allPayments, fn($p) => $p['status'] == 'verified'));
?>

<div class="admin-container">
    <div class="page-title">
        <h1>Payments Management</h1>
    </div>

    <!-- Dashboard Summary Cards -->
    <div class="dashboard-summary">
        <div class="summary-card total">
            <div class="card-icon">
                <i class="fas fa-money-bill-wave"></i>
            </div>
            <div class="card-content">
                <h3>Total Payments</h3>
                <p class="amount">₱<?php echo number_format($totalAmount, 2); ?></p>
            </div>
        </div>
        <div class="summary-card pending">
            <div class="card-icon">
                <i class="fas fa-clock"></i>
            </div>
            <div class="card-content">
                <h3>Pending Verification</h3>
                <p class="count"><?php echo $pendingCount; ?></p>
            </div>
        </div>
        <div class="summary-card verified">
            <div class="card-icon">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="card-content">
                <h3>Verified Payments</h3>
                <p class="count"><?php echo $verifiedCount; ?></p>
            </div>
        </div>
    </div>

    <!-- Filters Section -->
    <div class="filters-section">
        <form id="filterForm" method="GET">
            <div class="filter-group">
                <label for="status">Payment Status:</label>
                <select name="status" id="status" onchange="this.form.submit()">
                    <option value="all" <?php echo $status == 'all' ? 'selected' : ''; ?>>All Status</option>
                    <option value="pending" <?php echo $status == 'pending' ? 'selected' : ''; ?>>Pending</option>
                    <option value="verified" <?php echo $status == 'verified' ? 'selected' : ''; ?>>Verified</option>
                    <option value="rejected" <?php echo $status == 'rejected' ? 'selected' : ''; ?>>Rejected</option>
                </select>
            </div>
            
            <div class="filter-group">
                <label for="date_range">Date Range:</label>
                <select name="date_range" id="date_range" onchange="this.form.submit()">
                    <option value="all" <?php echo $dateRange == 'all' ? 'selected' : ''; ?>>All Time</option>
                    <option value="today" <?php echo $dateRange == 'today' ? 'selected' : ''; ?>>Today</option>
                    <option value="week" <?php echo $dateRange == 'week' ? 'selected' : ''; ?>>This Week</option>
                    <option value="month" <?php echo $dateRange == 'month' ? 'selected' : ''; ?>>This Month</option>
                </select>
            </div>
        </form>
    </div>

    <!-- Payments Table -->
    <div class="table-responsive">
        <table class="payments-table">
            <thead>
                <tr>
                    <th>Payment ID</th>
                    <th>Booking ID</th>
                    <th>Client Name</th>
                    <th>Amount</th>
                    <th>Payment Type</th>
                    <th>Status</th>
                    <th>Event Date</th>
                    <th>Proof</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($allPayments)): ?>
                    <tr>
                        <td colspan="9" class="no-records">No payments found</td>
                    </tr>
                <?php else: ?>
                    <?php foreach($allPayments as $pay): ?>
                    <tr>
                        <td><?php echo $pay['payment_id']; ?></td>
                        <td>#<?php echo $pay['booking_id']; ?></td>
                        <td><?php echo htmlspecialchars($pay['client_name']); ?></td>
                        <td>₱<?php echo number_format($pay['amount'], 2); ?></td>
                        <td><?php echo ucfirst($pay['payment_type']); ?></td>
                        <td>
                            <span class="status-badge <?php echo $pay['status']; ?>">
                                <?php echo ucfirst($pay['status']); ?>
                            </span>
                        </td>
                        <td><?php echo date('M d, Y', strtotime($pay['event_date'])); ?></td>
                        <td>
                            <?php if($pay['proof_of_payment']): ?>
                                <button onclick="viewProof('<?php echo htmlspecialchars($pay['proof_of_payment']); ?>')" 
                                        class="btn-view-proof">
                                    <i class="fas fa-eye"></i> View
                                </button>
                            <?php else: ?>
                                <span class="no-proof">No proof</span>
                            <?php endif; ?>
                        </td>
                        <td class="actions">
                            <?php if($pay['status'] == 'pending'): ?>
                                <button onclick="updatePaymentStatus(<?php echo $pay['payment_id']; ?>, 'verified')" 
                                        class="btn-verify">
                                    <i class="fas fa-check"></i>
                                </button>
                                <button onclick="updatePaymentStatus(<?php echo $pay['payment_id']; ?>, 'rejected')" 
                                        class="btn-reject">
                                    <i class="fas fa-times"></i>
                                </button>
                            <?php else: ?>
                                <a href="?page=payment_details&id=<?php echo $pay['payment_id']; ?>" 
                                   class="btn-view">Details</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Payment Proof Modal -->
<div id="proofModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h2>Proof of Payment</h2>
        <img id="proofImage" src="" alt="Payment Proof" style="max-width: 100%; height: auto;">
    </div>
</div>

<style>
.admin-container {
    padding: 20px;
    max-width: 1400px;
    margin: 0 auto;
}

.page-title {
    margin-bottom: 30px;
    text-align: center;
}

.page-title h1 {
    color: #333;
    font-size: 2em;
}

/* Dashboard Summary */
.dashboard-summary {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}

.summary-card {
    background: white;
    padding: 20px;
    border-radius: 10px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    display: flex;
    align-items: center;
}

.card-icon {
    font-size: 2em;
    margin-right: 15px;
}

.summary-card.total .card-icon { color: #2ecc71; }
.summary-card.pending .card-icon { color: #f1c40f; }
.summary-card.verified .card-icon { color: #3498db; }

.card-content h3 {
    margin: 0;
    font-size: 1em;
    color: #666;
}

.card-content p {
    margin: 5px 0 0;
    font-size: 1.5em;
    font-weight: bold;
    color: #333;
}

/* Filters Section */
.filters-section {
    background: white;
    padding: 20px;
    border-radius: 10px;
    margin-bottom: 30px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.filter-group {
    display: inline-block;
    margin-right: 20px;
}

.filter-group label {
    margin-right: 10px;
    color: #666;
}

.filter-group select {
    padding: 8px;
    border: 1px solid #ddd;
    border-radius: 5px;
    background: white;
}

/* Table Styles */
.table-responsive {
    overflow-x: auto;
    background: white;
    border-radius: 10px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.payments-table {
    width: 100%;
    border-collapse: collapse;
    margin: 0;
}

.payments-table th {
    background: #f8f9fa;
    padding: 12px;
    text-align: left;
    color: #333;
    font-weight: 600;
}

.payments-table td {
    padding: 12px;
    border-top: 1px solid #eee;
}

/* Status Badges */
.status-badge {
    padding: 5px 10px;
    border-radius: 15px;
    font-size: 0.9em;
}

.status-badge.pending { background: #fff3cd; color: #856404; }
.status-badge.verified { background: #d4edda; color: #155724; }
.status-badge.rejected { background: #f8d7da; color: #721c24; }

/* Buttons */
.btn-verify, .btn-reject, .btn-view-proof, .btn-view {
    padding: 6px 12px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-size: 0.9em;
    margin: 0 2px;
}

.btn-verify { background: #2ecc71; color: white; }
.btn-reject { background: #e74c3c; color: white; }
.btn-view-proof { background: #3498db; color: white; }
.btn-view { background: #95a5a6; color: white; text-decoration: none; }

/* Modal */
.modal {
    display: none;
    position: fixed;
    z-index: 1000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0,0,0,0.7);
}

.modal-content {
    background-color: #fefefe;
    margin: 5% auto;
    padding: 20px;
    border-radius: 8px;
    width: 80%;
    max-width: 800px;
    position: relative;
}

.close {
    position: absolute;
    right: 20px;
    top: 10px;
    color: #aaa;
    font-size: 28px;
    font-weight: bold;
    cursor: pointer;
}

.close:hover {
    color: #333;
}

#proofImage {
    display: block;
    margin: 20px auto;
    max-height: 80vh;
    object-fit: contain;
}

.no-records {
    text-align: center;
    padding: 20px;
    color: #666;
}

.no-proof {
    color: #999;
    font-style: italic;
}

/* Responsive Design */
@media (max-width: 768px) {
    .dashboard-summary {
        grid-template-columns: 1fr;
    }

    .filter-group {
        display: block;
        margin-bottom: 10px;
    }

    .actions {
        display: flex;
        gap: 5px;
    }
}
</style>

<script>
function viewProof(imagePath) {
    const baseUrl = '/SIA/uploads/payments/'; // Update this path to match your server structure
    document.getElementById('proofImage').src = baseUrl + imagePath;
    document.getElementById('proofModal').style.display = 'block';
}

function updatePaymentStatus(paymentId, status) {
    if (!confirm(`Are you sure you want to mark this payment as ${status}?`)) {
        return;
    }

    // Show loading state
    const btn = event.target.closest('button');
    const originalContent = btn.innerHTML;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
    btn.disabled = true;

    fetch('/SIA/api/update_payment_status.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            payment_id: paymentId,
            status: status
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            // Show success message
            alert('Payment status updated successfully!');
            // Reload the page to show updated data
            location.reload();
        } else {
            throw new Error(data.message || 'Failed to update payment status');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error: ' + error.message);
        // Restore button state
        btn.innerHTML = originalContent;
        btn.disabled = false;
    });
}

// Close modal when clicking the X
document.querySelector('.close').onclick = function() {
    document.getElementById('proofModal').style.display = 'none';
}

// Close modal when clicking outside
window.onclick = function(event) {
    const modal = document.getElementById('proofModal');
    if (event.target == modal) {
        modal.style.display = 'none';
    }
}
</script>
