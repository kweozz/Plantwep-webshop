<?php
session_start();
include_once(__DIR__ . '/classes/Db.php');
include_once(__DIR__ . '/classes/Category.php');
include_once(__DIR__ . '/classes/Product.php');

// Check if the user is logged in and has the correct role
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 1) {
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
    <link rel="stylesheet" href="css/style.css">
    <title>Admin Dashboard</title>
</head>

<body>
    <?php include 'classes/Nav.php'; ?>

    <h1 class="padding">Admin Dashboard</h1>
    <section class=" admin-section category padding">
        <h2>Categorieën</h2>
        <div class="admin-options ">

            <div>

                <a href="add-category.php" class="btn btn-admin">Categorie toevoegen</a>
            </div>
         
            <div>


                <a class="btn btn-admin" href="manage-categories.php">Bewerk of verwijder Categorie</a>



            </div>
        </div>
    </section>

    <section class="admin-section product padding">
        <h2>Producten</h2>
        <div class="admin-options">
            <div>

                <a href="add-product.php" class="btn btn-admin">Product toevoegen</a>
            </div>
            <div>
                <a class="btn btn-admin" href="manage-products.php">Bewerk of verwijder Product</a>
            </div>
        </div>
    </section>
</body>

</html>