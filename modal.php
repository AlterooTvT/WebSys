<style>
    .booking-modal {
        background: white;
        width: 360px;
        border-radius: 20px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
        overflow: hidden;
    }
    
    .modal-header {
        text-align: center;
        padding: 15px 0;
        border-bottom: 1px solid #eee;
    }
    
    .modal-header h2 {
        margin: 0;
        font-size: 20px;
    }
    
    .modal-content {
        padding: 15px;
    }
    
    .form-row {
        margin-bottom: 12px;
    }
    
    .form-label {
        font-size: 12px;
        font-weight: bold;
        margin-bottom: 5px;
    }
    
    .form-value {
        font-size: 14px;
        margin: 0;
    }
    
    .select-container {
        position: relative;
        margin-bottom: 12px;
    }
    
    .form-select {
        width: 100%;
        padding: 8px 10px;
        border: 1px solid #ccc;
        border-radius: 20px;
        appearance: none;
        background-color: white;
        font-size: 14px;
    }
    
    .select-arrow {
        position: absolute;
        right: 15px;
        top: 50%;
        transform: translateY(-50%);
        width: 0;
        height: 0;
        border-left: 5px solid transparent;
        border-right: 5px solid transparent;
        border-top: 5px solid #333;
        pointer-events: none;
    }
    
    .time-selector {
        display: flex;
        align-items: center;
        margin-bottom: 12px;
    }
    
    .time-icon {
        margin-right: 10px;
    }
    
    .time-range {
        display: flex;
        flex-grow: 1;
        gap: 5px;
        align-items: center;
    }
    
    .time-select {
        flex-grow: 1;
        position: relative;
    }
    
    .name-row {
        display: flex;
        gap: 10px;
        margin-bottom: 12px;
    }
    
    .form-input {
        width: 100%;
        padding: 8px 10px;
        border: 1px solid #ccc;
        border-radius: 20px;
        font-size: 14px;
    }
    
    .package-section {
        margin-top: 15px;
    }
    
    .package-title {
        font-size: 12px;
        font-weight: bold;
        margin-bottom: 8px;
    }
    
    .checkbox-group {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }
    
    .checkbox-label {
        display: flex;
        align-items: center;
        font-size: 14px;
    }
    
    .checkbox-custom {
        margin-right: 8px;
        width: 16px;
        height: 16px;
        border: 1px solid #ccc;
    }
    
    .estimated-price {
        text-align: center;
        margin: 20px 0 10px;
    }
    
    .price-label {
        font-size: 14px;
        font-weight: bold;
    }
    
    .price-value {
        font-size: 22px;
        font-weight: bold;
    }
    
    .booking-note {
        text-align: center;
        font-size: 12px;
        color: #666;
        margin-bottom: 15px;
    }
    
    .submit-button {
        display: block;
        width: 90%;
        margin: 0 auto 15px;
        padding: 10px;
        background: #f0ff4d;
        border: none;
        border-radius: 25px;
        font-weight: bold;
        font-size: 16px;
        cursor: pointer;
    }
    
    .special-requests {
        margin-bottom: 15px;
    }
    
    .requests-input {
        padding: 12px;
        min-height: 60px;
        width: 100%;
        border: 1px solid #ccc;
        border-radius: 12px;
        font-size: 14px;
        resize: none;
        margin-bottom: 8px;
    }
    
    .image-upload {
        display: flex;
        align-items: center;
        justify-content: center;
        border: 1px dashed #ccc;
        border-radius: 12px;
        height: 100px;
        position: relative;
        overflow: hidden;
        cursor: pointer;
    }
    
    .image-upload-label {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        height: 100%;
        width: 100%;
        cursor: pointer;
    }
    
    .image-upload-icon {
        font-size: 24px;
        margin-bottom: 5px;
    }
    
    .image-upload-text {
        font-size: 14px;
        color: #666;
    }
    
    .image-preview {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: none;
    }
    
    #imageUpload {
        display: none;
    }
    
    .other-category {
        margin-top: 8px;
        display: none;
    }
</style>
</head>
<body>
    <div class="booking-modal">
        <div class="modal-header">
            <h2>Booking</h2>
        </div>
        <div class="modal-content">
            <div class="form-row">
                <div class="form-label">Selected Date:</div>
                <div class="form-value">February 20, 2025</div>
            </div>
            
            <div class="select-container">
                <select class="form-select">
                    <option>Select Province/City</option>
                    <option>Misamis Oriental</option>
                    <option>Lanao del Norte</option>
                    <option>Bukidnon</option>
                    <option>Camiguin</option>
                </select>
                <div class="select-arrow"></div>
            </div>
            
            <div class="time-selector">
                <div class="time-icon">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"></circle>
                        <polyline points="12 6 12 12 16 14"></polyline>
                    </svg>
                </div>
                <div class="time-range">
                    <div class="time-select">
                        <select class="form-select">
                            <option>Start</option>
                            <option>8:00 AM</option>
                            <option>9:00 AM</option>
                            <option>10:00 AM</option>
                            <option>11:00 AM</option>
                            <option>12:00 PM</option>
                            <option>1:00 PM</option>
                            <option>2:00 PM</option>
                            <option>3:00 PM</option>
                            <option>4:00 PM</option>
                            <option>5:00 PM</option>
                            <option>6:00 PM</option>
                            <option>7:00 PM</option>
                            <option>8:00 PM</option>
                        </select>
                        <div class="select-arrow"></div>
                    </div>
                    <span>to</span>
                    <div class="time-select">
                        <select class="form-select">
                            <option>End</option>
                            <option>9:00 AM</option>
                            <option>10:00 AM</option>
                            <option>11:00 AM</option>
                            <option>12:00 PM</option>
                            <option>1:00 PM</option>
                            <option>2:00 PM</option>
                            <option>3:00 PM</option>
                            <option>4:00 PM</option>
                            <option>5:00 PM</option>
                            <option>6:00 PM</option>
                            <option>7:00 PM</option>
                            <option>8:00 PM</option>
                            <option>9:00 PM</option>
                            <option>10:00 PM</option>
                        </select>
                        <div class="select-arrow"></div>
                    </div>
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-label">Category:</div>
                <div class="select-container">
                    <select class="form-select" id="categorySelect">
                        <option value="">Select Category</option>
                        <option value="wedding">Wedding</option>
                        <option value="birthday">Birthday</option>
                        <option value="corporate">Corporate Event</option>
                        <option value="debut">Debut</option>
                        <option value="graduation">Graduation</option>
                        <option value="reunion">Reunion</option>
                        <option value="other">Other (please specify)</option>
                    </select>
                    <div class="select-arrow"></div>
                </div>
                <div class="other-category" id="otherCategoryContainer">
                    <input type="text" class="form-input" placeholder="Please specify category" id="otherCategory">
                </div>
            </div>
            
            <div class="name-row">
                <div class="name-input">
                    <div class="form-label">First Name</div>
                    <input type="text" class="form-input" placeholder="First name">
                </div>
                <div class="name-input">
                    <div class="form-label">Last Name</div>
                    <input type="text" class="form-input" placeholder="Last name">
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-label">Email</div>
                <input type="email" class="form-input" placeholder="Email">
            </div>
            
            <div class="form-row">
                <div class="form-label">Phone Number</div>
                <input type="tel" class="form-input" placeholder="Phone Number">
            </div>
            
            <div class="special-requests">
                <div class="form-label">Special Requests or Event Theme</div>
                <textarea class="requests-input" placeholder="Enter details about your event theme or any special requests"></textarea>
                <div class="image-upload" id="imageUploadContainer">
                    <label class="image-upload-label" for="imageUpload">
                        <div class="image-upload-icon">+</div>
                        <div class="image-upload-text">Upload reference image</div>
                    </label>
                    <img id="imagePreview" class="image-preview">
                    <input type="file" id="imageUpload" accept="image/*">
                </div>
            </div>
            
            <div class="package-section">
                <div class="package-title">Pick a Package</div>
                <div class="checkbox-group">
                    <label class="checkbox-label">
                        <input type="checkbox" style="display:none;" value="1400" checked>
                        <div class="checkbox-custom" style="background-color: #f0ff4d;"></div>
                        Standard Photobooth
                    </label>
                    <label class="checkbox-label">
                        <input type="checkbox" style="display:none;" value="2000">
                        <div class="checkbox-custom"></div>
                        360 Booth Experience
                    </label>
                    <label class="checkbox-label">
                        <input type="checkbox" style="display:none;" value="1800">
                        <div class="checkbox-custom"></div>
                        Magazine Booth
                    </label>
                    <label class="checkbox-label">
                        <input type="checkbox" style="display:none;" value="1600">
                        <div class="checkbox-custom"></div>
                        Party Cart
                    </label>
                </div>
            </div>
            
            <div class="estimated-price">
                <div class="price-label">Estimated Price:</div>
                <div class="price-value">₱ 1,400.00</div>
            </div>
            
            <div class="booking-note">
                You'll receive an email confirmation after booking.
            </div>
            
            <button class="submit-button">Submit</button>
        </div>
    </div>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const checkboxes = document.querySelectorAll('.checkbox-label input[type="checkbox"]');
            const priceDisplay = document.querySelector('.price-value');
            
            checkboxes.forEach(checkbox => {
                const customCheckbox = checkbox.nextElementSibling;
                if (checkbox.checked) {
                    customCheckbox.style.backgroundColor = '#f0ff4d';
                } else {
                    customCheckbox.style.backgroundColor = 'white';
                }
                
                checkbox.addEventListener('change', function() {
                    if (this.checked) {
                        customCheckbox.style.backgroundColor = '#f0ff4d';
                    } else {
                        customCheckbox.style.backgroundColor = 'white';
                    }
                    
                    let total = 0;
                    checkboxes.forEach(cb => {
                        if (cb.checked) {
                            total += parseInt(cb.value);
                        }
                    });
                    
                    priceDisplay.textContent = '₱ ' + total.toLocaleString() + '.00';
                });
            });
            
            const customCheckboxes = document.querySelectorAll('.checkbox-custom');
            customCheckboxes.forEach((customCheckbox, index) => {
                customCheckbox.addEventListener('click', function() {
                    const checkbox = checkboxes[index];
                    checkbox.checked = !checkbox.checked;
                    
                    const event = new Event('change');
                    checkbox.dispatchEvent(event);
                });
            });

            const imageUpload = document.getElementById('imageUpload');
            const imagePreview = document.getElementById('imagePreview');
        });
    </script>
</body>