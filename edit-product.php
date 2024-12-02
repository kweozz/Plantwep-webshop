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



// Haal het product op dat bewerkt moet worden
if (isset($_GET['product_id'])) {
    $productId = $_GET['product_id'];
    $product = Product::getById($productId);

    if (!$product) {
        echo '<div style="text-align: center; padding:10%; color:red; font-family:Helvetica;">Product niet gevonden.</div>';
        exit();
    }
}

// Verwerk het bewerkte product
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $stock = $_POST['stock'];
    $image = $_POST['image']; // Hier kun je een nieuwe afbeelding uploaden als je dat wilt

    try {
        $product->setName($name);
        $product->setDescription($description);
        $product->setPrice($price);
        $product->setStock($stock);
        $product->setImage($image); // Als je de afbeelding wilt aanpassen

        if ($product->update()) {
            $message = 'Product successfully updated!';
            header("Location: edit-product.php?product_id=" . $productId . "&message=" . urlencode($message));
            exit();
        } else {
            $message = 'Failed to update product.';
        }
    } catch (Exception $e) {
        $message = 'Error: ' . $e->getMessage();
    }
    echo '<div class="message">' . htmlspecialchars($message) . '</div>';
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Edit Product</title>
</head>
<body>
    <nav>
        <!-- Navigation links -->
    </nav>

    <h3>Product Bewerken</h3>

    <?php if (isset($message)): ?>
        <div class="message"><?= htmlspecialchars($message); ?></div>
    <?php endif; ?>

    <form class="form-group" action="" method="POST">
        <input type="text" name="name" value="<?= htmlspecialchars($product->getName()); ?>" required><br>

        <label for="description">Beschrijving:</label>
        <textarea name="description" required><?= htmlspecialchars($product->getDescription()); ?></textarea><br>

        <label for="price">Prijs:</label>
        <input type="number" name="price" value="<?= htmlspecialchars($product->getPrice()); ?>" required><br>

        <label for="stock">Voorraad:</label>
        <input type="number" name="stock" value="<?= htmlspecialchars($product->getStock()); ?>" required><br>

        <label for="image">Afbeelding (URL):</label>
        <input type="text" name="image" value="<?= htmlspecialchars($product->getImage()); ?>"><br>

        <button type="submit">Bewerken</button>
    </form>

    <a href="delete-product.php">Terug naar producten bewerken verwijderen</a>

</body>
</html>
