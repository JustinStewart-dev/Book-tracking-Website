<?php
require_once 'config.php';

session_start();
if (!isset($_SESSION['user_id'])) 
{
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$sql = "SELECT b.Book_ID, b.Title, rs.Completed, IFNULL(r.Review_Left, 0) AS Review_Left
        FROM books b
        INNER JOIN reading_status rs ON b.Book_ID = rs.Book_ID
        LEFT JOIN (
            SELECT Book_ID, COUNT(*) AS Review_Left
            FROM reviews
            WHERE User_ID = $user_id
            GROUP BY Book_ID
        ) r ON b.Book_ID = r.Book_ID
        WHERE rs.User_ID = $user_id";

$result = mysqli_query($conn, $sql);

if(isset($_POST['submit_review'])) 
{
    $book_id = $_POST['book_id'];
    $rating = $_POST['rating'];
    $comment = $_POST['comment'];

    if(!empty($book_id) && !empty($comment)) 
    {
        $insert_query = "INSERT INTO reviews (User_ID, Book_ID, Rating, Comment, Review_Date) 
                         VALUES ('$user_id', '$book_id', '$rating', '$comment', NOW())";

       $result = mysqli_query($conn, $insert_query);

        if($result) 
        {
            header("Location: ".$_SERVER['PHP_SELF']);
            exit();
        }
        else 
        {
            echo "Error submitting review.";
        }
    }
    else 
    {
        echo "Please fill out all required fields.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Book Reviews</title>
    <link rel="stylesheet" href="/stylesheets/styles.css">
    <link rel="stylesheet" href="/stylesheets/reviewStyle.css">
</head>

<header>
    <h2>Your Completed Books</h2>
    <a href="welcome.php">Back to home page</a>
</header>

<body>
<ul>
    <?php while ($row = mysqli_fetch_assoc($result)): ?>
        <li>
            <?php echo $row['Title']; ?>

            <?php if ($row['Completed'] == 1 && $row['Review_Left'] == 0): ?>
                <button onclick="openReviewPopup('<?php echo $row['Book_ID']; ?>')">Leave Review</button>
            <?php elseif ($row['Review_Left'] == 1): ?>
                <button class="disabled" disabled>Review Left</button>
            <?php else: ?>
                <button class="disabled" disabled>Book Not Completed</button>
            <?php endif; ?>

            <div id="modal-<?php echo $row['Book_ID']; ?>" class="modal">
                <div class="modal-content">
                
                    <span class="close" onclick="closeReviewPopup('<?php echo $row['Book_ID']; ?>')">&times;</span>
                    <h3>Leave Review for "<?php echo $row['Title']; ?>"</h3>

                    <form method="post" action="" onsubmit="return validateForm('<?php echo $row['Book_ID']; ?>')">

                        <input type="hidden" name="book_id" value="<?php echo $row['Book_ID']; ?>">
                        <div class="rating">
                            <span class="star" onclick="setRating(<?php echo $row['Book_ID']; ?>, 1)">&#9733;</span>
                            <span class="star" onclick="setRating(<?php echo $row['Book_ID']; ?>, 2)">&#9733;</span>
                            <span class="star" onclick="setRating(<?php echo $row['Book_ID']; ?>, 3)">&#9733;</span>
                            <span class="star" onclick="setRating(<?php echo $row['Book_ID']; ?>, 4)">&#9733;</span>
                            <span class="star" onclick="setRating(<?php echo $row['Book_ID']; ?>, 5)">&#9733;</span>
                            <input type="hidden" id="rating_<?php echo $row['Book_ID']; ?>" name="rating" value="">
                        </div>

                        <br>

                        <label for="comment">Comment:</label><br>
                        <textarea name="comment" rows="4" cols="50" required></textarea>

                        <br>
                        <br>

                        <input type="submit" name="submit_review" value="Submit Review">
                    </form>
                </div>
            </div>
        </li>
    <?php endwhile; ?>
</ul>
<script src="/js/reviews.js"></script>
</body>
</html>
