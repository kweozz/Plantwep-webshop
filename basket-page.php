<?php
include_once __DIR__ . '/classes/Db.php';
include_once __DIR__ . '/classes/Basket.php';
include_once __DIR__ . '/classes/BasketItem.php';
include_once __DIR__ . '/classes/Product.php';

// Start session
session_start();

if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit();
}

$userId = $_SESSION['user']['id'];
$basket = Basket::getBasket($userId);

if (!$basket) {
    die('Basket not found.');
}

$basketItems = BasketItem::getItemsByBasketId($basket['id']);
?>

<!DOCTYPE html>
<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['clear_basket'])) {
    BasketItem::clearBasket($basket['id']);
    header('Location: basket-page.php');
    exit();
}
?>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Basket - Plantwerp Webshop</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="style.css">
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
            <?php if (isset($_SESSION['user']['currency'])): ?>
                <span class="currency-display">
                    <i class="fas fa-coins"></i>
                    <?php echo htmlspecialchars($_SESSION['user']['currency']); ?>
                </span>
            <?php endif; ?>
        </div>
    </nav>

    <section class="basket-container">
        <h1>Your Basket</h1>
        <ul class="basket-list">
            <?php foreach ($basketItems as $item): ?>
                <?php $product = Product::getById($item['product_id']); ?>
                <li class="basket-item">
                    <div class="basket-item-info">
                        <h4 class="product-name"><?php echo htmlspecialchars($product['name']); ?></h4>
                        <p>Quantity: <?php echo $item['quantity']; ?></p>
                        <p>Total Price: €<?php echo number_format($item['total_price'], 2); ?></p>
                    </div>
                    <div class="basket-item-actions">
                        <form action="" method="POST">
                            <input type="hidden" name="basket_item_id" value="<?php echo $item['id']; ?>">
                            <button type="submit" class="btn">Remove</button>
                        </form>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>
        <div class="basket-summary">
            <form action="" method="POST">
                <input type="hidden" name="clear_basket" value="1">
                <button type="submit" class="btn">Clear Basket</button>
            </form>
        </div>
        <a class="padding" href="checkout.php">Proceed to Checkout</a>
    </section>
</body>
</html>