<?php
require_once 'config.php';

session_start();

if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true)
{
    header("location: welcome.php");
    exit;
}

$username = "";
$password = "";

$username_err = "";
$password_err = "";

if($_SERVER["REQUEST_METHOD"] == "POST")
{
    if(empty(trim($_POST["username"])))
    {
        $username_err = "Please enter username.";
    }
    else
    {
        $username = trim($_POST["username"]);
    }
    
    if(empty(trim($_POST["password"])))
    {
        $password_err = "Please enter your password.";
    }
    else
    {
        $password = trim($_POST["password"]);
    }
    
    if(empty($username_err) && empty($password_err))
    {
        $username = mysqli_real_escape_string($conn, $username);
        $password = mysqli_real_escape_string($conn, $password);

        $sql = "SELECT User_ID, Username, Password FROM users WHERE Username = '$username'";
        
        $result = mysqli_query($conn, $sql);
        
        if($result)
        {
            if(mysqli_num_rows($result) == 1)
            {
                $row = mysqli_fetch_assoc($result);

                if(password_verify($password, $row['Password']))
                {
                    session_start();
                    
                    $_SESSION["loggedin"] = true;
                    $_SESSION["user_id"] = $row['User_ID'];
                    $_SESSION["username"] = $row['Username'];                            

                    header("location: welcome.php");
                }
                else
                {
                    $password_err = "The password you entered was not valid.";
                }
            }
            else
            {
                $username_err = "No account found with that username.";
            }
        }
        else
        {
            echo "Oops! Something went wrong. Please try again later.";
        }
    }

    mysqli_close($conn);
}
?>
 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="stylesheet" href="/stylesheets/styles.css">
</head>
<body>
    <div class="login">
        <h2>Login</h2>
        <p>Please fill in your credentials to login.</p>
        <form action="login.php" method="post">
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
                <input type="submit" value="Login">
            </div>
            <p>Don't have an account? <a href="index.php">Sign up now</a></p>
        </form>
    </div>    
</body>
</html>
