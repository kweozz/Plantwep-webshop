<?php

session_start();



include_once(__DIR__ . '/classes/Db.php');
include_once(__DIR__ . '/classes/User.php');

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
    // Redirect als je niet ingloggd bent
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
                echo "Password changed successfully!";
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
</head>

<body>

<nav> 
        <a href="index.html"><img class="logo" src="images/logo-plantwerp.png" alt="Plantwerp Logo"></a>
        <div class="nav-items">
            <input type="text" placeholder="Zoek naar planten..." class="search-bar">
            <a href="profile.php" class="icon profile-icon" aria-label="Profiel">
                <i class="fas fa-user"></i> <!-- Profiel icoon -->
            </a>
            <a href="#" class="icon basket-icon" aria-label="Winkelmand">
                <i class="fas fa-shopping-basket"></i> <!-- Winkelmand icoon -->
            </a>
        </div>
</nav>
    <div class="profile-container">
    <h1>Welkom, <?php echo htmlspecialchars($user->getFirstname()); ?>!</h1>
    
    </div>
    <div class="profile-info">
        <div class="profile-details">
            <h2>Profielgegevens</h2>
            <p>Voornaam: <?php echo htmlspecialchars($user->getFirstname()); ?></p>
            <p>Achternaam: <?php echo htmlspecialchars($user->getLastname()); ?></p>
            <p>Email: <?php echo htmlspecialchars($user->getEmail()); ?></p>
    </div>

        <section class="profile-actions">
            <!-- Wachtwoord wijzigen -->
            <form action="profile.php" method="POST" class="change-password-form">
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

            <!-- Uitloggen -->
            <form action="profile.php" method="POST" class="logout-form">
                <br>
                <button type="submit" name="logout" class="logout-btn btn">Uitloggen</butto>
            </form>
        </section>

        <!-- Bestellingen -->
        <section class="profile-orders">
            <h2>Bestellingen</h2>
            <p>Er zijn geen bestellingen gevonden.</p>
    </div>
    </section>

</body>

</html>
