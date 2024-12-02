<?php
session_start();
include_once(__DIR__ . '/classes/Db.php');
include_once(__DIR__ . '/classes/Category.php');
include_once(__DIR__ . '/classes/Product.php');
include_once(__DIR__ . '/classes/ImageUploader.php');

// Controleer of de gebruiker rechten heeft
if ($_SESSION['role'] !== 1) {
    echo '<h1 style="text-align: center; padding:10%; color:red; font-family:Helvetica;">' . htmlspecialchars('You do not have access to this page') . '</h1>';
    header("refresh:3;url=login.php");
    exit();
}

// Haal het specifieke product op
$productId = isset($_GET['id']) && is_numeric($_GET['id']) ? intval($_GET['id']) : null;
if (!$productId) {
    die('Product ID is not valid.');
}

$product = Product::getById($productId);
if (!$product) {
    die('Product not found.');
}

// Haal categorieën op
$category = new Category();
$categories = $category->getAll();

$message = '';

// Verwerk het bewerkte product
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = htmlspecialchars(trim($_POST['name']), ENT_QUOTES, 'UTF-8');
    $description = htmlspecialchars(trim($_POST['description']), ENT_QUOTES, 'UTF-8');
    $price = htmlspecialchars(trim($_POST['price']), ENT_QUOTES, 'UTF-8');
    $stock = htmlspecialchars(trim($_POST['stock']), ENT_QUOTES, 'UTF-8');
    $categoryId = htmlspecialchars(trim($_POST['category']), ENT_QUOTES, 'UTF-8');
    $imagePath = $product['image']; // Standaard huidige afbeelding gebruiken

    try {
        // Controleer of er een nieuwe afbeelding is geüpload
        if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] === UPLOAD_ERR_OK) {
            $imageUploader = new ImageUploader();
            $uploadResult = $imageUploader->uploadImage($_FILES['product_image']);
            if ($uploadResult) {
                $imagePath = "images/uploads/{$uploadResult}";
            } else {
                throw new Exception('Afbeelding uploaden mislukt.');
            }
        }

        // Update het product
        $updateMessage = Product::updateProduct($productId, $name, $price, $description, $categoryId, $imagePath, $stock);
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
    <title>Product Bewerken</title>
</head>

<body>
    <nav>
        <a href="index.php"><img class="logo" src="images/logo-plantwerp.png" alt="Plantwerp Logo"></a>
    </nav>

    <section class="product padding">

        <!-- Succes- of foutmeldingen -->
        <?= $message; ?>

        <form class="form-group add-product-container" method="post" action="" enctype="multipart/form-data">
            <a class="back-icon" href="manage-products.php">
                <i class="fa fa-arrow-left" aria-hidden="true"></i>
            </a>
            <!-- Afbeelding upload -->
            <div class="product-image">
                <label for="image" class="image-upload-label">
                    <img id="imagePreview" src="<?= htmlspecialchars($product['image']); ?>"
                        style="<?= $product['image'] ? 'display:block;' : 'display:none;'; ?>">
                    <span class="upload-icon">+</span>
                </label>
                <input type="file" id="image" name="product_image" accept="image/*"
                    onchange="previewImage(event, 'imagePreview')" style="display: none;">
            </div>

            <!-- Productdetails -->
            <div class="product-details">
                <h2>Product Bewerken</h2>

                <label for="name">Naam:</label>
                <input type="text" id="name" name="name" value="<?= htmlspecialchars($product['name']); ?>" required>

                <label for="description">Beschrijving:</label>
                <textarea id="description" name="description"
                    required><?= htmlspecialchars($product['description']); ?></textarea>

                <label for="price">Prijs:</label>
                <input type="number" id="price" name="price" step="0.01"
                    value="<?= htmlspecialchars($product['price']); ?>" required>

                <label for="stock">Voorraad:</label>
                <input type="number" id="stock" name="stock" value="<?= htmlspecialchars($product['stock']); ?>"
                    required>

                <label for="category">Categorie:</label>
                <select id="category" name="category">
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= htmlspecialchars($cat['id']); ?>" <?= $cat['id'] == $product['category_id'] ? 'selected' : ''; ?>>
                            <?= htmlspecialchars($cat['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>

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