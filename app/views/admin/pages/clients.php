<?php
require_once '../../../helpers/auth.php';
require_once '../../../database/database.php';
require_once '../../../app/models/User.php';

requireAdmin();

$database  = new Database();
$db        = $database->getConnection();

$userModel = new User($db);
// Ensure getAllClients() returns fields like user_id, first_name, last_name, email, phone, and role.
$clients = $userModel->getAllClients();
?>
  <style>
    /* Scoped styles for this client page */
    .client-admin-page .admin-container {
      max-width: 1200px;
      margin: 40px auto;
      padding: 20px;
      background: #fff;
      border-radius: 8px;
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }
    .client-admin-page h1 {
      font-size: 2em;
      color: #333;
      margin-bottom: 20px;
    }
    .client-admin-page .filter-section {
      display: flex;
      flex-wrap: wrap;
      gap: 15px;
      margin-bottom: 20px;
    }
    .client-admin-page .filter-section input[type="search"],
    .client-admin-page .filter-section select {
      padding: 10px;
      font-size: 1em;
      border: 1px solid #ddd;
      border-radius: 4px;
    }
    .client-admin-page .data-table {
      width: 100%;
      border-collapse: collapse;
    }
    .client-admin-page .data-table th,
    .client-admin-page .data-table td {
      padding: 12px 15px;
      text-align: left;
      border-bottom: 1px solid #ddd;
    }
    .client-admin-page .data-table th {
      background-color: #f5f5f5;
    }
    .client-admin-page .data-table tbody tr:nth-child(even) {
      background-color: #fafafa;
    }
    .client-admin-page .data-table a {
      color: #3498db;
      text-decoration: none;
    }
    .client-admin-page .data-table a:hover {
      text-decoration: underline;
    }
    /* Style buttons only within this page */
    .client-admin-page button {
      padding: 7px 12px;
      font-size: 0.9em;
      border: none;
      border-radius: 4px;
      cursor: pointer;
      margin-right: 5px;
    }
    .client-admin-page button:nth-child(1) { background-color: #3498db; color: #fff; }
    .client-admin-page button:nth-child(2) { background-color: #f1c40f; color: #fff; }
    .client-admin-page button:nth-child(3) { background-color: #e74c3c; color: #fff; }
    .client-admin-page button:nth-child(4) { background-color: #2ecc71; color: #fff; }
    .client-admin-page button:hover {
      opacity: 0.9;
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
      padding: 30px;
      width: 500px;
      max-width: 90%;
      position: relative;
      box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
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
    /* Client Details Specific Styles */
    #clientDetailsContent {
      padding: 20px 0;
    }
    #clientDetailsContent h2 {
      color: #2d3748;
      font-size: 1.8rem;
      margin-bottom: 25px;
      padding-bottom: 15px;
      border-bottom: 2px solid #edf2f7;
    }
    .client-detail-item {
      margin-bottom: 20px;
      display: flex;
      align-items: flex-start;
    }
    .client-detail-label {
      width: 120px;
      font-weight: 600;
      color: #4a5568;
      flex-shrink: 0;
    }
    .client-detail-value {
      color: #2d3748;
      flex-grow: 1;
    }
    /* Close Button */
    .close {
      position: absolute;
      right: 20px;
      top: 20px;
      font-size: 24px;
      color: #a0aec0;
      cursor: pointer;
      transition: color 0.2s ease;
      background: none;
      border: none;
      padding: 0;
      width: 24px;
      height: 24px;
      display: flex;
      align-items: center;
      justify-content: center;
    }
    .close:hover {
      color: #4a5568;
    }
    /* Responsive Design */
    @media (max-width: 640px) {
      .modal-content {
        padding: 20px;
      }
      .client-detail-item {
        flex-direction: column;
      }
      .client-detail-label {
        width: 100%;
        margin-bottom: 5px;
      }
    }
  </style>
</head>
<body>
<div class="client-admin-page">
  <div class="admin-container">
    <h1>Manage Clients</h1>
    
    <!-- Filter Section -->
    <div class="filter-section">
      <input type="search" id="clientSearch" placeholder="Search by name, email or phone">
      <select id="statusFilter">
        <option value="">All Status</option>
        <option value="active">Active</option>
        <option value="banned">Banned</option>
        <option value="pending">Pending Verification</option>
      </select>
    </div>
    
    <!-- Client List Table -->
    <table class="data-table">
      <thead>
        <tr>
          <th>Client ID</th>
          <th>Full Name</th>
          <!-- Uncomment the email header if needed -->
          <!-- <th>Email</th> -->
          <th>Role</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody id="clientTable">
        <?php foreach($clients as $client): ?>
          <tr id="client-row-<?php echo $client['user_id']; ?>">
            <td><?php echo $client['user_id']; ?></td>
            <td>
              <!-- The button here displays the full name and holds all info in data attributes -->
              <button class="view-details" 
                data-user-id="<?php echo $client['user_id']; ?>"
                data-first-name="<?php echo $client['first_name']; ?>"
                data-last-name="<?php echo $client['last_name']; ?>"
                data-email="<?php echo $client['email'] ?? ''; ?>"
                data-phone="<?php echo $client['phone'] ?? ''; ?>"
                data-role="<?php echo $client['role']; ?>"
                onclick="showClientDetails(this)">
                <?php echo $client['first_name'] . ' ' . $client['last_name']; ?>
              </button>
            </td>
            <!-- Uncomment cell below if you wish to display email in the table -->
            <!-- <td><?php echo $client['email']; ?></td> -->
            <td><?php echo ucfirst($client['role']); ?></td>
            <td>
              <button class="view-details" 
                data-user-id="<?php echo $client['user_id']; ?>"
                data-first-name="<?php echo $client['first_name']; ?>"
                data-last-name="<?php echo $client['last_name']; ?>"
                data-email="<?php echo $client['email'] ?? ''; ?>"
                data-phone="<?php echo $client['phone'] ?? ''; ?>"
                data-role="<?php echo $client['role']; ?>"
                onclick="showClientDetails(this)">View Details</button>
              <button class="edit-client"
                data-user-id="<?php echo $client['user_id']; ?>"
                data-first-name="<?php echo $client['first_name']; ?>"
                data-last-name="<?php echo $client['last_name']; ?>"
                data-email="<?php echo $client['email'] ?? ''; ?>"
                data-phone="<?php echo $client['phone'] ?? ''; ?>"
                data-role="<?php echo $client['role']; ?>"
                onclick="showEditClientModal(this)">Edit</button>
              <button onclick="deleteClient(<?php echo $client['user_id']; ?>)">Delete</button>
              <?php if($client['role'] == 'banned'): ?>
                <button onclick="unbanClient(<?php echo $client['user_id']; ?>)">Unban</button>
              <?php else: ?>
                <button onclick="banClient(<?php echo $client['user_id']; ?>)">Ban</button>
              <?php endif; ?>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div><!-- .admin-container -->

  <!-- Client Details Modal -->
  <div id="clientDetailsModal" class="modal">
    <div class="modal-content">
      <span class="close" onclick="closeDetailsModal()">&times;</span>
      <div id="clientDetailsContent">
        <!-- Content will be populated by JavaScript -->
      </div>
    </div>
  </div>

  <!-- Client Edit Modal -->
  <div id="clientEditModal" class="modal">
    <div class="modal-content">
      <span class="close" onclick="closeEditModal()">&times;</span>
      <h2>Edit Client Information</h2>
      <form id="clientEditForm">
        <input type="hidden" id="editClientId" name="client_id">
        <label>First Name:
          <input type="text" id="editFirstName" name="first_name" required>
        </label>
        <label>Last Name:
          <input type="text" id="editLastName" name="last_name" required>
        </label>
        <label>Email:
          <input type="email" id="editEmail" name="email" required>
        </label>
        <label>Phone:
          <input type="text" id="editPhone" name="phone">
        </label>
        <button type="submit">Save Changes</button>
      </form>
    </div>
  </div>
</div><!-- .client-admin-page -->

<script>
  // Close modals
  function closeDetailsModal() {
    document.getElementById('clientDetailsModal').style.display = 'none';
  }
  function closeEditModal() {
    document.getElementById('clientEditModal').style.display = 'none';
  }

  // Show Client Details Modal using data from the button
  function showClientDetails(btn) {
    var userId    = btn.getAttribute('data-user-id');
    var firstName = btn.getAttribute('data-first-name');
    var lastName  = btn.getAttribute('data-last-name');
    var email     = btn.getAttribute('data-email');
    var phone     = btn.getAttribute('data-phone');
    var role      = btn.getAttribute('data-role');

    var content = `
        <h2>Client Details</h2>
        <div class="client-detail-item">
            <span class="client-detail-label">ID:</span>
            <span class="client-detail-value">${userId}</span>
        </div>
        <div class="client-detail-item">
            <span class="client-detail-label">Full Name:</span>
            <span class="client-detail-value">${firstName} ${lastName}</span>
        </div>
        <div class="client-detail-item">
            <span class="client-detail-label">Email:</span>
            <span class="client-detail-value">${email}</span>
        </div>
        <div class="client-detail-item">
            <span class="client-detail-label">Phone:</span>
            <span class="client-detail-value">${phone || 'Not provided'}</span>
        </div>
        <div class="client-detail-item">
            <span class="client-detail-label">Role:</span>
            <span class="client-detail-value">${role.charAt(0).toUpperCase() + role.slice(1)}</span>
        </div>
    `;

    document.getElementById('clientDetailsContent').innerHTML = content;
    document.getElementById('clientDetailsModal').style.display = 'flex';
  }

  // Show Client Edit Modal and pre-fill form fields using button data
  function showEditClientModal(btn) {
    document.getElementById('editClientId').value    = btn.getAttribute('data-user-id');
    document.getElementById('editFirstName').value   = btn.getAttribute('data-first-name');
    document.getElementById('editLastName').value    = btn.getAttribute('data-last-name');
    document.getElementById('editEmail').value       = btn.getAttribute('data-email');
    document.getElementById('editPhone').value       = btn.getAttribute('data-phone');
    document.getElementById('clientEditModal').style.display = 'block';
  }

  // Handle the Edit form submission (simulate AJAX update)
  document.getElementById('clientEditForm').addEventListener('submit', function(e) {
    e.preventDefault();
    var clientId  = document.getElementById('editClientId').value;
    var firstName = document.getElementById('editFirstName').value;
    var lastName  = document.getElementById('editLastName').value;
    var email     = document.getElementById('editEmail').value;
    var phone     = document.getElementById('editPhone').value;

    // Update the corresponding row in the table (simulate successful update)
    var row = document.getElementById('client-row-' + clientId);
    if (row) {
      // Update the cell with the client's name (the button inside contains the name)
      var nameCell = row.cells[1];
      var viewBtn  = nameCell.querySelector('button.view-details');
      viewBtn.innerText = firstName + ' ' + lastName;
      viewBtn.setAttribute('data-first-name', firstName);
      viewBtn.setAttribute('data-last-name', lastName);
      viewBtn.setAttribute('data-email', email);
      viewBtn.setAttribute('data-phone', phone);
      // Also update the "Edit" button in the same row
      var editBtn = row.querySelector('button.edit-client');
      editBtn.setAttribute('data-first-name', firstName);
      editBtn.setAttribute('data-last-name', lastName);
      editBtn.setAttribute('data-email', email);
      editBtn.setAttribute('data-phone', phone);
    }
    alert("Client details updated successfully!");
    closeEditModal();
  });

  // Action stubs for Delete, Ban, and Unban (replace with real calls if needed)
  function deleteClient(id) {
    if (confirm("Are you sure you want to delete this client?")) {
      alert("Deleted client: " + id);
      // Here you would remove the row from the table and update the server.
    }
  }
  function banClient(id) {
    if (confirm("Are you sure you want to ban this client?")) {
      alert("Banned client: " + id);
      // Send ban request to the server.
    }
  }
  function unbanClient(id) {
    if (confirm("Are you sure you want to unban this client?")) {
      alert("Unbanned client: " + id);
      // Send unban request to the server.
    }
  }

  // Client-side live search filtering
  document.getElementById('clientSearch').addEventListener('keyup', function() {
    var searchTerm = this.value.toLowerCase();
    var rows = document.querySelectorAll('#clientTable tr');
    rows.forEach(function(row) {
      var text = row.textContent.toLowerCase();
      row.style.display = text.indexOf(searchTerm) > -1 ? '' : 'none';
    });
  });
  document.getElementById('statusFilter').addEventListener('change', function() {
    var filterValue = this.value.toLowerCase();
    var rows = document.querySelectorAll('#clientTable tr');
    rows.forEach(function(row) {
      // Assuming the role is in the third column (index 2)
      var role = row.cells[2].textContent.toLowerCase();
      row.style.display = filterValue === "" || role.indexOf(filterValue) > -1 ? '' : 'none';
    });
  });
</script>
</body>
</html>
