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
    <section>
    <h1>Plantwerp</h1>
    <div class="hero">
    </div>
    </section>


    <section class="category-section">
        <h2>Categories</h2>
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
                        <img src="<?php echo htmlspecialchars($category['image']); ?>"
                            alt="<?php echo htmlspecialchars($category['name']); ?>">
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
                        <img src="<?php echo htmlspecialchars($product['image']); ?>"
                            alt="<?php echo htmlspecialchars($product['name']); ?>">
                        <h4><?php echo htmlspecialchars($product['name']); ?></h4>
                        <p>€<?php echo htmlspecialchars(number_format($product['price'], 2)); ?></p>
                    </a>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </section>

    <script>

        const categories = document.querySelector('.categories');
        const leftBtn = document.querySelector('.left-btn');
        const rightBtn = document.querySelector('.right-btn');

        // Function to check if the categories are overflowing
        function checkOverflow() {
            const isOverflowing = categories.scrollWidth > categories.clientWidth;
            if (isOverflowing) {
                leftBtn.style.display = 'block';
                rightBtn.style.display = 'block';
            } else {
                leftBtn.style.display = 'none';
                rightBtn.style.display = 'none';
            }
        }

        // Initial check for overflow when the page loads
        checkOverflow();

        // Optionally, recheck overflow when the window is resized
        window.addEventListener('resize', checkOverflow);

        // Add event listeners for scroll buttons
        leftBtn.addEventListener('click', () => {
            categories.scrollBy({
                left: -200,
                behavior: 'smooth',
            });
        });

        rightBtn.addEventListener('click', () => {
            categories.scrollBy({
                left: 200,
                behavior: 'smooth',
            });
        });


    </script>

</body>

</html>