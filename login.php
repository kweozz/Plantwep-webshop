<?php
session_start(); // Start the session
include_once(__DIR__ . '/classes/Db.php');
include_once(__DIR__ . '/classes/User.php');

$error = ''; // Variable to hold error message

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the form data (sanitize and escape)
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $password = htmlspecialchars($_POST['password'], ENT_QUOTES, 'UTF-8');

    try {
        // Call the login method from the User class
        $loginSuccess = User::login($email, $password);

        if ($loginSuccess) {
            // Check user role and redirect accordingly
            if ($_SESSION['role'] == 'admin') {
                header('Location: admin-dash.php');
                exit();
            } else {
                // Customer role
                header('Location: index.php');
                exit();
            }
        } else {
            $error = 'Invalid email or password';
        }
    } catch (Exception $e) {
        error_log($e->getMessage()); // Log the error for debugging purposes
        $error = $e->getMessage(); // Display any error message
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Plantwerp</title>
    <link rel="stylesheet" href="form.css">
</head>
<body>
    <div class="signup-container">
        <a href="index.html"><img class="logo" src="images/logo-plantwerp.png" alt="Plantwerp Logo"></a>
        <h2>Login to Your Account</h2>
        <form action="login.php" method="POST" class="signup-form">
            <?php if (!empty($error)): ?>
                <p class="error"><?php echo htmlspecialchars($error); ?></p>
            <?php endif; ?>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" placeholder="Enter your email" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="Enter your password" required>
            </div>
            <button type="submit" class="signup-btn">Login</button>
        </form>
        <p>Don’t have an account? <a href="signup.php">Sign up</a></p>
    </div>
</body>
</html>
