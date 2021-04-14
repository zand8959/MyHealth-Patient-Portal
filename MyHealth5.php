<?php

session_start();
$inputEmail = '';
$inputPassword = '';
$query = '';
$search_result=null;
$page_loaded = "login";

// error_reporting(0);
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

        <div class="pageContainer" id="loginPageContainer">
            <h2>Log In</h2>
            <form action = "MyHealth5.php" method = "post" id = "loginForm">
                <section class = "loginfields">
                    <?php
                        if(isset($_POST['email'])){
                            $inputEmail = $_POST['email'];
                        }
                        if(isset($_POST['password'])){
                            $inputPassword = $_POST['password'];
                        }
                    
                    ?>
                    <legend>Email</legend>
                    <input class = "FormElement" name = "email" id = "loginEmail" type="text"></input>
                    <br/>
                    <legend>Password</legend>
                    <input class = "FormElement" name = "password" id = "loginPassword" type="password"></input>
                </section>

                <?php
                    //LOGIN FUNCTIONALITY
                    if(isset($_POST['loginButton'])) {
                        $query = "select * from Patients where EmailAddress = '$inputEmail' and Password = '$inputPassword';";
                        $search_result = mysqli_query($conn, $query);
                        if($search_result->num_rows){
                            echo "Patient Login";
                            $_SESSION["email"] = $inputEmail;
                            $_SESSION["role"] = "patient";
                            header("Location:PatientPortal.php");
                            exit();
                        }else{
                            $query = "select * from Doctors where EmailAddress = '$inputEmail' and Password = '$inputPassword';";
                            $search_result = mysqli_query($conn, $query);
                            if($search_result->num_rows){
                                echo "Doctor Login";
                                $_SESSION["email"] = $inputEmail;
                                $_SESSION["role"] = "doctor";
                                header("Location:DoctorPortal.php");
                                exit();
                            }else{
                                echo '<span class="errorMessage">Login Failed. Please try again.</span>';
                            }
                        }
                    }
                    if(isset($_POST['registerButton'])) {
                        header("Location:RegisterPage.php");
                        exit();
                    }
                ?>
                <br/>
                <section class="loginButtons">
                    <input type="submit" name="loginButton" class="button" value="Log In"/>
                    <input type="submit" name="registerButton" class="button" value="Register"/>
                </section>
            </form>
        </div>

        <script src="MyHealth5.js"></script>
    </body>
</html>
