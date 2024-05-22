<?php
session_start();
include_once "config.php";

$errorMessage = '';

$userGenres = array();
$userId = $_SESSION['user_id'];
$sql_user_genres = "SELECT Genre_ID FROM users_genre WHERE User_ID = $userId";
$result_user_genres = $conn->query($sql_user_genres);

if ($result_user_genres->num_rows > 0) 
{
    while ($row = $result_user_genres->fetch_assoc()) 
    {
        $userGenres[] = $row['Genre_ID'];
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") 
{
    $newUsername = isset($_POST['new_username']) ? $_POST['new_username'] : '';
    $newPassword = isset($_POST['new_password']) ? $_POST['new_password'] : '';
    $confirmPassword = isset($_POST['confirm_password']) ? $_POST['confirm_password'] : '';

    if (!empty($newUsername)) 
    {
        $sql_check_username = "SELECT User_ID FROM users WHERE Username = '$newUsername'";
        $result = mysqli_query($conn, $sql_check_username);
        if (mysqli_num_rows($result) > 0) 
        {
            $errorMessage = "This username is already taken.";
        }
        else 
        {

            $newUsername = mysqli_real_escape_string($conn, $newUsername);
        }
    }

    if (!empty($newPassword)) 
    {
        if (strlen($newPassword) < 6) 
        {
            $errorMessage = "Password must have at least 6 characters.";
        } 
        else if ($newPassword !== $confirmPassword) 
        {
            $errorMessage = "Passwords do not match.";
        } 
        else 
        {
            $newPassword = mysqli_real_escape_string($conn, $newPassword);
        }
    }

    if (empty($errorMessage)) 
    {
        $userId = $_SESSION['user_id'];

        if (!empty($newUsername)) 
        {
            $newUsername = mysqli_real_escape_string($conn, $newUsername);

            $sql_update_username = "UPDATE users SET Username='$newUsername' WHERE User_ID=$userId";

            if (mysqli_query($conn, $sql_update_username)) 
            {
                $_SESSION['username'] = $newUsername;
                $successMessage = "Username updated successfully.";
            } 
            else
            {
                $errorMessage = "Error updating username: " . mysqli_error($conn);
            }
        }

        if (!empty($newPassword)) 
        {
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            $sql_update_password = "UPDATE users SET Password='$hashedPassword' WHERE User_ID=$userId";

            if (mysqli_query($conn, $sql_update_password)) 
            {
                $successMessage = "Password updated successfully.";
            }
            else
            {
                $errorMessage = "Error updating password: " . mysqli_error($conn);
            }
        }
    }
    
    if(isset($_POST['genres'])) 
    {
        $userId = $_SESSION['user_id'];

        $sql_delete_genres = "DELETE FROM users_genre WHERE User_ID = $userId";
        if (mysqli_query($conn, $sql_delete_genres)) 
        {
            $stmt = $conn->prepare("INSERT INTO users_genre (User_ID, Genre_ID) VALUES (?, ?)");
            foreach($_POST['genres'] as $genre_id)
            {
                $stmt->bind_param("ii", $userId, $genre_id);
                $stmt->execute();
            }

            $stmt->close();
            $successMessage = "Preferred genres updated successfully.";
            
        }
        else 
        {
            $errorMessage = "Error updating preferred genres: " . mysqli_error($conn);
        }

    } 
    else
    {
        $userId = $_SESSION['user_id'];
        $sql_delete_all_genres = "DELETE FROM users_genre WHERE User_ID = $userId";
        if (mysqli_query($conn, $sql_delete_all_genres)) 
        {
            $successMessage = "No preferred genres selected. All genres removed.";
        }
        else
        {
            $errorMessage = "Error updating preferred genres: " . mysqli_error($conn);
        }
    }
    header("Refresh:0");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Account Settings</title>
    <link rel="stylesheet" href="/stylesheets/styles.css">
    <link rel="stylesheet" href="/stylesheets/accountStyle.css">
</head>
    
<header>
    <h2>Account Settings</h2>
    <a href="welcome.php" class="backHome">Back to home page</a>
</header>

<body>
    

    <?php if (!empty($errorMessage)): ?>
        <div class="error"><?php echo $errorMessage; ?></div>
    <?php endif; ?>

    <button onclick="openModal('usernameModal')">Change Username</button>

    <button onclick="openModal('passwordModal')">Change Password</button>

    <div id="usernameModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal('usernameModal')">&times;</span>
            <h2>Change Username</h2>

            <p>Current Username: <?php echo $_SESSION['username']; ?></p>
            <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">

                <label for="new_username">New Username:</label><br>
                <input type="text" id="new_username" name="new_username" value=""><br>

                <input type="submit" value="Update">
            </form>
        </div>
    </div>

    <div id="passwordModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal('passwordModal')">&times;</span>
            <h2>Change Password</h2>

            <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                <label for="new_password">New Password:</label><br>
                <input type="password" id="new_password" name="new_password"><br>
                <label for="confirm_password">Confirm New Password:</label><br>
                <input type="password" id="confirm_password" name="confirm_password"><br>
                <input type="submit" value="Update">
            </form>
        </div>
    </div>

    <div>
        <h3 style="padding-top:20px;">Select Preferred Genres</h3>
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <?php

            $sql = "SELECT * FROM genre";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) 
                {
                    $checked = in_array($row['Genre_ID'], $userGenres) ? 'checked' : '';
                    echo '<input type="checkbox" name="genres[]" value="' . $row['Genre_ID'] . '" ' . $checked . '> ' . $row['Genre_Name'] . '<br>';
                }
            }
            ?>
            <br>
            <input type="submit" value="Save">
        </form>
    </div>


</body>
<script src="/js/account.js"></script>
</html>
