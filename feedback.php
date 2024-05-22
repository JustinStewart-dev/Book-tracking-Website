<?php
session_start();

include_once "config.php";

$username = $_SESSION["username"];
$user_id = $_SESSION["user_id"];

$successMessage = "Feedback submitted successfully, redirecting to home page.";
$unsuccessMessage = "Something went wrong. Please try again later.";
$emptyMessage = "Comment cannot be empty.";

if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true)
{
    header("location: login.php");
    exit;
}


if($_SERVER["REQUEST_METHOD"] == "POST") 
{

    if(!empty($_POST["comment"])) 
    {
        $comment = mysqli_real_escape_string($conn, $_POST["comment"]);

        $sql = "INSERT INTO feedback (User_ID, Comment) VALUES ('$user_id', '$comment')";

        $result = mysqli_query($conn, $sql);

        if($result) 
        {
            echo "<script>alert('$successMessage'); window.location.href = 'welcome.php';</script>";
            exit();
        }
        else
        {
            echo "<script>alert('$unsuccessMessage'); window.location.href = 'welcome.php';</script>";
        }
    }
    else
    {
        echo "<script>alert('$emptyMessage');</script>";
    }
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
    <h2>Leave Feedback</h2>
    <a href="welcome.php">Back to home page</a>
</header>

<body>
    <h3>Leaving feedback from, <?php echo htmlspecialchars($username); ?></h3>
    
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        <div class="feedback-container">
            <textarea name="comment" rows="4" class="feedback-text"></textarea>
            <input type="hidden" name="user_id" value="<?php echo $user_id; ?>">
            <input type="submit" value="Submit">
        </div>
    </form>

</body>
</html>
