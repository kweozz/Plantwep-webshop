<?php
include_once __DIR__ . '/classes/Db.php';
include_once __DIR__ . '/classes/Product.php';
include_once __DIR__ . '/classes/Category.php';
include_once __DIR__ . '/classes/ProductOption.php';

// Start session
session_start();

// Controleer of de id-parameter aanwezig is
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die('Product ID is not valid.');
}

// Haal het specifieke product op
$productId = intval($_GET['id']);
$product = Product::getById($productId);

// Haal de categorie op van het product
$category = Category::getById($product['category_id']);
$product['category_name'] = $category['name'];

// Haal de opties voor dit product op via de ProductOption klasse
$options = ProductOption::getByProductId($productId);
if (!$product) {
    die('Product not found.');
}


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($product['name']); ?> - Product Details</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <nav>
        <a href="index.php"><img class="logo" src="images/logo-plantwerp.png" alt="Plantwerp Logo"></a>
        <input type="text" placeholder="Zoek naar planten..." class="search-bar">
        <div class="nav-items">
            <a href="profile.php" class="icon profile-icon" aria-label="Profiel">
                <i class="fas fa-user"></i>
            </a>
            <a href="#" class="icon basket-icon" aria-label="Winkelmand">
                <i class="fas fa-shopping-basket"></i>
            </a>
            <?php if (isset($_SESSION['role']) && $_SESSION['role'] == 1): ?>
                <a href="admin-dash.php" class="icon admin-icon" aria-label="Admin Dashboard">
                    <i class="fas fa-tools"></i>
                </a>
            <?php endif; ?>
            <?php if (isset($_SESSION['user']['currency'])): ?>
                <span class="currency-display">
                    <i class="fas fa-coins"></i>
                    <?php echo htmlspecialchars($_SESSION['user']['currency']); ?>
                </span>
            <?php endif; ?>
        </div>
    </nav>

    <div class="product-container">
        <div class="product-image">
            <img src="<?php echo htmlspecialchars($product['image']); ?>"
                alt="<?php echo htmlspecialchars($product['name']); ?>">
        </div>
        <div class="product-details">
            <h1><?php echo htmlspecialchars($product['name']); ?></h1>
            <p class="product-price">€<?php echo htmlspecialchars(number_format($product['price'], 2)); ?></p>
            <p class="product-description"><?php echo htmlspecialchars($product['description']); ?></p>

            <div class="options-group">
                <label>Beschikbare maten:</label>
                <!-- Select All Sizes -->
                <label class="option-button">
                    <input type="checkbox" id="select-all-sizes">
                    <span>Select All Sizes</span>
                </label>

                <!-- Displaying available sizes -->
                <?php foreach ($options as $option): ?>
                    <?php if ($option['type'] == 'size'): ?>
                        <label class="option-button">
                            <input type="checkbox" class="size-checkbox" name="options[]"
                                value="<?= htmlspecialchars($option['id']); ?>">
                            <span><?= htmlspecialchars($option['name']); ?></span>
                        </label>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>

            <div class="options-group">
                <label>Beschikbare potten:</label>
                <!-- Displaying available pots -->
                <?php foreach ($options as $option): ?>
                    <?php if ($option['type'] == 'pot'): ?>
                        <label class="option-button">
                            <input type="checkbox" class="pot-checkbox" name="options[]"
                                value="<?= htmlspecialchars($option['id']); ?>">
                            <span><?= htmlspecialchars($option['name']); ?></span>
                        </label>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>

            <div class="form-group">
                <label for="quantity">Aantal:</label>
                <input type="number" id="quantity" name="quantity" value="1" min="1">
            </div>
        </div>
    </div>



    <div class="product-price">
        <p>Price: €<span id="finalPrice"><?php echo htmlspecialchars($product['price']); ?></span></p>
    <form action="cart.php" method="POST">
        <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
        <input type="hidden" name="product_price" value="<?php echo $product['price']; ?>">
        <button class="btn" type="submit">Add to Cart</button>
    </form>
    </div>
    </div>

    <script>
        // JavaScript to update price dynamically based on selected options and quantity
        const sizeCheckboxes = document.querySelectorAll('.size-checkbox');
        const potCheckboxes = document.querySelectorAll('.pot-checkbox');
        const quantityInput = document.getElementById('quantity');
        const finalPrice = document.getElementById('finalPrice');

        // Calculate the total price based on selected options and quantity
        function calculatePrice() {
            let price = <?php echo $product['price']; ?>;
            let quantity = quantityInput.value;

            sizeCheckboxes.forEach(checkbox => {
                if (checkbox.checked) {
                    price += parseFloat(checkbox.value);
                }
            });

            potCheckboxes.forEach(checkbox => {
                if (checkbox.checked) {
                    price += parseFloat(checkbox.value);
                }
            });

            finalPrice.textContent = (price * quantity).toFixed(2);
        }

        // Event listeners for checkboxes and quantity input
        sizeCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', calculatePrice);
        });

        potCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', calculatePrice);
        });

        quantityInput.addEventListener('input', calculatePrice);

        // Select All Sizes functionality
        document.getElementById('select-all-sizes').addEventListener('change', function () {
            sizeCheckboxes.forEach(checkbox => checkbox.checked = this.checked);
            calculatePrice();
        });
        

        

    </script>

    <h2>Meer van de categorie <span
            style="color:green;"><?php echo htmlspecialchars($product['category_name']); ?></span></h2>
    <div class="related-products">
        <div class="products">
            <?php
            // Fetch products from the same category, excluding the current product
            $relatedProducts = Product::getByCategory($product['category_id']);
            foreach ($relatedProducts as $relatedProduct):
                if ($relatedProduct['id'] == $product['id'])
                    continue;
                ?>
                <a href="product-page.php?id=<?php echo $relatedProduct['id']; ?>" class="product-card">
                    <img src="<?php echo htmlspecialchars($relatedProduct['image']); ?>"
                        alt="<?php echo htmlspecialchars($relatedProduct['name']); ?>">
                    <p><?php echo htmlspecialchars($relatedProduct['name']); ?></p>
                    <p>€<?php echo htmlspecialchars(number_format($relatedProduct['price'], 2)); ?></p>
                </a>
            <?php endforeach; ?>
        </div>
    </div>

</body>

</html>