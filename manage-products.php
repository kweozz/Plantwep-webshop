<?php
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 1) {
    header("refresh:3;url=login.php");
    echo '<h1 style="text-align: center; padding:10%; color:red; font-family:Helvetica;">' . htmlspecialchars('You do not have access to this page') . '</h1>';
    exit();
}

include_once __DIR__ . '/classes/Db.php';
include_once __DIR__ . '/classes/Category.php';
include_once __DIR__ . '/classes/Product.php';

// Fetch categories from the database
$categories = Category::getAll();

// Get the selected category from the URL (if any)
$selectedCategoryId = isset($_GET['category_id']) ? intval($_GET['category_id']) : null;

// Fetch products based on the selected category
$products = $selectedCategoryId ? Product::getByCategory($selectedCategoryId) : Product::getAll(); // Show all products if no category is selected

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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="css/normalize.css">
    <link rel="stylesheet" href="css/style.css">
    <title>Beheer Producten</title>
</head>

<body>
    <?php include 'classes/Nav.php'; ?>
    <section class="manage">
        <div class="back">
            <a class="back-icon" href="admin-dash.php">
                <i class="fa fa-arrow-left" aria-hidden="true"></i>
            </a>
            <h1>Beheer producten</h1>
        </div>
    </section>
    <?php if (isset($_GET['message'])): ?>
        <div class="message">
            <?php echo htmlspecialchars($_GET['message']); ?>
        </div>
    <?php endif; ?>
    <div class="manage-products">
        <h2>Filter op categorie</h2>
        <section class="category-section manage-category">
            <div class="categories-wrapper">
                <button class="scroll-btn left-btn"><i class="fas fa-arrow-left"></i></button>
                <div class="categories">
                    <a href="manage-products.php"
                        class="category-card <?= $selectedCategoryId === null ? 'active' : ''; ?>">
                        <p>Alle</p>
                    </a>
                    <?php foreach ($categories as $category): ?>
                        <a href="manage-products.php?category_id=<?php echo $category['id']; ?>"
                            class="category-card <?= $selectedCategoryId == $category['id'] ? 'active' : ''; ?>">
                            <p><?php echo htmlspecialchars($category['name']); ?></p>
                        </a>
                    <?php endforeach; ?>
                </div>
                <button class="scroll-btn right-btn"><i class="fas fa-arrow-right"></i></button>
            </div>
        </section>
        <section class="products-section">
            <h2>Producten</h2>
            <!-- Dit is de HTML weergave van de producten -->

            <?php if (isset($deleteSuccessMessage)): ?>
                <div class="alert-success">
                    <?php echo htmlspecialchars($deleteSuccessMessage); ?>
                </div>
            <?php endif; ?>
            <?php if (isset($deleteErrorMessage)): ?>
                <div class="alert-danger">
                    <?php echo htmlspecialchars($deleteErrorMessage); ?>
                </div>
            <?php endif; ?>
            <div class="manage-product"> <?php if (empty($products)): ?>
                    <p>Geen producten gevonden voor deze categorie.</p>
                <?php else: ?>

                    <?php foreach ($products as $product): ?>
                        <div class=" manage-card">
                            <!-- Verwijder knop (Rood kruisje) -->
                            <form class="delete-form" action="" method="POST" style="display:inline;">
                                <input type="hidden" name="product_id" value="<?= intval($product['id']); ?>">
                                <button class="delete-btn" type="submit" name="delete_product">
                                    <i class="fas fa-times-circle"></i> </button>
                            </form>
                            <img src="<?= htmlspecialchars($product['image']); ?>"
                                alt="<?= htmlspecialchars($product['name']); ?>">
                            <h4><?= htmlspecialchars($product['name']); ?></h4>
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
    <script src="script/manage-products.js">
    </script>
</body>

</html>