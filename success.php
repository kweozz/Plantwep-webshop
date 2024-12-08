<?php
session_start();
include_once __DIR__ . '/classes/Order.php';
include_once __DIR__ . '/classes/OrderItem.php';
include_once __DIR__ . '/classes/Product.php';

// Get the last order for the user
$userId = $_SESSION['user']['id'];
$orders = Order::getByUserId($userId);
$lastOrder = $orders[0];
$orderItems = OrderItem::getByOrderId($lastOrder['id']);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Successful</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <div class="success-container">
        <div class="success-icon">
            <!-- Voeg een icoon of afbeelding toe als nodig -->
            <i class="icon">✔</i>
        </div>
        <h1 class="success-title">Succesvol!</h1>
        <p class="success-message">Je bestelling is succesvol geplaatst. Bedankt voor je aankoop!</p>
        <div class="success-actions">
           
            <div class="order-summary">
                <h3>Order Summary</h3>
                <ul class="order-items">
             
                </ul>
                <div class="order-total">
                    <p>Total Price: €<?php echo number_format($lastOrder['total_price'], 2); ?></p>
                </div>
                <a href="index.php" class="btn">Ga naar de homepage</a>
            </div>
        </div>
    </div>




</body>

</html>