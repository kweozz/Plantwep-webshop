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
$deleteErrorMessage = '';
$deleteSuccessMessage = '';

$category = new Category();
$categories = $category->getAll();
$product = new Product();
$products = $product->getAll();

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
//delete category
if (isset($_POST['delete_category'])) {
    $categoryId = htmlspecialchars(trim($_POST['category_id']), ENT_QUOTES, 'UTF-8');
    try {
        if (Admin::deleteCategory($categoryId)) {
            $deleteSuccessMessage = 'Category deleted successfully!';
        } else {
            $deleteErrorMessage = 'Failed to delete category.';
        }
    } catch (Exception $e) {
        $categoryErrorMessage = 'Error: ' . $e->getMessage();
    }
}

if (isset($_POST['delete_product'])) {
    $productId = htmlspecialchars(trim($_POST['product_id']), ENT_QUOTES, 'UTF-8');
    try {
        if (Admin::deleteProduct($productId)) {
            $deleteSuccessMessage = 'Product deleted successfully!';
        } else {
            $deleteErrorMessage = 'Failed to delete product.';
        }
    } catch (Exception $e) {
        $productErrorMessage = 'Error: ' . $e->getMessage();
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
                    $productSuccessMessage = 'Product succesvol toegevoegd!';
                } else {
                    $productErrorMessage = 'Product toevoegen mislukt.';
                }
            } else {
                $productErrorMessage = 'Afbeelding uploaden mislukt.';
            }
        } catch (Exception $e) {
            $productErrorMessage = 'Error: ' . $e->getMessage();
        }
    } else {
        $productErrorMessage = 'Kies een afbeelding.';
    }
}
//updatee product
if (isset($_POST['update_product'])){
    $productId = htmlspecialchars(trim($_POST['product_id']), ENT_QUOTES, 'UTF-8');
    $productName = htmlspecialchars(trim($_POST['product_name']), ENT_QUOTES, 'UTF-8');
    $productPrice = htmlspecialchars(trim($_POST['product_price']), ENT_QUOTES, 'UTF-8');
    $productDescription = htmlspecialchars(trim($_POST['product_description']), ENT_QUOTES, 'UTF-8');
    $productStock = htmlspecialchars(trim($_POST['product_stock']), ENT_QUOTES, 'UTF-8');
    $categoryId = htmlspecialchars(trim($_POST['category']), ENT_QUOTES, 'UTF-8');

    if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] === 0) {
        try {
            $uploadResult = $admin->uploadImage($_FILES['product_image']);
            if ($uploadResult) {
                $productImage = "images/uploads/{$uploadResult}";
            } else {
                $productErrorMessage = 'Image upload failed.';
            }
        } catch (Exception $e) {
            $productErrorMessage = 'Error: ' . $e->getMessage();
        }
    } else {
        $productImage = null; // No new image uploaded
    }

    try {
        if ($admin->updateProduct($productId, $productName, $productPrice, $productDescription, $productStock, $categoryId, $productImage)) {
            $productSuccessMessage = 'Product updated successfully!';
        } else {
            $productErrorMessage = 'Failed to update product.';
        }
    } catch (Exception $e) {
        $productErrorMessage = 'Error: ' . $e->getMessage();
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

    

    <h1>Admin Dashboard</h1>

    <section class="category admin-section padding">
        <h2>Categories</h2>
        <div class="admin-options">
            <div>
                <h3>Add Category</h3>

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
                    <input type="file" id="category_image" name="category_image" accept="image/*" required
                        onchange="previewImage(event, 'category_image_preview')">
                    <br>
                    <div>
                        <h3>Preview</h3>
                        <img id="category_image_preview" src="" alt="Category Image Preview"
                            style="max-width: 200px; max-height: 200px; display: none;border-radius:16px">
                    </div>
                    <br>
                    <button class="btn btn-admin" type="submit" name="add_category">Add Category</button>
                </form>

            </div>
            <div>
                <h3>Delete Category</h3>
                <?php if (!empty($deleteSuccessMessage)): ?>
                    <div class=" alert-succes"><?= htmlspecialchars($categorySuccessMessage); ?></div>
                <?php endif; ?>

                <?php if (!empty($deleteErrorMessage)): ?>
                    <div class="alert-danger"><?= htmlspecialchars($deleteErrorMessage); ?></div>
                <?php endif; ?>
                <form class="form-group" method="post" action="">
                    <label for="category_id">Select Category:</label>
                    <select id="category_id" name="category_id" required>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?= htmlspecialchars($category['id']); ?>">
                                <?= htmlspecialchars($category['name']); ?> (ID: <?= $category['id']; ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <br>
                    <button class="btn btn-admin" type="submit" name="delete_category">Delete Category</button>
                </form>

            </div>

    </section>

    <section class="admin-section product padding">
        <h2>Products</h2>
        <div class="admin-options">
            <div>
                <h3>Add Product</h3>

                <?php if (!empty($productSuccessMessage)): ?>
                    <div class="alert-succes"><?= htmlspecialchars($productSuccessMessage); ?></div>
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
                    <input type="file" id="product_image" name="product_image" required
                        onchange="previewImage(event, 'preview_img')">
                    <br>
                    <div id="product_image_preview" style="margin-top: 10px;">
                        <img id="preview_img" src="" alt="Preview" style="max-width: 200px; display: none; border-radius:16px">
                    </div>
                    <label for="product_description">Product Description:</label>
                    <textarea id="product_description" name="product_description" required></textarea>
                    <br>
                    <label for="category">Category:</label>
                    <select name="category" id="category">
                        <?php if (!empty($categories)): ?>
                            <?php foreach ($categories as $category): ?>
                                <option value="<?= htmlspecialchars($category['id']); ?>">
                                    <?= htmlspecialchars($category['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <option value="">No categories available</option>
                        <?php endif; ?>
                    </select>
                    <br>

                    <label for="product_stock">Product Stock:</label>
                    <input type="number" id="product_stock" name="product_stock" required>
                    <br>
                    <button class="btn btn-admin" type="submit" name="add_product">Add Product</button>
                </form>
            </div>
            <div>
                <h3>Delete Product</h3>
                <?php if (!empty($deleteSuccessMessage)): ?>
                    <div class="alert-success"><?= htmlspecialchars($deleteSuccessMessage); ?></div>
                <?php endif; ?>

                <?php if (!empty($deleteErrorMessage)): ?>
                    <div class="alert-danger"><?= htmlspecialchars($deleteErrorMessage); ?></div>
                <?php endif; ?>
                <form class="form-group" method="post" action="">
                    <label for="product_id">Select Product:</label>
                    <select id="product_id" name="product_id" required>

                        <?php foreach ($products as $product): ?>
                            <option value="<?= htmlspecialchars($product['id']); ?>">
                                <?= htmlspecialchars($product['name']); ?> (ID: <?= $product['id']; ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <br>
                    <button class="btn btn-admin" type="submit" name="delete_product">Delete Product</button>
                </form>
            </div>
        </d>
    </section>
    </div>
    <script>

        // Image previewer
        function previewImage(event, previewId) {
            // Maak een nieuwe FileReader aan, js object dat bestanden kan lezen
            const reader = new FileReader();
            //als het geladen is dan wordt de functie uitgevoerd
            reader.onload = function () {
                // Zoek het <img> element op de pagina met het meegegeven id (previewId)
                const preview = document.getElementById(previewId);
                // zet de src (bron) van img naar de gelezen data 
                preview.src = reader.result;
                // maakt de afbeelding zichtbaar
                preview.style.display = 'block';
            };
            // Lees het bestand als een data URL (base64 encoded image) --> voor mijzelf een data url is een url waarin de afbeelding is opgeslagen --> vragen aan joris
            reader.readAsDataURL(event.target.files[0]);
        }


    </script>
</body>

</html>