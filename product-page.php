<?php
include_once(__DIR__ . '/classes/Db.php');
include_once(__DIR__ . '/classes/Product.php');

// Start session
session_start();

// Controleer of de id-parameter aanwezig is
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die('Product ID is not valid.');
}

// Haal het specifieke product op
$productId = intval($_GET['id']);
$product = Product::getById($productId);

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
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <nav>
        <a href="index.html"><img class="logo" src="images/logo-plantwerp.png" alt="Plantwerp Logo"></a>
        <input type="text" placeholder="Search for plants..." class="search-bar">
        <div class="nav-items">
            <a href="#" class="icon profile-icon" aria-label="Profile">
                <i class="fas fa-user"></i>
            </a>
            <a href="#" class="icon basket-icon" aria-label="Basket">
                <i class="fas fa-shopping-basket"></i>
            </a>
        </div>
    </nav>

    <div class="product-container">
        <div class="product-image">
            <img src="<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
        </div>
        <div class="product-details">
            <h1><?php echo htmlspecialchars($product['name']); ?></h1>
            <p class="product-price">€<?php echo htmlspecialchars(number_format($product['price'], 2)); ?></p>
            <p class="product-description"><?php echo htmlspecialchars($product['description']); ?></p>

            <div class="product-options">
                <div class="option">
                    <label for="quantity">Quantity:</label>
                    <input type="number" id="quantity" name="quantity" min="1" max="5" value="1">
                </div>
                <div class="option">
                    <label for="size">Size:</label>
                    <select name="size" id="size">
                        <option value="small">Small</option>
                        <option value="medium">Medium (+ 5,00)</option>
                        <option value="large">Large (+ 10,00)</option>
                    </select>
                </div>

                <div class="option">
                    <label for="pot">With or without pot:</label>
                    <select name="pot" id="pot">
                        <option value="with">With pot (+ 5,00)</option>
                        <option value="without">Without pot</option>
                    </select>
                </div>
                <a class="btn" href="#">Add to Cart</a>
            </div>
        </div>
    </div>

    <h2>You might also like</h2>
    <div class="related-products">
        <div class="products">
            <?php 
            // Fetch all products except the current one
            $products = Product::getAll();
            foreach ($products as $relatedProduct): 
                if ($relatedProduct['id'] == $product['id']) continue;
            ?>
                <a href="product-page.php?id=<?php echo $relatedProduct['id']; ?>" class="product-card">
                    <img src="<?php echo htmlspecialchars($relatedProduct['image']); ?>" alt="<?php echo htmlspecialchars($relatedProduct['name']); ?>">
                    <p><?php echo htmlspecialchars($relatedProduct['name']); ?></p>
                    <p>€<?php echo htmlspecialchars(number_format($relatedProduct['price'], 2)); ?></p>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</body>

</html>
