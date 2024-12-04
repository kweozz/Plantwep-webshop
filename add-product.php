<?php
session_start();
include_once(__DIR__ . '/classes/Db.php');
include_once(__DIR__ . '/classes/Product.php');
include_once(__DIR__ . '/classes/Category.php');
include_once(__DIR__ . '/classes/ImageUploader.php');
include_once(__DIR__ . '/classes/Option.php');

// Check if the user is an admin
if (!isset($_SESSION['role']) || (int)$_SESSION['role'] !== 1) {
    header("Location: login.php");
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $productName = htmlspecialchars(trim($_POST['product_name']), ENT_QUOTES, 'UTF-8');
    $productDescription = htmlspecialchars(trim($_POST['product_description']), ENT_QUOTES, 'UTF-8');
    $productPrice = (float) htmlspecialchars(trim($_POST['product_price']), ENT_QUOTES, 'UTF-8');
    $categoryId = (int) htmlspecialchars(trim($_POST['category']), ENT_QUOTES, 'UTF-8');

    // Get options and stock data
    $options = isset($_POST['options']) ? (array) $_POST['options'] : [];
    $stockData = isset($_POST['stock']) ? $_POST['stock'] : [];

    if (empty($productName)) {
        $productErrorMessage = 'Product name cannot be empty.';
    } elseif (isset($_FILES['product_image']) && $_FILES['product_image']['error'] === 0) {
        try {
            $imageUploader = new ImageUploader();
            $uploadResult = $imageUploader->uploadImage($_FILES['product_image']);

            if ($uploadResult) {
                // Instantiate the product object
                $product = new Product();
                $product->setName($productName);
                $product->setPrice($productPrice);
                $product->setDescription($productDescription);
                $product->setCategory($categoryId);
                $product->setImage($uploadResult);

                // Create the product and get the product ID
                $productId = $product->create($options);

                if ($productId) {
                    // Insert stock data for each size/pot combination
                    foreach ($stockData as $sizeId => $pots) {
                        foreach ($pots as $potId => $stockQuantity) {
                            if ($stockQuantity > 0) {
                                $query = "INSERT INTO product_stock (product_id, size_id, pot_type_id, stock_quantity)
                                          VALUES ($productId, $sizeId, $potId, $stockQuantity)";
                                $db->executeQuery($query); // Ensure this query is executed properly via your DB class
                            }
                        }
                    }

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

// Fetch categories and options
$categories = Category::getAll();
$options = Option::getAll();
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

    <!-- Success or error messages -->
    <?php if (!empty($productSuccessMessage)): ?>
        <div class="alert-success"><?= htmlspecialchars($productSuccessMessage); ?></div>
    <?php endif; ?>

    <?php if (!empty($productErrorMessage)): ?>
        <div class="alert-danger"><?= htmlspecialchars($productErrorMessage); ?></div>
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
            <input type="file" id="image" name="product_image" accept="image/*" required
                onchange="previewImage(event, 'imagePreview')" style="display: none;">
        </div>

        <!-- Product Details Form -->
        <div class="product-details">
            <h2>Add products</h2>
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

            <div class="options-group">
                <label>Beschikbare maten:</label>
                <label class="option-button">
                    <input type="checkbox" id="select-all-sizes">
                    <span>Select All Sizes</span>
                </label>

                <!-- Individuele maten -->
                <?php foreach ($options as $option): ?>
                    <?php if ($option['type'] == 'size'): ?>
                        <label class="option-button">
                            <input type="checkbox" class="size-checkbox" name="options[]" value="<?= $option['id']; ?>">
                            <span><?= htmlspecialchars($option['name']); ?></span>
                        </label>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>

            <div class="options-group">
                <label>Beschikbare potten:</label>
                <?php foreach ($options as $option): ?>
                    <?php if ($option['type'] == 'pot'): ?>
                        <label class="option-button">
                            <input type="checkbox" class="pot-checkbox" name="options[]" value="<?= $option['id']; ?>">
                            <span><?= htmlspecialchars($option['name']); ?></span>
                        </label>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>

            <div id="stock-options"></div> <!-- Dynamically stock options will be appended here -->

            <button class="btn btn-admin" type="submit" name="add_product">Add Product</button>
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

    // Select All Sizes functionality
    document.getElementById('select-all-sizes').addEventListener('change', function () {
        const sizeCheckboxes = document.querySelectorAll('.size-checkbox');
        sizeCheckboxes.forEach(checkbox => checkbox.checked = this.checked);
    });

    // Dynamically add stock input fields for each size/pot combination
    document.querySelector('form').addEventListener('submit', function(e) {
        var stockData = {};
        var selectedSizes = document.querySelectorAll('.size-checkbox:checked');
        var selectedPots = document.querySelectorAll('.pot-checkbox:checked');

        selectedSizes.forEach(function(size) {
            selectedPots.forEach(function(pot) {
                var stockInput = document.createElement('input');
                stockInput.type = 'number';
                stockInput.name = 'stock[' + size.value + '][' + pot.value + ']';
                stockInput.placeholder = 'Stock for size ' + size.nextElementSibling.textContent + ' and pot ' + pot.nextElementSibling.textContent;
                stockInput.required = true;
                document.getElementById('stock-options').appendChild(stockInput);
            });
        });
    });
</script>

</body>
</html>
