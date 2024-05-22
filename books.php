<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Books List</title>
    <link rel="stylesheet" href="/stylesheets/styles.css">
    <link rel="stylesheet" href="/stylesheets/bookStyle.css">
</head>
<body>

<header>
    <h2>Books List</h2>
    <a href="welcome.php" class="backHome">Back to home page</a>
</header>

<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
    <label for="genre_filter">Filter by Genre:</label>
    <select name="genre_filter" id="genre_filter">
    <option value="">All Genres</option>
    <option value="Preferred">Preferred Genre</option>
    <?php
        require_once 'config.php';
        $genre_query = "SELECT Genre_Name FROM genre";
        $genre_result = $conn->query($genre_query);
        if ($genre_result->num_rows > 0) {
            while ($genre_row = $genre_result->fetch_assoc()) {
                $genre_name = $genre_row['Genre_Name'];
                echo "<option value='$genre_name'";
                if(isset($_POST['genre_filter']) && $_POST['genre_filter'] == $genre_name) echo ' selected';
                echo ">$genre_name</option>";
            }
        }
    ?>
</select>

    <button type="submit">Apply Filter</button>
</form>

<table>
    <thead>
        <tr>
            <th>Title</th>
            <th>Author</th>
            <th>Publication Year</th>
            <th>Genre</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>

    <?php
        session_start();

        if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) 
        {
            header("location: login.php");
            exit;
        }

        require_once 'config.php';

        if ($conn->connect_error) 
        {
            die("Connection failed: " . $conn->connect_error);
        }

        $user_id = $_SESSION['user_id'];

        $sql = "SELECT b.Book_ID, b.Title, b.Author, b.Pub_Year, GROUP_CONCAT(g.Genre_Name SEPARATOR ', ') AS Genres
                FROM books b
                LEFT JOIN book_genre bg ON b.Book_ID = bg.Book_ID 
                LEFT JOIN genre g ON bg.Genre_ID = g.Genre_ID
                GROUP BY b.Book_ID";

        if(isset($_POST['genre_filter']) && $_POST['genre_filter'] != '') 
        {
            $selected_genre = $_POST['genre_filter'];
            
            $sql .= " HAVING FIND_IN_SET('$selected_genre', Genres)";
        
            if($_POST['genre_filter'] == 'Preferred')
            {
                $selected_genre = $_POST['genre_filter'];

                $sql = "SELECT b.Book_ID, b.Title, b.Author, b.Pub_Year, GROUP_CONCAT(g.Genre_Name SEPARATOR ', ') AS Genres
                        FROM books b
                        JOIN book_genre bg ON b.Book_ID = bg.Book_ID
                        JOIN genre g ON bg.Genre_ID = g.Genre_ID
                        JOIN users_genre ug ON bg.Genre_ID = ug.Genre_ID
                        WHERE ug.User_ID = $user_id
                        GROUP BY b.Book_ID";
            }
        }
        
        $result = $conn->query($sql);

        if ($result->num_rows > 0) 
        {
            while ($row = $result->fetch_assoc()) 
            {
                $book_id = $row['Book_ID'];
                $check_sql = "SELECT * FROM reading_status WHERE User_ID = '$user_id' AND Book_ID = '$book_id'";
                $check_result = $conn->query($check_sql);

                if ($check_result->num_rows > 0) 
                {
                    $disabled = "disabled";
                    $button_text = "Already in Reading List";
                }
                else 
                {
                    $disabled = "";
                    $button_text = "Add to Reading Status";
                }

                echo "<tr>
                        <td>".$row["Title"]."</td>
                        <td>".$row["Author"]."</td>
                        <td>".$row["Pub_Year"]."</td>
                        <td>".$row["Genres"]."</td>
                        <td>
                            <form action='".$_SERVER["PHP_SELF"]."' method='post' class='add-form'>
                                <input type='hidden' name='book_id' value='".$row["Book_ID"]."'>
                                <button type='submit' name='add_to_reading_status' $disabled>$button_text</button>
                            </form>
                        </td>
                    </tr>";
            }
        }
        else 
        {
            echo "<tr><td colspan='5'>No results found</td></tr>";
        }

        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_to_reading_status'])) 
        {

            $user_id = $_SESSION['user_id'];
            $book_id = $_POST["book_id"];

            $check_sql = "SELECT * FROM reading_status WHERE User_ID = '$user_id' AND Book_ID = '$book_id'";
            $check_result = $conn->query($check_sql);

            $sql_insert = "INSERT INTO reading_status (User_ID, Book_ID, Completed) VALUES ('$user_id', '$book_id', 0)";

            if ($conn->query($sql_insert) === TRUE) 
            {
                echo "<script>
                        if (confirm('Book added to reading status successfully!')) {
                            window.location.href = 'books.php'; // Redirect to the books list page
                        } else {
                            window.location.href = 'books.php'; // Redirect to the books list page
                        }
                        </script>";
            }
            else 
            {
                echo "Error adding book to reading status: " . $conn->error;
            }
            
        }

        $conn->close();
    ?>

    </tbody>
</table>


</body>
</html>
