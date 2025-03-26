<?php
require_once '../../../database/database.php';
require_once '../../../config/config.php';
require_once '../../../app/models/Gallery.php';

$database = new Database();
$db = $database->getConnection();
$gallery = new Gallery($db);

if ($_POST) {
    if (isset($_FILES['images'])) {
        foreach ($_FILES['images']['tmp_name'] as $key => $tmp_name) {
            $file_name = $_FILES['images']['name'][$key];
            // Optionally add file validations here
            $gallery->uploadImage($tmp_name, $file_name, $_POST['category']);
        }
    }
}

$images = $gallery->getAllImages();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Gallery Management - Admin</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
    <style>
        /* Custom styling to enhance the gallery page */
        .admin-container {
            max-width: 1200px;
            margin: 30px auto;
            padding: 20px;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        h1 {
            font-size: 2.2em;
            color: #333;
            margin-bottom: 20px;
            text-align: center;
        }
        .upload-form {
            background-color: #f0f3f7;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 30px;
        }
        .upload-form .form-group {
            margin-bottom: 15px;
        }
        .upload-form label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #333;
        }
        .upload-form input[type="file"],
        .upload-form select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccd1d9;
            border-radius: 4px;
            background-color: #fff;
        }
        .btn-primary {
            display: inline-block;
            background-color: #3498db;
            color: #fff;
            padding: 12px 30px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 1em;
            transition: background-color 0.3s ease;
        }
        .btn-primary:hover {
            background-color: #2980b9;
        }
        .gallery-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            grid-gap: 20px;
            margin-top: 30px;
        }
        .gallery-item {
            position: relative;
            overflow: hidden;
            border-radius: 8px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
            background-color: #fff;
        }
        .gallery-item img {
            width: 100%;
            height: auto;
            display: block;
            transition: transform 0.3s ease;
        }
        .gallery-item:hover img {
            transform: scale(1.05);
        }
        /* Category label */
        .image-category {
            position: absolute;
            top: 5px;
            left: 5px;
            background-color: rgba(0, 0, 0, 0.6);
            color: #fff;
            padding: 4px 8px;
            border-radius: 3px;
            font-size: 0.9em;
            text-transform: capitalize;
            z-index: 5;
            pointer-events: none;
        }
        /* Action buttons container */
        .gallery-actions {
            position: absolute;
            bottom: 5px;
            right: 5px;
            z-index: 10;
        }
        .btn-delete {
            background-color: rgba(231, 76, 60, 0.8);
            color: #fff;
            border: none;
            padding: 8px 12px;
            border-radius: 4px;
            font-size: 0.9em;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        .btn-delete:hover {
            background-color: rgba(231, 76, 60, 1);
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <div class="main-content">
            <h1>Gallery Management</h1>
            
            <form method="POST" enctype="multipart/form-data" class="upload-form">
                <div class="form-group">
                    <label>Upload Images</label>
                    <input type="file" name="images[]" multiple accept="image/*" required>
                </div>
                <div class="form-group">
                    <label>Category</label>
                    <select name="category" required>
                        <option value="wedding">Wedding</option>
                        <option value="birthday">Birthdays</option>
                        <option value="christineng">Christineng</option>
                        <option value="thanksgiving">Thanksgiving</option>
                    </select>
                </div>
                <button type="submit" class="btn-primary">Upload Images</button>
            </form>

            <div class="gallery-grid">
                <?php while ($image = $images->fetch(PDO::FETCH_ASSOC)): ?>
                    <div class="gallery-item" id="image-<?php echo $image['image_id']; ?>">
                        <img src="../../../public/assets/images/gallery/<?php echo $image['filename']; ?>" alt="Gallery Image">
                        <div class="image-category"><?php echo ucfirst($image['service_type']); ?></div>
                        <div class="gallery-actions">
                            <button onclick="deleteImage(<?php echo $image['image_id']; ?>)" class="btn-delete">Delete</button>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        </div>
    </div>
    <script>
        function deleteImage(imageId) {
            if (confirm("Are you sure you want to delete this image?")) {
                var xhr = new XMLHttpRequest();
                xhr.open("POST", "../../../api/gallery_delete.php", true);
                xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                xhr.onreadystatechange = function() {
                    if (xhr.readyState === 4) {
                        if (xhr.status === 200) {
                            // Remove the image element from the DOM
                            var elem = document.getElementById("image-" + imageId);
                            if (elem) {
                                elem.parentNode.removeChild(elem);
                            }
                        } else {
                            alert("Failed to delete image. Please try again.");
                        }
                    }
                };
                xhr.send("image_id=" + encodeURIComponent(imageId));
            }
        }
    </script>
</body>
</html>
