<?php
class User
{
    // Refer to database connection
    private $db;

    // Instantiate object with database connection
    public function __construct($db_conn)
    {
        $this->db = $db_conn;
    }

    // Register new users
    public function register($user_name, $user_email, $user_password)
    {
        try {
            // Hash password
            $user_hashed_password = password_hash($user_password, PASSWORD_DEFAULT);

            // Define query to insert values into the users table
            $sql = "INSERT INTO users(user_name, user_email, user_password) VALUES(:user_name, :user_email, :user_password)";

            // Prepare the statement
            $query = $this->db->prepare($sql);

            // Bind parameters
            $query->bindParam(":user_name", $user_name);
            $query->bindParam(":user_email", $user_email);
            $query->bindParam(":user_password", $user_hashed_password);

            // Execute the query
            $query->execute();
        } catch (PDOException $e) {
            array_push($errors, $e->getMessage());
        }
    }

    // Log in registered users with either their username or email and their password
    public function login($user_name, $user_email, $user_password)
    {
        try {
            // Define query to insert values into the users table
            $sql = "SELECT * FROM users WHERE user_name=:user_name OR user_email=:user_email LIMIT 1";

            // Prepare the statement
            $query = $this->db->prepare($sql);

            // Bind parameters
            $query->bindParam(":user_name", $user_name);
            $query->bindParam(":user_email", $user_email);

            // Execute the query
            $query->execute();

            // Return row as an array indexed by both column name
            $returned_row = $query->fetch(PDO::FETCH_ASSOC);

            // Check if row is actually returned
            if ($query->rowCount() > 0) {
                // Verify hashed password against entered password
                if (password_verify($user_password, $returned_row['user_password'])) {
                    // Define session on successful login
                    $_SESSION['user_session'] = $returned_row['user_id'];
                    return true;
                } else {
                    // Define failure
                    return false;
                }
            }
        } catch (PDOException $e) {
            array_push($errors, $e->getMessage());
        }
    }

    // Check if the user is already logged in
    public function is_logged_in() {
        // Check if user session has been set
        if (isset($_SESSION['user_session'])) {
            return true;
        }
    }

    // Redirect user
    public function redirect($url) {
        header("Location: $url");
    }

    // Log out user
    public function log_out() {
        // Destroy and unset active session
        session_destroy();
        unset($_SESSION['user_session']);
        return true;
    }
}

?>