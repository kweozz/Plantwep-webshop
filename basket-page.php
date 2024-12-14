<?php
include_once __DIR__ . '/classes/Db.php';
include_once __DIR__ . '/classes/Basket.php';
include_once __DIR__ . '/classes/BasketItem.php';
include_once __DIR__ . '/classes/Product.php';
include_once __DIR__ . '/classes/Option.php';

// Start session
session_start();

if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit();
}

$userId = $_SESSION['user']['id'];
$basket = Basket::getBasket($userId);
$totalItems = 0;

if ($basket) {
    $totalItems = BasketItem::getTotalItems($basket['id']);
}

if (!$basket) {
    die('Basket not found.');
}

function deleteBasketItem($basketItemId)
{
    BasketItem::removeItemFromBasket($basketItemId);
    header('Location: basket-page.php?removed=1');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_item'])) {
    $basketItemId = $_POST['basket_item_id'];
    deleteBasketItem($basketItemId);
}

$basketItems = BasketItem::getItemsByBasketId($basket['id']);
$totalPrice = 0;

$clearBasketMessage = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['clear_basket'])) {
    if (empty($basketItems)) {
        $clearBasketMessage = '<p class="alert-danger">Mandje kan niet worden leeggemaakt, het is al leeg.</p>';
    } else {
        BasketItem::clearBasket($basket['id']);
        $clearBasketMessage = '<p class="alert-success">Mandje succesvol leeggemaakt.</p>';
        header('Location: basket-page.php?cleared=1');
        exit();
    }
}

if (isset($_GET['cleared']) && $_GET['cleared'] == 1) {
    $clearBasketMessage = '<p class="alert-success">Mandje succesvol leeggemaakt.</p>';
}

$removeItemMessage = '';
if (isset($_GET['removed']) && $_GET['removed'] == 1) {
    $removeItemMessage = '<p class="alert-success">Product succesvol verwijderd.</p>';
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Basket - Plantwerp Webshop</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <?php include 'classes/Nav.php'; ?>

    <section class="basket-container">
        <form action="" method="POST">
            <input type="hidden" name="clear_basket" value="1">
            <div class="basket-header">
                <h2>Uw winkelmandje</h2>
               <button class="remove" type="submit" class="icon-btn"><i class="fas fa-trash-alt"></i> <p>Leeg mandje</p></button>
            </div>
        </form>
        <?php echo $clearBasketMessage; ?>
        <?php echo $removeItemMessage; ?>
        <ul class="basket-list">
            <?php foreach ($basketItems as $item): ?>
                <?php
                $product = Product::getById($item['product_id']);
                $totalPrice += $item['total_price'];
                $options = json_decode($item['option_ids'], true); // Assuming options are stored as JSON
                ?>
                <li class="basket-item">
                    <img src="<?php echo $product['image']; ?>" alt="<?php echo htmlspecialchars($product['name']); ?>"
                        class="product-image-basket">
                    <div class="basket-item-info">
                        <h4 class="product-name"><?php echo htmlspecialchars($product['name']); ?></h4>
                        <p class="product-quantity">Aantal: <?php echo $item['quantity']; ?></p>
                        <?php if (!empty($options)): ?>
                            <?php foreach ($options as $optionId):
                                $option = BasketItem::getOptionById($optionId); // Assuming you have a method to get option by ID in BasketItem class
                                ?>
                                <p>Option: <?php echo htmlspecialchars($option['name']); ?></p>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                    <div class="basket-info">
                        <div class="basket-item-actions">
                            <form action="" method="POST">
                                <input type="hidden" name="basket_item_id" value="<?php echo $item['id']; ?>">
                                <button class="delete-btn" type="submit" name="delete_item" aria-label="Remove">
                                    <i class="fas fa-times-circle"></i>
                                </button>
                            </form>
                        </div>
                        <p class="product-price">€<?php echo number_format($item['total_price'], 2); ?></p>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>
        <div class="basket-summary">
            <h2 class="total-price">Total: €<?php echo number_format($totalPrice, 2); ?></h2>
            <div class="redirect">
                <a href="index.php" class="btn">Continue Shopping</a>
                <?php if ($totalPrice > 0): ?>
                    <form action="checkout.php" method="POST">
                        <button type="submit" class="btn">Proceed to Checkout</button>
                    </form>
                <?php else: ?>
                    <p class="alert-danger basket-alert">Your basket is empty. Add items to your basket to proceed to checkout.</p>
                <?php endif; ?>
            </div>
        </div>
    </section>
</body>

</html>