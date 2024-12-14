<?php
include_once __DIR__ . '/classes/Db.php';
include_once __DIR__ . '/classes/Product.php';
include_once __DIR__ . '/classes/Category.php';
include_once __DIR__ . '/classes/ProductOption.php';
include_once __DIR__ . '/classes/Order.php';
include_once(__DIR__ . '/classes/Review.php');

// Start session
session_start();

// Check if the id parameter is present
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die('Product ID is not valid.');
}

// Retrieve the specific product
$productId = intval($_GET['id']);
$product = Product::getById($productId);

// Retrieve the category of the product
$category = Category::getById($product['category_id']);
$product['category_name'] = $category['name'];

// Retrieve the options for this product via the ProductOption class
$options = ProductOption::getByProductId($productId);
if (!$product) {
    die('Product niet gevonden.');
}

// Filter size options
$sizeOptions = array_filter($options, function ($option) {
    return $option['type'] == 'size';
});

// Filter pot options
$potOptions = array_filter($options, function ($option) {
    return $option['type'] == 'pot';
});

// Fetch reviews for the product
$reviews = Review::getByProductId($product['id']);
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
    <?php include 'classes/Nav.php'; ?>
    <section class="product">
        <h1><?php echo htmlspecialchars($product['name']); ?></h1>

        <div class="product-container">

            <div class="product-image">
                <img src="<?php echo htmlspecialchars($product['image']); ?>"
                    alt="<?php echo htmlspecialchars($product['name']); ?>">
            </div>
            <div class="product-details">
                <h2>Kies uw product</h2>

                <form id="add-to-basket-form" action="add-to-basket.php" method="POST">

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
                        <input type="number" id="quantity" name="quantity" value="1" min="1"
                            max="<?php echo $product['stock']; ?>">
                        <p id="quantity-error" class="alert-danger" style="display: none;">Opgelet, je kan niet meer dan
                            <?php echo htmlspecialchars($product['stock']); ?> bestellen, uitverkocht.
                        </p>
                    </div>
                    <div class="product-stock">
                        <?php if ($product['stock'] < 3): ?>
                            <p class="alert-danger">Snel, Het is bijna uitverkocht!</p>
                        <?php endif; ?>
                    </div>
                    <div class="product-price">
                        <p>Price: €<span id="finalPrice"><?php echo htmlspecialchars($product['price']); ?></span></p>
                        <button class="btn" type="submit" <?php echo $product['stock'] <= 0 ? 'disabled' : ''; ?>>Winkelmandje</button>
                        <?php if ($product['stock'] <= 0): ?>
                            <p class="alert-danger">Het product is uitverkocht</p>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
        </div>
    </section>
    <!-- Display reviews -->
    <section class="review-section">
        <h2>Reviews</h2>

        <div class="reviews">
            <?php if (empty($reviews)): ?>
                <h3 class="">Dit product heeft nog geen reviews.</h3>
            <?php else: ?>
                <?php foreach ($reviews as $review): ?>
                    <div class="review">
                        <div class="review-details">
                            <h3><?php echo htmlspecialchars($review['firstname'] . ' ' . $review['lastname']); ?>
                            </h3>
                            <p>
                                <?php echo str_repeat('<i class="fas fa-star"></i>', $review['rating']) . str_repeat('<i class="far fa-star"></i>', 5 - $review['rating']); ?>
                            </p>
                            <p><?php echo htmlspecialchars($review['comment']); ?></p>
                        </div>
                        <div class="date">
                            <p><?php echo htmlspecialchars(date('Y-m-d', strtotime($review['created_at']))); ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <?php if (Order::hasPurchasedProduct($_SESSION['user']['id'], $product['id'])): ?>
            <h3>Beoordeel dit product!</h3>
            <div class="create-review">

                <div class="create-review-content">
                    <div class="post-review-form">
                        <div class="star-rating  ">
                            <input type="radio" id="star5" name="rating" value="5"><label for="star5" title="5 stars"><i
                                    class="fas fa-star"></i></label>
                            <input type="radio" id="star4" name="rating" value="4"><label for="star4" title="4 stars"><i
                                    class="fas fa-star"></i></label>
                            <input type="radio" id="star3" name="rating" value="3"><label for="star3" title="3 stars"><i
                                    class="fas fa-star"></i></label>
                            <input type="radio" id="star2" name="rating" value="2"><label for="star2" title="2 stars"><i
                                    class="fas fa-star"></i></label>
                            <input type="radio" id="star1" name="rating" value="1"><label for="star1" title="1 star"><i
                                    class="fas fa-star"></i></label>
                        </div>
                        <div class="form-group">
                            <input type="text" placeholder="Wat denk je van dit product?" id="reviewText" required>
                            <a href="#" class="btn" id="btnAddReview" data-productid="<?php echo $product['id']; ?>"
                                data-userid="<?php echo $_SESSION['user']['id']; ?>">Plaats review</a>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </section>

    <section class="related-products">
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
                    <a href="product-page.php?id=<?php echo htmlspecialchars($relatedProduct['id']); ?>"
                        class="product-card">
                        <img src="<?php echo htmlspecialchars($relatedProduct['image']); ?>"
                            alt="<?php echo htmlspecialchars($relatedProduct['name']); ?>">
                        <h4><?php echo htmlspecialchars($relatedProduct['name']); ?></h4>
                        <p>€<?php echo htmlspecialchars(number_format($relatedProduct['price'], 2)); ?></p>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <script src="script/app.js">
        const sizeCheckboxes = document.querySelectorAll('.size-checkbox');
        const potCheckboxes = document.querySelectorAll('.pot-checkbox');
        const finalPrice = document.getElementById('finalPrice');
        const productPrice = parseFloat(finalPrice.innerText);
        const quantityInput = document.getElementById('quantity');
        const quantityError = document.getElementById('quantity-error');
        const addToBasketForm = document.getElementById('add-to-basket-form');

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
            const quantity = quantityInput.value;
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
        quantityInput.addEventListener('input', function () {
            const maxQuantity = parseInt(quantityInput.getAttribute('max'), 10);
            if (quantityInput.value > maxQuantity) {
                quantityError.style.display = 'block';
                quantityInput.value = maxQuantity;
            } else {
                quantityError.style.display = 'none';
            }
            calculatePrice();
        });

        // Prevent form submission if quantity exceeds stock
        addToBasketForm.addEventListener('submit', function (event) {
            const maxQuantity = parseInt(quantityInput.getAttribute('max'), 10);
            if (quantityInput.value > maxQuantity) {
                event.preventDefault();
                quantityError.style.display = 'block';
            }
        });
    </script>

</body>

</html>