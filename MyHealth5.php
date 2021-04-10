<?php

$inputEmail = '';
$inputPassword = '';

error_reporting(0);
include 'includes/dbconn.php';
$conn = new mysqli($servername, $username, "", "db1", $sqlport, $socket);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

?>

<!------------- HTML ------------->

<html>

    <head>
        <title>MyHealth5 Portal</title>
        <link href = 'MyHealth.css' rel = 'stylesheet'>
    </head>

    <body>

        <h2>LOG IN</h2>
        <form action = "MyHealth5.php" method = "post" id = "loginForm">
            <section class = "loginfields">
                <legend>Email</legend>
                <textarea class = "FormElement" name = "email" id = "loginEmail" cols = "40" rows = "1"><?php echo $inputEmail ?></textarea>
                <br/>
                <legend>Password</legend>
                <textarea class = "FormElement" name = "password" id = "loginPassword" cols = "40" rows = "1"><?php echo $inputPassword ?></textarea>
            </section>

            <?php
                if(isset($_POST['loginButton'])) {
                    echo "You pushed login";
                }
                if(isset($_POST['registerButton'])) {
                    echo "You pushed register";
                }
            ?>
            <br/>
            <section class="loginButtons">
                <input type="submit" name="loginButton" class="button" value="Log In"/>
                <input type="submit" name="registerButton" class="button" value="Register"/>
            </section>
        </form>

    </body>
</html>
