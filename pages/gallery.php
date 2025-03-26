<?php
// Include necessary configuration and model files.
require_once 'database/database.php';
require_once 'config/config.php';
require_once 'app/models/Gallery.php';

// Create a database connection.
$database = new Database();
$db = $database->getConnection();
$gallery = new Gallery($db);

/*
 * Retrieve images for each category.
 *
 * Note: We assume that the DB values are:
 * - 'wedding'
 * - 'birthday' (for the Birthdays section)
 * - 'christening'
 * - 'thanksgiving'
 */
$weddingImages      = $gallery->getImagesByCategory('wedding');
$birthdayImages     = $gallery->getImagesByCategory('birthday');
$christeningImages  = $gallery->getImagesByCategory('christening');
$thanksgivingImages = $gallery->getImagesByCategory('thanksgiving');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Gallery - Landing Page</title>
    <link rel="stylesheet" href="public/assets/css/gallery.css">
</head>
<body>
    <div class="gallery-container">
        <!-- Wedding Section -->
        <div class="gallery-section">
            <h2>Wedding</h2>
            <div class="gallery-grid">
                <?php foreach ($weddingImages as $img): ?>
                    <div class="gallery-item">
                        <img src="public/assets/images/gallery/<?php echo $img['filename']; ?>" alt="Wedding Photo">
                    </div>
                <?php endforeach; ?>
            </div>
            <a href="?page=gallery&category=wedding" class="see-more toggle">See more...</a>
        </div>

        <!-- Birthdays Section -->
        <div class="gallery-section">
            <h2>Birthdays</h2>
            <div class="gallery-grid">
                <?php foreach ($birthdayImages as $img): ?>
                    <div class="gallery-item">
                        <img src="public/assets/images/gallery/<?php echo $img['filename']; ?>" alt="Birthday Photo">
                    </div>
                <?php endforeach; ?>
            </div>
            <a href="?page=gallery&category=birthday" class="see-more toggle">See more...</a>
        </div>

        <!-- Christening Section -->
        <div class="gallery-section">
            <h2>Christening</h2>
            <div class="gallery-grid">
                <?php foreach ($christeningImages as $img): ?>
                    <div class="gallery-item">
                        <img src="public/assets/images/gallery/<?php echo $img['filename']; ?>" alt="Christening Photo">
                    </div>
                <?php endforeach; ?>
            </div>
            <a href="?page=gallery&category=christening" class="see-more toggle">See more...</a>
        </div>

        <!-- Thanksgiving Section -->
        <div class="gallery-section">
            <h2>Thanksgiving</h2>
            <div class="gallery-grid">
                <?php foreach ($thanksgivingImages as $img): ?>
                    <div class="gallery-item">
                        <img src="public/assets/images/gallery/<?php echo $img['filename']; ?>" alt="Thanksgiving Photo">
                    </div>
                <?php endforeach; ?>
            </div>
            <a href="?page=gallery&category=thanksgiving" class="see-more toggle">See more...</a>
        </div>

        <!-- Book Now Button -->
        <div class="book-now-container">
            <a href="?page=booking" class="book-now-btn">Book Now</a>
        </div>
    </div>
    <script>
        document.addEventListener("DOMContentLoaded", function(){
            // The height at which the grid is considered collapsed (must match the CSS value)
            var collapseHeight = 300; 

            document.querySelectorAll('.gallery-section').forEach(function(section) {
                var grid = section.querySelector('.gallery-grid');
                var toggle = section.querySelector('.see-more.toggle');
                
                if (grid && grid.scrollHeight > collapseHeight) {
                    // If the content is taller than the threshold, collapse it
                    grid.classList.add('collapsed');
                    toggle.textContent = "See More...";
                    toggle.style.display = "block";
                } else {
                    // Hide the toggle if there's not enough content to collapse
                    if (toggle) {
                        toggle.style.display = "none";
                    }
                }
                
                // Toggle the collapse on click
                if (toggle) {
                    toggle.addEventListener('click', function(e) {
                        e.preventDefault();  // Prevent default link action
                        if (grid.classList.contains('collapsed')) {
                            // Expand the grid
                            grid.classList.remove('collapsed');
                            toggle.textContent = "See Less...";
                        } else {
                            // Collapse the grid
                            grid.classList.add('collapsed');
                            toggle.textContent = "See More...";
                        }
                    });
                }
            });
        });
    </script>
</body>
</html>
