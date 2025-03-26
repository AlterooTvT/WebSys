<div class="contact-container">
    <div class="contact-info">
        <h1>Contact us</h1>
        <h2>"Get in Touch with Us!"</h2>
        <p>"We'd love to hear from you! Send us a message, and we'll get back to you as soon as possible."</p>
    </div>

    <div class="contact-form">
        <form id="contactForm" method="POST">
            <div class="form-group">
                <label>Name <span class="required">(required)</span></label>
                <div class="name-group">
                    <input type="text" name="first_name" placeholder="First name" required>
                    <input type="text" name="last_name" placeholder="Last name" required>
                </div>
            </div>

            <div class="form-group">
                <label>Email <span class="required">(required)</span></label>
                <input type="email" name="email" placeholder="Email" required>
            </div>

            <div class="form-group">
                <label>Phone number <span class="optional">(optional)</span></label>
                <div class="phone-input">
                    <span class="country-code">+63</span>
                    <input type="tel" name="phone" placeholder="Phone number">
                </div>
            </div>

            <div class="form-group">
                <label>Message <span class="required">(required)</span></label>
                <textarea name="message" rows="6" placeholder="Message" required></textarea>
            </div>

            <button type="submit" class="submit-btn">Submit</button>
        </form>
    </div>
</div>  

<style>
.contact-container {
    display: flex;
    background-color: #000;
    color: #fff;
    padding: 50px;
    min-height: calc(100vh - 82px);
    gap: 50px;
}

.contact-info {
    flex: 1;
    padding-top: 50px;
}

.contact-info h1 {
    font-size: 42px;
    margin-bottom: 30px;
}

.contact-info h2 {
    font-size: 24px;
    margin-bottom: 20px;
}

.contact-info p {
    font-size: 18px;
    line-height: 1.6;
    max-width: 400px;
}

.contact-form {
    flex: 1.5;
}

.form-group {
    margin-bottom: 25px;
}

.form-group label {
    display: block;
    margin-bottom: 8px;
    font-size: 14px;
}

.required {
    color: #fff;
    font-size: 12px;
}

.optional {
    color: #888;
    font-size: 12px;
}

.name-group {
    display: flex;
    gap: 20px;
}

.name-group input {
    flex: 1;
}

input, textarea {
    width: 100%;
    padding: 12px;
    background: transparent;
    border: 1px solid #fff;
    border-radius: 25px;
    color: #fff;
    font-size: 16px;
}

textarea {
    resize: vertical;
    min-height: 150px;
}

.phone-input {
    display: flex;
    align-items: center;
    gap: 10px;
}

.country-code {
    background: transparent;
    border: 1px solid #fff;
    padding: 12px 15px;
    border-radius: 25px;
    color: #fff;
}

.phone-input input {
    flex: 1;
}

.submit-btn {
    background-color: #FFD700;
    color: #000;
    padding: 12px 40px;
    border: none;
    border-radius: 25px;
    font-size: 16px;
    cursor: pointer;
    transition: transform 0.3s ease;
}

.submit-btn:hover {
    transform: scale(1.05);
}

/* Placeholder styling */
input::placeholder,
textarea::placeholder {
    color: #666;
}

/* Focus styles */
input:focus,
textarea:focus {
    outline: none;
    border-color: #FFD700;
}

/* Responsive Design */
@media (max-width: 768px) {
    .contact-container {
        flex-direction: column;
        padding: 30px;
    }

    .contact-info {
        text-align: center;
        padding-top: 20px;
    }

    .contact-info p {
        margin: 0 auto;
    }

    .name-group {
        flex-direction: column;
        gap: 15px;
    }
}
</style>

<script>
document.getElementById('contactForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    // Here you would typically add form validation and submission logic
    // For example:
    const formData = new FormData(this);
    
    // You can handle the form submission here
    // Example: Send to server using fetch
    fetch('process_contact.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if(data.success) {
            alert('Message sent successfully!');
            this.reset();
        } else {
            alert('Error sending message. Please try again.');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred. Please try again later.');
    });
});
</script>