<?php
session_start();

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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Status</title>
    <link rel="stylesheet" href="/stylesheets/styles.css">

    <link href="jq_scripts/jtable/themes/redmond/jquery-ui-1.8.16.custom.css" rel="stylesheet" type="text/css" />
    <link href="jq_scripts/jtable/themes/lightcolor/blue/jtable.css" rel="stylesheet" type="text/css" />
    <script src="jq_scripts/jquery-1.6.4.min.js" type="text/javascript"></script>
    <script src="jq_scripts/jquery-ui-1.8.16.custom.min.js" type="text/javascript"></script>
    <script src="jq_scripts/jtable/jquery.jtable.js" type="text/javascript"></script>

</head>

<header>
    <h1>Reading Status</h1>
    <a href="welcome.php">Back to home page</a>
</header>

<body>
    <div id="StatusTable"></div>

    <script type="text/javascript">
    $(document).ready(function() {
        $('#StatusTable').jtable({
            title: 'Your Books',
            actions: 
            {
                listAction: '/status_action.php?action=list',
                updateAction: '/status_action.php?action=update',
                deleteAction: '/status_action.php?action=delete'
            },

            fields: {
                Status_ID: {
                    key: true,
                    title: "Status_ID",
                    list: false
                },

                Title: {
                    title: "Title",
                    edit: false

                },

                Author: {
                    title: "Author",
                    edit: false
                },

                Completed: {
                    title: "Completed",
                    edit: true,
                    input: function (data) {
                        var isChecked = data.record.Completed === '1' ? 'checked' : '';
                        return '<input type="checkbox" name="Completed" ' + isChecked + '/>';
                    },
                    display: function (data) {
                        
                        return data.record.Completed === '1' ? 'Yes' : 'No';
                    }
                }
            },
            recordUpdated: function(event, data) 
            {
                $('#StatusTable').jtable('reload');
            }
        });
        $('#StatusTable').jtable('load');
	 
    });

</script>

</body>
</html>
