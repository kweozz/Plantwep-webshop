<?php
session_start();
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit();
}
include_once(__DIR__ . '/classes/Db.php');
include_once(__DIR__ . '/classes/Category.php');
include_once(__DIR__ . '/classes/Product.php');

// Fetch categories from the database
$categories = Category::getAll();

// Get the selected category from the URL (if any)
$selectedCategoryId = isset($_GET['category_id']) ? intval($_GET['category_id']) : null;

// Fetch products based on the selected category
if ($selectedCategoryId) {
    $products = Product::getByCategory($selectedCategoryId);
} else {
    $products = Product::getAll(); // Show all products if no category is selected
}
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
            <input type="text" placeholder="Search for plants..." class="search-bar">
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
                <!-- Show categories -->
                <a href="index.php" class="category-card <?= $selectedCategoryId === null ? 'active' : ''; ?>">
                    <p>All</p>
                </a>
                <?php foreach ($categories as $category): ?>
                    <a href="index.php?category_id=<?php echo $category['id']; ?>" 
                       class="category-card <?= $selectedCategoryId == $category['id'] ? 'active' : ''; ?>">
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
            <!-- Show products -->
            <?php if (empty($products)): ?>
                <p>No products found for this category.</p>
            <?php else: ?>
                <?php foreach ($products as $product): ?>
                    <a href="product-page.php?id=<?php echo $product['id']; ?>" class="product-card">
                        <img src="<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                        <p><?php echo htmlspecialchars($product['name']); ?></p>
                        <p>€<?php echo htmlspecialchars(number_format($product['price'], 2)); ?></p>
                    </a>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </section>

   
</body>

</html>
