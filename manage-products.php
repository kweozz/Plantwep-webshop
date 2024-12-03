<?php
session_start();

if ($_SESSION['role'] !== 1) {
    header("refresh:3;url=login.php");
    echo '<h1 style="text-align: center; padding:10%; color:red; font-family:Helvetica;">' . htmlspecialchars('You do not have access to this page') . '</h1>';
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

// Delete Product
if (isset($_POST['delete_product'])) {
    $productId = intval($_POST['product_id']);
    try {
        $product = new Product();
        $product->setId($productId); // Zorg ervoor dat de ID goed wordt ingesteld
        if ($product->delete()) {
            $deleteSuccessMessage = 'Product successfully deleted!';
        } else {
            $deleteErrorMessage = 'Failed to delete product.';
        }
    } catch (Exception $e) {
        $deleteErrorMessage = 'Error: ' . $e->getMessage();
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="style.css">
    <title>Delete Product</title>
</head>

<body>
    <nav>
        <a href="index.php"><img class="logo" src="images/logo-plantwerp.png" alt="Plantwerp Logo"></a>
        <input type="text" placeholder="Zoek naar planten..." class="search-bar">
        <div class="nav-items">
            <!-- Navigation Links -->
        </div>
    </nav>

    <h1>Manage products</h1>

    <?php if (isset($_GET['message'])): ?>
        <div class="message">
            <?php echo htmlspecialchars($_GET['message']); ?>
        </div>
    <?php endif; ?>
    <div class="manage-products">
        <h2>Filter by categories</h2>
        <section class="category-section manage-category">

            <div class="categories-wrapper">
                <button class="scroll-btn left-btn">&#8592;</button>
                <div class="categories">
                    <a href="manage-products.php"
                        class="category-card <?= $selectedCategoryId === null ? 'active' : ''; ?>">
                        <p>All</p>
                    </a>
                    <?php foreach ($categories as $category): ?>
                        <a href="manage-products.php?category_id=<?php echo $category['id']; ?>"
                            class="category-card <?= $selectedCategoryId == $category['id'] ? 'active' : ''; ?>">
                            <p><?php echo htmlspecialchars($category['name']); ?></p>
                        </a>
                    <?php endforeach; ?>
                </div>
                <button class="scroll-btn right-btn">&#8594;</button>
            </div>
        </section>
        <section class="products-section">
            <h2>Products</h2>
            <!-- Dit is de HTML weergave van de producten -->

            <div class="products"> <?php if (empty($products)): ?>
                    <p>No products found for this category.</p>
                <?php else: ?>

                    <?php foreach ($products as $product): ?>
                        <div class="product-card manage-card">
                            <!-- Verwijder knop (Rood kruisje) -->
                            <form class="delete-form" action="" method="POST" style="display:inline;">
                                <input type="hidden" name="product_id" value="<?= intval($product['id']); ?>">
                                <button class="delete-btn" type="submit" name="delete_product">
                                    <i class="fas fa-times-circle"></i> </button>
                            </form>
                            <img src="<?= htmlspecialchars($product['image']); ?>"
                                alt="<?= htmlspecialchars($product['name']); ?>">
                            <h4><?= htmlspecialchars($product['name']); ?></h4>
                            <p><?= htmlspecialchars($product['description']); ?></p>
                            <p>€<?= htmlspecialchars($product['price']); ?></p>
                            <p>Stock: <?= htmlspecialchars($product['stock']); ?></p>


                            <!-- In je productcard, voeg een bewerken knop toe -->
                            <a href="edit-product.php?id=<?= $product['id']; ?>" class="btn btn-edit">
                                <i class="fa fa-edit"></i> Bewerken
                            </a>

                        </div>

                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

        </section>
    </div>
    <script>
        // Optional: Handle category scrolling if needed
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
        //are yyou sure u want to delte this product
        //if yes, delete
        //if no, cancel
        document.querySelectorAll('.delete-form').forEach(form => {
            form.addEventListener('submit', function (event) {
                event.preventDefault();
                if (confirm('Are you sure you want to delete this product?')) {
                    this.submit();
                }
            });
        });
    </script>

</body>

</html>