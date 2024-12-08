<?php
session_start();
include_once __DIR__ . '/classes/Db.php';
include_once __DIR__ . '/classes/Basket.php';
include_once __DIR__ . '/classes/BasketItem.php';
include_once __DIR__ . '/classes/Order.php';
include_once __DIR__ . '/classes/OrderItem.php';
include_once __DIR__ . '/classes/User.php';

// Check if user is logged in
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit();
}

// Get the user's ID and balance
$userId = $_SESSION['user']['id'];
$currentCurrency = $_SESSION['user']['currency'];

// Get the user's basket
$basket = Basket::getBasket($userId);
if (!$basket) {
    die('Basket not found.');
}

// Get basket items
$basketItems = BasketItem::getItemsByBasketId($basket['id']);
if (empty($basketItems)) {
    die('Your basket is empty.');
}

// Calculate total price of the basket
$totalPrice = 0;
foreach ($basketItems as $item) {
    $totalPrice += $item['total_price'];
}

// Check if user has enough currency
if ($currentCurrency < $totalPrice) {
    die('You do not have enough digital currency to complete this purchase.');
}

try {
    // Start a database transaction
    $db = Db::getConnection();
    $db->beginTransaction();

    // Deduct total price from user's currency
    $newCurrency = $currentCurrency - $totalPrice;
    $updateCurrencyQuery = $db->prepare('UPDATE users SET currency = :currency WHERE id = :user_id');
    $updateCurrencyQuery->bindValue(':currency', $newCurrency, PDO::PARAM_INT);
    $updateCurrencyQuery->bindValue(':user_id', $userId, PDO::PARAM_INT);
    $updateCurrencyQuery->execute();

    // Save the order
    $order = new Order();
    $order->setUserId($userId);
    $order->setTotalPrice($totalPrice);
    $orderId = $order->save();

    // Save each item in the order
    foreach ($basketItems as $item) {
        $orderItem = new OrderItem();
        $orderItem->setOrderId($orderId);
        $orderItem->setProductId($item['product_id']);
        $orderItem->setQuantity($item['quantity']);
        $orderItem->setPrice($item['price']);
        $orderItem->setOptionIds($item['option_ids']);
        $orderItem->setPriceAddition($item['price_addition']);
        $orderItem->setTotalPrice($item['total_price']);
        $orderItem->save();
    }

    // Clear the user's basket
    BasketItem::clearBasket($basket['id']);

    // Commit the transaction
    $db->commit();

    // Update the user's session currency
    $_SESSION['user']['currency'] = $newCurrency;

    // Redirect to success page or confirmation
    header('Location: success.php');
    exit();
} catch (Exception $e) {
    // Rollback the transaction if an error occurs
    $db->rollBack();
    die('An error occurred during payment: ' . $e->getMessage());
}
?>
