<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
    <title>Login Required</title>
</head>

<body>
    <?php include 'classes/Nav.php'; ?>

    <section class="login-prompt padding">
        <h2>U moet eerst inloggen of een account maken</h2>
        <p>Om verder te gaan, moet u inloggen of een account maken.</p>
        <div class="login-prompt-buttons">
            <a href="login.php" class="btn">Inloggen</a>
            <a href="signup.php" class="btn">Account maken</a>
        </div>
    </section>
</body>

</html>