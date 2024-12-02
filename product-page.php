<?php
include_once(__DIR__ . '/classes/Db.php');
include_once(__DIR__ . '/classes/Product.php');
include_once(__DIR__ . '/classes/Category.php');

// Start session
session_start();

// Controleer of de id-parameter aanwezig is
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die('Product ID is not valid.');
}

// Haal het specifieke product op
$productId = intval($_GET['id']);
$product = Product::getById($productId);

//haal de categorie op van het product
$category = Category::getById($product['category_id']);
$product['category_name'] = $category['name'];

if (!$product) {
    die('Product not found.');
}
?>
<!DOCTYPE html>
<html lang="en">


<head>
    <meta charset="UTF-8">

</html>
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
            <!-- Profiel -->
            <a href="profile.php" class="icon profile-icon" aria-label="Profiel">
                <i class="fas fa-user"></i>
            </a>
            <!-- Winkelmand -->
            <a href="#" class="icon basket-icon" aria-label="Winkelmand">
                <i class="fas fa-shopping-basket"></i>
            </a>
            <!-- Admin Dashboard (zichtbaar alleen voor admins) -->
            <?php if (isset($_SESSION['role']) && $_SESSION['role'] == 1): ?>
                <a href="admin-dash.php" class="icon admin-icon" aria-label="Admin Dashboard">
                    <i class="fas fa-tools"></i>
                </a>
            <?php endif; ?>
            <!-- Currency -->
            <?php if (isset($_SESSION['user']['currency'])): ?>
                <span class="currency-display">
                    <i class="fas fa-coins"></i> <!-- Icoon voor currency -->
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

            <div class="product-options form-group ">
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

            <h3 class="final-price">Final Price: €<span
                    id="finalPrice"><?php echo htmlspecialchars(number_format($product['price'], 2)); ?></span></>

                <button class="btn" type="submit">Add to Cart</button>
        </div>
    </div>



    <h2>Meer van de categorie <span
            style="color:green;"><?php echo htmlspecialchars($product['category_name']); ?></span></h2>
    <div class="related-products">
        <div class="products">
            <?php
            // Fetch all products except the current one
            $products = Product::getAll();
            foreach ($products as $relatedProduct):
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
            // als
            const quantity = document.getElementById('quantity').value;
            const finalPrice = (basePrice + sizePrice + potPrice) * quantity;
            document.getElementById('finalPrice').textContent = finalPrice.toFixed(2);
        }


    </script>
</body>

</html>