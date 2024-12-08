<?php

session_start();

include_once __DIR__ . '/classes/Db.php';
include_once __DIR__ . '/classes/User.php';
include_once __DIR__ . '/classes/Order.php';
include_once __DIR__ . '/classes/OrderItem.php';
include_once __DIR__ . '/classes/Product.php';



// Controleer of de gebruiker is ingelogd
if (isset($_SESSION['user'])) {
    // haaal de user data op uit de sessie
    $userData = $_SESSION['user'];

    // maak een nieuwe User obj
    $user = new User();
    $user->setFirstname($userData['firstname']);
    $user->setLastname($userData['lastname']);
    $user->setEmail($userData['email']);
} else {
    // Redirect als je niet ingelogd bent
    header("Location: login.php");
    exit;
}

if (isset($_POST["change_password"])) {
    // Check if all fields are filled
    if (empty($_POST["current_password"]) || empty($_POST["new_password"]) || empty($_POST["confirm_password"])) {
        $error = "All fields are required!";
    } else {
        // Check of het nieuwe wachtwoord en de bevestiging overeenkomen 
        if ($_POST["new_password"] !== $_POST["confirm_password"]) {
            $error = "New password and confirmation do not match!";
        } else {
            try {
                $user->changePassword($_POST["current_password"], $_POST["new_password"]);
                $success = "Password changed successfully!";  // Set success message here
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

    <nav>
        <a href="index.php"><img class="logo" src="images/logo-plantwerp.png" alt="Plantwerp Logo"></a>
        <input type="text" placeholder="Zoek naar planten..." class="search-bar">
        <div class="nav-items">
            <!-- Profiel -->
            <a href="profile.php" class="icon profile-icon" aria-label="Profiel">
                <i class="fas fa-user"></i>
            </a>

            <!-- Winkelmand -->
            <a href="#" class="icon basket-icon" aria-label="Winkelmand">
                <i class="fas fa-shopping-basket"></i>
            </a>

            <!-- Admin Dashboard (zichtbaar alleen voor admins) -->
            <?php if (isset($_SESSION['role']) && $_SESSION['role'] == 1): ?>
                <a href="admin-dash.php" class="icon admin-icon" aria-label="Admin Dashboard">
                    <i class="fas fa-tools"></i>
                </a>
            <?php endif; ?>

            <!-- Currency -->
            <?php if (isset($_SESSION['user']['currency'])): ?>
                <span class="currency-display">
                    <i class="fas fa-coins"></i> <!-- Icoon voor currency -->
                    <?php echo htmlspecialchars($_SESSION['user']['currency']); ?>
                </span>
            <?php endif; ?>
        </div>
    </nav>




    <h1 class="padding">Welkom, <?php echo htmlspecialchars($user->getFirstname()); ?>!</h1>


    <div class="profile-info">
        <div class="profile-details">
            <h2>Profielgegevens</h2>
            <p><span>Voornaam:</span> <?php echo htmlspecialchars($user->getFirstname()); ?></p>
            <p><span>Achternaam:</span> <?php echo htmlspecialchars($user->getLastname()); ?></p>
            <p><span> Email:</span> <?php echo htmlspecialchars($user->getEmail()); ?></p>
            <p> <span>Saldo:</span> <?php echo htmlspecialchars($_SESSION['currency']); ?> units</p>
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

        <?php
        // Haal bestellingen op voor de ingelogde gebruiker
        $orders = Order::getByUserId($userData['id']);


        ?>

        <section class="profile-orders ">
            <h2>Bestellingen</h2>
            <?php if (!empty($orders)): ?>
                <ul class="basket-list">
                    <?php foreach ($orders as $order): ?>
                        <?php $orderItems = OrderItem::getByOrderId($order['id']); ?>
                        <?php foreach ($orderItems as $orderItem): ?>
                            <?php $product = Product::getById($orderItem['product_id']); ?>
                            <li class="basket-item">
                                <img class="product-image-basket" src="<?php echo $product['image']; ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                                <p class="product-quantity">Datum: <?php echo htmlspecialchars($order['created_at']); ?></p>
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
        </section>
        <!-- Uitloggen -->
        <form action="profile.php" method="POST" class="logout-form form-group">
            <h2>Uitloggen</h2>
            <script>
                document.querySelector('.logout-form').addEventListener('submit', function (event) {
                    if (!confirm('Weet je zeker dat je wilt uitloggen?')) {
                        event.preventDefault();
                    }
                });
            </script>
            <button type="submit" name="logout" class="logout-btn btn">Uitloggen</button>
        </form>


        </div>

</body>

</html>