<?php
// Include your database and model files (adjust the paths as needed)
require_once 'database/database.php';
require_once 'app/models/Package.php';
require_once 'app/models/Service.php';

// Initialize the database and models
$database    = new Database();
$db          = $database->getConnection();

$serviceModel = new Service($db);
$packageModel = new Package($db);

// Fetch all records from the services and packages tables
$servicesData = $serviceModel->getAllServices()->fetchAll(PDO::FETCH_ASSOC);
$packagesData = $packageModel->getAllPackages()->fetchAll(PDO::FETCH_ASSOC);

// Optional: Define media mappings for services—so that we can use custom images or videos
$serviceMediaMap = [
    "Photobooth"      => ["type" => "image", "file" => "PhotoCard.jpg"],
    "Partycart"       => ["type" => "image", "file" => "partyCart.jpg"],
    "360 Booth"       => ["type" => "video", "file" => "360.mp4"],
    "Magazine Booth"  => ["type" => "image", "file" => "magazine.jpg"],
];

// ------------------ Group Packages by Category ------------------

// Group packages by service_type (converted to lowercase)
$groupedPackages = [];
foreach ($packagesData as $pkg) {
    $type = strtolower($pkg['service_type']);
    $groupedPackages[$type][] = $pkg;
}

// Define the order in which you want these categories to appear
$order = ['360booth', 'photobooth', 'partycart', 'magazinebooth'];

// Define category labels for display
$categoryLabels = [
    '360booth'     => '360 Booth',
    'photobooth'   => 'Photobooth',
    'partycart'    => 'Party Cart',
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
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Services & Packages</title>
  <link rel="stylesheet" href="public/assets/css/services.css">
  <style>
    /* Additional styles for package cards */
    .package-card {
      flex: 0 1 300px;
      padding: 20px;
      border-radius: 15px;
      text-align: center;
      color: white;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    }
    /* Packages Section Styling – These styles apply only to the packages section */
    #services {
      padding: 50px 20px;
      background-color: rgb(0, 0, 0);
      color: #FFD700;
    }
    #services h2 {
      text-align: center;
      margin-bottom: 30px;
    }
    .category-heading {
      text-align: left;
      color: #FFD700;
      margin: 30px 0 10px;
      font-size: 1.5em;
    }
    .packages-category-container {
      display: flex;
      gap: 20px;
      overflow-x: auto;
      padding-bottom: 20px;
      -webkit-overflow-scrolling: touch;
    }
    .package-card h3 {
      margin: 10px 0;
      font-size: 1.3em;
    }
    .package-card p {
      margin: 10px 0;
    }
    .cta-btn {
      display: inline-block;
      margin-top: 15px;
      padding: 10px 20px;
      background: #FFD700;
      color: #000;
      text-decoration: none;
      border-radius: 5px;
      transition: transform 0.3s ease;
    }
    .cta-btn:hover {
      transform: scale(1.05);
    }
  </style>
</head>
<body>

    <!-- Top Section: Display Services from the database -->
    <div class="services-container">
      <div class="services-grid">
        <?php foreach ($servicesData as $svc): ?>
          <div class="service-card">
            <div class="service-image">
              <?php 
                // Use media mapping if available; otherwise, use 'default.jpg'
                $media = isset($serviceMediaMap[$svc['name']]) ? $serviceMediaMap[$svc['name']] : null;
                if ($media && $media['type'] === 'video'): ?>
                  <video autoplay muted loop>
                    <source src="public/assets/images/services/<?php echo htmlspecialchars($media['file']); ?>" type="video/mp4">
                    Your browser does not support the video tag.
                  </video>
              <?php else: ?>
                  <img src="public/assets/images/services/<?php echo $media ? htmlspecialchars($media['file']) : 'default.jpg'; ?>" 
                       alt="<?php echo htmlspecialchars($svc['name']); ?>">
              <?php endif; ?>
            </div>
            <div class="service-content">
              <h2><?php echo htmlspecialchars($svc['name']); ?></h2>
              <p class="price">Starting at ₱<?php echo number_format($svc['price'], 0); ?></p>
              <?php if (!empty($svc['duration'])): ?>
                <p><strong><?php echo htmlspecialchars($svc['duration']); ?> Hours</strong></p>
              <?php endif; ?>
              <p><?php echo nl2br(htmlspecialchars($svc['description'])); ?></p>
              <!-- Book Now link with GET parameters using $svc -->
              <a href="<?php 
                echo SITE_URL . '/app/views/auth/auth.php'
              ?>" class="book-now-btn">Book Now</a>            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
    
    <!-- Packages Section: Display packages grouped by category -->
    <section id="services">
      <h2>Our Services & Packages</h2>
      <?php 
      // Loop through each category in our defined order
      foreach ($order as $type) {
          if (isset($groupedPackages[$type])) {
              echo '<h3 class="category-heading">' . htmlspecialchars($categoryLabels[$type]) . '</h3>';
              echo '<div class="packages-category-container">';
              foreach ($groupedPackages[$type] as $pkg) {
                  $gradient = isset($packageGradientMap[$type]) ? $packageGradientMap[$type] : 'linear-gradient(135deg, #333, #555)';
                  echo '<div class="package-card" style="background: ' . $gradient . ';">';
                  echo '<h3>' . htmlspecialchars($pkg['name']) . '</h3>';
                  echo '<p>Starting at <strong>₱' . number_format($pkg['price'], 0) . '</strong></p>';
                  echo '<p><strong>' . htmlspecialchars($pkg['duration']) . ' Hours</strong></p>';
                  echo '<p>' . nl2br(htmlspecialchars($pkg['description'])) . '</p>';
                  // Use $pkg instead of $package
                  echo '<a href=' . SITE_URL . '/app/views/auth/auth.php class="cta-btn">Book Now</a>';
                  echo '</div>';
              }
              echo '</div>';
          }
      }
      ?>
    </section>
    
    <script>
      // Handle video loading if any videos in the service image container
      document.addEventListener('DOMContentLoaded', function() {
        const videos = document.querySelectorAll('.service-image video');
        videos.forEach(video => {
          video.addEventListener('loadeddata', function() {
            video.classList.add('loaded');
          });
        });
      });
    </script>
    
</body>
</html>
