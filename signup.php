<?php
// Include the User class
include_once(__DIR__ . '/classes/User.php');
include_once(__DIR__ . '/classes/Basket.php');

if (!empty($_POST)) {
    try {
        // Check if the account already exists by email
        if (User::exists($_POST['email'])) {
            // If the account exists, show the feedback message
            $error = 'Account bestaat al!';
        } else {
            // Create a new User instance
            $user = new User();
            // Set the firstname
            $user->setFirstname($_POST['firstname']);
            // Set the lastname
            $user->setLastname($_POST['lastname']);
            // Set the email
            $user->setEmail($_POST['email']);
            // Set the password
            $user->setPassword($_POST['password']);

            // set the currency
            $user->setCurrency(1000);
            // Save the user
            $user->save();
            // Create a new basket for the user
            $basket = new Basket();
            $basket->setUserId($user->id);
            $basket->save();

            // Success message
            $success = "Account is aangemaakt!";

        }
    } catch (Exception $e) {
        $error = $e->getMessage(); // Catch and display any errors
    }
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Signup - Plantwerp</title>
    <link rel="stylesheet" href="css/login.css">
</head>

<body>
    <div class="signup-container">
        <a href="index.php"><img class="logo" src="images/logo-plantwerp.png" alt="Plantwerp Logo"></a>
        <h2>Maak een account</h2>

        <!-- Display the error or success  -->
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

        <form action="signup.php" method="POST">
            <div class="form-group">
                <label for="firstname">Voornaam</label>
                <input type="text" id="firstname" name="firstname" placeholder="Vul je voornaam in" required>
            </div>
            <div class="form-group">
                <label for="lastname">Achternaam</label>
                <input type="text" id="lastname" name="lastname" placeholder="Vul je achternaam in" required>
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" placeholder="Vul je email in" required>
            </div>
            <div class="form-group">
                <label for="password">Wachtwoord</label>
                <input type="password" id="password" name="password" placeholder="Vul je wachtwoord in" required>
            </div>
            <button type="submit" class="signup-btn">Sign Up</button>
        </form>
        <p>Heb je al een account? <a href="login.php">Login</a></p>
    </div>
</body>

</html>