<?php
session_start();
require_once(__DIR__ . '/classes/ImageUploader.php');
require_once(__DIR__ . '/classes/Category.php');

// Check if the user is logged in and has the correct role
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 1) {
    header("refresh:3;url=login.php");
    echo '<h1 style="text-align: center; padding:10%; color:red; font-family:Helvetica;">' . htmlspecialchars('You do not have access to this page') . '</h1>';
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
                    $categorySuccessMessage = 'Categorie toegevoegd!';
                } else {
                    $categoryErrorMessage = 'Categorie kon niet worden toegevoegd.';
                }
            } else {
                $categoryErrorMessage = 'Afbeelding uploaden mislukt.';
            }
        } catch (Exception $e) {
            $categoryErrorMessage = 'Error: ' . $e->getMessage();
        }
    } else {
        $categoryErrorMessage = 'Kies een afbeelding.';
    }

    // Store messages in session and redirect
    $_SESSION['categorySuccessMessage'] = $categorySuccessMessage;
    $_SESSION['categoryErrorMessage'] = $categoryErrorMessage;
    header("Location: add-category.php");
    exit();
}

// Retrieve messages from session
if (isset($_SESSION['categorySuccessMessage'])) {
    $categorySuccessMessage = $_SESSION['categorySuccessMessage'];
    unset($_SESSION['categorySuccessMessage']);
}

if (isset($_SESSION['categoryErrorMessage'])) {
    $categoryErrorMessage = $_SESSION['categoryErrorMessage'];
    unset($_SESSION['categoryErrorMessage']);
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
    <title>Categorie toevoegen</title>
</head>

<body>

    <section class="category padding">

        <!-- Success or error messages -->
        <?php if (!empty($categorySuccessMessage)): ?>
            <div class="alert-success"><?= htmlspecialchars($categorySuccessMessage); ?></div>
        <?php endif; ?>

        <?php if (!empty($categoryErrorMessage)): ?>
            <div class="alert-danger"><?= htmlspecialchars($categoryErrorMessage); ?></div>
        <?php endif; ?>

        <form class="form-group add-product-container" method="post" action="" enctype="multipart/form-data">

            <a class="back-icon" href="admin-dash.php">
                <i class="fa fa-arrow-left" aria-hidden="true"></i>
            </a>

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
                <h2>Voeg categorie toe</h2>
                <label for="category_name">Categorie naam::</label>
                <input type="text" id="category_name" name="category_name" required>

                <button class="btn btn-admin" type="submit" name="add_category">Voeg categorie toe</button>
            </div>

        </form>

    </section>

    <script src="script/image-preview.js"></script>
</body>

</html>