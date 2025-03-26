<?php
// ========================
// PHP PROCESSING LOGIC & SETUP
// ========================
require_once '../../../database/database.php';
require_once '../../../app/models/Package.php';
require_once '../../../app/models/Service.php';
require_once '../../../app/models/AuthMiddleware.php';

use App\Middleware\AuthMiddleware;

// Require admin access and initialize the DB connection
AuthMiddleware::requireAdmin();
$database = new Database();
$db = $database->getConnection();

// Initialize objects
$package = new Package($db);
$service = new Service($db);

// Generate CSRF token if not exists
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!AuthMiddleware::validateCSRF()) {
        $_SESSION['error'] = 'Invalid CSRF token. Please try again.';
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'create_package':
                $package->name         = $_POST['name'];
                $package->service_type = $_POST['service_type'];
                $package->description  = $_POST['description'];
                $package->price        = $_POST['price'];
                $package->duration     = $_POST['duration'];
                $package->features     = $_POST['features'];
                $success_message = $package->create() ? "Package created successfully" : "Failed to create package";
                break;
            case 'update_package':
                $package->package_id   = $_POST['package_id'];
                $package->name         = $_POST['name'];
                $package->service_type = $_POST['service_type'];
                $package->description  = $_POST['description'];
                $package->price        = $_POST['price'];
                $package->duration     = $_POST['duration'];
                $package->features     = $_POST['features'];
                $success_message = $package->update() ? "Package updated successfully" : "Failed to update package";
                break;
            case 'delete_package':
                $success_message = $package->delete($_POST['package_id']) ? "Package deleted successfully" : "Failed to delete package";
                break;
            case 'create_service':
                $service->name        = $_POST['name'];
                $service->description = $_POST['description'];
                $service->price       = $_POST['price'];
                $service->duration    = $_POST['duration'];
                $success_message = $service->create() ? "Service created successfully" : "Failed to create service";
                break;
            case 'update_service':
                $service->service_id  = $_POST['service_id'];
                $service->name        = $_POST['name'];
                $service->description = $_POST['description'];
                $service->price       = $_POST['price'];
                $service->duration    = $_POST['duration'];
                $success_message = $service->update() ? "Service updated successfully" : "Failed to update service";
                break;
            case 'delete_service':
                $success_message = $service->delete($_POST['service_id']) ? "Service deleted successfully" : "Failed to delete service";
                break;
        }
    }
}

// Fetch packages and services
$packages = $package->getAllPackages()->fetchAll(PDO::FETCH_ASSOC);
$services = $service->getAllServices()->fetchAll(PDO::FETCH_ASSOC);

// Group packages by service type
$grouped_packages = [];
foreach ($packages as $pkg) {
    $grouped_packages[$pkg['service_type']][] = $pkg;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <!-- ========================
       HEAD SECTION: Meta, Bootstrap, Font Awesome & Custom CSS 
       ======================== -->
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Services & Packages Management</title>


  <style>
    /* ========================
       CUSTOM CSS: Cards, Animations & UI Elements
       ======================== */

    /* Base styling for cards */
    .service-card, .package-card {
        transition: all 0.3s ease;
        border: none;
        border-radius: 12px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        height: 100%;
        display: flex;
        flex-direction: column;
        position: relative;
    }
    .service-card:hover, .package-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 5px 20px rgba(0,0,0,0.15);
    }
    .card-body {
        flex: 1;
        display: flex;
        flex-direction: column;
    }
    .action-buttons {
        margin-top: auto;
        padding-top: 1rem;
    }

    /* Color coding for package types */
    .package-card[data-service-type="photobooth"] {
        border-left: 5px solid #4CAF50;
    }
    .package-card[data-service-type="360booth"] {
        border-left: 5px solid #2196F3;
    }
    .package-card[data-service-type="magazinebooth"] {
        border-left: 5px solid #9C27B0;
    }
    .package-card[data-service-type="partycart"] {
        border-left: 5px solid #FF9800;
    }
    /* Service cards */
    .service-card { 
        border-left: 5px solid #E91E63;
    }

    .price-tag {
        font-size: 1.5rem;
        font-weight: bold;
        color: #28a745;
        background: -webkit-linear-gradient(45deg, #28a745, #20c997);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }

    .duration-badge {
        position: absolute;
        top: 10px;
        right: 10px;
        background: linear-gradient(45deg, #17a2b8, #20c997);
        color: white;
        padding: 5px 15px;
        border-radius: 20px;
        font-size: 0.9rem;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }

    /* Navigation pills & filter sections */
    .nav-pills .nav-link {
        border-radius: 50px;
        padding: 0.5rem 1.5rem;
        font-weight: 500;
        color: #6c757d;
        background-color: #f8f9fa;
        border: 1px solid #dee2e6;
        margin-right: 0.5rem;
    }
    .nav-pills .nav-link.active {
        background: linear-gradient(45deg, #007bff, #0056b3);
        border-color: transparent;
        color: white;
    }
    .nav-pills .nav-link:hover:not(.active) {
        background-color: #e9ecef;
    }
    .filter-section {
        background: #f8f9fa;
        padding: 1.5rem;
        border-radius: 12px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    }
    .filter-section .form-control, .filter-section .form-select {
        border-radius: 20px;
        padding: 0.5rem 1.25rem;
        border: 1px solid #dee2e6;
    }
    .btn-group .sort-btn {
        border-radius: 0;
        padding: 0.5rem 1.25rem;
    }
    .btn-group .sort-btn:first-child {
        border-top-left-radius: 20px;
        border-bottom-left-radius: 20px;
    }
    .btn-group .sort-btn:last-child {
        border-top-right-radius: 20px;
        border-bottom-right-radius: 20px;
    }
    .sort-btn.active {
        background-color: #007bff;
        color: white;
        border-color: #007bff;
    }
    .service-type-header {
        color: #2c3e50;
        font-weight: 600;
        padding-bottom: 0.5rem;
        border-bottom: 2px solid #e9ecef;
        margin-top: 1.5rem;
    }
    .features-list {
        list-style: none;
        padding-left: 0;
        margin-bottom: 1rem;
    }
    .features-list li {
        padding: 0.25rem 0;
        position: relative;
        padding-left: 1.5rem;
    }
    .features-list li:before {
        content: "✓";
        position: absolute;
        left: 0;
        color: #28a745;
        font-weight: bold;
    }
    /* Card entry animation */
    .card {
        animation: fadeInUp 0.5s ease-out;
    }
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(20px); }
        to   { opacity: 1; transform: translateY(0); }
    }
    /* Ensure modals are hidden on load */
    .modal {
        display: none;
    }
  </style>
</head>
<body>
  <!-- ========================
       MAIN PAGE CONTENT
       ======================== -->
  <div class="content-wrapper">
    <div class="container-fluid">
        <h1 class="mt-4">Services & Packages Management</h1>
        
        <?php if (isset($success_message)): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?php echo $success_message; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        
        <?php if (isset($error_message)): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?php echo $error_message; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Navigation & Action Buttons -->
        <div class="d-flex justify-content-between align-items-center mb-4 sticky-top bg-white py-3">
            <ul class="nav nav-pills flex-nowrap" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active d-flex align-items-center" id="packages-tab" data-bs-toggle="tab" href="#packages" role="tab">
                        <i class="fas fa-box me-2"></i>Packages
                    </a>
                </li>
                <li class="nav-item ms-3">
                    <a class="nav-link d-flex align-items-center" id="services-tab" data-bs-toggle="tab" href="#services" role="tab">
                        <i class="fas fa-concierge-bell me-2"></i>Additional Services
                    </a>
                </li>
            </ul>
            <div class="d-flex">
                <button type="button" class="btn btn-primary me-2" data-bs-toggle="modal" data-bs-target="#addPackageModal">
                    <i class="fas fa-plus me-2"></i>Add New Package
                </button>
                <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addServiceModal">
                    <i class="fas fa-plus me-2"></i>Add New Service
                </button>
            </div>
        </div>

        <!-- Search & Filter Section -->
        <div class="filter-section bg-light rounded p-3 mb-4">
            <div class="row g-3">
                <div class="col-md-4">
                    <input type="text" id="searchInput" class="form-control" placeholder="Search packages or services...">
                </div>
                <div class="col-md-4">
                    <select id="typeFilter" class="form-select">
                        <option value="">All Types</option>
                        <option value="photobooth">Photo Booth</option>
                        <option value="360booth">360 Booth</option>
                        <option value="magazinebooth">Magazine Booth</option>
                        <option value="partycart">Party Cart</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <select id="priceFilter" class="form-select">
                        <option value="">Price Range</option>
                        <option value="0-1000">₱0 - ₱1,000</option>
                        <option value="1000-5000">₱1,000 - ₱5,000</option>
                        <option value="5000-10000">₱5,000 - ₱10,000</option>
                        <option value="10000+">₱10,000+</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="btn-group mb-4">
            <button class="btn btn-outline-primary sort-btn active" data-sort="name">Sort by Name</button>
            <button class="btn btn-outline-primary sort-btn" data-sort="price">Sort by Price</button>
            <button class="btn btn-outline-primary sort-btn" data-sort="duration">Sort by Duration</button>
        </div>

        <!-- Tab Content -->
        <div class="tab-content">
            <!-- Packages Tab -->
            <div class="tab-pane fade show active" id="packages" role="tabpanel">
                <div class="row g-4">
                    <?php foreach ($grouped_packages as $service_type => $pkgs): ?>
                        <div class="col-12">
                            <h4 class="service-type-header mb-3">
                                <?php echo ucfirst(str_replace('booth', ' Booth', $service_type)); ?>
                            </h4>
                            <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4 mb-4">
                                <?php foreach ($pkgs as $pkg): ?>
                                    <div class="col">
                                        <div class="card package-card h-100"
                                             data-service-type="<?php echo $pkg['service_type']; ?>"
                                             data-name="<?php echo htmlspecialchars($pkg['name']); ?>"
                                             data-price="<?php echo $pkg['price']; ?>"
                                             data-duration="<?php echo $pkg['duration']; ?>">
                                            <div class="duration-badge">
                                                <i class="far fa-clock me-1"></i><?php echo $pkg['duration']; ?> hours
                                            </div>
                                            <div class="card-body">
                                                <h5 class="card-title"><?php echo htmlspecialchars($pkg['name']); ?></h5>
                                                <p class="price-tag mb-3">₱<?php echo number_format($pkg['price'], 2); ?></p>
                                                <p class="card-text"><?php echo htmlspecialchars($pkg['description']); ?></p>
                                                <ul class="features-list">
                                                    <?php 
                                                    $features = explode("\n", $pkg['features']);
                                                    foreach ($features as $feature):
                                                        if (trim($feature)):
                                                    ?>
                                                        <li><?php echo htmlspecialchars(trim($feature)); ?></li>
                                                    <?php 
                                                        endif;
                                                    endforeach; 
                                                    ?>
                                                </ul>
                                                <div class="action-buttons">
                                                    <button class="btn btn-sm btn-primary edit-package"
                                                            data-package='<?php echo json_encode($pkg); ?>'
                                                            data-bs-toggle="modal" 
                                                            data-bs-target="#editPackageModal">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-danger delete-package"
                                                            data-package-id="<?php echo $pkg['package_id']; ?>"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#deletePackageModal">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Services Tab -->
            <div class="tab-pane fade" id="services" role="tabpanel">
                <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
                <?php foreach ($services as $svc): ?>
                    <div class="col">
                        <div class="card service-card h-100"
                            data-name="<?php echo htmlspecialchars($svc['name']); ?>"
                            data-price="<?php echo $svc['price']; ?>"
                            data-duration="<?php echo $svc['duration']; ?>">
                            <div class="duration-badge">
                                <i class="far fa-clock me-1"></i><?php echo $svc['duration']; ?> hours
                            </div>
                            <div class="card-body">
                                <h5 class="card-title"><?php echo htmlspecialchars($svc['name']); ?></h5>
                                <p class="price-tag mb-3">₱<?php echo number_format($svc['price'], 2); ?></p>
                                <p class="card-text"><?php echo htmlspecialchars($svc['description']); ?></p>
                                <!-- Action buttons for edit and delete -->
                                <div class="action-buttons">
                                    <button class="btn btn-sm btn-primary edit-service" 
                                            data-service='<?php echo json_encode($svc); ?>'
                                            data-bs-toggle="modal" 
                                            data-bs-target="#editServiceModal">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-sm btn-danger delete-service"
                                            data-service-id="<?php echo $svc['service_id']; ?>"
                                            data-bs-toggle="modal"
                                            data-bs-target="#deleteServiceModal">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
  </div>

  <!-- ========================
       MODALS: Add, Edit & Delete
       ======================== -->

  <!-- Add Package Modal -->
  <div class="modal fade" id="addPackageModal" tabindex="-1" aria-labelledby="addPackageModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
          <div class="modal-content">
              <div class="modal-header bg-light">
                  <h5 class="modal-title" id="addPackageModalLabel">
                      <i class="fas fa-box-open me-2"></i>Add New Package
                  </h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <form method="POST" class="needs-validation" novalidate>
                  <div class="modal-body">
                      <p class="text-muted mb-4">Create a new package by filling out the form details.</p>
                      <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                      <input type="hidden" name="action" value="create_package">
                      
                      <div class="mb-3">
                          <label class="form-label" for="package_name">Name</label>
                          <input type="text" class="form-control" id="package_name" name="name" required>
                          <div class="invalid-feedback">Please provide a package name.</div>
                      </div>
                      
                      <div class="mb-3">
                          <label class="form-label" for="package_service_type">Service Type</label>
                          <select class="form-select" id="package_service_type" name="service_type" required>
                              <option value="">Select a service type</option>
                              <option value="photobooth">Photo Booth</option>
                              <option value="360booth">360 Booth</option>
                              <option value="magazinebooth">Magazine Booth</option>
                              <option value="partycart">Party Cart</option>
                          </select>
                          <div class="invalid-feedback">Please select a service type.</div>
                      </div>
                      
                      <div class="mb-3">
                          <label class="form-label" for="package_description">Description</label>
                          <textarea class="form-control" id="package_description" name="description" rows="3" required></textarea>
                          <div class="invalid-feedback">Please provide a description.</div>
                      </div>
                      
                      <div class="mb-3">
                          <label class="form-label" for="package_price">Price</label>
                          <div class="input-group">
                              <span class="input-group-text">₱</span>
                              <input type="number" class="form-control" id="package_price" name="price" step="0.01" min="0" required>
                              <div class="invalid-feedback">Please provide a valid price.</div>
                          </div>
                      </div>
                      
                      <div class="mb-3">
                          <label class="form-label" for="package_duration">Duration (hours)</label>
                          <input type="number" class="form-control" id="package_duration" name="duration" min="1" required>
                          <div class="invalid-feedback">Please provide a valid duration.</div>
                      </div>
                      
                      <div class="mb-3">
                          <label class="form-label" for="package_features">Features (one per line)</label>
                          <textarea class="form-control" id="package_features" name="features" rows="4" required 
                                    placeholder="Unlimited shots&#10;Soft copy of photos&#10;2 photographers"></textarea>
                          <div class="invalid-feedback">Please provide package features.</div>
                      </div>
                  </div>
                  <div class="modal-footer bg-light">
                      <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                          <i class="fas fa-times me-2"></i>Cancel
                      </button>
                      <button type="submit" class="btn btn-primary">
                          <i class="fas fa-plus me-2"></i>Add Package
                      </button>
                  </div>
              </form>
          </div>
      </div>
  </div>

  <!-- Add Service Modal -->
  <div class="modal fade" id="addServiceModal" tabindex="-1" aria-labelledby="addServiceModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
          <div class="modal-content">
              <div class="modal-header bg-light">
                  <h5 class="modal-title" id="addServiceModalLabel">
                      <i class="fas fa-concierge-bell me-2"></i>Add New Service
                  </h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <form method="POST" class="needs-validation" novalidate>
                  <div class="modal-body">
                      <p class="text-muted mb-4">Create a new additional service by filling out the form details.</p>
                      <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                      <input type="hidden" name="action" value="create_service">
                      
                      <div class="mb-3">
                          <label class="form-label" for="service_name">Name</label>
                          <input type="text" class="form-control" id="service_name" name="name" required>
                          <div class="invalid-feedback">Please provide a service name.</div>
                      </div>
                      
                      <div class="mb-3">
                          <label class="form-label" for="service_description">Description</label>
                          <textarea class="form-control" id="service_description" name="description" rows="3" required></textarea>
                          <div class="invalid-feedback">Please provide a description.</div>
                      </div>
                      
                      <div class="mb-3">
                          <label class="form-label" for="service_price">Price</label>
                          <div class="input-group">
                              <span class="input-group-text">₱</span>
                              <input type="number" class="form-control" id="service_price" name="price" step="0.01" min="0" required>
                              <div class="invalid-feedback">Please provide a valid price.</div>
                          </div>
                      </div>
                      
                      <div class="mb-3">
                          <label class="form-label" for="service_duration">Duration (hours)</label>
                          <input type="number" class="form-control" id="service_duration" name="duration" min="1" required>
                          <div class="invalid-feedback">Please provide a valid duration.</div>
                      </div>
                  </div>
                  <div class="modal-footer bg-light">
                      <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                          <i class="fas fa-times me-2"></i>Cancel
                      </button>
                      <button type="submit" class="btn btn-primary">
                          <i class="fas fa-plus me-2"></i>Add Service
                      </button>
                  </div>
              </form>
          </div>
      </div>
  </div>

  <!-- Edit Package Modal -->
  <div class="modal fade" id="editPackageModal" tabindex="-1" aria-labelledby="editPackageModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
          <div class="modal-content">
              <div class="modal-header">
                  <h5 class="modal-title" id="editPackageModalLabel">
                      <i class="fas fa-edit me-2"></i>Edit Package
                  </h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <form method="POST" class="needs-validation" novalidate>
                  <div class="modal-body">
                      <p class="text-muted mb-4">Modify the package details below.</p>
                      <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                      <input type="hidden" name="action" value="update_package">
                      <input type="hidden" name="package_id" id="edit_package_id">
                      
                      <div class="mb-3">
                          <label class="form-label" for="edit_name">Name</label>
                          <input type="text" class="form-control" id="edit_name" name="name" required>
                          <div class="invalid-feedback">Please provide a package name.</div>
                      </div>
                      
                      <div class="mb-3">
                          <label class="form-label" for="edit_service_type">Service Type</label>
                          <select class="form-select" id="edit_service_type" name="service_type" required>
                              <option value="photobooth">Photo Booth</option>
                              <option value="360booth">360 Booth</option>
                              <option value="magazinebooth">Magazine Booth</option>
                              <option value="partycart">Party Cart</option>
                          </select>
                          <div class="invalid-feedback">Please select a service type.</div>
                      </div>
                      
                      <div class="mb-3">
                          <label class="form-label" for="edit_description">Description</label>
                          <textarea class="form-control" id="edit_description" name="description" rows="3" required></textarea>
                          <div class="invalid-feedback">Please provide a description.</div>
                      </div>
                      
                      <div class="mb-3">
                          <label class="form-label" for="edit_price">Price</label>
                          <div class="input-group">
                              <span class="input-group-text">₱</span>
                              <input type="number" class="form-control" id="edit_price" name="price" step="0.01" min="0" required>
                              <div class="invalid-feedback">Please provide a valid price.</div>
                          </div>
                      </div>
                      
                      <div class="mb-3">
                          <label class="form-label" for="edit_duration">Duration (hours)</label>
                          <input type="number" class="form-control" id="edit_duration" name="duration" min="1" required>
                          <div class="invalid-feedback">Please provide a valid duration.</div>
                      </div>
                      
                      <div class="mb-3">
                          <label class="form-label" for="edit_features">Features (one per line)</label>
                          <textarea class="form-control" id="edit_features" name="features" rows="4" required></textarea>
                          <div class="invalid-feedback">Please provide package features.</div>
                      </div>
                  </div>
                  <div class="modal-footer">
                      <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                          <i class="fas fa-times me-2"></i>Cancel
                      </button>
                      <button type="submit" class="btn btn-primary">
                          <i class="fas fa-save me-2"></i>Update Package
                      </button>
                  </div>
              </form>
          </div>
      </div>
  </div>

  <!-- Edit Service Modal -->
  <div class="modal fade" id="editServiceModal" tabindex="-1" aria-labelledby="editServiceModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
          <div class="modal-content">
              <div class="modal-header">
                  <h5 class="modal-title" id="editServiceModalLabel">
                      <i class="fas fa-edit me-2"></i>Edit Service
                  </h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <form method="POST" class="needs-validation" novalidate>
                  <div class="modal-body">
                      <p class="text-muted mb-4">Modify the service details below.</p>
                      <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                      <input type="hidden" name="action" value="update_service">
                      <input type="hidden" name="service_id" id="edit_service_id">
                      
                      <div class="mb-3">
                          <label class="form-label" for="edit_service_name">Name</label>
                          <input type="text" class="form-control" id="edit_service_name" name="name" required>
                          <div class="invalid-feedback">Please provide a service name.</div>
                      </div>
                      
                      <div class="mb-3">
                          <label class="form-label" for="edit_service_description">Description</label>
                          <textarea class="form-control" id="edit_service_description" name="description" rows="3" required></textarea>
                          <div class="invalid-feedback">Please provide a description.</div>
                      </div>
                      
                      <div class="mb-3">
                          <label class="form-label" for="edit_service_price">Price</label>
                          <div class="input-group">
                              <span class="input-group-text">₱</span>
                              <input type="number" class="form-control" id="edit_service_price" name="price" step="0.01" min="0" required>
                              <div class="invalid-feedback">Please provide a valid price.</div>
                          </div>
                      </div>
                      
                      <div class="mb-3">
                          <label class="form-label" for="edit_service_duration">Duration (hours)</label>
                          <input type="number" class="form-control" id="edit_service_duration" name="duration" min="1" required>
                          <div class="invalid-feedback">Please provide a valid duration.</div>
                      </div>
                  </div>
                  <div class="modal-footer">
                      <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                          <i class="fas fa-times me-2"></i>Cancel
                      </button>
                      <button type="submit" class="btn btn-primary">
                          <i class="fas fa-save me-2"></i>Update Service
                      </button>
                  </div>
              </form>
          </div>
      </div>
  </div>

  <!-- Delete Package Modal -->
  <div class="modal fade" id="deletePackageModal" tabindex="-1" aria-labelledby="deletePackageModalLabel" aria-hidden="true">
      <div class="modal-dialog">
          <div class="modal-content">
              <div class="modal-header">
                  <h5 class="modal-title">Delete Package</h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <form method="POST">
                  <div class="modal-body">
                      <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                      <input type="hidden" name="action" value="delete_package">
                      <input type="hidden" name="package_id" id="delete_package_id">
                      <p>Are you sure you want to delete this package? This action cannot be undone.</p>
                  </div>
                  <div class="modal-footer">
                      <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                      <button type="submit" class="btn btn-danger">Delete</button>
                  </div>
              </form>
          </div>
      </div>
  </div>

  <!-- Delete Service Modal -->
  <div class="modal fade" id="deleteServiceModal" tabindex="-1" aria-labelledby="deleteServiceModalLabel" aria-hidden="true">
      <div class="modal-dialog">
          <div class="modal-content">
              <div class="modal-header">
                  <h5 class="modal-title">Delete Service</h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <form method="POST">
                  <div class="modal-body">
                      <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                      <input type="hidden" name="action" value="delete_service">
                      <input type="hidden" name="service_id" id="delete_service_id">
                      <p>Are you sure you want to delete this service? This action cannot be undone.</p>
                  </div>
                  <div class="modal-footer">
                      <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                      <button type="submit" class="btn btn-danger">Delete</button>
                  </div>
              </form>
          </div>
      </div>
  </div>
  <!-- ========================
       SCRIPTS: Bootstrap JS, jQuery & Custom JS
       ======================== -->

  <!-- jQuery (required for the reset code below) -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <!-- Bootstrap Bundle with Popper -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  
  <script>
  document.addEventListener('DOMContentLoaded', function() {
      // Initialize variables
      const searchInput = document.getElementById('searchInput');
      const typeFilter = document.getElementById('typeFilter');
      const priceFilter = document.getElementById('priceFilter');
      const sortButtons = document.querySelectorAll('.sort-btn');
      
      // Handle edit package button clicks
      document.querySelectorAll('.edit-package').forEach(button => {
          button.addEventListener('click', function() {
              const packageData = JSON.parse(this.dataset.package);
              document.getElementById('edit_package_id').value = packageData.package_id;
              document.getElementById('edit_name').value = packageData.name;
              document.getElementById('edit_service_type').value = packageData.service_type;
              document.getElementById('edit_description').value = packageData.description;
              document.getElementById('edit_price').value = packageData.price;
              document.getElementById('edit_duration').value = packageData.duration;
              document.getElementById('edit_features').value = packageData.features;
          });
      });

      // Handle edit service button clicks
      document.querySelectorAll('.edit-service').forEach(button => {
          button.addEventListener('click', function() {
              const serviceData = JSON.parse(this.dataset.service);
              document.getElementById('edit_service_id').value = serviceData.service_id;
              document.getElementById('edit_service_name').value = serviceData.name;
              document.getElementById('edit_service_description').value = serviceData.description;
              document.getElementById('edit_service_price').value = serviceData.price;
              document.getElementById('edit_service_duration').value = serviceData.duration;
          });
      });

      // Handle delete package button clicks
      document.querySelectorAll('.delete-package').forEach(button => {
          button.addEventListener('click', function() {
              document.getElementById('delete_package_id').value = this.dataset.packageId;
          });
      });

      // Handle delete service button clicks
      document.querySelectorAll('.delete-service').forEach(button => {
          button.addEventListener('click', function() {
              document.getElementById('delete_service_id').value = this.dataset.serviceId;
          });
      });
      
      // Function to filter and sort cards
      function filterAndSortCards() {
          const searchTerm = searchInput.value.toLowerCase();
          const selectedType = typeFilter.value;
          const selectedPriceRange = priceFilter.value;
          const activeSort = document.querySelector('.sort-btn.active').dataset.sort;
          
          // Get all cards (both packages and services)
          const packageCards = document.querySelectorAll('.package-card');
          const serviceCards = document.querySelectorAll('.service-card');
          const allCards = [...packageCards, ...serviceCards];
          
          allCards.forEach(card => {
              let show = true;
              const cardName = card.querySelector('.card-title').textContent.toLowerCase();
              const cardPrice = parseFloat(card.dataset.price);
              const cardType = card.dataset.serviceType;
              
              // Search filter
              if (searchTerm && !cardName.includes(searchTerm)) {
                  show = false;
              }
              
              // Type filter
              if (selectedType && cardType !== selectedType) {
                  show = false;
              }
              
              // Price filter
              if (selectedPriceRange) {
                  const [min, max] = selectedPriceRange.split('-').map(val => val === '+' ? Infinity : parseFloat(val));
                  if (cardPrice < min || cardPrice > max) {
                      show = false;
                  }
              }
              
              // Show/hide card with animation
              const col = card.closest('.col');
              if (show) {
                  col.style.display = '';
                  col.style.opacity = '0';
                  setTimeout(() => {
                      col.style.opacity = '1';
                      col.style.transition = 'opacity 0.3s ease-in';
                  }, 50);
              } else {
                  col.style.display = 'none';
              }
          });
          
          // Sort visible cards
          const visibleCards = [...allCards].filter(card => card.closest('.col').style.display !== 'none');
          visibleCards.sort((a, b) => {
              switch(activeSort) {
                  case 'name':
                      return a.dataset.name.localeCompare(b.dataset.name);
                  case 'price':
                      return parseFloat(a.dataset.price) - parseFloat(b.dataset.price);
                  case 'duration':
                      return parseInt(a.dataset.duration) - parseInt(b.dataset.duration);
                  default:
                      return 0;
              }
          });
          
          // Reorder cards with animation
          visibleCards.forEach((card, index) => {
              const col = card.closest('.col');
              const container = col.parentElement;
              container.appendChild(col);
              setTimeout(() => {
                  col.style.transform = 'translateY(0)';
                  col.style.opacity = '1';
              }, index * 50);
          });
      }
      
      // Event listeners for search, filter, and sort
      searchInput.addEventListener('input', filterAndSortCards);
      typeFilter.addEventListener('change', filterAndSortCards);
      priceFilter.addEventListener('change', filterAndSortCards);
      
      sortButtons.forEach(button => {
          button.addEventListener('click', function() {
              sortButtons.forEach(btn => btn.classList.remove('active'));
              this.classList.add('active');
              filterAndSortCards();
          });
      });
      
      // Initialize tooltips
      const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
      tooltipTriggerList.map(function (tooltipTriggerEl) {
          return new bootstrap.Tooltip(tooltipTriggerEl);
      });

      // Auto-dismiss alerts after 5 seconds
      const alerts = document.querySelectorAll('.alert');
      alerts.forEach(alert => {
          setTimeout(() => {
              bootstrap.Alert.getInstance(alert)?.close();
          }, 5000);
      });
      
      // Enhanced hover effects for cards
      const cards = document.querySelectorAll('.package-card, .service-card');
      cards.forEach(card => {
          card.addEventListener('mouseenter', function() {
              this.style.transform = 'translateY(-10px)';
              this.style.transition = 'all 0.3s ease';
          });
          card.addEventListener('mouseleave', function() {
              this.style.transform = 'translateY(0)';
              this.style.transition = 'all 0.3s ease';
          });
      });

      // Proper modal initialization & vertical centering
      const modals = document.querySelectorAll('.modal');
      modals.forEach(modal => {
          modal.addEventListener('show.bs.modal', function () {
              document.body.style.overflow = 'hidden';
          });
          
          modal.addEventListener('hidden.bs.modal', function () {
              document.body.style.overflow = '';
          });
          
          modal.addEventListener('shown.bs.modal', function () {
              const dialog = this.querySelector('.modal-dialog');
              const modalHeight = dialog.offsetHeight;
              const windowHeight = window.innerHeight;
              if (modalHeight < windowHeight) {
                  dialog.style.marginTop = Math.max(0, (windowHeight - modalHeight) / 2) + 'px';
              } else {
                  dialog.style.marginTop = '';
              }
          });
      });
      
      // Reset form on modal close
      document.querySelectorAll('[data-bs-dismiss="modal"]').forEach(button => {
          button.addEventListener('click', function() {
              const form = this.closest('.modal').querySelector('form');
              if (form) form.reset();
          });
      });
  });

  // Form validation
  (function () {
      'use strict'
      var forms = document.querySelectorAll('.needs-validation')
      Array.prototype.slice.call(forms)
          .forEach(function (form) {
              form.addEventListener('submit', function (event) {
                  if (!form.checkValidity()) {
                      event.preventDefault();
                      event.stopPropagation();
                  }
                  form.classList.add('was-validated');
              }, false);
          });
  })();

  // Reset form validation when modal is hidden using jQuery for convenience
  $('#addPackageModal, #addServiceModal').on('hidden.bs.modal', function () {
      $(this).find('form').removeClass('was-validated')[0].reset();
  });
  </script>
</body>
</html>
