<?php
// admin-dash.php
include_once __DIR__ . '/classes/Db.php';
include_once __DIR__ . '/classes/User.php';
include_once __DIR__ . '/classes/Category.php';
include_once __DIR__ . '/classes/Product.php';
include_once __DIR__ . '/classes/ImageUploader.php';

session_start();

if ($_SESSION['role'] !== 1) {
    echo '<h1 style="text-align: center; padding:10%; color:red; font-family:Helvetica;">' . htmlspecialchars('You do not have access to this page') . '</h1>';
    header("refresh:3;url=login.php");
    exit();
}

$categorySuccessMessage = '';
$categoryErrorMessage = '';
$productSuccessMessage = '';
$productErrorMessage = '';
$deleteErrorMessage = '';
$deleteSuccessMessage = '';

$category = new Category();
$categories = $category->getAll();
$product = new Product();
$products = $product->getAll();


/// Add Product
if (isset($_POST['add_product'])) {
    $productName = htmlspecialchars(trim($_POST['product_name']), ENT_QUOTES, 'UTF-8');
    $productPrice = htmlspecialchars(trim($_POST['product_price']), ENT_QUOTES, 'UTF-8');
    $productDescription = htmlspecialchars(trim($_POST['product_description']), ENT_QUOTES, 'UTF-8');
    $productStock = htmlspecialchars(trim($_POST['product_stock']), ENT_QUOTES, 'UTF-8');
    $categoryId = htmlspecialchars(trim($_POST['category']), ENT_QUOTES, 'UTF-8');

    if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] === 0) {
        try {
            $imageUploader = new ImageUploader();
            $uploadResult = $imageUploader->uploadImage($_FILES['product_image']);
            if ($uploadResult) {
                $product = new Product();
                $product->setName($productName);
                $product->setPrice($productPrice);
                $product->setDescription($productDescription);
                $product->setStock($productStock);
                $product->setCategory($categoryId);
                $product->setImage("images/uploads/{$uploadResult}");
                if ($product->create()) {
                    $productSuccessMessage = 'Product added successfully!';
                } else {
                    $productErrorMessage = 'Failed to add product.';
                }
            } else {
                $productErrorMessage = 'Image upload failed.';
            }
        } catch (Exception $e) {
            $productErrorMessage = 'Error: ' . $e->getMessage();
        }
    } else {
        $productErrorMessage = 'Please choose an image.';
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


    <section class="product padding">
        <h2>Add products</h2>

        <!-- Success or error messages -->
        <?php if (!empty($productSuccessMessage)): ?>
            <div class="alert-succes alert"><?= htmlspecialchars($productSuccessMessage); ?></div>
        <?php endif; ?>

        <?php if (!empty($productErrorMessage)): ?>
            <div class="alert-danger"><?= htmlspecialchars($productErrorMessage); ?></div>
        <?php endif; ?>


        <form class="form-group add-product-container" method="post" action="" enctype="multipart/form-data">

            <!-- File upload input (hidden) and preview section -->
            <div class="product-image">
                <label for="image" class="image-upload-label">
                    <img id="imagePreview" src="" style="display:none;"> <!-- Hide preview initially -->
                    <span class="upload-icon">+</span> <!-- Make sure this is visible -->
                </label>
                <input type="file" id="image" name="product_image" accept="image/*" required
                    onchange="previewImage(event, 'imagePreview')" style="display: none;">
            </div>

            <!-- Product Details Form -->
            <div class="product-details">
                <label for="product_name">Product Name:</label>
                <input type="text" id="product_name" name="product_name" required>

                <label for="product_price">Product Price:</label>
                <input type="number" id="product_price" name="product_price" required>

                <label for="product_description">Product Description:</label>
                <textarea id="product_description" name="product_description" required></textarea>

                <label for="category">Category:</label>
                <select name="category" id="category">
                    <?php if (!empty($categories)): ?>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?= htmlspecialchars($category['id']); ?>">
                                <?= htmlspecialchars($category['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <option value="">No categories available</option>
                    <?php endif; ?>
                </select>

                <label for="product_stock">Product Stock:</label>
                <input type="number" id="product_stock" name="product_stock" required>
                <button class="btn btn-admin" type="submit" name="add_product">Add Product</button>
            </div>
           
        </form>

    </section>
    </div>
    </div>
    <script>

        // Image previewer
        function previewImage(event, previewId) {
            // Maak een nieuwe FileReader aan, js object dat bestanden kan lezen
            const reader = new FileReader();
            //als het geladen is dan wordt de functie uitgevoerd
            reader.onload = function () {
                // Zoek het <img> element op de pagina met het meegegeven id (previewId)
                const preview = document.getElementById(previewId);
                // zet de src (bron) van img naar de gelezen data 
                preview.src = reader.result;
                // maakt de afbeelding zichtbaar
                preview.style.display = 'block';
            };
            // Lees het bestand als een data URL (base64 encoded image) --> voor mijzelf een data url is een url waarin de afbeelding is opgeslagen --> vragen aan joris
            reader.readAsDataURL(event.target.files[0]);
        }

    </script>
</body>