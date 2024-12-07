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

// Bereken de totale prijs op basis van opties
$basePrice = $productPrice; // Basisprijs van het product
$optionPriceAdditions = 0.0; // Houd de prijsverhogingen bij
$selectedOptionIds = [];

foreach ($options as $optionId => $option) {
    if (isset($option['price_addition']) && isset($option['id'])) {
        $priceAddition = floatval($option['price_addition']);
        $optionPriceAdditions += $priceAddition;
        $selectedOptionIds[] = intval($option['id']);
    }
}

$totalPrice = ($basePrice + $optionPriceAdditions) * $quantity; // Bereken de totale prijs

// Debugging: controleer berekende prijzen
var_dump($totalPrice, $optionPriceAdditions);

// Krijg of maak de winkelmand van de gebruiker
$basket = Basket::getBasket($userId);
if (!$basket) {
    Basket::create($userId);
    $basket = Basket::getBasket($userId);
}
$basketId = $basket['id'];

// Voeg het product met opties toe aan de winkelmand
BasketItem::createBasketItem($basketId, $productId, $quantity, $productPrice, json_encode($selectedOptionIds), $optionPriceAdditions, $totalPrice);

// Redirect naar de winkelmand
header('Location: basket-page.php');
exit();
?>
