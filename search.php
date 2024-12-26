<?php
session_start();
include_once __DIR__ . '/classes/Db.php';
include_once __DIR__ . '/classes/Product.php';

// Check if the search query is set
if (!isset($_GET['query']) || empty(trim($_GET['query']))) {
    header('Location: index.php');
    exit();
}

$query = htmlspecialchars(trim($_GET['query']), ENT_QUOTES, 'UTF-8');

// Fetch products that match the search query
$products = Product::search($query);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Resultaten - Plantwerp</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>

<body>
<?php include 'classes/Nav.php'; ?>

<section class="search-results">
    <h2 class="padding">Zoek resultaten voor "<?php echo $query; ?>"</h2>
    <div class="products">
        <?php if (empty($products)): ?>
            <p>Geen producten gevonden.</p>
        <?php else: ?>
            <?php foreach ($products as $product): ?>
                <a href="product-page.php?id=<?php echo $product['id']; ?>" class="product-card search">
                    <img src="<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                    <h4><?php echo htmlspecialchars($product['name']); ?></h4>
                    <p>€<?php echo htmlspecialchars(number_format($product['price'], 2)); ?></p>
                </a>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</section>
</body>

</html>