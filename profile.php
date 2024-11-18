
<?php
session_start();

include_once(__DIR__ . '/classes/Db.php');
include_once(__DIR__ . '/classes/User.php');
//als er geeen user is ingelogd, redirect naar login.php
if (isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
// Haal de ingelogde gebruiker op
$user = User::getById($_SESSION['user_id']);


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Wachtwoord wijzigen
    if (isset($_POST['change_password'])) {
        $current_password = $_POST['current_password'];
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];

        if ($new_password === $confirm_password) {
            try {
                $user->changePassword($new_password);
                echo "Wachtwoord gewijzigd!";
            
            } catch (Exception $e) {
                echo $e->getMessage();
            }
        } else {
           echo"De wachtwoorden komen niet overeen.";
        }
    }
    // Uitloggen
    if (isset($_POST['logout'])) {
        session_destroy();
        header("Location: login.php");
        exit;
    }
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
    <div class="profile-container">
    <h1>Welkom, <?php echo htmlspecialchars($user->getFirstname()); ?>!</h1>
<p>Email: <?php echo htmlspecialchars($user->getEmail()); ?></p>

        

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
                <button type="submit" name="change_password">Wijzig wachtwoord</button>
            </form>

            <!-- Uitloggen -->
            <form action="profile.php" method="POST" class="logout-form">
                <button type="submit" name="logout" class="logout-btn">Uitloggen</button>
            </form>
        </section>

        <!-- Bestellingen -->
        <section class="profile-orders">
            <h2>Bestellingen</h2>
            <p>Er zijn geen bestellingen gevonden.</p>
    </div>
</body>

</html>
