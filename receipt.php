<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Photobooth Booking Invoice</title>
<style>
    body {
    font-family: 'Courier New', Courier, monospace;
    max-width: 800px;                               /* Increased width for invoice */
    margin: 0 auto;
    padding: 20px;
    background-color: #f5f5f5;
}

/* Main receipt container */
.receipt {
    border: 1px solid #ccc;
    padding: 30px;
    background-color: white;
    box-shadow: 0 0 10px rgba(0,0,0,0.1);
}

/* Header section styling */
.header {
    text-align: center;
    margin-bottom: 30px;
    padding-bottom: 20px;
    border-bottom: 2px solid #333;
}

/* Business name styling */
.business-name {
    font-size: 2em;
    font-weight: bold;
    margin-bottom: 10px;
    color: #333;
}

/* Receipt details section */
.receipt-details {
    margin: 20px 0;
    padding: 15px 0;
    border-bottom: 1px dashed #ccc;
}

/* Client details and Booking details sections */
.client-details, .booking-details, .payment-details {
    margin: 25px 0;
    padding: 15px;
    background-color: #f9f9f9;
    border-radius: 5px;
}

h3 {
    color: #333;
    border-bottom: 1px solid #ccc;
    padding-bottom: 10px;
    margin-top: 0;
}

.info-group {
    margin: 15px 0;
}

.info-line {
    display: flex;
    justify-content: space-between;
    margin: 10px 0;
    padding: 5px 0;
}

.label {
    font-weight: bold;
    color: #555;
    width: 40%;
}

.value {
    width: 60%;
    text-align: right;
}

/* Items section */
.items {
    margin: 20px 0;
}

.item-header {
    display: flex;
    justify-content: space-between;
    font-weight: bold;
    padding: 10px 0;
    border-bottom: 1px solid #ccc;
}

/* Individual item styling */
.item {
    display: flex;
    justify-content: space-between;
    margin: 15px 0;
    align-items: flex-start;
}

.package-details {
    font-size: 0.9em;
    color: #666;
    margin: 5px 0;
    padding-left: 20px;
}

.package-details li {
    margin: 3px 0;
}

/* Totals section */
.totals {
    margin-top: 25px;
    border-top: 2px solid #333;
    padding-top: 15px;
}

/* Individual total line styling */
.total-line {
    display: flex;
    justify-content: space-between;
    margin: 8px 0;
    font-size: 1.1em;
}

/* Footer styling */
.footer {
    text-align: center;
    margin-top: 30px;
    padding-top: 20px;
    border-top: 1px dashed #ccc;
}

.footer p {
    margin: 5px 0;
}

.small {
    font-size: 0.8em;
    color: #666;
    font-style: italic;
}
</style>
</head>
<body>
    <div class="receipt">
        <div class="header">
            <div class="business-name">PIESQUARE PHOTOBOOTH</div>
            <div>Creating Memories, One Click at a Time</div>
            <div>Cel: (+63) 927-332-1983</div>
            <div>Email: piesquarephoto@gmail.com</div>
        </div>

        <div class="receipt-details">
            <div>Invoice #: PB-2024-001</div>
            <div>Date Issued: <span id="current-date"></span></div>
        </div>

        <div class="client-details">
            <h3>CLIENT INFORMATION</h3>
            <div class="info-group">
                <div class="info-line">
                    <span class="label">Client Name:</span>
                    <span class="value">John Doe</span>
                </div>
                <div class="info-line">
                    <span class="label">Event Type:</span>
                    <span class="value">Wedding Reception</span>
                </div>
                <div class="info-line">
                    <span class="label">Event Date:</span>
                    <span class="value">March 15, 2024</span>
                </div>
                <div class="info-line">
                    <span class="label">Location:</span>
                    <span class="value">Grand Ballroom, Luxury Hotel</span>
                </div>
                <div class="info-line">
                    <span class="label">Time:</span>
                    <span class="value">2:00 PM - 6:00 PM</span>
                </div>
            </div>
        </div>

        <div class="booking-details">
            <h3>BOOKING DETAILS</h3>
            <div class="items">
                <div class="item-header">
                    <span>Package/Service</span>
                    <span>Amount</span>
                </div>
                <div class="item">
                    <span>Premium Wedding Package
                        <ul class="package-details">
                            <li>4 Hours Service</li>
                            <li>Unlimited Prints</li>
                            <li>Digital Copy of All Photos</li>
                            <li>Custom Photo Layout</li>
                            <li>Props and Accessories</li>
                        </ul>
                    </span>
                    <span>₱44,744.00</span>
                </div>
                <div class="item">
                    <span>Backdrop Upgrade</span>
                    <span>₱2,800.00</span>
                </div>
                <div class="item">
                    <span>Guest Book Album</span>
                    <span>₱4,200.00</span>
                </div>
            </div>
        </div>

        <div class="totals">
            <div class="total-line">
                <span>Subtotal:</span>
                <span>₱51,744.00</span>
            </div>
            <div class="total-line">
                <span>Tax (12%):</span>
                <span>₱6,209.28</span>
            </div>
            <div class="total-line" style="font-weight: bold;">
                <span>TOTAL:</span>
                <span>₱57,953.28</span>
            </div>
        </div>

        <div class="payment-details">
            <h3>PAYMENT INFORMATION</h3>
            <div class="info-group">
                <div class="info-line">
                    <span class="label">Deposit Required (50%):</span>
                    <span class="value">₱28,976.64</span>
                </div>
                <div class="info-line">
                    <span class="label">Balance Due:</span>
                    <span class="value">₱28,976.64</span>
                </div>
                <div class="info-line">
                    <span class="label">Due Date:</span>
                    <span class="value">March 1, 2024</span>
                </div>
            </div>
        </div>

        <div class="footer">
            <p>Thank you for choosing PieSquare Photobooth!</p>
            <p>Terms & Conditions Apply</p>
            <p class="small">Please note that the balance must be paid in full 2 weeks before the event.</p>
        </div>
    </div>

    <script>
        // Add current date
        const now = new Date();
        document.getElementById('current-date').textContent = now.toLocaleDateString();
    </script>
</body>
</html>