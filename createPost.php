<?php
require('config/db.php');
session_start();

$emptyError = $backendError = "";

// If user accessed page via typing url but isn't logged in:
if (!isset($_SESSION['id'])) {
    // ..redirect to login page with prompt
    header('location: login.php?loginRequired=true');
    exit();
}

// Check form submission
if (isset($_POST['submit'])) {
    $inArray = [$_POST['title'], $_POST['content']];
    if (checkEmpty($inArray) == true) {
        $emptyError = "Title / Content cannot be blank";
    } else {
        $title = test_input($connection, $_POST['title']);
        $content = test_input($connection, $_POST['content']);
        createPost($connection, $title, $content);
    }
}
// Check fields are not empty
function checkEmpty($inArray)
{
    $isEmpty = false;
    foreach ($inArray as $field) {
        if (empty($field)) {
            $isEmpty = true;
        }
    }
    return $isEmpty;
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

// Add new post to db
function createPost($connection, $title, $content)
{
    $sql = "INSERT INTO post (author_id, title, content) VALUES (?,?,?);";
    $stmt = mysqli_stmt_init($connection);
    if (!mysqli_stmt_prepare($stmt, $sql)) {
        echo "<h1>Error, stmt to add post failed preperation.</h1>";
        exit();
    }
    mysqli_stmt_bind_param($stmt, "sss", $_SESSION['id'], $title, $content);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    header('location: postList.php?postCreated=true');
}


?>
<?php include('include/header.php'); ?>
<!--NOTE: class names starting with 'w3-' are W3CSS class names, linking to the W3CSS framework, pulled in on header.php-->

<div class="w3-container center-me">
    <h1>Create Post</h1>

    <form action="createPost.php" method="post" id="create_post">
        <span class="error"><?php echo $emptyError ?></span>
        <span class="error"><?php echo $backendError ?></span>
        <input class="w3-input w3-border" type="text" placeholder="Post Title" name="title" style="max-width:600px;" value="<?php if (!empty($_POST["title"])) {
                                                                                                                                echo $_POST["title"];
                                                                                                                            } ?>"><br>
        <textarea class="w3-input w3-border" form="create_post" placeholder="Post Content" name="content" rows="20"></textarea><br>
        <input class="w3-button w3-teal" type="submit" name="submit" value="Create">
    </form>

</div>
<?php include('include/footer.php'); ?>