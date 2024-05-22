<?php
session_start();

// Check if the user is not logged in, redirect to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true)
{
    header("location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Welcome</title>
    <link rel="stylesheet" href="/stylesheets/styles.css">
</head>

<header>
    <h2>Welcome, <?php echo htmlspecialchars($_SESSION["username"]); ?>!</h2>

    <p>What would you like to do?</p>
</header>

<body>
    <div>
        <ul>
            <div class="welcomeNav">
                <li><a href="books.php">Select Books to Add to Reading List</a></li>
                <li><a href="status.php">View and Update Reading Status</a></li>
                <li><a href="review.php">Review a book</a></li>
                <li><a href="account.php">Account settings</a></li>
                <li><a href="feedback.php">Leave feedback</a></li>
            </div>
        </ul>
        
        <p><a href="logout.php" style="font-size: 30px; text-align: center;">Logout</a></p>
    </div>    
</body>
</html>
