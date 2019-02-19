<?php
// Include necessary file
include_once './includes/db.inc.php';

// Check if user is not logged in
if (!$user->is_logged_in()) {
    $user->redirect('index.php');
}

try {
    // Define query to select values from the users table
    $sql = "SELECT * FROM users WHERE user_id=:user_id";

    // Prepare the statement
    $query = $db_conn->prepare($sql);

    // Bind the parameters
    $query->bindParam(':user_id', $_SESSION['user_session']);

    // Execute the query
    $query->execute();

    // Return row as an array indexed by both column name
    $returned_row = $query->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    array_push($errors, $e->getMessage());
}

if (isset($_GET['logout']) && ($_GET['logout'] == 'true')) {
    $user->log_out();
    $user->redirect('index.php');
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>OOP PHP - Home</title>
</head>
<body>
    <h1>Home</h1>

    <?php if (count($errors > 0)): ?>
    <p>Error(s):</p>
    <ul>
        <?php foreach ($errors as $error): ?>
            <li><?= $error ?></li>
        <?php endforeach ?>
    </ul>
    <?php endif ?>

    <p>Welcome, <?= $returned_row['user_name']; ?>. <a href="?logout=true">Log out</a></p>
</body>
</html>