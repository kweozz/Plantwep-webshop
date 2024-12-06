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
                $product = new Product(); // Create the product object

                // Now set the product properties
                $product->setName($productName);
                $product->setPrice($productPrice);
                $product->setDescription($productDescription);
                $product->setStock($productStock);
                $product->setCategory($categoryId);

                // Set the image only after the product has been created
                $product->setImage($uploadResult);

                // Create the product and assign options with price additions
                $productId = $product->create(); // Create the product first

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
                <div class="options-group">
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
                </div>
                <div class="options-group">
                    <label>Beschikbare maten:</label>
                    <!-- Select All Sizes -->
                    <label class="option-button">
                        <input type="checkbox" id="select-all-sizes">
                        <span>Select All Sizes</span>
                    </label>

                    <!-- Individuele maten -->
                    <?php foreach ($options as $option): ?>
                        <?php if ($option['type'] == 'size'): ?>

                            <label class="option-button">
                                <input type="checkbox" class="size-checkbox" name="options[<?= $option['id']; ?>][id]"
                                    value="<?= $option['id']; ?>"
                                    data-price-addition-input="price_addition_<?= $option['id']; ?>">
                                <span><?= htmlspecialchars($option['name']); ?></span>
                            </label>
                            <div class="price-addition" id="price_addition_<?= $option['id']; ?>" style="display:none;">
                                <label for="price_addition_<?= $option['id']; ?>">Price Addition:</label>
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
                                    <label for="price_addition_<?= $option['id']; ?>">Price Addition:</label>
                                    <input type="number" name="options[<?= $option['id']; ?>][price_addition]" step="0.01"
                                        min="0" placeholder="Price addition">
                                </div>
                           
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
                <label for="product_stock">Product Stock:</label>
                <input type="number" id="product_stock" name="product_stock" required>
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

        // Before form submission, ensure that options are always an array
        document.querySelector('form').addEventListener('submit', function (e) {
            var options = document.querySelectorAll('input[name="options[]"]:checked');
            var optionsArray = [];

            options.forEach(function (option) {
                optionsArray.push(option.value);
            });

            // Ensure that optionsArray is always passed even if it's empty
            if (optionsArray.length === 0) {
                var hiddenInput = document.createElement('input');
                hiddenInput.type = 'hidden';
                hiddenInput.name = 'options[]';
                hiddenInput.value = '';
                this.appendChild(hiddenInput);
            }
        });
        // Function to handle checkbox changes
        document.querySelectorAll('.option-button input[type="checkbox"]').forEach(function (checkbox) {
            checkbox.addEventListener('change', function () {
                var priceAdditionInputId = this.getAttribute('data-price-addition-input');
                var priceAdditionDiv = document.getElementById(priceAdditionInputId);

                // Show or hide the price addition input depending on checkbox state
                if (this.checked) {
                    priceAdditionDiv.style.display = 'block';
                } else {
                    priceAdditionDiv.style.display = 'none';
                }
            });
        });

        // Select All Sizes functionality
        document.getElementById('select-all-sizes').addEventListener('change', function () {
            const sizeCheckboxes = document.querySelectorAll('.size-checkbox');
            sizeCheckboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
                var priceAdditionInputId = checkbox.getAttribute('data-price-addition-input');
                var priceAdditionDiv = document.getElementById(priceAdditionInputId);

                // Show or hide the price addition input based on the "Select All" checkbox
                priceAdditionDiv.style.display = this.checked ? 'block' : 'none';
            });
        });
    </script>
</body>

</html>