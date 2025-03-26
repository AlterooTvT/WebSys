<?php
// Check if payment ID is provided
if (!isset($_GET['id'])) {
    header('Location: ?page=payments');
    exit();
}

$payment_id = $_GET['id'];
$payment_details = $payment->getPaymentDetails($payment_id);

// Verify payment belongs to user
if (!$payment_details || $payment_details['user_id'] != $_SESSION['user_id']) {
    header('Location: ?page=payments');
    exit();
}
?>

<div class="client-container">
    <div class="page-title">
        <h1>Payment Details</h1>
        <a href="?page=payments" class="back-btn">
            <i class="fas fa-arrow-left"></i> Back to Payments
        </a>
    </div>

    <div class="payment-details-container">
        <div class="payment-info-card">
            <div class="card-header">
                <h2>Payment Information</h2>
                <span class="status-badge <?php echo $payment_details['status']; ?>">
                    <?php echo ucfirst($payment_details['status']); ?>
                </span>
            </div>
            
            <div class="info-grid">
                <div class="info-item">
                    <label>Booking ID</label>
                    <p>#<?php echo $payment_details['booking_id']; ?></p>
                </div>
                <div class="info-item">
                    <label>Event Date</label>
                    <p><?php echo date('M d, Y', strtotime($payment_details['event_date'])); ?></p>
                </div>
                <div class="info-item">
                    <label>Amount</label>
                    <p class="amount">₱<?php echo number_format($payment_details['amount'], 2); ?></p>
                </div>
                <div class="info-item">
                    <label>Payment Type</label>
                    <p><?php echo ucfirst(str_replace('_', ' ', $payment_details['payment_type'])); ?></p>
                </div>
                <div class="info-item">
                    <label>Date Submitted</label>
                    <p><?php echo date('M d, Y h:i A', strtotime($payment_details['created_at'])); ?></p>
                </div>
                <?php if ($payment_details['verified_at']): ?>
                <div class="info-item">
                    <label>Date Verified</label>
                    <p><?php echo date('M d, Y h:i A', strtotime($payment_details['verified_at'])); ?></p>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <?php if ($payment_details['proof_of_payment']): ?>
        <div class="payment-proof-card">
            <h2>Proof of Payment</h2>
            <div class="proof-image">
                <img src="/SIA/uploads/payments/<?php echo $payment_details['proof_of_payment']; ?>" 
                     alt="Proof of Payment"
                     onclick="showImageModal(this.src)">
            </div>
        </div>
        <?php endif; ?>

        <?php if ($payment_details['admin_remarks']): ?>
        <div class="remarks-card">
            <h2>Admin Remarks</h2>
            <p><?php echo nl2br(htmlspecialchars($payment_details['admin_remarks'])); ?></p>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Image Modal -->
<div id="imageModal" class="modal">
    <span class="close">&times;</span>
    <img class="modal-content" id="modalImage">
</div>

<style>
.client-container {
    padding: 20px;
    max-width: 1200px;
    margin: 0 auto;
}

.page-title {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 30px;
}

.back-btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 8px 16px;
    background: #f8f9fa;
    color: #333;
    border-radius: 5px;
    text-decoration: none;
    transition: all 0.3s ease;
}

.back-btn:hover {
    background: #e9ecef;
}

.payment-details-container {
    display: grid;
    gap: 20px;
}

.payment-info-card,
.payment-proof-card,
.remarks-card {
    background: white;
    border-radius: 10px;
    padding: 20px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.card-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
}

.info-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
}

.info-item label {
    display: block;
    color: #666;
    margin-bottom: 5px;
    font-size: 0.9em;
}

.info-item p {
    margin: 0;
    font-size: 1.1em;
    color: #333;
}

.amount {
    color: #2ecc71;
    font-weight: bold;
}

.proof-image {
    text-align: center;
}

.proof-image img {
    max-width: 100%;
    max-height: 400px;
    border-radius: 5px;
    cursor: pointer;
    transition: opacity 0.3s ease;
}

.proof-image img:hover {
    opacity: 0.9;
}

.remarks-card p {
    margin: 0;
    line-height: 1.6;
    color: #666;
}

/* Image Modal */
.modal {
    display: none;
    position: fixed;
    z-index: 1000;
    padding-top: 50px;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0,0,0,0.9);
}

.modal-content {
    margin: auto;
    display: block;
    max-width: 90%;
    max-height: 90vh;
}

.close {
    position: absolute;
    right: 35px;
    top: 15px;
    color: #f1f1f1;
    font-size: 40px;
    font-weight: bold;
    cursor: pointer;
}

@media (max-width: 768px) {
    .info-grid {
        grid-template-columns: 1fr;
    }
}
</style>

<script>
function showImageModal(src) {
    const modal = document.getElementById('imageModal');
    const modalImg = document.getElementById('modalImage');
    modal.style.display = "block";
    modalImg.src = src;
}

document.querySelector('#imageModal .close').onclick = function() {
    document.getElementById('imageModal').style.display = "none";
}

document.getElementById('imageModal').onclick = function(e) {
    if (e.target === this) {
        this.style.display = "none";
    }
}
</script> 