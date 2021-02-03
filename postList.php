<?php
require('config/db.php');

$username = "";
session_start();
if (isset($_SESSION['username'])) {
    $username = $_SESSION['username'];
}

//SQL Query
$query = '
SELECT p.id, p.title, p.author_id, p.date_created, a.fname, a.sname
FROM post p, author a
WHERE p.author_id = a.id';

//Specifiy Connection from config/db.php
$result = mysqli_query($connection, $query);

// Number of posts in DB
$postCount = mysqli_num_rows($result);

// How many results I want per page
$resultsPerPage = 3;

// Number of pages needed
$totalPageCount = ceil($postCount / $resultsPerPage);

// What page is user on
if (!isset($_GET['page'])) {
    $page = 1;
} else {
    // Sanitised so user cannot enter code into url and access DB
    $page = test_input($connection, $_GET['page']);
}

mysqli_free_result($result);

// SQL query for page the user is on
$startLimitNumber = ($page - 1) * $resultsPerPage;

$query2 = '
SELECT p.id, p.title, p.author_id, p.date_created, a.fname, a.sname
FROM post p, author a
WHERE p.author_id = a.id
LIMIT ' . $startLimitNumber . ' , ' . $resultsPerPage;

//Specifiy Connection from config/db.php
$result2 = mysqli_query($connection, $query2);

//Specify data format to be returned
$posts = mysqli_fetch_all($result2, MYSQLI_ASSOC);


mysqli_close($connection);

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
    <?php if (isset($_GET['postCreated'])) : ?>
        <h3>Post created!</h3>
    <?php elseif (isset($_GET['postUpdated'])) : ?>
        <h3>Post updated!</h3>
    <?php elseif (isset($_GET['delete'])) : ?>
        <h3>Post deleted</h3>
    <?php elseif (isset($_GET['login'])) : ?>
        <h3>Welcome: <?php echo (isset($_SESSION['username'])) ? $_SESSION['username'] :  "error, no log on" ?></h3>
    <?php elseif (isset($_GET['idError'])) : ?>
        <h3>ID error when trying to edit post</h3>
    <?php elseif (isset($_GET['authError'])) : ?>
        <h3>Auth error: You cannot edit that post, you didn't write it.</h3>
    <?php endif; ?>
    <h1>Posts</h1>
    <span>Page: </span>
    <?php for ($i = 1; $i <= $totalPageCount; $i++) {
        echo '<a class="w3-button" href="postList.php?page=' . $i . '">' . $i . '</a>';
    }

    ?>
    <?php foreach ($posts as $post) : ?>
        <a style="text-decoration:none;" href="post.php?id=<?php echo $post['id']; ?>">
            <div class="w3-card w3-padding w3-margin-top w3-hover-shadow w3-round-xlarge">
                <div class="w3-row">
                    <div class="w3-container w3-threequarter">
                        <h2>
                            <?php echo $post['title']; ?>
                        </h2>
                        <p>By: <?php echo $post['fname'] . ' ' . $post['sname'] ?></p>
                        <p>Created: <?php echo $post['date_created'] ?></p>
                    </div>
                    <div class="w3-container w3-quarter">
                        <?php
                        if (isset($_SESSION['id'])) {
                            if ($post['author_id'] == $_SESSION['id']) : ?>
                                <h4>You wrote this post! Click below to edit / delete...</h4>
                                <a href="editPost.php?id=<?php echo $post['id'] ?>" class="w3-button w3-teal">Edit</a>
                                <a href="deletePost.php?id=<?php echo $post['id'] ?>" class="w3-button w3-teal">Delete</a>
                        <?php endif;
                        } ?>
                    </div>
                </div>
            </div>
        </a>
    <?php endforeach; ?>
    <br>
    <span>Page: </span>
    <?php for ($i = 1; $i <= $totalPageCount; $i++) {
        echo '<a class="w3-button" href="postList.php?page=' . $i . '">' . $i . '</a>';
    }

    ?>
</div>
<?php include('include/footer.php'); ?>