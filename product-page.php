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
  </html>  <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($product['name']); ?> - Product Details</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
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
                <option value="medium">Medium (+ €5,00)</option>
                <option value="large">Large (+ €10,00)</option>
            </select>
        </div>

        <div class="option">
            <label for="pot">With or without pot:</label>
            <select name="pot" id="pot">
                <option value="with">With pot (+ €5,00)</option>
                <option value="without">Without pot</option>
            </select>
        </div>
    </div>

    <!-- Calculate final price based on options -->
    <h3 class="final-price">Final Price: €<span id="finalPrice"><?php echo htmlspecialchars(number_format($product['price'], 2)); ?></span></>
    
    <button class="btn" type="submit">Add to Cart</button>
</div>

<script>
    // JavaScript om de prjs te berekenen op basis van de geselecteerde opties
    //de 2 argumenten zijn de id's van de select elementen
    document.querySelectorAll('select,#quantity').forEach(function (select) {
        select.addEventListener('change', updatePrice);
    });
//
    function updatePrice() {
        let basePrice = <?php echo $product['price']; ?>;
        let sizePrice = 0;
        let potPrice = 0;

      // op basis van de prijs van de plant en de geselecteerde opties de prijs berekenen
        const size = document.getElementById('size').value;
        if (size === 'medium') {
            sizePrice = 5;
        } else if (size === 'large') {
            sizePrice = 10;
        }

        // Pot pricing
        const pot = document.getElementById('pot').value;
        if (pot === 'with') {
            potPrice = 5;
        }
        // if Quantity rises above 1, the price will be multiplied by the quantity
        const quantity = document.getElementById('quantity').value;
        const finalPrice = (basePrice + sizePrice + potPrice) * quantity;
        document.getElementById('finalPrice').textContent = finalPrice.toFixed(2);
    }

 
</script>

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
