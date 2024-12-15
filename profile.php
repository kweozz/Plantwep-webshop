<?php
session_start();
include_once __DIR__ . '/classes/Db.php';
include_once __DIR__ . '/classes/User.php';
include_once __DIR__ . '/classes/Order.php';
include_once __DIR__ . '/classes/OrderItem.php';
include_once __DIR__ . '/classes/Product.php';

// Check if the user is logged in
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit();
}

$user = User::getById($_SESSION['user']['id']);

// Fetch the last 3 orders
$orders = Order::getLastOrderByUserId($user->getId(), 3);

// Fetch all orders if requested
$allOrders = false;
if (isset($_GET['view_all_orders'])) {
    $orders = Order::getByUserId($user->getId());
    $allOrders = true;
}

if (isset($_POST["change_password"])) {
    // Check if all fields are filled
    if (empty($_POST["current_password"]) || empty($_POST["new_password"]) || empty($_POST["confirm_password"])) {
        $error = "Alle velden moeten ingevuld zijn!";
    } else {
        // Check of het nieuwe wachtwoord en de bevestiging overeenkomen 
        if ($_POST["new_password"] !== $_POST["confirm_password"]) {
            $error = "Nieuw passwoord en confirmatie matchen niet!";
        } else {
            try {
                $user->changePassword($_POST["current_password"], $_POST["new_password"]);
                $success = "Passwoord veranderd!";  // Set success message here
            } catch (Exception $e) {
                $error = $e->getMessage();
            }
        }
    }
}

//logout
if (isset($_POST["logout"])) {
    session_destroy();

    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profiel - Plantwerp</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>

<body>

<?php include 'classes/Nav.php'; ?>

    <h1 class="padding">Welkom, <?php echo htmlspecialchars($user->getFirstname()); ?>!</h1>

    <div class="profile-info">
        <div class="profile-details">
            <h2>Profielgegevens</h2>
            <p><span>Voornaam:</span> <?php echo htmlspecialchars($user->getFirstname()); ?></p>
            <p><span>Achternaam:</span> <?php echo htmlspecialchars($user->getLastname()); ?></p>
            <p><span> Email:</span> <?php echo htmlspecialchars($user->getEmail()); ?></p>
            <p> <span>Saldo:</span> € <?php echo htmlspecialchars($_SESSION['user']['currency']);?></p>
        </div>
    </div>

    <section class="profile-actions">
        <!-- Display error or success message above the form -->
        <?php if (isset($error)): ?>
            <div class="alert alert-danger" role="alert">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <?php if (isset($success)): ?>
            <div class="alert alert-success" role="alert">
                <?php echo $success; ?>
            </div>
        <?php endif; ?>

        <!-- Wachtwoord wijzigen -->
        <form action="profile.php" method="POST" class="change-password-form ">
            <h2>Wachtwoord wijzigen</h2>
            <div class="form-group">
                <label for="current_password">Huidig wachtwoord:</label>
                <input type="password" id="current_password" name="current_password" required>
            </div>
            <div class="form-group">
                <label for="new_password">Nieuw wachtwoord:</label>
                <input type="password" id="new_password" name="new_password" required>
            </div>
            <div class="form-group">
                <label for="confirm_password">Bevestig nieuw wachtwoord:</label>
                <input type="password" id="confirm_password" name="confirm_password" required>
            </div>
            <button type="submit" class="btn" name="change_password">Wijzig wachtwoord</button>
        </form>

        <section class="profile-orders ">
            <h2>Bestellingen</h2>
            <?php if (!empty($orders)): ?>
                <ul class="basket-list">
                    <?php foreach ($orders as $order): ?>
                        <?php $orderItems = OrderItem::getByOrderId($order['id']); ?>
                        <?php foreach ($orderItems as $orderItem): ?>
                            <?php $product = Product::getById($orderItem['product_id']); ?>
                            <h4 class="product-name"><?php echo htmlspecialchars($product['name']); ?></h4>
                            <li class="basket-item basket-item-profile">
                             
                                <img class="product-image-basket" src="<?php echo $product['image']; ?>"
                                    alt="<?php echo htmlspecialchars($product['name']); ?>">
                                <div class="basket-item-info">
                                    <p class="product-quantity">Aantal: <?php echo $orderItem['quantity']; ?></p>
                                    
                                    <p class="product-price">Prijs: €<?php echo number_format($orderItem['price'] + $orderItem['price_addition'], 2); ?></p>
                                </div>
                                <div class="basket-info">
                                    <p class="product-price">Totaal: €<?php echo htmlspecialchars($order['total_price']); ?></p>
                                </div>
                            </li>
                        <?php endforeach; ?>

                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p>Er zijn geen bestellingen gevonden.</p>
            <?php endif; ?>
            <?php if (!$allOrders): ?>
            <div><a href="profile.php?view_all_orders=true" class="btn">Bekijk alle bestellingen</a></div>
                
            <?php else: ?>
                <div><a href="profile.php" class="btn">Verberg alle bestellingen</a></div>
            <?php endif; ?>
        </section>
        <!-- Uitloggen -->
        <form action="profile.php" method="POST" class="logout-form form-group">
            <h2>Uitloggen</h2>
            <script src="script/profile.js" >
            </script>
            <button type="submit" name="logout" class="logout-btn btn">Uitloggen</button>
        </form>

        </div>

</body>

</html>