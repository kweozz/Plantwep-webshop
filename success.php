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
    <section class="success">
    <div class="success-container">
    <h1 class="success-title">Succesvol!</h1>
        <div class="success-image">
            <img src="images/checkout-image.png" alt="checkout bag image">
        </div>
        <h3 class="success-message">Je bestelling is succesvol geplaatst. Bedankt voor je aankoop!</h3>
        <p class="success-message">De geschatte levertijd naar <?php echo htmlspecialchars($lastOrder['address']);?> is 3-5 werkdagen.</p>

        <div class="success-actions">
            <a href="index.php" class="btn">Terug naar Plantwerp</a>
        </div>
        </div>
        </section>
    </body>

</html>