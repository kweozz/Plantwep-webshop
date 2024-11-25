<?php
session_start();
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit();
}
include_once(__DIR__ . '/classes/Db.php');
include_once(__DIR__ . '/classes/Category.php');
include_once(__DIR__ . '/classes/Product.php');

// Fetch categories and products from the database
$categories = Category::getAll();
$products = Product::getAll();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="style.css">
    <title>Plantwerp</title>
</head>

<body>
    <nav>
        <a href="index.php"><img class="logo" src="images/logo-plantwerp.png" alt="Plantwerp Logo"></a>
        <div class="nav-items">
            <input type="text" placeholder=" Search for plants..." class="search-bar">
            <a href="profile.php" class="icon profile-icon" aria-label="Profile">
                <i class="fas fa-user"></i> 
            </a>
            <a href="#" class="icon basket-icon" aria-label="Basket">
                <i class="fas fa-shopping-basket"></i> 
            </a>
        </div>
    </nav>

    <h1>Plantwerp</h1>
    <div class="hero">
    </div>

    <h2>Categories</h2>
    <section class="category-section">
        <div class="categories-wrapper">
            <button class="scroll-btn left-btn">&#8592;</button>
            <div class="categories">
                <?php foreach ($categories as $category): ?>
                    <a href="#<?php echo htmlspecialchars($category['name']); ?>" class="category-card">
                        <img src="<?php echo htmlspecialchars($category['image']); ?>" alt="<?php echo htmlspecialchars($category['name']); ?>">
                        <p><?php echo htmlspecialchars($category['name']); ?></p>
                    </a>
                <?php endforeach; ?>
            </div>
            <button class="scroll-btn right-btn">&#8594;</button>
        </div>
    </section>

    <section class="products-section">
        <h2>Products</h2>
        <div class="products">
            <?php foreach ($products as $product): ?>
                <a href="product-page.php?id=<?php echo $product['id']; ?>" class="product-card">
                    <img src="<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                    <p><?php echo htmlspecialchars($product['name']); ?></p>
                    <p>€<?php echo htmlspecialchars(number_format($product['price'], 2)); ?></p>
                </a>
            <?php endforeach; ?>
        </div>
    </section>

    <script src="script.js" defer></script>
</body>

</html>
