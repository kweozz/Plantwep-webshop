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

// Filter size options
$sizeOptions = array_filter($options, function ($option) {
    return $option['type'] == 'size';
});

// Filter pot options
$potOptions = array_filter($options, function ($option) {
    return $option['type'] == 'pot';
});

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
    <section>
        <h1><?php echo htmlspecialchars($product['name']); ?></h1>

        <div class="product-container">

            <div class="product-image">
                <img src="<?php echo htmlspecialchars($product['image']); ?>"
                    alt="<?php echo htmlspecialchars($product['name']); ?>">
            </div>
            <div class="product-details">
                <h2>Kies uw product</h2>

                <form action="add-to-basket.php" method="POST">

                    <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                    <input type="hidden" name="product_price" value="<?php echo $product['price']; ?>">
                    <input type="hidden" name="final_quantity" id="finalQuantityInput" value="1">
                    <?php if (count($sizeOptions) > 1): ?>
                        <div class="options-group form-group">
                            <label>Beschikbare maten:</label>
                            <?php foreach ($sizeOptions as $option): ?>
                                <label class="option-button">
                                    <input type="checkbox" class="size-checkbox"
                                        name="options[<?php echo $option['id']; ?>][id]"
                                        value="<?= htmlspecialchars($option['id']); ?>"
                                        data-price="<?= htmlspecialchars($option['price_addition']); ?>">
                                    <span><?= htmlspecialchars($option['name']); ?></span>
                                </label>
                                <input type="hidden" name="options[<?php echo $option['id']; ?>][price_addition]"
                                    value="<?= htmlspecialchars($option['price_addition']); ?>">
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                    <div class="options-group form-group">
                        <label>Beschikbare potten:</label>
                        <?php foreach ($potOptions as $option): ?>
                            <label class="option-button">
                                <input type="checkbox" class="pot-checkbox" name="options[<?php echo $option['id']; ?>][id]"
                                    value="<?= htmlspecialchars($option['id']); ?>"
                                    data-price="<?= htmlspecialchars($option['price_addition']); ?>">
                                <span><?= htmlspecialchars($option['name']); ?></span>
                            </label>
                            <input type="hidden" name="options[<?php echo $option['id']; ?>][price_addition]"
                                value="<?= htmlspecialchars($option['price_addition']); ?>">
                        <?php endforeach; ?>
                    </div>

                    <div class="form-group">
                        <label for="quantity">Aantal:</label>
                        <input type="number" id="quantity" name="quantity" value="1" min="1">
                    </div>
                    <div class="product-price">
                        <h3>Price: €<span id="finalPrice"><?php echo htmlspecialchars($product['price']); ?></span></h3>
                        <button class="btn" type="submit">Add to Cart</button>
                    </div>
                </form>
            </div>
        </div>
        <h2>Meer van de categorie <span
                style="color:green;"><?php echo htmlspecialchars($product['category_name']); ?></span></h2>
        <div class="related-products">
            <div class="products">
                <?php
                // Fetch products from the same category, excluding the current product
                $relatedProducts = Product::getByCategory($product['category_id']);
                $count = 0;
                foreach ($relatedProducts as $relatedProduct):
                    if ($relatedProduct['id'] == $product['id'] || $count >= 3)
                        continue;
                    $count++;
                    ?>
                    <div class="product-card">
                        <img src="<?php echo htmlspecialchars($relatedProduct['image']); ?>"
                            alt="<?php echo htmlspecialchars($relatedProduct['name']); ?>">
                        <h4><?php echo htmlspecialchars($relatedProduct['name']); ?></h4>
                        <p>€<?php echo htmlspecialchars(number_format($relatedProduct['price'], 2)); ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <script>
        const sizeCheckboxes = document.querySelectorAll('.size-checkbox');
        const potCheckboxes = document.querySelectorAll('.pot-checkbox');
        const finalPrice = document.getElementById('finalPrice');
        const productPrice = parseFloat(finalPrice.innerText);

        function calculatePrice() {
            let price = productPrice;

            sizeCheckboxes.forEach(checkbox => {
                if (checkbox.checked) {
                    price += parseFloat(checkbox.dataset.price || 0);
                }
            });

            potCheckboxes.forEach(checkbox => {
                if (checkbox.checked) {
                    price += parseFloat(checkbox.dataset.price || 0);
                }
            });

            // Add the quantity to the price
            const quantity = document.getElementById('quantity').value;
            price *= quantity;

            // Ensure the price does not go below the base price
            if (price < productPrice) {
                price = productPrice;
            }

            finalPrice.innerText = price.toFixed(2);
        }

        // Add event listeners to checkboxes
        sizeCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function () {
                sizeCheckboxes.forEach(cb => cb.checked = false);
                this.checked = true;
                calculatePrice();
            });
        });

        potCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function () {
                potCheckboxes.forEach(cb => cb.checked = false);
                this.checked = true;
                calculatePrice();
            });
        });

        // Add event listener to quantity input
        document.getElementById('quantity').addEventListener('input', calculatePrice);
    </script>
</body>

</html>