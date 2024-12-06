<?php
session_start();
include_once(__DIR__ . '/classes/Db.php');
include_once(__DIR__ . '/classes/Category.php');
include_once(__DIR__ . '/classes/Product.php');

// Controleer of de gebruiker rechten heeft
if ($_SESSION['role'] !== 1) {
    header("refresh:3;url=login.php");
    echo '<h1 style="text-align: center; padding:10%; color:red; font-family:Helvetica;">' . htmlspecialchars('You do not have access to this page') . '</h1>';
    exit();
}

// Haal categorieën en producten op
$category = new Category();
$categories = $category->getAll();

$product = new Product();
$products = $product->getAll();

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="style.css">
    <title>Admin Dashboard</title>
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

            <!-- Currency -->
            <?php if (isset($_SESSION['user']['currency'])): ?>
                <span class="currency-display">
                    <i class="fas fa-coins"></i>
                    <?php echo htmlspecialchars($_SESSION['user']['currency']); ?>
                </span>
            <?php endif; ?>
        </div>
    </nav>

        <h1 class="padding" >Admin Dashboard</h1>



        <section class=" admin-section category padding">
        <h2>Categories</h2>
            <div class="admin-options ">
            
                <div>
                    <h3>Add Category</h3>
                    <a href="add-category.php" class="btn btn-admin">Add Category</a>
                </div>
                <div>
                    <h3>Manage Categories</h3>
                    <?php if (!empty($deleteErrorMessage)): ?>
                        <div class="alert-danger"><?= htmlspecialchars($deleteErrorMessage); ?></div>
                    <?php endif; ?>
                    <form class="form-group" method="post" action="manage-categories.php">
                        <button class="btn btn-admin" type="submit" name="edit_category">Edit or delete
                            Category</button>

                    </form>
                </div>
            </div>
        </section>

        <section class="admin-section product padding">
            <h2>Products</h2>
            <div class="admin-options">
                <div>
                    <h3>Add Product</h3>
                    <a href="add-product.php" class="btn btn-admin">Add Product</a>
                </div>
                <div>
                    <h3>Manage Products</h3>
                    <?php if (!empty($deleteErrorMessage)): ?>
                        <div class="alert-danger"><?= htmlspecialchars($deleteErrorMessage); ?></div>
                    <?php endif; ?>
                    <form class="form-group" method="post" action="manage-products.php">
                        <button class="btn btn-admin" type="submit" name="edit_product">Edit or delete Product</button>

                    </form>
                </div>
            </div>
        </section>

    <script>
        function previewImage(event, previewId) {
            const reader = new FileReader();
            reader.onload = function () {
                const preview = document.getElementById(previewId);
                preview.src = reader.result;
                preview.style.display = 'block';
            };
            reader.readAsDataURL(event.target.files[0]);
        }
    </script>
</body>

</html>