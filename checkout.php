<?php
session_start();
include_once __DIR__ . '/classes/Db.php';
include_once __DIR__ . '/classes/Basket.php';
include_once __DIR__ . '/classes/BasketItem.php';
include_once __DIR__ . '/classes/Order.php';
include_once __DIR__ . '/classes/OrderItem.php';
include_once __DIR__ . '/classes/User.php';
include_once __DIR__ . '/classes/Product.php';

// Check if user is logged in
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['checkout_form_submitted'])) {
    if (isset($_POST['street_name']) && isset($_POST['city']) && isset($_POST['number']) && isset($_POST['postal_code']) && isset($_POST['payment_method'])) {
        $streetName = htmlspecialchars(trim($_POST['street_name']), ENT_QUOTES, 'UTF-8');
        $city = htmlspecialchars(trim($_POST['city']), ENT_QUOTES, 'UTF-8');
        $number = htmlspecialchars(trim($_POST['number']), ENT_QUOTES, 'UTF-8');
        $postalCode = htmlspecialchars(trim($_POST['postal_code']), ENT_QUOTES, 'UTF-8');
        $paymentMethod = htmlspecialchars(trim($_POST['payment_method']), ENT_QUOTES, 'UTF-8');

        // Join the address components into a single string
        $address = "$streetName $number, $postalCode $city";

        try {
            Order::processCheckout($_SESSION['user']['id'], $address, $paymentMethod);
            header('Location: success.php');
            exit();
        } catch (Exception $e) {
            die('An error occurred during checkout: ' . $e->getMessage());
        }
    } else {
        echo "Please fill in all required fields.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - Plantwerp Webshop</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <?php include 'classes/Nav.php'; ?>

    <section class="checkout-container">
        <h2>Checkout</h2>
        <form action="checkout.php" method="POST">
            <input type="hidden" name="checkout_form_submitted" value="1">
            <div class="form-group">
                <label for="street_name">Straatnaam</label>
                <input type="text" id="street_name" name="street_name" placeholder="Enter your street name" required>
            </div>
            <div class="form-group">
                <label for="number">Huisnummer</label>
                <input type="number" id="number" name="number" placeholder="Enter your house number" required>
            </div>
            <div class="form-group">
                <label for="postal_code">Post code</label>
                <input type="number" id="postal_code" name="postal_code" placeholder="Enter your postal code" required>
            </div>
            <div class="form-group">
                <label for="city">Stad</label>
                <input type="text" id="city" name="city" placeholder="Enter your city" required>
            </div>
            <div class="form-group">
                <label for="payment-method">Betaal Methode</label>
                <select id="payment-method" name="payment_method" required>
                    <option value="credit_card">Credit Card</option>
                    <option value="paypal">PayPal</option>
                    <option value="bank_transfer">Bank Transfer</option>
                </select>
            </div>
            <div class="form-group">
                <button type="submit" class="btn">Place Order</button>
            </div>
        </form>
    </section>
</body>

</html>