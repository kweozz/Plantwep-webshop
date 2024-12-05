<?php
include_once(__DIR__ . '/classes/Db.php');
include_once(__DIR__ . '/classes/Product.php');
include_once(__DIR__ . '/classes/Category.php');
include_once(__DIR__ . '/classes/ProductOption.php');

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
var_dump($options);
die();
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
        <img src="<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
    </div>
    <div class="product-details">
        <h1><?php echo htmlspecialchars($product['name']); ?></h1>
        <p class="product-price">€<?php echo htmlspecialchars(number_format($product['price'], 2)); ?></p>
        <p class="product-description"><?php echo htmlspecialchars($product['description']); ?></p>

        <?php if (!empty($options)): ?>
            <div class="product-options form-group">
                <div class="option">
                    <label for="quantity">Quantity:</label>
                    <input type="number" id="quantity" name="quantity" min="1" max="5" value="1">
                </div>

                <?php foreach ($options as $option): ?>
                    <div class="option">
                        <label for="<?php echo $option->getType(); ?>"><?php echo htmlspecialchars($option->getType()); ?>:</label>
                        <select name="<?php echo $option->getName(); ?>" id="<?php echo $option->getName(); ?>" class="option-select">
                            <option value="default">Select <?php echo htmlspecialchars($option->getName()); ?></option>

                            <?php
                            $availableValues = $option->getAvailableValues();
                            foreach ($availableValues as $value):
                                $extraCost = 0;
                                if ($option->getType() == 'size') {
                                    if ($value == 'medium') $extraCost = 5;
                                    if ($value == 'large') $extraCost = 10;
                                }
                                if ($option->getType() == 'pot' && $value == 'with') {
                                    $extraCost = 5;
                                }
                            ?>
                                <option value="<?php echo $value; ?>" data-extra-cost="<?php echo $extraCost; ?>">
                                    <?php echo ucfirst($value); ?> 
                                    <?php echo ($extraCost > 0) ? "(+ €$extraCost)" : ''; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <h3 class="final-price">Final Price: €<span id="finalPrice"><?php echo htmlspecialchars(number_format($product['price'], 2)); ?></span></h3>
        <form action="cart.php" method="POST">
            <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
            <input type="hidden" name="product_price" value="<?php echo $product['price']; ?>">
            <button class="btn" type="submit">Add to Cart</button>
        </form>
    </div>
</div>

<script>
    // JavaScript to update price dynamically
    document.querySelectorAll('.option-select, #quantity').forEach(function (select) {
        select.addEventListener('change', updatePrice);
    });

    function updatePrice() {
        let basePrice = <?php echo $product['price']; ?>;
        let sizePrice = 0;
        let potPrice = 0;

        document.querySelectorAll('.option-select').forEach(function (select) {
            const selectedOption = select.options[select.selectedIndex];
            const extraCost = parseFloat(selectedOption.getAttribute('data-extra-cost')) || 0;
            if (select.id === 'size') {
                sizePrice = extraCost;
            } else if (select.id === 'pot') {
                potPrice = extraCost;
            }
        });

        const quantity = document.getElementById('quantity').value;
        const finalPrice = (basePrice + sizePrice + potPrice) * quantity;
        document.getElementById('finalPrice').textContent = finalPrice.toFixed(2);
    }
</script>

<h2>Meer van de categorie <span style="color:green;"><?php echo htmlspecialchars($product['category_name']); ?></span></h2>
<div class="related-products">
    <div class="products">
        <?php
        // Fetch products from the same category, excluding the current product
        $relatedProducts = Product::getByCategory($product['category_id']);
        foreach ($relatedProducts as $relatedProduct):
            if ($relatedProduct['id'] == $product['id']) continue;
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
