<?php
// Include necessary file
require_once('./includes/db.inc.php');

// Check if user is already logged in
if ($user->is_logged_in()) {
    // Redirect logged in user to their home page
    $user->redirect('home.php');
}

// Check if log-in form is submitted
if (isset($_POST['log_in'])) {
    // Retrieve form input
    $user_name = trim($_POST['user_name_email']);
    $user_email = trim($_POST['user_name_email']);
    $user_password = trim($_POST['user_password']);

    // Check for empty and invalid inputs
    if (empty($user_name) || empty($user_email)) {
        array_push($errors, "Please enter a valid username or e-mail address");
    } elseif (empty($user_password)) {
        array_push($errors, "Please enter a valid password.");
    } else {
        // Check if the user may be logged in
        if ($user->login($user_name, $user_email, $user_password)) {
            // Redirect if logged in successfully
            $user->redirect('home.php');
        } else {
            array_push($errors, "Incorrect log-in credentials.");
        }
    }
}

// Check if register form is submitted
if (isset($_POST['register'])) {
    // Retrieve form input
    $user_name = trim($_POST['user_name']);
    $user_email = trim($_POST['user_email']);
    $user_password = trim($_POST['user_password']);

    // Check for empty and invalid inputs
    if (empty($user_name)) {
        array_push($errors, "Please enter a valid username.");
    } elseif (empty($user_email)) {
        array_push($errors, "Please enter a valid e-mail address.");
    } elseif (empty($user_password)) {
        array_push($errors, "Please enter a valid password.");
    } elseif (!filter_var($user_email, FILTER_VALIDATE_EMAIL)) {
        array_push($errors, "Please enter a valid e-mail address.");
    } else {
        try {
            // Define query to select matching values
            $sql = "SELECT user_name, user_email FROM users WHERE user_name=:user_name OR user_email=:user_email";

            // Prepare the statement
            $query = $db_conn->prepare($sql);

            // Bind parameters
            $query->bindParam(':user_name', $user_name);
            $query->bindParam(':user_email', $user_email);

            // Execute the query
            $query->execute();

            // Return clashes row as an array indexed by both column name
            $returned_clashes_row = $query->fetch(PDO::FETCH_ASSOC);

            // Check for usernames or e-mail addresses that have already been used
            if ($returned_clashes_row['user_name'] == $user_name) {
                array_push($errors, "That username is taken. Please choose something different.");
            } elseif ($returned_clashes_row['user_email'] == $user_email) {
                array_push($errors, "That e-mail address is taken. Please choose something different.");
            } else {
                // Check if the user may be registered
                if ($user->register($user_name, $user_email, $user_password)) {
                    echo "Registered";
                }
            }
        } catch (PDOException $e) {
            array_push($errors, $e->getMessage());
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>OOP PHP - Login and Register</title>
</head>
<body>
    <h1>Welcome</h1>

    <?php if (count($errors > 0)): ?>
    <p>Error(s):</p>
    <ul>
        <?php foreach ($errors as $error): ?>
            <li><?= $error ?></li>
        <?php endforeach ?>
    </ul>
    <?php endif ?>

    <!-- Log in -->
    <h2>Log in</h2>
    <form action="index.php" method="POST">
        <label for="user_name_email">Username or E-mail Address:</label>
        <input type="text" name="user_name_email" id="user_name_email" required>

        <label for="user_password_log_in">Password:</label>
        <input type="password" name="user_password" id="user_password_log_in" required>

        <input type="submit" name="log_in" value="Log in">
    </form>

    <!-- Register -->
    <h2>Register</h2>
    <form action="index.php" method="POST">
        <label for="user_name">Username:</label>
        <input type="text" name="user_name" id="user_name" required>

        <label for="user_email">E-mail Address:</label>
        <input type="email" name="user_email" id="user_email" required>

        <label for="user_password">Password:</label>
        <input type="password" name="user_password" id="user_password" required>

        <input type="submit" name="register" value="Register">
    </form>
</body>
</html>