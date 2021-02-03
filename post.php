<?php
require('config/db.php');
session_start();

$postID = test_input($connection, $_GET['id']);
$sql = "
SELECT p.id, p.title, p.author_id, p.date_created, p.content, a.fname, a.sname
FROM post p, author a
WHERE p.author_id = a.id
AND p.id ='$postID';";
$result = mysqli_query($connection, $sql);
$post = mysqli_fetch_assoc($result);

// Security Checks
function test_input($connection, $in)
{
    $in = trim($in);
    $in = stripslashes($in);
    $in = htmlspecialchars($in);
    $in = mysqli_real_escape_string($connection, $in);

    return $in;
}
?>
<?php include('include/header.php'); ?>
<!--NOTE: class names starting with 'w3-' are W3CSS class names, linking to the W3CSS framework, pulled in on header.php-->

<div class="w3-container center-me">
    <p><a href="postList.php">All Posts</a> / <?php echo $post['title'] ?></p>
    <h1><?php echo $post['title']; ?></h1>
    <p><?php echo $post['fname'] . ' ' . $post['sname'] ?></p>
    <p><?php echo $post['date_created'] ?></p>
    <?php
    if (isset($_SESSION['id'])) {
        if ($post['author_id'] == $_SESSION['id']) : ?>
            <p>You wrote this post! Click below to edit / delete...</p>
            <a href="editPost.php?id=<?php echo $post['id'] ?>" class="w3-button w3-teal">Edit</a>
            <a href="deletePost.php?id=<?php echo $post['id'] ?>" class="w3-button w3-teal">Delete</a>
    <?php endif;
    } ?>
    <hr>
    <p><?php echo $post['content'] ?></p>
    <hr>
    <a href="postList.php" class="w3-button w3-teal">Back</a>
</div>
<?php include('include/footer.php'); ?>