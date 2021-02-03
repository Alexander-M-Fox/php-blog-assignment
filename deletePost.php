<?php
require('config/db.php');
session_start();

$postID = test_input($connection, $_GET['id']);

// AUTH -  Check session exists
if (!isset($_SESSION['id'])) {
    echo "<h1>_SESSION['id'] doesn't exist.</h1>";
    exit();
}

// AUTH - check correct user is logged in
if (checkUser($connection, $postID) == True) {

    $sql = "DELETE FROM post WHERE id='$postID';";

    if (mysqli_query($connection, $sql)) {
        echo "Record deleted successfully";
        header('location: postList.php?delete=True');
    } else {
        echo mysqli_error($conn);
    }
}

function checkUser($connection, $postID)
{
    $sql = "SELECT p.id, p.author_id
    FROM post p, author a 
    WHERE p.id='$postID' and
    p.author_id = a.id    
    ;";
    $result = mysqli_query($connection, $sql);
    if (mysqli_num_rows($result) > 0) {
        // User is authenticated to perform requested delete task
        return true;
    } else {
        echo "<h1>Wrong user logged in, auth failed.  No records were deleted.</h1>";
        exit();
    }
}


// Security Checks
function test_input($connection, $in)
{
    $in = trim($in);
    $in = stripslashes($in);
    $in = htmlspecialchars($in);
    $in = mysqli_real_escape_string($connection, $in);

    return $in;
}
