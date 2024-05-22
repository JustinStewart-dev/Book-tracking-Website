<?php
// Config file to connect to the database
require_once 'config.php';

session_start();

// Check if user is already logged in
if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true)
{
    header("location: welcome.php");
    exit;
}

// Define variables and initialize with empty values
$email = "";
$username = "";
$password = "";

$email_err = "";
$username_err = "";
$password_err = "";


if($_SERVER["REQUEST_METHOD"] == "POST")
{
 
    // Validate email
    if(empty(trim($_POST["email"])))
    {
        $email_err = "Please enter an email.";
    }
    else
    {
        $check_email = trim($_POST["email"]);
        $sql_check_email = "SELECT User_ID FROM users WHERE Email = '$check_email'";
        $result = mysqli_query($conn, $sql_check_email);

        if(mysqli_num_rows($result) > 0)
        {
            $email_err = "This email is already in use.";
        }
        else
        {
            $email = $check_email;
        }
    }
    
    // Validate username
    if(empty(trim($_POST["username"])))
    {
        $username_err = "Please enter a username.";
    }
    else
    {
        $check_username = trim($_POST["username"]);
        $sql_check_name = "SELECT User_ID FROM users WHERE Username = '$check_username'";
        $result = mysqli_query($conn, $sql_check_name);

        if(mysqli_num_rows($result) > 0)
        {
            $username_err = "This username is already taken.";
        }
        else
        {
            $username = $check_username;
        }
    }
    
    // Validate password
    if(empty(trim($_POST["password"])))
    {
        $password_err = "Please enter a password.";
    }
    elseif(strlen(trim($_POST["password"])) < 6){
        $password_err = "Password must have at least 6 characters.";
    }
    else
    {
        $password = trim($_POST["password"]);
    }
    
    // Check input errors before inserting into database
    if(empty($email_err) && empty($username_err) && empty($password_err))
    {
        $email = mysqli_real_escape_string($conn, $email);
        $username = mysqli_real_escape_string($conn, $username);
        $password = mysqli_real_escape_string($conn, $password);
        
        // Hash the password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        // Insert user data into database
        $sql = "INSERT INTO users (Email, Username, Password) VALUES ('$email', '$username', '$hashed_password')";
        
        if(mysqli_query($conn, $sql))
        {
            // Redirect to login page
            header("location: login.php");
        }
        else
        {
            echo "Something went wrong. Please try again later.";
        }
    }
    
    // Close connection
    mysqli_close($conn);
}
?>

 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sign Up</title>
    <link rel="stylesheet" href="/stylesheets/styles.css">
</head>

<body>
    <div class="signup">
        <h2>Sign Up</h2>

        <p>Please fill this form to create an account.</p>

        <form action="index.php" method="post">
            <div>
                <label>Email</label>
                <input type="text" name="email" value="<?php echo $email; ?>">
                <div class="error"><?php echo $email_err; ?></div>
            </div>  

            <div>
                <label>Username</label>
                <input type="text" name="username" value="<?php echo $username; ?>">
                <div class="error"><?php echo $username_err; ?></div>
            </div>

            <div>
                <label>Password</label>
                <input type="password" name="password">
                <div class="error"><?php echo $password_err; ?></div>
            </div>

            <div>
                <input type="submit" value="Submit">
            </div>

            <p>Already have an account? <a href="login.php">Login here</a></p>
        </form>
    </div>    
</body>
</html>
