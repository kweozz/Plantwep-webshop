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
    <link rel="stylesheet" href="css/normalize.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="style.css">
    <title>Plantwerp</title>
</head>

<body>
    <?php include 'classes/Nav.php'; ?>

    <section class="hero-section">
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

        // Save the current scroll position before navigating
        document.querySelectorAll('.category-card').forEach(card => {
            card.addEventListener('click', () => {
                // Save the current scroll position to localStorage
                localStorage.setItem('scrollPosition', window.scrollY);
            });
        });

        // Restore the scroll position on page load
        document.addEventListener('DOMContentLoaded', () => {
            const savedPosition = localStorage.getItem('scrollPosition');
            if (savedPosition) {
                window.scrollTo(0, parseInt(savedPosition, 10));
                localStorage.removeItem('scrollPosition'); // Clear the position after restoring
            }
        });



    </script>

</body>

</html>