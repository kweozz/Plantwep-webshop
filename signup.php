<?php
// Include the User class
include_once(__DIR__ . '/classes/user.php');
if (!empty($_POST)) {
    try {
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
        // Save the user
        $user->save();
        // Redirect to the login page
        $succes = "Account created successfully!";
        header('Location: login.php');
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}
$users = User::getAll();


?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Signup - Plantwerp</title>
    <link rel="stylesheet" href="form.css">
</head>


<body>
    <?php if (isset($error)): ?>
        <div class="alert alert-danger" role="alert">
            <?php echo $error; ?>
        </div>
    <?php endif; ?>
    <?php if (isset($succes)): ?>
        <div class="alert alert-success" role="alert">
            <?php echo $succes; ?>
        </div>
    <?php endif; ?>
    <div class="signup-container">
        <a href="index.html"><img class="logo" src="images/logo-plantwerp.png" alt="Plantwerp Logo"></a>
        <h2>Create an Account</h2>
        <form action="signup.php" method="POST">
            <div class="form-group">
                <label for="firstname">First Name</label>
                <input type="text" id="firstname" name="firstname" placeholder="Enter your first name" required>
            </div>
            <div class="form-group">
                <label for="lastname">Last Name</label>
                <input type="text" id="lastname" name="lastname" placeholder="Enter your last name" required>
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" placeholder="Enter your email" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="Enter your password" required>
            </div>
            <button type="submit" class="signup-btn">Sign Up</button>
        </form>
        <p>Already have an account? <a href="login.html">Login</a></p>
    </div>

</body>

</html>