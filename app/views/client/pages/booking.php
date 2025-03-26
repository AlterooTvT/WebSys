<?php
// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    // Store the current URL including parameters for redirect after login
    $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
    
    // Redirect to auth.php with the current URL parameters
    $redirect_url = '../../../app/views/auth/auth.php?next=' . urlencode($_SERVER['REQUEST_URI']);
    header("Location: " . $redirect_url);
    exit();
}

// Get the selected item details if they exist
$selectedItemType = isset($_GET['item_type']) ? $_GET['item_type'] : null;
$selectedItemId = isset($_GET['item_id']) ? $_GET['item_id'] : null;

// booking.php

require_once $_SERVER['DOCUMENT_ROOT'] . '/SIA/database/database.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/SIA/app/models/Booking.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/SIA/app/models/Service.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/SIA/app/models/Package.php';

$database = new Database();
$db = $database->getConnection();
$booking = new Booking($db);
$serviceModel = new Service($db);
$packageModel = new Package($db);

$isLoggedIn = isset($_SESSION['user_id']);

// Fetch all services and packages data
$servicesData = $serviceModel->getAllServices()->fetchAll(PDO::FETCH_ASSOC);
$packagesData = $packageModel->getAllPackages()->fetchAll(PDO::FETCH_ASSOC);

// Group packages by service_type (normalized to lowercase)
$groupedPackages = [];
foreach ($packagesData as $pkg) {
    $type = strtolower($pkg['service_type']);
    $groupedPackages[$type][] = $pkg;
}

// Define display order for package categories
$order = ['360booth', 'photobooth', 'partycart', 'magazinebooth'];

// Define labels for each category
$categoryLabels = [
    '360booth'     => '360 Booth',
    'photobooth'   => 'Photobooth',
    'partycart'    => 'Party Packages',
    'magazinebooth'=> 'Magazine Booth'
];

// Define gradient mappings for package cards (using lowercase keys)
$packageGradientMap = [
    'photobooth'    => 'linear-gradient(135deg, #ef6c00, #ffa726)',
    '360booth'      => 'linear-gradient(135deg, #4a148c, #7b1fa2)',
    'partycart'     => 'linear-gradient(135deg, #00695c, #26a69a)',
    'magazinebooth' => 'linear-gradient(135deg, #01579b, #0288d1)'
];

?>

<head>
  <title>New Booking</title>
  <!-- Update CSS file paths -->
  <link rel="stylesheet" href="../../../public/assets/css/client_booking_modal.css">
  <link rel="stylesheet" href="../../../public/assets/css/book.css">
  <style>
    /* If you need additional tweaks, they can be added here. */
    .current-month {
      color: aliceblue;
    }
    .booking-modal {
      width: 80%;
      max-width: 600px;
      max-height: 80%;
      padding: 30px;
    }
  </style>
</head>
<body>
  <!-- Booking Container with Calendar -->
  <div class="booking-container">
    <h2>Select Event Date</h2>
    <div class="calendar-wrapper">
      <div class="calendar-header">
        <button type="button" class="prev-month">❮</button>
        <h2 id="currentMonth" class="current-month"></h2>
        <button type="button" class="next-month">❯</button>
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
      <div id="calendarGrid" class="calendar-grid"></div>
      <!-- Hidden input to store the selected date -->
      <input type="hidden" id="selectedDate" name="event_date">
    </div>
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

  <!-- Booking Modal Content -->
  <div class="modal-content scrollable-content">
    <form method="POST" action="booking.php">
      <div class="modal-overlay" id="modalOverlay"></div>
      <div class="booking-modal">
        <div class="modal-header">
          <h2>Booking</h2>
        </div>
        <div class="modal-content">
          <!-- Selected Date -->
          <div class="form-row">
            <div class="form-label">Selected Date:</div>
            <div class="form-value" id="displaySelectedDate">February 20, 2025</div>
          </div>
          <!-- Province/City Selection -->
          <div class="select-container">
            <select class="form-select" id="provinceSelect">
              <option>Select Province/City</option>
              <option value="misamis_oriental">Misamis Oriental</option>
              <option value="lanao_del_norte">Lanao del Norte</option>
              <option value="bukidnon">Bukidnon</option>
              <option value="camiguin">Camiguin</option>
            </select>
            <div class="select-arrow"></div>
          </div>
          <div class="select-container" id="cityContainer" style="display: none;">
            <select class="form-select" id="citySelect">
              <option>Select City</option>
            </select>
            <div class="select-arrow"></div>
          </div>
          <div id="addressDetails" style="display: none;">
            <div class="form-row">
              <input type="text" class="form-input" id="street" placeholder="Street">
            </div>
            <div class="form-row">
              <input type="text" class="form-input" id="barangay" placeholder="Barangay">
            </div>
            <div class="form-row">
              <input type="text" class="form-input" id="zone" placeholder="Zone/Building">
            </div>
          </div>
          
          <!-- Booking Options -->
          <div class="booking-options">
            <div class="booking-type-selector">
              <h3>Select Booking Type:</h3>
              <div class="toggle-buttons">
                <button type="button" class="toggle-btn active" data-type="services">Single Services</button>
                <button type="button" class="toggle-btn" data-type="packages">Party Packages</button>
              </div>
            </div>
            
            <!-- Services Section (multiple selections allowed) -->
            <div class="services-section" id="servicesSection">
              <h4>Available Services</h4>
              <div class="service-items">
                <?php foreach ($servicesData as $service): ?>
                  <label class="service-item">
                    <input type="checkbox" name="service_ids[]" value="<?php echo $service['service_id']; ?>" 
                           data-price="<?php echo $service['price']; ?>"
                           data-duration="<?php echo isset($service['duration']) ? $service['duration'] : '0'; ?>"
                           class="service-checkbox">
                    <div class="service-info">
                      <span class="service-name"><?php echo htmlspecialchars($service['name']); ?></span>
                      <span class="service-price">₱<?php echo number_format($service['price'], 2); ?></span>
                      <span class="service-duration"><?php echo isset($service['duration']) ? $service['duration'] . ' hours' : 'N/A'; ?></span>
                      <!-- Use data-service attribute for details -->
                      <button type="button" class="details-btn" data-service='<?php echo json_encode($service, JSON_HEX_APOS | JSON_HEX_QUOT); ?>' onclick="showServiceDetailsFromData(this)">View Details</button>
                    </div>
                  </label>
                <?php endforeach; ?>
              </div>
            </div>
            
            <!-- Packages Section (multiple selections allowed) -->
            <div class="packages-section" id="packagesSection" style="display: none;">
              <?php 
                foreach ($order as $type):
                  if (isset($groupedPackages[$type])):
              ?>
                  <h3 class="category-heading"><?php echo htmlspecialchars($categoryLabels[$type]); ?></h3>
                  <div class="packages-category-container">
                    <?php foreach ($groupedPackages[$type] as $pkg): 
                      $gradient = isset($packageGradientMap[$type]) ? $packageGradientMap[$type] : 'linear-gradient(135deg, #333, #555)';
                    ?>
                      <div class="package-card" style="background: <?php echo $gradient; ?>;">
                        <input type="checkbox" name="package_ids[]" value="<?php echo $pkg['package_id']; ?>" 
                               data-price="<?php echo $pkg['price']; ?>"
                               data-duration="<?php echo $pkg['duration']; ?>"
                               class="package-checkbox">
                        <h5><?php echo htmlspecialchars($pkg['name']); ?></h5>
                        <p class="package-price">₱<?php echo number_format($pkg['price'], 2); ?></p>
                        <p class="package-duration"><?php echo htmlspecialchars($pkg['duration']); ?> hours</p>
                        <p class="package-description"><?php echo nl2br(htmlspecialchars($pkg['description'])); ?></p>
                        <div class="package-features"><?php echo nl2br(htmlspecialchars($pkg['features'])); ?></div>
                      </div>
                    <?php endforeach; ?>
                  </div>
              <?php 
                  endif;
                endforeach;
              ?>
            </div>
          </div><!-- end booking-options -->
          
          <!-- Time Selector -->
          <div class="time-selector">
            <div class="time-icon">
              <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="12" r="10"></circle>
                <polyline points="12 6 12 12 16 14"></polyline>
              </svg>
            </div>
            <h4>Select Time:</h4>
    <div class="time-range">
        <div class="time-select">
            <label for="startTime">Start Time:</label>
            <select id="startTime" class="form-select">
                <option value="">Select Start Time</option>
                <option value="06:00">6:00 AM</option>
                <option value="07:00">7:00 AM</option>
                <option value="08:00">8:00 AM</option>
                <option value="09:00">9:00 AM</option>
                <option value="10:00">10:00 AM</option>
                <option value="11:00">11:00 AM</option>
                <option value="12:00">12:00 PM</option>
                <option value="13:00">1:00 PM</option>
                <option value="14:00">2:00 PM</option>
                <option value="15:00">3:00 PM</option>
                <option value="16:00">4:00 PM</option>
                <option value="17:00">5:00 PM</option>
                <option value="18:00">6:00 PM</option>
                <option value="19:00">7:00 PM</option>
                <option value="20:00">8:00 PM</option>
            </select>
        </div>
        <span>to</span>
        <div class="time-select">
            <label for="endTime">End Time:</label>
            <select id="endTime" class="form-select">
                <option value="">Select End Time</option>
                <option value="08:00">8:00 AM</option>
                <option value="09:00">9:00 AM</option>
                <option value="10:00">10:00 AM</option>
                <option value="11:00">11:00 AM</option>
                <option value="12:00">12:00 PM</option>
                <option value="13:00">1:00 PM</option>
                <option value="14:00">2:00 PM</option>
                <option value="15:00">3:00 PM</option>
                <option value="16:00">4:00 PM</option>
                <option value="17:00">5:00 PM</option>
                <option value="18:00">6:00 PM</option>
                <option value="19:00">7:00 PM</option>
                <option value="20:00">8:00 PM</option>
            </select>
                <div class="select-arrow"></div>
              </div>
            </div>
          </div>
          
          <!-- Category Selection -->
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
            <div class="other-category" id="otherCategoryContainer" style="display: none;">
              <input type="text" class="form-input" placeholder="Specify category">
            </div>
          </div>
          
          <!-- Booking Summary -->
          <div class="booking-summary">
            <h4>Booking Summary</h4>
            <div id="selectedItems"></div>
            <div class="total-price">
              <span>Total Price:</span>
              <span id="totalPrice">₱0.00</span>
            </div>
            <div class="total-duration">
              <span>Total Duration:</span>
              <span id="timeDuration">0 hours</span>
            </div>
          </div>
          
          <div class="booking-note">
            You'll receive an email confirmation after booking. Please wait for the confirmation email before proceeding to payment/downpayment.
          </div>
          
          <!-- Special Requests -->
          <div class="special-requests">
            <textarea class="requests-input" placeholder="Any special requests or notes?"></textarea>
          </div>
          
          <!-- Image Upload -->
          <div class="image-upload">
            <label for="imageUpload" class="image-upload-label">
              <div class="image-upload-icon">+</div>
              <div class="image-upload-text">Upload reference image</div>
            </label>
            <input type="file" id="imageUpload" accept="image/*">
            <img src="#" alt="Preview" class="image-preview" style="display: none;">
          </div>
          
          <button type="button" class="submit-button">Submit Booking</button>
        </div><!-- end modal-content -->
      </div><!-- end booking-modal -->
    </form>
  </div><!-- end modal-content -->

  <!-- Details Modal for Service Details -->
  <div id="detailsModal" class="details-modal" style="display: none;">
    <div class="details-modal-content">
      <span class="close-details">&times;</span>
      <div id="detailsContent"></div>
    </div>
  </div>

  <script>
    // Update booking summary based on selected checkboxes
    document.querySelectorAll('.service-checkbox, .package-checkbox').forEach(input => {
      input.addEventListener('change', updateBookingSummary);
    });

    function updateBookingSummary() {
      let totalPrice = 0;
      let totalDuration = 0;
      let selectedItemsHtml = '';

      // Sum pricing from selected services
      document.querySelectorAll('.service-checkbox:checked').forEach(checkbox => {
      const parent = checkbox.parentElement;
      const name = parent.querySelector('.service-name').textContent;
      const price = parseFloat(checkbox.dataset.price);
      totalPrice += price;
      selectedItemsHtml += `<div>${name} - ₱${price.toFixed(2)}</div>`;
  });

  document.querySelectorAll('.package-checkbox:checked').forEach(checkbox => {
      const parent = checkbox.closest('.package-card');
      const name = parent.querySelector('h5').textContent;
      const price = parseFloat(checkbox.dataset.price);
      totalPrice += price;
      selectedItemsHtml += `<div>${name} - ₱${price.toFixed(2)}</div>`;
  });


      document.getElementById('selectedItems').innerHTML = selectedItemsHtml;
      document.getElementById('totalPrice').textContent = `₱${totalPrice.toFixed(2)}`;
      document.getElementById('totalDuration').textContent = `${totalDuration} hours`;
    }

  document.addEventListener('DOMContentLoaded', () => {
    const startTimeSelect = document.getElementById('startTime');
    const endTimeSelect = document.getElementById('endTime');
    const timeDurationDisplay = document.getElementById('timeDuration');

    function calculateDuration() {
    const startTime = startTimeSelect.value;
    const endTime = endTimeSelect.value;

    if (!startTime || !endTime) {
        // If one of the times is not selected, reset the duration
        timeDurationDisplay.textContent = "0 hours";
        return;
    }

    // Convert time strings (e.g., "08:00") to Date objects
    const start = new Date(`1970-01-01T${startTime}:00`);
    const end = new Date(`1970-01-01T${endTime}:00`);

    // Check if valid dates were created
    if (isNaN(start) || isNaN(end)) {
        timeDurationDisplay.textContent = "Invalid time selected";
        return;
    }

    // Calculate the difference in hours
    let duration = (end - start) / (1000 * 60 * 60);

    // Handle cases where end time is earlier than start time (overnight booking)
    if (duration < 0) {
        duration = 24 + duration; // Add 24 hours to handle overnight events
    }

    timeDurationDisplay.textContent = `${duration} hours`;
}


    // Add event listeners to update duration
    startTimeSelect.addEventListener('change', calculateDuration);
    endTimeSelect.addEventListener('change', calculateDuration);
});


    document.addEventListener('DOMContentLoaded', function() {
      // Calendar Logic
      let currentDate = new Date();
      let currentMonth = currentDate.getMonth();
      let currentYear = currentDate.getFullYear();
      let selectedDateCell = null;
      
      const monthNames = ["January", "February", "March", "April", "May", "June",
                          "July", "August", "September", "October", "November", "December"];
      
      async function updateCalendar(month, year) {
          document.getElementById('currentMonth').textContent = `${monthNames[month]} ${year}`;
          let bookings = [];
          try {
              const response = await fetch('get_bookings.php', {
                  method: 'POST',
                  headers: {'Content-Type': 'application/json'},
                  body: JSON.stringify({ month: month + 1, year: year })
              });
              bookings = await response.json();
          } catch (error) {
              console.error("Error fetching bookings:", error);
          }
          
          const firstDay = new Date(year, month, 1);
          const lastDay = new Date(year, month + 1, 0);
          const startingDay = firstDay.getDay();
          const monthLength = lastDay.getDate();
          const calendarGrid = document.getElementById('calendarGrid');
          calendarGrid.innerHTML = '';
          
          // Empty cells before first day of month
          for (let i = 0; i < startingDay; i++) {
              const emptyCell = document.createElement('div');
              emptyCell.className = 'calendar-day unavailable';
              calendarGrid.appendChild(emptyCell);
          }
          
          function getDateAvailability(dateStr) {
              const maxBookings = 3;
              let count = 0;
              bookings.forEach(function(b) {
                  if (b.event_date === dateStr) count++;
              });
              if (count >= maxBookings) return 'fully-booked';
              else if (count >= (maxBookings - 1)) return 'almost-booked';
              return 'available';
          }
          
          // Render each day
          for (let day = 1; day <= monthLength; day++) {
              const dayCell = document.createElement('div');
              dayCell.classList.add('calendar-day');
              dayCell.textContent = day;
              
              const cellDate = new Date(year, month, day);
              const today = new Date();
              today.setHours(0,0,0,0);
              const dateStr = `${year}-${String(month+1).padStart(2,'0')}-${String(day).padStart(2,'0')}`;
              
              if (cellDate < today) {
                  dayCell.classList.add('unavailable');
              } else {
                  const availability = getDateAvailability(dateStr);
                  dayCell.classList.add(availability);
                  if (availability !== 'fully-booked') {
                      dayCell.addEventListener('click', function() {
                          if (selectedDateCell) {
                              selectedDateCell.classList.remove('selected');
                          }
                          dayCell.classList.add('selected');
                          selectedDateCell = dayCell;
                          document.getElementById('selectedDate').value = dateStr;
                          showModal(new Date(year, month, day));
                      });
                  }
              }
              calendarGrid.appendChild(dayCell);
          }
      }
      
      updateCalendar(currentMonth, currentYear);
      
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
      
      // Province and City selection logic
      const cities = {
          misamis_oriental: ["Cagayan de Oro", "El Salvador", "Gingoog"],
          lanao_del_norte: ["Iligan", "Kapatagan", "Sultan Naga Dimaporo"],
          bukidnon: ["Malaybalay", "Valencia", "Maramag"],
          camiguin: ["Mambajao", "Catarman", "Sagay"]
      };

      document.getElementById('provinceSelect').addEventListener('change', function() {
          const province = this.value;
          const citySelect = document.getElementById('citySelect');
          const cityContainer = document.getElementById('cityContainer');
          const addressDetails = document.getElementById('addressDetails');

          if (province) {
              citySelect.innerHTML = '<option>Select City</option>';
              cities[province].forEach(function(city) {
                  const option = document.createElement('option');
                  option.value = city;
                  option.textContent = city;
                  citySelect.appendChild(option);
              });
              cityContainer.style.display = 'block';
          } else {
              cityContainer.style.display = 'none';
              addressDetails.style.display = 'none';
          }
      });

      document.getElementById('citySelect').addEventListener('change', function() {
          const city = this.value;
          const addressDetails = document.getElementById('addressDetails');
          if (city) {
              addressDetails.style.display = 'block';
          } else {
              addressDetails.style.display = 'none';
          }
      });
      
      // Toggle between services and packages sections
      const toggleBtns = document.querySelectorAll('.toggle-btn');
      const servicesSection = document.getElementById('servicesSection');
      const packagesSection = document.getElementById('packagesSection');

      toggleBtns.forEach(btn => {
      btn.addEventListener('click', function() {
          toggleBtns.forEach(b => b.classList.remove('active'));
          this.classList.add('active');
          
          if (this.dataset.type === 'services') {
              servicesSection.style.display = 'block';
              packagesSection.style.display = 'none';
          } else {
              servicesSection.style.display = 'none';
              packagesSection.style.display = 'block';
          }
          // Do not uncheck boxes here!
      });
  });


      // Image upload preview
      document.getElementById('imageUpload').addEventListener('change', function(event) {
          const file = event.target.files[0];
          if (file) {
              const reader = new FileReader();
              reader.onload = function(e) {
                  const preview = document.querySelector('.image-preview');
                  preview.src = e.target.result;
                  preview.style.display = 'block';
                  document.querySelector('.image-upload-label').style.display = 'none';
              }
              reader.readAsDataURL(file);
          }
      });

      // Modify the submit button event listener
      document.querySelector('.submit-button').addEventListener('click', function(e) {
          e.preventDefault(); // Prevent default form submission
          
          // Collect all form data
          const formData = new FormData();
          
          // Add user ID from session
          formData.append('user_id', '<?php echo $_SESSION['user_id']; ?>');
          
          // Add event date
          formData.append('event_date', document.getElementById('selectedDate').value);
                    // Add special requests
          formData.append('special_requests', document.querySelector('.requests-input').value);

          
          // Add location details
          const province = document.getElementById('provinceSelect').value;
          const city = document.getElementById('citySelect').value;
          const street = document.getElementById('street').value;
          const barangay = document.getElementById('barangay').value;
          const zone = document.getElementById('zone').value;
          const location = `${street}, ${barangay}, ${zone}, ${city}, ${province}`;
          formData.append('location', location);
          
          // Add time slot
          const startTime = document.querySelector('.time-range .time-select:first-child select').value;
          const endTime = document.querySelector('.time-range .time-select:last-child select').value;
          formData.append('start_time', startTime);
          formData.append('end_time', endTime);


          
          // Add event type
          const eventType = document.getElementById('categorySelect').value;
          formData.append('event_type', eventType === 'other' ? 
              document.querySelector('.other-category input').value : eventType);
          
          // Add selected services
          const selectedServices = [];
          document.querySelectorAll('.service-checkbox:checked').forEach(checkbox => {
              selectedServices.push(checkbox.value);
          });
          formData.append('services', JSON.stringify(selectedServices));
          
          // Add selected package
          const selectedPackages = [];
          document.querySelectorAll('.package-checkbox:checked').forEach(checkbox => {
              selectedPackages.push(checkbox.value);
          });
          formData.append('packages', JSON.stringify(selectedPackages));
          
          // Add estimated price
          const totalPrice = document.getElementById('totalPrice').textContent;
          formData.append('estimated_price', totalPrice.replace('₱', '').trim());
          

          
          // Add reference image if exists
          const imageFile = document.getElementById('imageUpload').files[0];
          if (imageFile) {
              formData.append('reference_image', imageFile);
          }

          // Submit the booking
          fetch('../../../app/controllers/process_booking.php', {
              method: 'POST',
              body: formData
          })
          .then(response => response.json())
          .then(data => {
              if (data.status === 'success') {
                  alert('Booking submitted successfully! You will receive a confirmation email.');
                  window.location.href = 'main.php?pages=dashboard';
              } else {
                  alert('Error: ' + data.message);
              }
          })
          .catch(error => {
              console.error('Error:', error);
              alert('An error occurred while submitting your booking.');
          });
      });
      
      // Close booking modal function
      function closeModal() {
          const modal = document.querySelector('.booking-modal');
          const overlay = document.getElementById('modalOverlay');
          modal.style.display = 'none';
          overlay.style.display = 'none';
      }
      
      // Click overlay to close booking modal
      document.getElementById('modalOverlay').addEventListener('click', closeModal);

      // Add this function to handle auto-selection of services/packages
      function autoSelectBookingItem() {
          const itemType = '<?php echo $selectedItemType; ?>';
          const itemId = '<?php echo $selectedItemId; ?>';
          
          if (!itemType || !itemId) return;

          if (itemType === 'service') {
              // Switch to services tab
              document.querySelector('.toggle-btn[data-type="services"]').click();
              
              // Find and check the corresponding service checkbox
              const serviceCheckbox = document.querySelector(`.service-checkbox[value="${itemId}"]`);
              if (serviceCheckbox) {
                  serviceCheckbox.checked = true;
                  updateBookingSummary();
              }
          } else if (itemType === 'package') {
              // Switch to packages tab
              document.querySelector('.toggle-btn[data-type="packages"]').click();
              
              // Find and check the corresponding package checkbox
              const packageCheckbox = document.querySelector(`.package-checkbox[value="${itemId}"]`);
              if (packageCheckbox) {
                  packageCheckbox.checked = true;
                  updateBookingSummary();
              }
          }
      }

      // Modify the DOMContentLoaded event listener
      document.addEventListener('DOMContentLoaded', function() {
          // ... existing DOMContentLoaded code ...
          
          // Add auto-selection call at the end
          autoSelectBookingItem();
      });
    });

    // Function to show booking modal when a date is clicked
    function showModal(date) {
        const modal = document.querySelector('.booking-modal');
        const overlay = document.getElementById('modalOverlay');
        const dateDisplay = modal.querySelector('.form-value') || document.getElementById('displaySelectedDate');
        dateDisplay.textContent = date.toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' });
        modal.style.display = 'block';
        overlay.style.display = 'block';
    }

    // Function to show service details using the data attribute from the button
    function showServiceDetailsFromData(button) {
      // Retrieve the JSON-encoded service data from the clicked button
      const serviceData = button.getAttribute('data-service');
      const service = JSON.parse(serviceData);
      
      // Get the modal and content container elements
      const modal = document.getElementById('detailsModal');
      const content = document.getElementById('detailsContent');
      
      // Build the details HTML using a template literal
      let detailsHTML = `
        <div class="service-details-content">
          <h3>${service.name}</h3>
          <div class="price-tag">₱${parseFloat(service.price).toLocaleString('en-PH', { minimumFractionDigits: 2 })}</div>
          <p><strong>Duration:</strong> ${service.duration} hours</p>
          <div class="features-list">
            <h4>Features & Inclusions:</h4>
            <ul>
      `;
      // Customize the list based on the service name
      if (service.name.includes('Photobooth')) {
          detailsHTML += `
              <li>Unlimited Photo Shots and Prints</li>
              <li>Customized Layout, 3-4 shots in one print</li>
              <li>Use of our Props, Backdrop, Studio Lights</li>
              <li>Live Screen Viewing</li>
              <li>HD Shots Using DSLR CAMERA</li>
              <li>High Quality 4R Prints</li>
          `;
      } else if (service.name.includes('360')) {
          detailsHTML += `
              <li>20-30 seconds 360 video in HIGH DEFINITION</li>
              <li>Up to 3 background music selections</li>
              <li>Elegant and professional setup</li>
              <li>High quality studio lights</li>
              <li>Black sequins backdrop</li>
              <li>iPad Sharing Station</li>
          `;
      } else if (service.name.includes('Magazine')) {
          detailsHTML += `
              <li>Elegant setup with customized decals</li>
              <li>Professional Photographer</li>
              <li>Unlimited 4R Prints</li>
              <li>FB Online Gallery</li>
              <li>Enhanced Soft Copies</li>
          `;
      }
      
      detailsHTML += `
            </ul>
          </div>
          <p class="note">* Transportation fee applies depending on location</p>
        </div>
      `;
      
      // Set the modal content and display the modal
      content.innerHTML = detailsHTML;
      modal.style.display = "block";
  }

  // Close the details modal when the user clicks on the close button
  document.querySelector('.close-details').onclick = function() {
      document.getElementById('detailsModal').style.display = "none";
  };

  // Also close the modal if clicking outside of its content area
  window.onclick = function(event) {
      const detailsModal = document.getElementById('detailsModal');
      if (event.target == detailsModal) {
          detailsModal.style.display = "none";
      }
  };
  </script>
</body>
</html>
