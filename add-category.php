<?php
// admin-dash.php
include_once __DIR__ . '/classes/Db.php';
include_once __DIR__ . '/classes/Category.php';
include_once __DIR__ . '/classes/ImageUploader.php';

session_start();

if ($_SESSION['role'] !== 1) {
    echo '<h1 style="text-align: center; padding:10%; color:red; font-family:Helvetica;">' . htmlspecialchars('You do not have access to this page') . '</h1>';
    header("refresh:3;url=login.php");
    exit();
}

$categorySuccessMessage = '';
$categoryErrorMessage = '';

// Handle adding category
if (isset($_POST['add_category'])) {
    $categoryName = htmlspecialchars(trim($_POST['category_name']), ENT_QUOTES, 'UTF-8');

    // Check if an image is uploaded
    if (isset($_FILES['category_image']) && $_FILES['category_image']['error'] === 0) {
        try {
            $imageUploader = new ImageUploader();
            $uploadResult = $imageUploader->uploadImage($_FILES['category_image']);
            if ($uploadResult) {
                $category = new Category();
                $category->setName($categoryName);
                $category->setImage($uploadResult);
                if ($category->create()) {
                    $categorySuccessMessage = 'Category added successfully!';
                } else {
                    $categoryErrorMessage = 'Failed to add category.';
                }
            } else {
                $categoryErrorMessage = 'Image upload failed.';
            }
        } catch (Exception $e) {
            $categoryErrorMessage = 'Error: ' . $e->getMessage();
        }
    } else {
        $categoryErrorMessage = 'Please choose an image.';
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="style.css">
    <title>Admin Dashboard</title>
</head>

<body>

    <section class="category padding">

  
        <!-- Success or error messages -->
        <?php if (!empty($categorySuccessMessage)): ?>
            <div class="alert-succes"><?= htmlspecialchars($categorySuccessMessage); ?></div>
        <?php endif; ?>

        <?php if (!empty($categoryErrorMessage)): ?>
            <div class="alert-danger"><?= htmlspecialchars($categoryErrorMessage); ?></div>
        <?php endif; ?>

        <form class="form-group add-product-container" method="post" action="" enctype="multipart/form-data">
        <div class="back">
                <a class="back-icon" href="admin-dash.php">
                    <i class="fa fa-arrow-left" aria-hidden="true"></i>
                </a>
            </div>
            <!-- File upload input (hidden) and preview section -->
            <div class="product-image">
                <label for="image" class="image-upload-label">
                    <img id="imagePreview" src="" style="display:none;"> <!-- Hide preview initially -->
                    <span class="upload-icon">+</span> <!-- Make sure this is visible -->
                </label>
                <input type="file" id="image" name="category_image" accept="image/*" required
                    onchange="previewImage(event, 'imagePreview')" style="display: none;">
            </div>

            <!-- Category Details Form -->
            <div class="category-details">
                <h2>Add Category</h2>
                <label for="category_name">Category Name:</label>
                <input type="text" id="category_name" name="category_name" required>

                <button class="btn btn-admin" type="submit" name="add_category">Add Category</button>
            </div>

        </form>

    </section>

    <script>
        // Image previewer
        function previewImage(event, previewId) {
            const reader = new FileReader();
            reader.onload = function () {
                const preview = document.getElementById(previewId);
                preview.src = reader.result;
                preview.style.display = 'block';
            };
            reader.readAsDataURL(event.target.files[0]);
        }
    </script>
</body>

</html>