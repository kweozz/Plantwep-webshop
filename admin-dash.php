<?php
// admin-dash.php
include_once __DIR__ . '/classes/Db.php';
include_once __DIR__ . '/classes/User.php';
include_once __DIR__ . '/classes/Admin.php';
include_once __DIR__ . '/classes/Category.php';
include_once __DIR__ . '/classes/Product.php';
session_start();

if ($_SESSION['role'] !== 1) {
    echo '<h1 style="text-align: center; padding:10%; color:red; font-family:Helvetica;">' . htmlspecialchars('You do not have access to this page') . '</h1>';
    header("refresh:3;url=login.php");
    exit();
}

$admin = new Admin();
$categorySuccessMessage = '';
$categoryErrorMessage = '';
$productSuccessMessage = '';
$productErrorMessage = '';

$category = new Category();
$categories = $category->getAll();
$product = new Product();

if (isset($_POST['add_category'])) {
    $categoryName = htmlspecialchars(trim($_POST['category_name']), ENT_QUOTES, 'UTF-8');
    if (isset($_FILES['category_image']) && $_FILES['category_image']['error'] === 0) {
        try {
            $uploadResult = $admin->uploadImage($_FILES['category_image']);
            if ($uploadResult) {
                $category = new Category();
                $category->setName($categoryName);
                $category->setImage("images/uploads/{$uploadResult}");
                if ($category->create()) {
                    $categorySuccessMessage = 'Category added successfully!';
                } else {
                    $categoryErrorMessage = 'Failed to add category.';
                }
            } else {
                $categoryErrorMessage = 'Image upload failed.';
            }
        } catch (Exception $e) {
            $categoryErrorMessage = 'Error: ' . $e->getMessage();
        }
    } else {
        $categoryErrorMessage = 'Please choose an image.';
    }
}

if (isset($_POST['add_product'])) {
    $productName = htmlspecialchars(trim($_POST['product_name']), ENT_QUOTES, 'UTF-8');
    $productPrice = htmlspecialchars(trim($_POST['product_price']), ENT_QUOTES, 'UTF-8');
    $productDescription = htmlspecialchars(trim($_POST['product_description']), ENT_QUOTES, 'UTF-8');
    $productStock = htmlspecialchars(trim($_POST['product_stock']), ENT_QUOTES, 'UTF-8');
    $categoryId = htmlspecialchars(trim($_POST['category']), ENT_QUOTES, 'UTF-8');

    if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] === 0) {
        try {
            $uploadResult = $admin->uploadImage($_FILES['product_image']);
            if ($uploadResult) {
                $product = new Product();
                $product->setName($productName);
                $product->setPrice($productPrice);
                $product->setDescription($productDescription);
                $product->setStock($productStock);
                $product->setCategory($categoryId);
                $product->setImage("images/uploads/{$uploadResult}");
                if ($product->create()) {
                    $productSuccessMessage = 'Product added successfully!';
                } else {
                    $productErrorMessage = 'Failed to add product.';
                }
            } else {
                $productErrorMessage = 'Image upload failed.';
            }
        } catch (Exception $e) {
            $productErrorMessage = 'Error: ' . $e->getMessage();
        }
    } else {
        $productErrorMessage = 'Please choose an image.';
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
    <title>Admin Dashboard</title>
</head>

<body>
<nav>
        <a href="index.php"><img class="logo" src="images/logo-plantwerp.png" alt="Plantwerp Logo"></a>
        <input type="text" placeholder="Search for plants..." class="search-bar">
        <div class="nav-items">
            <a href="admin-dash.php" class="icon profile-icon" aria-label="Profile">
                <i class="fas fa-user"></i>
            </a>
            <a href="#" class="icon basket-icon" aria-label="Basket">
                <i class="fas fa-shopping-basket"></i>
            </a>
        </div>
    </nav>
    <h1>Admin Dashboard</h1>

    <section class="category">
        <h2>Add Category</h2>

        <?php if (!empty($categorySuccessMessage)): ?>
            <div class="alert alert-success"><?= htmlspecialchars($categorySuccessMessage); ?></div>
        <?php endif; ?>

        <?php if (!empty($categoryErrorMessage)): ?>
            <div class="alert-danger"><?= htmlspecialchars($categoryErrorMessage); ?></div>
        <?php endif; ?>

        <form class="form-group" method="post" action="" enctype="multipart/form-data">
            <label for="category_name">Category Name:</label>
            <input type="text" id="category_name" name="category_name" required pattern="[A-Za-z0-9\s]+"
                title="Only letters, numbers, and spaces are allowed">
            <br>
            <label for="category_image">Category Picture:</label>
            <input type="file" id="category_image" name="category_image" required>
            <br>
            <button class="btn" type="submit" name="add_category">Add Category</button>
        </form>
    </section>

    <section class="product">
        <h2>Add Product</h2>

        <?php if (!empty($productSuccessMessage)): ?>
            <div class="success-message"><?= htmlspecialchars($productSuccessMessage); ?></div>
        <?php endif; ?>

        <?php if (!empty($productErrorMessage)): ?>
            <div class="alert-danger"><?= htmlspecialchars($productErrorMessage); ?></div>
        <?php endif; ?>

        <form class="form-group" method="post" action="" enctype="multipart/form-data">
            <label for="product_name">Product Name:</label>
            <input type="text" id="product_name" name="product_name" required>
            <br>
            <label for="product_price">Product Price:</label>
            <input type="number" id="product_price" name="product_price" required>
            <br>
            <label for="product_image">Product Picture:</label>
            <input type="file" id="product_image" name="product_image" required>
            <br>
            <label for="product_description">Product Description:</label>
            <textarea id="product_description" name="product_description" required></textarea>
            <br>
            <label for="category">Category:</label>
            <select name="category" id="category">
                <?php if (!empty($categories)): ?>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?= htmlspecialchars($category['id']); ?>"><?= htmlspecialchars($category['name']); ?></option>
                    <?php endforeach; ?>
                <?php else: ?>
                    <option value="">No categories available</option>
                <?php endif; ?>
            </select>
            <br>

            <label for="product_stock">Product Stock:</label>
            <input type="number" id="product_stock" name="product_stock" required>
            <br>
            <button class="btn" type="submit" name="add_product">Add Product</button>
        </form>
    </section>

</body>

</html>
