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

//if user accesses page but no id field is added
if (!isset($_GET['id'])) {
    header('location:postList.php?idError=true');
}

// Pull existing post from DB
$postID = test_input($connection, $_GET['id']);
$sql = "SELECT * FROM post WHERE id='$postID';";
$result = mysqli_query($connection, $sql);
$post = mysqli_fetch_assoc($result);

// Check correct user is trying to edit post
if ($post['author_id'] != $_SESSION['id']) {
    header('location:postList.php?authError=true');
}

// Check form submission
if (isset($_POST['submit'])) {
    $inArray = [$_POST['title'], $_POST['content']];
    if (checkEmpty($inArray) == true) {
        $emptyError = "Title / Content cannot be blank";
    } elseif (checkUpdated($inArray, $post) == false) {
        $backendError = "Nothing was updated.";
    } else {
        $title = test_input($connection, $_POST['title']);
        $content = test_input($connection, $_POST['content']);
        $id = test_input($connection, $_POST['postID']);
        updatePost($connection, $id, $title, $content);
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

// Check fields have different content in (save's updating DB unecessarily)
function checkUpdated($inArray, $post)
{
    $updated = true;
    if ($inArray[0] == $post['title'] && $inArray[1] == $post['content']) {
        $updated = false;
    }
    return $updated;
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
function updatePost($connection, $postID, $title, $content)
{
    $sql = "UPDATE post SET title='$title', content='$content' WHERE id='$postID';";
    if (mysqli_query($connection, $sql)) {
        header('location: postList.php?postUpdated=true');
    } else {
        echo "Error updating record: " . mysqli_error($connection);
    }
}


?>
<?php include('include/header.php'); ?>
<!--NOTE: class names starting with 'w3-' are W3CSS class names, linking to the W3CSS framework, pulled in on header.php-->

<div class="w3-container center-me">
    <h1>Edit Post</h1>

    <form action="editPost.php" method="post" id="edit_post">
        <span class="error"><?php echo $emptyError ?></span>
        <span class="error"><?php echo $backendError ?></span>
        <input type="hidden" name="postID" value="<?php echo $postID ?>">
        <input class="w3-input w3-border" type="text" placeholder="Post Title" name="title" style="max-width:600px;" value="<?php echo $post['title']; ?>"><br>
        <textarea class="w3-input w3-border" form="edit_post" placeholder="Post Content" name="content" rows="20"><?php echo $post['content']; ?></textarea><br>
        <input class="w3-button w3-teal" type="submit" name="submit" value="Edit">
    </form>

</div>
<?php include('include/footer.php'); ?>