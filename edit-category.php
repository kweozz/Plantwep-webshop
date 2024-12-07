<?php
session_start();
include_once(__DIR__ . '/classes/Db.php');
include_once(__DIR__ . '/classes/Category.php');
include_once(__DIR__ . '/classes/ImageUploader.php');

// Controleer of de gebruiker rechten heeft
if ($_SESSION['role'] !== 1) {
    echo '<h1 style="text-align: center; padding:10%; color:red; font-family:Helvetica;">' . htmlspecialchars('You do not have access to this page') . '</h1>';
    header("refresh:3;url=login.php");
    exit();
}

// Haal het specifieke categorie op
$categoryId = isset($_GET['id']) && is_numeric($_GET['id']) ? intval($_GET['id']) : null;
if (!$categoryId) {
    die('Category ID is not valid.');
}

$category = Category::getById($categoryId);
if (!$category) {
    die('Category not found.');
}

$message = '';

// Verwerk de bewerkte categorie
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = htmlspecialchars(trim($_POST['name']), ENT_QUOTES, 'UTF-8');
    $imagePath = $category['image']; // Huidige afbeelding behouden, tenzij er een nieuwe wordt geüpload

    try {
        // Controleer of er een nieuwe afbeelding is geüpload
        if (isset($_FILES['category_image']) && $_FILES['category_image']['error'] === UPLOAD_ERR_OK) {
            $imageUploader = new ImageUploader();
            $uploadResult = $imageUploader->uploadImage($_FILES['category_image']);
            if ($uploadResult) {
                $imagePath = $uploadResult;
                $category['image'] = $imagePath;
            } else {
                throw new Exception('Afbeelding uploaden mislukt.');
            }
        }

        // Update de categorie
        $updateMessage = Category::updateCategory($categoryId, $name, $imagePath);
        if (strpos($updateMessage, 'succesvol') !== false) {
            $message = '<div class="alert-success">' . $updateMessage . '</div>';
        } else {
            $message = '<div class="alert-danger">' . $updateMessage . '</div>';
        }
    } catch (Exception $e) {
        $message = '<div class="alert-danger">Fout: ' . htmlspecialchars($e->getMessage()) . '</div>';
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
    <title>Categorie Bewerken</title>
</head>

<body>
    <nav>
        <a href="index.php"><img class="logo" src="images/logo-plantwerp.png" alt="Plantwerp Logo"></a>
    </nav>

    <section class="product padding">
        <!-- Succes- of foutmeldingen -->
        <?= $message; ?>

        <form class="form-group add-product-container" method="post" action="" enctype="multipart/form-data">
            <a class="back-icon" href="manage-categories.php">
                <i class="fa fa-arrow-left" aria-hidden="true"></i>
            </a>

            <!-- Categorie afbeelding upload -->
            <div class="category-image">
                <label for="image" class="image-upload-label">
                    <img id="imagePreview" src="<?= htmlspecialchars($category['image']); ?>"
                        style="<?= $category['image'] ? 'display:block;' : 'display:none;'; ?>">
                    <span class="upload-icon">+</span>
                </label>
                <input type="file" id="image" name="category_image" accept="image/*"
                    onchange="previewImage(event, 'imagePreview')" style="display: none;">
            </div>

            <!-- Categorie details -->
            <div class="category-details">
                <h2>Categorie Bewerken</h2>

                <label for="name">Naam:</label>
                <input type="text" id="name" name="name" value="<?= htmlspecialchars($category['name']); ?>" required>

                <button class="btn btn-admin" type="submit">Opslaan</button>
            </div>
        </form>
    </section>

    <script>
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