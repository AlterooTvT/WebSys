<?php
// Check if user is logged in
$isLoggedIn = isset($_SESSION['user_id']);
?>

<div class="booking-container">
    <div class="calendar-section">
        <h2 class="section-title">Select Your Event Date</h2>
        <!-- Calendar -->
        <div class="calendar-wrapper">
            <div class="calendar-header">
                <button class="prev-month">❮</button>
                <h2 id="currentMonth"></h2>
                <button class="next-month">❯</button>
            </div>
            <div class="calendar-days">
                <div class="day-header sun">Sun</div>
                <div class="day-header">Mon</div>
                <div class="day-header">Tue</div>
                <div class="day-header">Wed</div>
                <div class="day-header">Thu</div>
                <div class="day-header">Fri</div>
                <div class="day-header">Sat</div>
            </div>
            <div class="calendar-grid" id="calendarGrid"></div>
        </div>

        <!-- Legend -->
        <div class="calendar-legend">
            <div class="legend-item">
                <div class="legend-color available"></div>
                <span>Available</span>
            </div>
            <div class="legend-item">
                <div class="legend-color almost-booked"></div>
                <span>Almost Full</span>
            </div>
            <div class="legend-item">
                <div class="legend-color fully-booked"></div>
                <span>Fully Booked</span>
            </div>
            <div class="legend-item">
                <div class="legend-color unavailable"></div>
                <span>Unavailable</span>
            </div>
        </div>
    </div>

    <div class="booking-info">
        <h1>Plan Your Event with Ease!</h1>
        <p class="subtitle">Create unforgettable memories with our premium photo booth services.</p>

        <div class="booking-steps">
            <h3>How to Book Your Event</h3>
            <div class="step-list">
                <div class="step">
                    <div class="step-icon">🖱</div>
                    <div class="step-content">
                        <h4>Step 1</h4>
                        <p>Select an available date from the calendar</p>
                    </div>
                </div>
                <div class="step">
                    <div class="step-icon">🔑</div>
                    <div class="step-content">
                        <h4>Step 2</h4>
                        <p>Log in or create your account</p>
                    </div>
                </div>
                <div class="step">
                    <div class="step-icon">📅</div>
                    <div class="step-content">
                        <h4>Step 3</h4>
                        <p>Choose your package and fill event details</p>
                    </div>
                </div>
                <div class="step">
                    <div class="step-icon">💳</div>
                    <div class="step-content">
                        <h4>Step 4</h4>
                        <p>Confirm booking and process payment</p>
                    </div>
                </div>
                <div class="step">
                    <div class="step-icon">🎉</div>
                    <div class="step-content">
                        <h4>Step 5</h4>
                        <p>Get ready for your awesome event!</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="cta-section">
            <?php if (!$isLoggedIn): ?>
                <a href="app/views/auth/auth.php?page=login&action=login&redirect=<?php echo urlencode('?page=booking'); ?>" class="login">Log in to Book</a>
                <p class="signup-text">Don't have an account? 
                    <a href="app/views/auth/auth.php?page=login&action=register&redirect=<?php echo urlencode('?page=booking'); ?>">Sign Up Now</a>
                </p>
            <?php endif; ?>
        </div>

        <div class="faq-section">
            <h3>Frequently Asked Questions</h3>
            <div class="faq-grid">
                <div class="faq-item">
                    <h4>Can I reschedule my booking?</h4>
                    <p>Yes, you can reschedule up to 48 hours before your event.</p>
                </div>
                <div class="faq-item">
                    <h4>What payment methods do you accept?</h4>
                    <p>We accept credit cards, GCash, and bank transfers.</p>
                </div>
                <div class="faq-item">
                    <h4>How far in advance should I book?</h4>
                    <p>We recommend booking at least 2 weeks in advance.</p>
                </div>
                <div class="faq-item">
                    <h4>What's included in the packages?</h4>
                    <p>Each package includes setup, props, and digital copies.</p>
                </div>
            </div>
        </div>

        <button id="howToBookBtn" class="how-to-book-btn">
            Watch How to Book Tutorial
            <i class="fas fa-play-circle"></i>
        </button>
    </div>
</div>

<!-- Tutorial Modal -->
<div id="tutorialModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h2>How to Book - Tutorial</h2>
        <div class="tutorial-content">
            <div class="tutorial-video">
                <!-- Replace with your actual video -->
                <video controls>
                    <source src="path/to/your/tutorial.mp4" type="video/mp4">
                    Your browser does not support the video tag.
                </video>
            </div>
            <div class="tutorial-steps">
                <!-- Tutorial steps will be here -->
            </div>
        </div>
    </div>
</div>

<style>
/* Main Container */
.booking-container {
    display: flex;
    background-color: #000;
    color: #fff;
    padding: 40px;
    min-height: calc(100vh - 80px);
    gap: 50px;
}

/* Calendar Section */
.calendar-section {
    flex: 0 0 400px;
}

.section-title {
    margin-bottom: 20px;
    font-size: 24px;
    color: #e5ff32;
}

.calendar-wrapper {
    background: #000;
    border-radius: 20px;
    padding: 20px;
    box-shadow: 0 8px 16px rgba(229, 255, 50, 0.1);
}

/* Calendar Header */
.calendar-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
    color: #fff;
}

.calendar-header button {
    background: none;
    border: none;
    color: #e5ff32;
    font-size: 24px;
    cursor: pointer;
    padding: 8px;
    transition: transform 0.2s;
}

.calendar-header button:hover {
    transform: scale(1.1);
}

.calendar-header h2 {
    color: #fff;
    font-size: 20px;
}

/* Calendar Grid */
.calendar-days {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    gap: 8px;
    margin-bottom: 15px;
    color: #fff;
}

.day-header {
    text-align: center;
    padding: 8px;
    font-weight: 500;
}

.day-header.sun {
    color: #ff4444;
}

.calendar-grid {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    gap: 8px;
}

.calendar-day {
    aspect-ratio: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #1a1a1a;
    border-radius: 4px;
    font-size: 16px;
    cursor: pointer;
    transition: all 0.3s ease;
    color: #fff;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
}

/* Calendar Day States */
.calendar-day.available {
    background: rgb(59, 206, 59);
    color: #fff;
    border: 1px solid #333;
}

.calendar-day.available:hover {
    background: #e5ff32;
    color: #000;
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(229, 255, 50, 0.2);
}

.calendar-day.almost-booked {
    background: #ffd700;
    color: #000;
}

.calendar-day.fully-booked {
    background: #ff4444;
    color: #fff;
    cursor: not-allowed;
}

.calendar-day.unavailable {
    background: #333;
    color: #666;
    cursor: not-allowed;
}

.calendar-day.selected {
    background: #e5ff32;
    color: #000;
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(229, 255, 50, 0.2);
}

/* Legend */
.calendar-legend {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 10px;
    margin-top: 20px;
    padding: 15px;
    background: #1a1a1a;
    border-radius: 10px;
}

.legend-item {
    display: flex;
    align-items: center;
    gap: 8px;
    color: #fff;
}

.legend-color {
    width: 20px;
    height: 20px;
    border-radius: 4px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
}

.legend-color.available {
    background:rgb(110, 255, 110);
    border: 1px solid #333;
}

.legend-color.almost-booked {
    background: #ffd700;
}

.legend-color.fully-booked {
    background: #ff4444;
}

.legend-color.unavailable {
    background: #333;
}

/* Booking Info Section */
.booking-info {
    flex: 1;
    display: flex;
    flex-direction: column;
    gap: 30px;
}

.booking-info h1 {
    font-size: 36px;
    color: #e5ff32;
    margin-bottom: 10px;
}

.subtitle {
    font-size: 18px;
    color: #fff;
    opacity: 0.9;
}

/* Booking Steps */
.booking-steps {
    background: rgba(255, 255, 255, 0.1);
    padding: 25px;
    border-radius: 15px;
}

.step-list {
    display: flex;
    flex-direction: column;
    gap: 20px;
    margin-top: 20px;
}

.step {
    display: flex;
    align-items: center;
    gap: 15px;
    padding: 15px;
    background: rgba(255, 255, 255, 0.05);
    border-radius: 10px;
    transition: transform 0.3s ease;
}

.step:hover {
    transform: translateX(10px);
}

.step-icon {
    font-size: 24px;
    width: 50px;
    height: 50px;
    background: #e5ff32;
    color: #000;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
}

.step-content h4 {
    margin: 0;
    color: #e5ff32;
}

/* CTA Section */
.cta-section {
    text-align: center;
    margin: 30px 0;
}

.login {
    background: #e5ff32;
    color: #000;
    padding: 15px 40px;
    border-radius: 30px;
    text-decoration: none;
    font-weight: 600;
    display: inline-block;
    transition: all 0.3s ease;
}

.login:hover {
    transform: translateY(-3px);
    box-shadow: 0 5px 15px rgba(229, 255, 50, 0.3);
}

.signup-text {
    margin-top: 15px;
}

.signup-text a {
    color: #e5ff32;
    text-decoration: none;
}

/* FAQ Section */
.faq-section {
    margin-top: 30px;
}

.faq-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 20px;
    margin-top: 20px;
}

.faq-item {
    background: rgba(255, 255, 255, 0.05);
    padding: 20px;
    border-radius: 10px;
    cursor: pointer;
    transition: all 0.3s ease;
}

.faq-item:hover {
    background: rgba(255, 255, 255, 0.1);
    transform: translateY(-3px);
}

.faq-item h4 {
    color: #e5ff32;
    margin: 0 0 10px 0;
}

/* Tutorial Button */
.how-to-book-btn {
    background: #e5ff32;
    color: #000;
    border: none;
    padding: 15px 30px;
    border-radius: 30px;
    font-size: 16px;
    font-weight: 600;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 10px;
    margin: 30px auto;
    transition: all 0.3s ease;
}

.how-to-book-btn:hover {
    transform: scale(1.05);
    box-shadow: 0 5px 15px rgba(229, 255, 50, 0.3);
}

/* Modal */
.modal {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.8);
    z-index: 1000;
}

.modal-content {
    position: relative;
    background: #fff;
    width: 90%;
    max-width: 800px;
    margin: 50px auto;
    padding: 30px;
    border-radius: 15px;
    color: #000;
}

.close {
    position: absolute;
    right: 20px;
    top: 15px;
    font-size: 28px;
    cursor: pointer;
    color: #000;
}

.tutorial-video {
    margin: 20px 0;
}

.tutorial-video video {
    width: 100%;
    border-radius: 10px;
}

/* Responsive Design */
@media (max-width: 1024px) {
    .booking-container {
        flex-direction: column;
        padding: 20px;
    }

    .calendar-section {
        flex: none;
        width: 100%;
    }

    .faq-grid {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 768px) {
    .calendar-wrapper {
        padding: 15px;
    }

    .calendar-grid {
        gap: 4px;
    }

    .calendar-day {
        font-size: 14px;
    }

    .calendar-legend {
        grid-template-columns: 1fr;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Calendar functionality
    let currentDate = new Date();
    let currentMonth = currentDate.getMonth();
    let currentYear = currentDate.getFullYear();
    let selectedDate = null;

    const monthNames = ["January", "February", "March", "April", "May", "June",
        "July", "August", "September", "October", "November", "December"
    ];

    function updateCalendar(month, year) {
        document.getElementById('currentMonth').textContent = 
            `${monthNames[month]} ${year}`;

        const firstDay = new Date(year, month, 1);
        const lastDay = new Date(year, month + 1, 0);
        const startingDay = firstDay.getDay();
        const monthLength = lastDay.getDate();

        const calendarGrid = document.getElementById('calendarGrid');
        calendarGrid.innerHTML = '';

        // Add empty cells for days before the first of the month
        for (let i = 0; i < startingDay; i++) {
            const emptyDay = document.createElement('div');
            emptyDay.className = 'calendar-day unavailable';
            calendarGrid.appendChild(emptyDay);
        }

        // Add the days of the month
        for (let day = 1; day <= monthLength; day++) {
            const dayDiv = document.createElement('div');
            dayDiv.className = 'calendar-day';
            dayDiv.textContent = day;

            const dateObj = new Date(year, month, day);
            const today = new Date();
            today.setHours(0, 0, 0, 0);

            if (dateObj < today) {
                dayDiv.classList.add('unavailable');
            } else {
                // Randomly assign booking status for demo
                const random = Math.random();
                if (random < 0.2) {
                    dayDiv.classList.add('fully-booked');
                } else if (random < 0.4) {
                    dayDiv.classList.add('almost-booked');
                } else {
                    dayDiv.classList.add('available');
                    // Add click event only for available dates
                    dayDiv.addEventListener('click', function() {
                        if (selectedDate) {
                            selectedDate.classList.remove('selected');
                        }
                        this.classList.add('selected');
                        selectedDate = this;
                        
                        // If user is not logged in, redirect to login
                        <?php if (!$isLoggedIn): ?>
                            window.location.href = 'app/views/auth/auth.php';
                        <?php else: ?>
                            // Handle date selection for logged-in users
                            const selectedDateStr = `${year}-${month + 1}-${day}`;
                            // Add your booking logic here
                        <?php endif; ?>
                    });
                }
            }

            calendarGrid.appendChild(dayDiv);
        }
    }

    // Initialize calendar
    updateCalendar(currentMonth, currentYear);

    // Calendar navigation
    document.querySelector('.prev-month').addEventListener('click', function() {
        currentMonth--;
        if (currentMonth < 0) {
            currentMonth = 11;
            currentYear--;
        }
        updateCalendar(currentMonth, currentYear);
    });

    document.querySelector('.next-month').addEventListener('click', function() {
        currentMonth++;
        if (currentMonth > 11) {
            currentMonth = 0;
            currentYear++;
        }
        updateCalendar(currentMonth, currentYear);
    });

    // Modal functionality
    const modal = document.getElementById('tutorialModal');
    const btn = document.getElementById('howToBookBtn');
    const span = document.getElementsByClassName('close')[0];

    btn.onclick = function() {
        modal.style.display = 'block';
        modal.classList.add('fade-in');
    }

    span.onclick = function() {
        modal.classList.add('fade-out');
        setTimeout(() => {
            modal.style.display = 'none';
            modal.classList.remove('fade-out');
        }, 300);
    }

    window.onclick = function(event) {
        if (event.target == modal) {
            span.onclick();
        }
    }

    // FAQ Interaction
    const faqItems = document.querySelectorAll('.faq-item');
    faqItems.forEach(item => {
        item.addEventListener('click', function() {
            this.classList.toggle('expanded');
        });
    });

    // Add smooth scrolling for internal links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            document.querySelector(this.getAttribute('href')).scrollIntoView({
                behavior: 'smooth'
            });
        });
    });
});
</script>