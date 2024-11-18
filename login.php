<?php
include_once(__DIR__ . '/classes/Db.php');
include_once(__DIR__ . '/classes/User.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    try {
        $user = User::login($email, $password);

        // Start session and store user details
        session_start();
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_firstname'] = $user['firstname'];

        // Redirect to the homepage or dashboard
        header("Location: index.html");
        exit;
    } catch (Exception $e) {
        $error = $e->getMessage();
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
        <p>Don’t have an account? <a href="signup.html">Sign up</a></p>
    </div>
</body>

</html>