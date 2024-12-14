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
// If the form has been submitted, use process checkout function from order.php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['address']) && isset($_POST['payment_method'])) {
        $address = $_POST['address'];
        $paymentMethod = $_POST['payment_method'];
        
        $order = new Order();
        Order::processCheckout($_SESSION['user']['id'], $address, $paymentMethod);
        header('Location: success.php');
        exit();
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
            <div class="form-group">
                <label for="address">Delivery Address</label>
                <input type="text" id="address" name="address" placeholder="Enter your delivery address" required>
            </div>
            <div class="form-group">
                <label for="payment-method">Payment Method</label>
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