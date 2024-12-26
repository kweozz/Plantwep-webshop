<?php
session_start();
include_once(__DIR__ . '/classes/Db.php');
include_once(__DIR__ . '/classes/Product.php');
include_once(__DIR__ . '/classes/Category.php');
include_once(__DIR__ . '/classes/ImageUploader.php');
include_once(__DIR__ . '/classes/ProductOption.php');
include_once(__DIR__ . '/classes/Option.php');

// Check if the user is an admin
if (!isset($_SESSION['role']) || (int) $_SESSION['role'] !== 1) {
    header("Location: login.php");
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $productName = htmlspecialchars(trim($_POST['product_name']), ENT_QUOTES, 'UTF-8');
    $productDescription = htmlspecialchars(trim($_POST['product_description']), ENT_QUOTES, 'UTF-8');
    $productPrice = (float) htmlspecialchars(trim($_POST['product_price']), ENT_QUOTES, 'UTF-8');
    $categoryId = (int) htmlspecialchars(trim($_POST['category']), ENT_QUOTES, 'UTF-8');
    $productStock = (int) htmlspecialchars(trim($_POST['product_stock']), ENT_QUOTES, 'UTF-8');

    if (empty($productName)) {
        $productErrorMessage = 'Product name cannot be empty.';
    } elseif (isset($_FILES['product_image']) && $_FILES['product_image']['error'] === 0) {
        try {
            $imageUploader = new ImageUploader();
            $uploadResult = $imageUploader->uploadImage($_FILES['product_image']);

            if ($uploadResult) {
                // Instantiate the product object here
                $product = new Product();

                // Now set the product properties
                $product->setName($productName);
                $product->setPrice($productPrice);
                $product->setDescription($productDescription);
                $product->setStock($productStock);
                $product->setCategory($categoryId);
                $product->setImage($uploadResult);

                // Create the product and assign options with price additions
                $productId = $product->create();

                if ($productId) {
                    // Loop through the selected options and save them with price addition
                    if (!empty($_POST['options'])) {
                        foreach ($_POST['options'] as $optionData) {
                            $optionId = $optionData['id'] ?? null;
                            $priceAddition = isset($optionData['price_addition']) ? (float) $optionData['price_addition'] : 0.0;

                            if ($optionId) {
                                ProductOption::save($productId, $optionId, $priceAddition);
                            }
                        }
                    }
                    $productSuccessMessage = 'Product succesvol toegevoegd!';
                } else {
                    $productErrorMessage = 'Product kon niet worden toegevoegd.';
                }
            } else {
                $productErrorMessage = 'Afbeelding uploaden mislukt.';
            }
        } catch (Exception $e) {
            $productErrorMessage = 'Error: ' . $e->getMessage();
        }
    } else {
        $productErrorMessage = 'Kies aub een afbeelding.';
    }
}

// Fetch categories
$categories = Category::getAll();

// Fetch options
$options = Option::getAll();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
    <title>Product toevoegen</title>
</head>

<body>
    <section class="product padding">
        <!-- Success or error messages -->
        <?php if (!empty($productSuccessMessage)): ?>
            <div class="alert-success"><?= htmlspecialchars($productSuccessMessage); ?></div>
        <?php endif; ?>

        <?php if (!empty($productErrorMessage)): ?>
            <div class="alert-danger"><?= htmlspecialchars($productErrorMessage); ?></div>
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
                <input type="file" id="image" name="product_image" accept="image/*" required
                    onchange="previewImage(event, 'imagePreview')" style="display: none;">
            </div>

            <!-- Product Details Form -->
            <div class="product-details">
                <h2>Maak nieuw product aan</h2>
                <div class="options-group">
                    <label for="product_name">Product naam</label>
                    <input type="text" id="product_name" name="product_name" required>

                    <label for="product_price">Product prijs:</label>
                    <input type="number" id="product_price" name="product_price" step="0.01" required>

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
                            <option value="">Geen categorieen beschikbaar</option>
                        <?php endif; ?>
                    </select>
                </div>
                <div class="options-group">
                    <label>Beschikbare maten:</label>
                    <label class="option-button">
                        <input type="checkbox" id="select-all-sizes">
                        <span>Selecteer alle maten</span>
                    </label>

                    <?php foreach ($options as $option): ?>
                        <?php if ($option['type'] == 'size'): ?>
                            <label class="option-button">
                                <input type="checkbox" class="size-checkbox" name="options[<?= $option['id']; ?>][id]"
                                    value="<?= $option['id']; ?>"
                                    data-price-addition-input="price_addition_<?= $option['id']; ?>">
                                <span><?= htmlspecialchars($option['name']); ?></span>
                            </label>
                            <div class="price-addition" id="price_addition_<?= $option['id']; ?>" style="display:none;">
                                <label for="price_addition_<?= $option['id']; ?>">Prijs toevoeging:</label>
                                <input type="number" name="options[<?= $option['id']; ?>][price_addition]" step="0.01" min="0"
                                    placeholder="Price addition">
                            </div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>

                <div class="options-group">
                    <label>Beschikbare potten:</label>
                    <?php foreach ($options as $option): ?>
                        <?php if ($option['type'] == 'pot'): ?>
                            <label class="option-button">
                                <input type="checkbox" class="pot-checkbox" name="options[<?= $option['id']; ?>][id]"
                                    value="<?= $option['id']; ?>"
                                    data-price-addition-input="price_addition_<?= $option['id']; ?>">
                                <span><?= htmlspecialchars($option['name']); ?></span>
                            </label>
                            <div class="price-addition" id="price_addition_<?= $option['id']; ?>" style="display:none;">
                                <label for="price_addition_<?= $option['id']; ?>">Prijs toevoeging:</label>
                                <input type="number" name="options[<?= $option['id']; ?>][price_addition]" step="0.01"
                                    min="0" placeholder="Price addition">
                            </div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>

                <label for="product_stock">Product Voorraad:</label>
                <input type="number" id="product_stock" name="product_stock" required>
                <button class="btn btn-admin" type="submit" name="add_product">Voeg product toe</button>
            </div>
        </form>
    </section>

    <script src="script/add-product.js"></script>
</body>

</html>
