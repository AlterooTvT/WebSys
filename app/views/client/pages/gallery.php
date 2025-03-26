<?php
require_once '../../../database/database.php';
require_once '../../../app/models/Gallery.php';

$database = new Database();
$db = $database->getConnection();
$gallery = new Gallery($db);

// Retrieve images dynamically by category.
// Adjust the category name values to match those used when uploading via admin.
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
<style>
.gallery-container {
    background-color: #fff;
    padding: 20px;
    min-height: 100vh;
}

.gallery-section {
    margin-bottom: 40px;
}

.gallery-section h2 {
    color: #000;
    margin-bottom: 20px;
    font-size: 24px;
    padding-left: 20px;
}

.gallery-grid {
    display: grid;
    grid-template-columns: repeat(5, 1fr);
    gap: 15px;
    padding: 0 20px;
    /* Set an expanded max-height high enough to show all rows */
    max-height: 2000px;
    transition: max-height 0.5s ease;
}

/* The collapsed state shows only about 2 rows (adjust 420px as needed) */
.gallery-grid.collapsed {
    max-height: 300px;
    overflow: hidden;
}

.gallery-item {
    aspect-ratio: 1;
    overflow: hidden;
    border-radius: 10px;
    transition: transform 0.3s ease;
}

.gallery-item:hover {
    transform: scale(1.05);
}

.gallery-item img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.see-more {
    display: block;
    text-align: center;
    color: #fff;
    text-decoration: none;
    margin-top: 15px;
    font-size: 14px;
}

.see-more:hover {
    text-decoration: underline;
}

.book-now-container {
    text-align: right;
    padding: 20px;
    position: fixed;
    bottom: 20px;
    right: 20px;
}

.book-now-btn {
    display: inline-block;
    background-color: #FFD700;
    color: #000;
    padding: 12px 30px;
    border-radius: 25px;
    text-decoration: none;
    font-weight: bold;
    transition: transform 0.3s ease;
}

.book-now-btn:hover {
    transform: scale(1.05);
}

/* Responsive Design */
@media (max-width: 1200px) {
    .gallery-grid {
        grid-template-columns: repeat(4, 1fr);
    }
}

@media (max-width: 992px) {
    .gallery-grid {
        grid-template-columns: repeat(3, 1fr);
    }
}

@media (max-width: 768px) {
    .gallery-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 480px) {
    .gallery-grid {
        grid-template-columns: 1fr;
    }
}

/* Optional: style the toggle link to show it’s clickable */
.see-more.toggle {
    cursor: pointer;
    text-decoration: underline;
}

</style>
</head>
<body>
    <div class="gallery-container">
        <!-- Wedding Section -->
        <div class="gallery-section">
            <h2>Wedding</h2>
            <div class="gallery-grid">
                <?php foreach ($weddingImages as $img): ?>
                    <div class="gallery-item">
                        <img src="../../../public/assets/images/gallery/<?php echo $img['filename']; ?>" alt="Wedding Photo">
                    </div>
                <?php endforeach; ?>
            </div>
            <a href="?pages=gallery&category=wedding" class="see-more toggle">See more...</a>
        </div>

        <!-- Birthdays Section -->
        <div class="gallery-section">
            <h2>Birthdays</h2>
            <div class="gallery-grid">
                <?php foreach ($birthdayImages as $img): ?>
                    <div class="gallery-item">
                        <img src="../../../public/assets/images/gallery/<?php echo $img['filename']; ?>" alt="Birthday Photo">
                    </div>
                <?php endforeach; ?>
            </div>
            <a href="?pages=gallery&category=birthday" class="see-more toggle">See more...</a>
        </div>

        <!-- Christening Section -->
        <div class="gallery-section">
            <h2>Christening</h2>
            <div class="gallery-grid">
                <?php foreach ($christeningImages as $img): ?>
                    <div class="gallery-item">
                        <img src="../../../public/assets/images/gallery/<?php echo $img['filename']; ?>" alt="Christening Photo">
                    </div>
                <?php endforeach; ?>
            </div>
            <a href="?pages=gallery&category=christening" class="see-more toggle">See more...</a>
        </div>

        <!-- Thanksgiving Section -->
        <div class="gallery-section">
            <h2>Thanksgiving</h2>
            <div class="gallery-grid">
                <?php foreach ($thanksgivingImages as $img): ?>
                    <div class="gallery-item">
                        <img src="../../../public/assets/images/gallery/<?php echo $img['filename']; ?>" alt="Thanksgiving Photo">
                    </div>
                <?php endforeach; ?>
            </div>
            <a href="?pages=gallery&category=thanksgiving" class="see-more toggle">See more...</a>
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