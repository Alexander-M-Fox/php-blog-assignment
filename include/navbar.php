<!--NOTE: class names starting with 'w3-' are W3CSS class names, linking to the W3CSS framework, pulled in on header.php-->

<div class="w3-container w3-header w3-teal">
    <div class="w3-row">
        <div class="center-me">
            <div class="w3-col m8 l9">
                <span class="w3-margin-right"><b>WpAssignment </b></span>
                <span class="w3-margin-right"><a href="postList.php" class="w3-button w3-teal">All Posts</a></span>
                <?php if (isset($_SESSION['username'])) : ?>
                    <span class="w3-margin-left"><a href="createPost.php" class="w3-button w3-teal">Create Post</a></span>
                <?php endif; ?>
            </div>
            <div class="w3-col m4 l3">
                <?php
                if (isset($_SESSION['username'])) : ?>
                    <span class="w3-margin-left"><a href="handle/signout.php" class="w3-button w3-teal">Log Out</a></span>
                <?php else : ?>
                    <span class="w3-margin-left"><a href="login.php" class="w3-button w3-teal">Log in</a></span>
                    <span class="w3-margin-left"><a href="signup.php" class="w3-button w3-teal">Sign up</a></span>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>