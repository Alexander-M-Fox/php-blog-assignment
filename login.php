<?php
require('config/db.php');

$newAccount = "";
$uname = $password = "";
$unameError = $passwordError = $backendError = $emptyError = "";
$errCount = 0;

if (isset($_POST["submit"])) {

    $inArray = [$_POST['uname'], $_POST['password']];
    if (checkEmpty($inArray) == true) {
        $emptyError = "Username / Password cannot be blank";
    } else {
        $username = test_input($connection, $_POST['uname']);
        $password = test_input($connection, $_POST['password']);
        if (validateLogin($connection, $username, $password) == true) {
            header('location: postList.php?login=True');
        } else {
            $backendError = "Incorrect username or password.";
        }
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

// Validate Login
function validateLogin($connection, $username, $password)
{
    $loggedIn = false;

    $hashedPassword = getHash($connection, $username);
    if (password_verify($password, $hashedPassword)) {
        // valid credentials entered. 
        // Get user details from db.  no need to get password. 
        $sql = "SELECT id, uname, fname, sname, date_registered FROM author WHERE uname='$username';";
        $result = mysqli_query($connection, $sql);

        if ($result && mysqli_num_rows($result) > 0) {
            // output data of each row
            while ($row = mysqli_fetch_assoc($result)) {
                // Login user
                session_start();

                $_SESSION['id'] = $row['id'];
                $_SESSION['username'] = $row['uname'];
                $_SESSION['fname'] = $row['fname'];
                $_SESSION['sname'] = $row['sname'];
                $_SESSION['date_registered'] = $row['date_registered'];

                $loggedIn = true;
            }
        }
    }
    return $loggedIn;
}

function getHash($connection, $username)
{
    // Pull pwd hash from db so we can use password_verify();
    $sql = "SELECT password FROM 
    author WHERE uname='$username';";
    $result = mysqli_query($connection, $sql);
    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        return $row['password'];
    } else {
        echo "SQL Statement Error";
    }
}

?>
<?php include('include/header.php'); ?>

<div class="w3-container center-me">
    <?php
    // Provide feedback for users directed here after creating an account on signup.php
    if (isset($_GET['newAccount'])) : ?>
        <div class="w3-container w3-border-green">
            <p>Account Created! Please log in below to get started</p>
        </div>
    <?php endif ?>

    <?php
    // Provide feedback for users directed here after signing out
    if (isset($_GET['signout'])) : ?>
        <div class="w3-container w3-border-green">
            <p>You have been signed out.</p>
        </div>
    <?php endif ?>

    <?php
    // Provide feedback for users directed here because login was required to perform a certain function
    if (isset($_GET['loginRequired'])) : ?>
        <!--NOTE: class names starting with 'w3-' are W3CSS class names, linking to the W3CSS framework, pulled in on header.php-->
        <div class="w3-container w3-border-green">
            <p>You need to login first...</p>
        </div>
    <?php endif ?>


    <h1>Log in</h1>
    <p>You can view posts without logging in <a href="postList.php">here</a>, but to create, edit or delete your own posts please sign in below...</p>

    <form action="login.php" method="post" style="max-width:600px;">
        <span class="error"><?php echo $emptyError ?></span>
        <span class="error"><?php echo $backendError ?></span>
        <span class="error"><?php echo $unameError ?></span>
        <input class="w3-input w3-border" type="text" placeholder="Username" name="uname" value="<?php if (!empty($_POST["uname"])) {
                                                                                                        echo $_POST["uname"];
                                                                                                    } ?>"><br>
        <span class="error"><?php echo $passwordError ?></span>
        <input class="w3-input w3-border" type="password" placeholder="Password" name="password"><br>
        <input class="w3-button w3-teal" type="submit" name="submit" value="Log in">
    </form>

</div>
<?php include('include/footer.php'); ?>