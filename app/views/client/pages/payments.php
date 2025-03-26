<?php
// Ensure session is started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: /SIA/auth/login.php');
    exit();
}

require_once $_SERVER['DOCUMENT_ROOT'] . '/SIA/database/database.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/SIA/app/models/Payment.php';

$database = new Database();
$db = $database->getConnection();
$payment = new Payment($db);

// Get user payments with error handling
try {
    $user_payments = $payment->getUserPayments($_SESSION['user_id']);
} catch (Exception $e) {
    error_log("Error fetching user payments: " . $e->getMessage());
    $user_payments = [];
}

// Calculate summary statistics
$totalAmount = array_sum(array_column($user_payments, 'amount'));
$pendingCount = count(array_filter($user_payments, fn($p) => $p['status'] == 'pending'));
$verifiedCount = count(array_filter($user_payments, fn($p) => $p['status'] == 'verified'));
?>

<div class="client-container">
    <div class="page-title">
        <h1>My Payments</h1>
    </div>

    <!-- Dashboard Summary Cards -->
    <div class="dashboard-summary">
        <div class="summary-card total">
            <div class="card-icon">
                <i class="fas fa-money-bill-wave"></i>
            </div>
            <div class="card-content">
                <h3>Total Amount</h3>
                <p class="amount">₱<?php echo number_format($totalAmount, 2); ?></p>
            </div>
        </div>
        <div class="summary-card pending">
            <div class="card-icon">
                <i class="fas fa-clock"></i>
            </div>
            <div class="card-content">
                <h3>Pending Payments</h3>
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

    <!-- Payments Table -->
    <div class="table-responsive">
        <?php if (empty($user_payments)): ?>
            <div class="no-payments">
                <i class="fas fa-file-invoice-dollar no-data-icon"></i>
                <p>No payments found.</p>
            </div>
        <?php else: ?>
            <table class="payments-table">
                <thead>
                    <tr>
                        <th>Booking ID</th>
                        <th>Event Date</th>
                        <th>Amount</th>
                        <th>Payment Type</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($user_payments as $payment): ?>
                    <tr>
                        <td>#<?php echo $payment['booking_id']; ?></td>
                        <td><?php echo date('M d, Y', strtotime($payment['event_date'])); ?></td>
                        <td>₱<?php echo number_format($payment['amount'], 2); ?></td>
                        <td><?php echo ucfirst(str_replace('_', ' ', $payment['payment_type'])); ?></td>
                        <td>
                            <span class="status-badge <?php echo $payment['status']; ?>">
                                <?php echo ucfirst($payment['status']); ?>
                            </span>
                        </td>
                        <td>
                            <?php if($payment['status'] == 'pending'): ?>
                                <button onclick="showPaymentModal(<?php echo $payment['payment_id']; ?>, <?php echo $payment['amount']; ?>)" 
                                        class="btn-pay">
                                    <i class="fas fa-upload"></i> Upload Payment
                                </button>
                            <?php else: ?>
                                <button onclick="viewPaymentDetails(<?php echo $payment['payment_id']; ?>)" 
                                        class="btn-view">
                                    <i class="fas fa-eye"></i> View Details
                                </button>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>

<!-- Payment Upload Modal -->
<div id="paymentModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h2>Upload Payment Proof</h2>
        
        <div class="payment-details">
            <p>Amount to Pay: <span id="paymentAmount" class="amount-display"></span></p>
        </div>

        <form id="paymentForm" enctype="multipart/form-data">
            <input type="hidden" id="payment_id" name="payment_id">
            
            <div class="form-group">
                <label for="proof_of_payment">Upload Proof of Payment:</label>
                <input type="file" 
                       id="proof_of_payment" 
                       name="proof_of_payment" 
                       accept="image/jpeg,image/png,image/jpg" 
                       required>
            </div>

            <div class="payment-instructions">
                <h4><i class="fas fa-info-circle"></i> Payment Instructions:</h4>
                <ol>
                    <li>Make your payment through your preferred payment method</li>
                    <li>Take a clear screenshot or photo of the payment confirmation</li>
                    <li>Upload the image using the form above</li>
                    <li>Wait for admin verification</li>
                </ol>
            </div>

            <button type="submit" class="btn-submit">
                <i class="fas fa-upload"></i> Submit Payment Proof
            </button>
        </form>
    </div>
</div>

<style>
.client-container {
    padding: 20px;
    max-width: 1200px;
    margin: 0 auto;
}

.page-title {
    margin-bottom: 30px;
}

.page-title h1 {
    color: #333;
    font-size: 2em;
}

/* Dashboard Summary */
.dashboard-summary {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 20px;
    margin-bottom: 30px;
}

.summary-card {
    background: white;
    border-radius: 10px;
    padding: 20px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    display: flex;
    align-items: center;
}

.card-icon {
    font-size: 2em;
    margin-right: 15px;
    width: 50px;
    text-align: center;
}

.total .card-icon { color: #2ecc71; }
.pending .card-icon { color: #f1c40f; }
.verified .card-icon { color: #3498db; }

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

/* Table Styles */
.table-responsive {
    background: white;
    border-radius: 10px;
    padding: 20px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    overflow-x: auto;
}

.payments-table {
    width: 100%;
    border-collapse: collapse;
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
.btn-pay, .btn-view, .btn-submit {
    padding: 8px 15px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    font-size: 0.9em;
    display: inline-flex;
    align-items: center;
    gap: 5px;
}

.btn-pay { background: #2ecc71; color: white; }
.btn-view { background: #3498db; color: white; }
.btn-submit { 
    background: #2ecc71; 
    color: white;
    width: 100%;
    padding: 12px;
    font-size: 1em;
    margin-top: 20px;
}

/* Modal Styles */
.modal {
    display: none;
    position: fixed;
    z-index: 1000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0,0,0,0.5);
}

.modal-content {
    background-color: #fefefe;
    margin: 5% auto;
    padding: 30px;
    border-radius: 10px;
    width: 90%;
    max-width: 500px;
}

.close {
    color: #aaa;
    float: right;
    font-size: 28px;
    font-weight: bold;
    cursor: pointer;
}

.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    margin-bottom: 5px;
    color: #333;
}

.amount-display {
    font-size: 1.5em;
    color: #2ecc71;
    font-weight: bold;
}

.payment-instructions {
    background: #f8f9fa;
    padding: 15px;
    border-radius: 5px;
    margin: 20px 0;
}

.payment-details {
    margin-top: 10px;
}

.no-payments {
    text-align: center;
    padding: 40px;
    color: #666;
}

.no-data-icon {
    font-size: 3em;
    color: #ddd;
    margin-bottom: 15px;
}

/* Responsive Design */
@media (max-width: 768px) {
    .dashboard-summary {
        grid-template-columns: 1fr;
    }

    .modal-content {
        margin: 10% auto;
        width: 95%;
    }
}

.form-group input[type="file"] {
    width: 100%;
    padding: 8px;
    border: 1px solid #ddd;
    border-radius: 4px;
    margin-top: 5px;
}

.btn-submit:disabled {
    background: #cccccc;
    cursor: not-allowed;
}
</style>

<script>
function showPaymentModal(paymentId, amount) {
    document.getElementById('payment_id').value = paymentId;
    document.getElementById('paymentAmount').textContent = '₱' + amount.toFixed(2);
    document.getElementById('paymentModal').style.display = 'block';
}

function viewPaymentDetails(paymentId) {
    window.location.href = `?page=payment_details&id=${paymentId}`;
}

// Close modal when clicking the X
document.querySelector('.close').onclick = function() {
    document.getElementById('paymentModal').style.display = 'none';
}

// Close modal when clicking outside
window.onclick = function(event) {
    if (event.target == document.getElementById('paymentModal')) {
        document.getElementById('paymentModal').style.display = 'none';
    }
}

// Handle payment form submission
document.getElementById('paymentForm').onsubmit = function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const submitBtn = this.querySelector('.btn-submit');
    const originalBtnText = submitBtn.innerHTML;
    
    // Validate file input
    const fileInput = document.getElementById('proof_of_payment');
    if (!fileInput.files || fileInput.files.length === 0) {
        alert('Please select a file to upload');
        return;
    }

    // Show loading state
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Uploading...';
    submitBtn.disabled = true;

    fetch('/SIA/api/upload_payment.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            alert('Payment proof uploaded successfully!');
            location.reload();
        } else {
            throw new Error(data.message || 'Failed to upload payment proof');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error: ' + error.message);
    })
    .finally(() => {
        // Reset button state
        submitBtn.innerHTML = originalBtnText;
        submitBtn.disabled = false;
    });
};
</script>