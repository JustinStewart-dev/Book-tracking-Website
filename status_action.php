<?php
session_start();

if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true)
{
    header("location: login.php");
    exit;
}

require_once 'config.php';

if ($conn->connect_error)
{
    die("Connection failed: ".$conn->connect_error);
}

if ($_GET["action"] == "list") 
{
    if (isset($_SESSION["user_id"])) 
    {
        $user_id = $_SESSION["user_id"];
        $sql = "SELECT rs.Status_ID, b.Title, b.Author, rs.Completed FROM books b INNER JOIN reading_status rs ON b.book_id = rs.book_id WHERE rs.User_ID = '$user_id'";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) 
        {
            $rows = array();
            while ($row = $result->fetch_assoc()) 
            {
                $rows[] = $row;
            }

            $result->free();
            $conn->close();

            $jTableResult = array();
            $jTableResult['Result'] = "OK";
            $jTableResult['Records'] = $rows;
            echo json_encode($jTableResult);
            
        }
    }
}

else if($_GET["action"] == "update")
{
    $status_id = $conn->real_escape_string($_POST["Status_ID"]);
    $completed = isset($_POST["Completed"]) ? 1 : 0;

    $sqlu = "UPDATE reading_status SET Completed = '$completed' WHERE Status_ID = '$status_id'";

    if (!$result = $conn->query($sqlu))
    {
        exit;
    }

    $row = array();
    $row["Status_ID"] = $status_id;
    $row["Completed"] = $completed;

    $jTableResult = array();
    $jTableResult['Result'] = "OK";
    $jTableResult['Record'] = $row;
    echo json_encode($jTableResult);

    $conn->close();
}

else if($_GET["action"] == "delete")
{
    $status_id = $conn->real_escape_string($_POST["Status_ID"]);

    $sqld ="DELETE FROM reading_status WHERE Status_ID = '$status_id'";
    
    $conn->query($sqld);

    $jTableResult = array();
    $jTableResult['Result'] = "OK";
    echo json_encode($jTableResult);

    $conn->close();
}
?>
