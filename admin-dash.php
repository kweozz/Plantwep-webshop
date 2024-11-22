<?php
// admin-dash.php
include_once(__DIR__ . '/classes/Db.php');
include_once(__DIR__ . '/classes/User.php');
include_once(__DIR__ . '/classes/Admin.php');
include_once(__DIR__ . '/classes/Category.php');
session_start();

if ($_SESSION['role'] !== 1) {
    // redirect naar log in
    echo '<h1  style="text-align: center;
    padding:10%; color:red; font-family:Helvetica;">' . htmlspecialchars('You do not have access to this page') . '</h1>';
    //wacht 3 seconden en redirect naar login
    header("refresh:3;url=login.php");
    exit();
}
$admin = new Admin();  // Maak een instantie van de Admin-klasse, weet niet zo goed of ik best gebruik maak van statische connectie of nieuw obj te maken
$successMessage = '';
$errorMessage = '';

if (isset($_POST['add_category'])) {
    $categoryName = htmlspecialchars(trim($_POST['category_name']), ENT_QUOTES, 'UTF-8');

    // Controleer of er een bestand is geüpload
    if (isset($_FILES['category_image']) && $_FILES['category_image']['error'] === 0) {

        try {
            // Upload de afbeelding via de Admin-klasse
            $uploadResult = $admin->uploadImage($_FILES['category_image']); // Verkrijg het bestandsnaam na upload

            // Als upload succesvol is, maak de categorie aan
            if ($uploadResult) {
                $category = new Category();
                $category->setName($categoryName);
                $category->setImage('images/uploads/' . $uploadResult); // Zet het pad van de geüploade afbeelding
                if ($category->create()) {
                    $successMessage = 'Category added successfully!';
                } else {
                    $errorMessage = 'Failed to add category.';
                }
            } else {
                $errorMessage = 'Image upload failed.';
            }
        } catch (Exception $e) {
            $errorMessage = 'Error: ' . $e->getMessage();
        }
    } else {
        $errorMessage = 'Please choose an image.';
    }
}




?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Admin Dashboard</title>
</head>

<body>
    <h1>Admin Dashboard</h1>
    <section class="category">
        <h2>Add Category</h2>
        <?php if (!empty($successMessage)): ?>
            <div class="success-message"><?= htmlspecialchars($successMessage); ?></div>
        <?php endif; ?>
        <?php if (!empty($errorMessage)): ?>
            <div class="alert-danger"><?= htmlspecialchars($errorMessage); ?></div>
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
        <form class="form-group" method="post" action="">
            <label for="product_name">Product Name:</label>
            <input type="text" id="product_name" name="product_name" required pattern="[A-Za-z0-9\s]+"
                title="Only letters, numbers, and spaces are allowed">
            <br>
            <label for="product_price">Product Price:</label>
            <input type="number" id="product_price" name="product_price" required>
            <br>
            <button class="btn" type="submit" name="add_product">Add Product</button>
        </form>
    </section>
</body>

</html>