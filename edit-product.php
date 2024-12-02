<?php
session_start();
include_once(__DIR__ . '/classes/Db.php');
include_once(__DIR__ . '/classes/Category.php');
include_once(__DIR__ . '/classes/Product.php');
include_once(__DIR__ . '/classes/ImageUploader.php');

if ($_SESSION['role'] !== 1) {
    header("refresh:3;url=login.php");
    echo '<h1 style="text-align: center; padding:10%; color:red; font-family:Helvetica;">' . htmlspecialchars('You do not have access to this page') . '</h1>';
    exit();
}



if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die('Product ID is not valid.');
}


// Haal het specifieke product op
$productId = intval($_GET['id']);
$product = Product::getById($productId);

//haal de categorie op van het product
$category = Category::getById($product['category_id']);
$product['category_name'] = $category['name'];

if (!$product) {
    die('Product not found.');
}

// Verwerk het bewerkte product
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = htmlspecialchars(trim($_POST['name']), ENT_QUOTES, 'UTF-8');
    $description = htmlspecialchars(trim($_POST['description']), ENT_QUOTES, 'UTF-8');
    $price = htmlspecialchars(trim($_POST['price']), ENT_QUOTES, 'UTF-8');
    $stock = htmlspecialchars(trim($_POST['stock']), ENT_QUOTES, 'UTF-8');
    $message = '';

    try {
        // Controleer of er een nieuwe afbeelding is geüpload
        if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] === UPLOAD_ERR_OK) {
            $imageUploader = new ImageUploader();
            $uploadResult = $imageUploader->uploadImage($_FILES['product_image']);

            if ($uploadResult['success']) {
                $product['image'] = $uploadResult['file_path']; // Wijzig de image in de array
            } else {
                throw new Exception($uploadResult['error']);
            }
        }

        // Werk productdetails bij
        $product['name'] = $name;
        $product['description'] = $description;
        $product['price'] = $price;
        $product['stock'] = $stock;

        // Bijwerken in de database
        $updateResult = Product::update($product); // Zorg ervoor dat de update-methode correct is aangepast
        if ($updateResult) {
            $message = 'Product succesvol bijgewerkt!';
            header("Location: edit-product.php?product_id=" . $productId . "&message=" . urlencode($message));
            exit();
        } else {
            $message = 'Product bijwerken mislukt.';
        }
    } catch (Exception $e) {
        $message = 'Fout: ' . $e->getMessage();
    }

    echo '<div class="message">' . htmlspecialchars($message) . '</div>';
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="style.css">
    <title>Edit Product</title>
</head>

<body>
    <nav>
        <!-- Navigation links -->
    </nav>
    
    <h3>Product Bewerken</h3>
    <a class="back-icon" href="delete-product.php">
        <i class="fa fa-arrow-left" aria-hidden="true"></i>
    </a>

    <?php if (isset($message)): ?>
        <div class="message"><?= htmlspecialchars($message); ?></div>
    <?php endif; ?>

    <form class="form-group add-product-container" action="" method="POST" enctype="multipart/form-data">
    <div class="product-image">
        <label for="image" class="image-upload-label">
            <img id="imagePreview" src="<?= htmlspecialchars($product['image']); ?>" 
                style="<?= $product['image'] ? 'display:block;' : 'display:none;'; ?>">
            <span class="upload-icon">+</span>
        </label>
        <input type="file" id="image" name="product_image" accept="image/*" 
            onchange="previewImage(event, 'imagePreview')" style="display: none;">
    </div>

    <div class="product-details">
        <label for="name">Naam:</label>
        <input type="text" name="name" value="<?= htmlspecialchars($product['name']); ?>" required><br>

        <label for="description">Beschrijving:</label>
        <textarea name="description" required><?= htmlspecialchars($product['description']); ?></textarea><br>

        <label for="price">Prijs:</label>
        <input type="number" step="0.01" name="price" value="<?= htmlspecialchars($product['price']); ?>" required><br>

        <label for="stock">Voorraad:</label>
        <input type="number" name="stock" value="<?= htmlspecialchars($product['stock']); ?>" required><br>

        <button class="btn btn-admin" type="submit">Aanpassingen opslaan</button>
    </div>
</form>


    <script>
        // Preview uploaded image
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
