<?php
require('config/db.php');

$uname = $password = $confpassword = $fname = $sname = "";
$unameError = $passwordError = $passwordMatchError = $fnameError = $snameError = $backendError = "";
$errCount = 0;

// Validation
if (isset($_POST["submit"])) {
    // Are inputs empty
    if (empty($_POST["uname"])) {
        $unameError = "Backend validation: No username given.";
        $errCount += 1;
    } else {
        // Secure the input
        $uname = test_input($connection, $_POST["uname"]);
        // Does input match regex rules? 
        if (!preg_match('^([A-Za-z0-9]){4,20}$^', $uname)) {
            $unameError = "Backend validation: Username does not comply with Regex rules";
            $errCount += 1;
        }
        // Is there a user with this uname in DB already? 
        $sql = "SELECT * FROM author WHERE uname = ?;";
        $stmt = mysqli_stmt_init($connection);
        if (!mysqli_stmt_prepare($stmt, $sql)) {
            $backendError = "Backend Error: mysqli_stmt_prepare($stmt, $sql) failed.";
            $errCount += 1;
        }
        mysqli_stmt_bind_param($stmt, "s", $uname);
        mysqli_stmt_execute($stmt);
        $resultData = mysqli_stmt_get_result($stmt);

        if (mysqli_fetch_assoc($resultData)) {
            $unameError = "Backend validation: User with that username already exists.";
            $errCount += 1;
        }
        mysqli_stmt_close($stmt);
    }
    if (empty($_POST["password"])) {
        $passwordError = "Backend validation: No password given";
        $errCount += 1;
    } else {
        $password = test_input($connection, $_POST["password"]);
        if (!preg_match('^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[a-zA-Z]).{8,}$^', $password)) {
            $passwordError = "Backend validation: Password does not comply with Regex rules";
            $errCount += 1;
        }
    }
    if (empty($_POST["confpassword"])) {
        $passwordMatchError = "Backend validation: Confirm Password left empty";
        $errCount += 1;
    } else {
        $confpassword = test_input($connection, $_POST["confpassword"]);
        if ($password != $confpassword) {
            $passwordMatchError = "Backend validation: Passwords do not match.";
            $errCount += 1;
        }
    }
    if (empty($_POST["fname"])) {
        $fnameError = "Backend validation: No first name given";
        $errCount += 1;
    } else {
        $fname = test_input($connection, $_POST["fname"]);
    }
    if (empty($_POST["sname"])) {
        $snameError = "Backend validation: No last name given";
        $errCount += 1;
    } else {
        $sname = test_input($connection, $_POST["sname"]);
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

if (isset($_POST["submit"]) && $errCount == 0) {
    createAuthor($connection, $uname, $password, $fname, $sname);
}

function createAuthor($connection, $uname, $password, $fname, $sname)
{
    $sql = "INSERT INTO author (uname, password, fname, sname) VALUES (?,?,?,?);";
    $stmt = mysqli_stmt_init($connection);
    if (!mysqli_stmt_prepare($stmt, $sql)) {
        echo "<h1>Error, stmt to add user failed preperation.</h1>";
        exit();
    }

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    mysqli_stmt_bind_param($stmt, "ssss", $uname, $hashedPassword, $fname, $sname);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    header("location: login.php?newAccount=true");
}


?>
<?php include('include/header.php'); ?>
<!--NOTE: class names starting with 'w3-' are W3CSS class names, linking to the W3CSS framework, pulled in on header.php-->

<div class="w3-container center-me">
    <h1>Sign Up</h1>
    <div class="w3-row">
        <div class="w3-half">
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" style="max-width:600px;">
                <span class="error"><?php echo $unameError ?></span>
                <input class="w3-input w3-border" type="text" pattern="^([A-Za-z0-9]){4,20}$" placeholder="Username" name="uname" value="<?php if (!empty($_POST["uname"])) {
                                                                                                                                                echo $_POST["uname"];
                                                                                                                                            } ?>" required><br>
                <span class="error"><?php echo $passwordError ?></span>
                <input class="w3-input w3-border" type="password" pattern="^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[a-zA-Z]).{8,}$" placeholder="Password" name="password" required><br>
                <span class="error"><?php echo $passwordMatchError ?></span>
                <input class="w3-input w3-border" type="password" pattern="^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[a-zA-Z]).{8,}$" placeholder="Confirm Password" name="confpassword" required><br>
                <span class="error"><?php echo $fnameError ?></span>
                <input class="w3-input w3-border" type="text" placeholder="First Name" name="fname" value="<?php if (!empty($_POST["fname"])) {
                                                                                                                echo $_POST["fname"];
                                                                                                            } ?>" required><br>
                <span class="error"><?php echo $snameError ?></span>
                <input class="w3-input w3-border" type="text" placeholder="Last Name" name="sname" value="<?php if (!empty($_POST["sname"])) {
                                                                                                                echo $_POST["sname"];
                                                                                                            } ?>" required><br>
                <input class="w3-button w3-teal" type="submit" name="submit" value="Sign Up">
                <span class="error"><?php echo $backendError ?></span>
            </form>
        </div>

        <div class="w3-half">
            <p id="desc" class="w3-margin-left"></p>
        </div>

    </div>
</div>
<script async defer src="static/signup.js"></script>
<?php include('include/footer.php'); ?>