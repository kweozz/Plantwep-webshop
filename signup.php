<?php
// Include the User class
include_once(__DIR__ . '/classes/user.php');

if (!empty($_POST)) {
    try {
        // Check if the account already exists by email
        if (User::exists($_POST['email'])) {
            // If the account exists, show the feedback message
            $error = 'Account with this email already exists!';

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
            // Save the user
            $user->save();
            // Success message
            $success = "Account created successfully!";
            // Redirect to the login page
            header('Location: login.php');
            exit(); // Don't forget to stop further execution after redirect
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
    <link rel="stylesheet" href="form.css">
</head>

<body>
    <div class="signup-container">
        <a href="index.html"><img class="logo" src="images/logo-plantwerp.png" alt="Plantwerp Logo"></a>
        <h2>Create an Account</h2>

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
        <p>Already have an account? <a href="login.php">Login</a></p>
    </div>
</body>

</html>