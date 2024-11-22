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
    padding:10%">' . htmlspecialchars('You do not have access to this page') . '</h1>';
    //wacht 3 seconden en redirect naar login
    header("refresh:3;url=login.php");
    exit();
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
    <form method="post" action="">
        <label for="category_name">Category Name:</label>
        <input type="text" id="category_name" name="category_name" required pattern="[A-Za-z0-9\s]+" title="Only letters, numbers, and spaces are allowed">
        <br>
        <label for="category_picture">Category Picture:</label>
        <input type="file" id="category_image" name="category_image" required>
    </form>
    <button type="submit" name="add_category">Add Category</button>
    </section>
    <section class="product">
    <h2>Add Product</h2>
    <form method="post" action="">
        <label for="product_name">Product Name:</label>
        <input type="text" id="product_name" name="product_name" required pattern="[A-Za-z0-9\s]+" title="Only letters, numbers, and spaces are allowed">
        <br>
        <label for="product_price">Product Price:</label>
        <input type="number" id="product_price" name="product_price" required>
        <br>
        <label for="category_id">Category:</label>
        <select id="category_id" name="category_id" required>
            <?php while ($row = $categories_result->fetch_assoc()): ?>
                <option value="<?php echo $row['id']; ?>"><?php echo $row['name']; ?></option>
            <?php endwhile; ?>
        </select>
        <br>
        <button type="submit" name="add_product">Add Product</button>
    </form>
</body>
</html>