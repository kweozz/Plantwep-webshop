<?php
session_start();
include_once __DIR__ . '/classes/Db.php';
include_once __DIR__ . '/classes/Basket.php';
include_once __DIR__ . '/classes/BasketItem.php';
include_once __DIR__ . '/classes/Option.php';

if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit();
}

// Haal gegevens op uit het formulier
$userId = $_SESSION['user']['id'];
$productId = intval($_POST['product_id']);
$productPrice = floatval($_POST['product_price']);
$quantity = intval($_POST['quantity']);
$options = isset($_POST['options']) ? $_POST['options'] : [];

// Valideer opties (zorg dat deze een array zijn)
if (!is_array($options)) {
    $options = [];
}

// Krijg of maak de winkelmand van de gebruiker
$basket = Basket::getBasket($userId);
if (!$basket) {
    Basket::create($userId);
    $basket = Basket::getBasket($userId);
}
$basketId = $basket['id'];

// Voeg het product met opties toe aan de winkelmand
$totalPrice = $productPrice * $quantity;
if (!empty($options)) {
    foreach ($options as $option) {
        if (isset($option['id']) && isset($option['price_addition'])) {
            $optionId = intval($option['id']);
            $priceAddition = floatval($option['price_addition']);
            $totalPrice += $priceAddition * $quantity;
            BasketItem::createBasketItem($basketId, $productId, $quantity, $productPrice, $optionId, $priceAddition, $totalPrice);
        }
    }
} else {
    // Voeg het product zonder opties toe
    BasketItem::createBasketItem($basketId, $productId, $quantity, $productPrice, null, 0, $totalPrice);
}

// Redirect naar de winkelmand
header('Location: basket-page.php');
exit();
?>
