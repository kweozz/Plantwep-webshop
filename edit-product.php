<?php
session_start();
include_once __DIR__ . '/classes/Db.php';
include_once __DIR__ . '/classes/Category.php';
include_once __DIR__ . '/classes/Product.php';
include_once __DIR__ . '/classes/ImageUploader.php';
include_once __DIR__ . '/classes/ProductOption.php';
include_once __DIR__ . '/classes/Option.php';

// Controleer of de gebruiker rechten heeft
if ($_SESSION['role'] !== 1) {
    echo '<h1 style="text-align: center; padding:10%; color:red; font-family:Helvetica;">' . htmlspecialchars('You do not have access to this page') . '</h1>';
    header("refresh:3;url=login.php");
    exit();
}

// Haal het specifieke product op
$productId = isset($_GET['id']) && is_numeric($_GET['id']) ? intval($_GET['id']) : null;
if (!$productId) {
    die('Product ID is not valid.');
}

$product = Product::getById($productId);
if (!$product) {
    die('Product not found.');
}

// Haal categorieën op
$category = new Category();
$categories = $category->getAll();

// Haal opties voor dit product op
$options = Option::getAll();
$productOptions = ProductOption::getByProductId($productId);
$selectedOptions = array_column($productOptions, 'option_id');
$priceAdditions = array_column($productOptions, 'price_addition', 'option_id');


$message = '';

// Verwerk het bewerkte product
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = htmlspecialchars(trim($_POST['name']), ENT_QUOTES, 'UTF-8');
    $description = htmlspecialchars(trim($_POST['description']), ENT_QUOTES, 'UTF-8');
    $price = (float) htmlspecialchars(trim($_POST['price']), ENT_QUOTES, 'UTF-8');
    $stock = (int) htmlspecialchars(trim($_POST['stock']), ENT_QUOTES, 'UTF-8');
    $categoryId = (int) htmlspecialchars(trim($_POST['category']), ENT_QUOTES, 'UTF-8');
    $imagePath = $product['image']; // Standaard huidige afbeelding gebruiken

    try {
        if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] === UPLOAD_ERR_OK) {
            $imageUploader = new ImageUploader();
            $uploadResult = $imageUploader->uploadImage($_FILES['product_image']);
            if ($uploadResult) {
                $imagePath = $uploadResult;
                $product['image'] = $imagePath; // Update the product image path
            } else {
                throw new Exception('Afbeelding uploaden mislukt.');
            }
        }

        // Update het product
        $updateMessage = Product::updateProduct($productId, $name, $price, $description, $categoryId, $imagePath, $stock);
        if (strpos($updateMessage, 'succesvol') !== false) {
            // Update de opties
            $options = [];
            if (!empty($_POST['options'])) {
                foreach ($_POST['options'] as $optionId => $data) {
                    if (isset($data['id'])) { // Ensure the option_id is set
                        $options[] = [
                            'option_id' => $data['id'],
                            'price_addition' => isset($data['price_addition']) ? (float) $data['price_addition'] : 0.0,
                        ];
                    }
                }
                ProductOption::updateOptions($productId, $options);
            }

            // Refresh the options after update
            $productOptions = ProductOption::getByProductId($productId);
            $selectedOptions = array_column($productOptions, 'option_id');
            $priceAdditions = array_column($productOptions, 'price_addition', 'option_id');

            $message = '<div class="alert-success">' . $updateMessage . '</div>';
        } else {
            $message = '<div class="alert-danger">' . $updateMessage . '</div>';
        }
    } catch (Exception $e) {
        $message = '<div class="alert-danger">Fout: ' . htmlspecialchars($e->getMessage()) . '</div>';
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
    <title>Product Bewerken</title>
</head>

<body>
<?php include 'classes/Nav.php'; ?>

<section class="product padding">

    <!-- Succes- of foutmeldingen -->
    <?= $message; ?>

    <form class="form-group add-product-container" method="post" action="" enctype="multipart/form-data">
        <a class="back-icon" href="manage-products.php">
            <i class="fa fa-arrow-left" aria-hidden="true"></i>
        </a>

        <div class="product-image">
            <label for="image" class="image-upload-label">
                <img id="imagePreview"
                    src="<?= htmlspecialchars($product['image'] ?: 'images/default-placeholder.png'); ?>"
                    style="display:block;">
                <span class="upload-icon">+</span>
            </label>
            <input type="file" id="image" name="product_image" accept="image/*"
                onchange="previewImage(event, 'imagePreview')" style="display: none;">
        </div>

        <div class="product-details">
            <h2>Product Bewerken</h2>
            <div class="options-group">
                <label for="name">Naam:</label>
                <input type="text" id="name" name="name" value="<?= htmlspecialchars($product['name']); ?>" required
                    autocomplete="name">

                <label for="description">Beschrijving:</label>
                <textarea id="description" name="description" required
                    autocomplete="description"><?= htmlspecialchars($product['description']); ?></textarea>

                <label for="price">Prijs:</label>
                <input type="number" id="price" name="price" step="0.01"
                    value="<?= htmlspecialchars($product['price']); ?>" required autocomplete="price">

                <label for="stock">Voorraad:</label>
                <input type="number" id="stock" name="stock" value="<?= htmlspecialchars($product['stock']); ?>"
                    required autocomplete="stock">

                <label for="category">Categorie:</label>
                <select id="category" name="category" autocomplete="category">
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= htmlspecialchars($cat['id']); ?>" <?= $cat['id'] == $product['category_id'] ? 'selected' : ''; ?>>
                            <?= htmlspecialchars($cat['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="options-group">
                <label>Opties:</label>
                <?php foreach ($options as $option): ?>
                    <?php if (isset($option['id'])): ?>
                        <label class="option-button" for="option_<?= $option['id']; ?>">
                            <input type="checkbox" id="option_<?= $option['id']; ?>"
                                name="options[<?= $option['id']; ?>][id]" value="<?= $option['id']; ?>"
                                <?= in_array($option['id'], $selectedOptions) ? 'checked' : ''; ?>
                                data-price-addition-input="price_addition_<?= $option['id']; ?>">
                            <span><?= htmlspecialchars($option['name']); ?></span>
                        </label>
                        <div class="price-addition" id="price_addition_<?= $option['id']; ?>"
                            style="display: <?= in_array($option['id'], $selectedOptions) ? 'block' : 'none'; ?>;">
                            <label for="price_addition_<?= $option['id']; ?>">Price Addition:</label>
                            <input type="number" id="price_addition_<?= $option['id']; ?>"
                                name="options[<?= $option['id']; ?>][price_addition]" step="0.01"
                                value="<?= in_array($option['id'], $selectedOptions) ? htmlspecialchars($priceAdditions[$option['id']]) : '0'; ?>"
                                autocomplete="price-addition">
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>

            <button class="btn btn-admin" type="submit">Opslaan</button>
        </div>
    </form>
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

    // Dynamisch tonen/verbergen van prijsverhoging invoervelden
    document.querySelectorAll('.option-button input[type="checkbox"]').forEach(function (checkbox) {
        checkbox.addEventListener('change', function () {
            const priceAdditionInputId = this.getAttribute('data-price-addition-input');
            const priceAdditionDiv = document.getElementById(priceAdditionInputId);

            if (this.checked) {
                priceAdditionDiv.style.display = 'block';
            } else {
                priceAdditionDiv.style.display = 'none';
            }
        });
    });
</script>
</body>

</html>