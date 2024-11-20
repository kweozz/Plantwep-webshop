<?php
// admin-dash.php

session_start();

// Check if the user is logged in as admin
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit();
}

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "plantwep_webshop";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Add Category
if (isset($_POST['add_category'])) {
    $category_name = $_POST['category_name'];
    $sql = "INSERT INTO categories (name) VALUES ('$category_name')";
    if ($conn->query($sql) === TRUE) {
        echo "New category added successfully";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

// Add Product
if (isset($_POST['add_product'])) {
    $product_name = $_POST['product_name'];
    $product_price = $_POST['product_price'];
    $category_id = $_POST['category_id'];
    $sql = "INSERT INTO products (name, price, category_id) VALUES ('$product_name', '$product_price', '$category_id')";
    if ($conn->query($sql) === TRUE) {
        echo "New product added successfully";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

// Fetch categories for the dropdown
$categories_result = $conn->query("SELECT * FROM categories");

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
</head>
<body>
    <h1>Admin Dashboard</h1>

    <h2>Add Category</h2>
    <form method="post" action="">
        <label for="category_name">Category Name:</label>
        <input type="text" id="category_name" name="category_name" required>
        <button type="submit" name="add_category">Add Category</button>
    </form>

    <h2>Add Product</h2>
    <form method="post" action="">
        <label for="product_name">Product Name:</label>
        <input type="text" id="product_name" name="product_name" required>
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