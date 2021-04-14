<?php
    session_start();
    $error_message = '';
    $firstName='';
    $lastName='';
    $ssn='';
    $dob='';
    $email='';
    $phone='';
    $street='';
    $city='';
    $state='';
    $zip='';
    $password='';
    $confirmPassword='';
    $addressId='';

    $query='';
    $result='';
    // error_reporting(0);
    include 'includes/dbconn.php';
    $conn = new mysqli($servername, $username, "", "db1", $sqlport, $socket);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
?>

<html>

    <head>
        <title>MyHealth5 Portal</title>
        <link href = 'MyHealth.css' rel = 'stylesheet'>
    </head>
    <body>
        <div class="pageContainer" id="registerPageContainer">
            <h2>Register</h2>

            <form action = "RegisterPage.php" method="post" id="registerForm">
                <section class = "registerFields">
                    <?php
                        if(isset($_POST['firstName'])){
                            $firstName = $_POST['firstName'];
                        }
                        if(isset($_POST['lastName'])){
                            $lastName = $_POST['lastName'];
                        }
                        if(isset($_POST['ssn'])){
                            $ssn = $_POST['ssn'];
                        }
                        if(isset($_POST['dob'])){
                            $dob = $_POST['dob'];
                        }
                        if(isset($_POST['email'])){
                            $email = $_POST['email'];
                        }
                        if(isset($_POST['phone'])){
                            $phone = $_POST['phone'];
                        }
                        if(isset($_POST['street'])){
                            $street = $_POST['street'];
                        }
                        if(isset($_POST['city'])){
                            $city = $_POST['city'];
                        }
                        if(isset($_POST['state'])){
                            $state = $_POST['state'];
                        }
                        if(isset($_POST['zip'])){
                            $zip = $_POST['zip'];
                        }
                        if(isset($_POST['password'])){
                            $password = $_POST['password'];
                        }
                        if(isset($_POST['confirmPassword'])){
                            $confirmPassword = $_POST['confirmPassword'];
                        }
                    ?>
                    <legend>First Name</legend>
                    <input class = "FormElement" name = "firstName" id = "firstName" type="text"></input>
                    <br/>
                    <legend>Last Name</legend>
                    <input class = "FormElement" name = "lastName" id = "lastName" type="text"></input>
                    <br/>
                    <legend>Social Security Number</legend>
                    <input class = "FormElement" name = "ssn" id = "ssn" type="text" placeholder="XXX-XX-XXXX"></input>
                    <br/>
                    <legend>Date Of Birth</legend>
                    <input class = "FormElement" name = "dob" id = "dob" type="text" placeholder="YYYY-MM-DD"></input>
                    <br/>
                    <legend>Email Address</legend>
                    <input class = "FormElement" name = "email" id = "email" type="text"></input>
                    <br/>
                    <legend>Phone Number</legend>
                    <input class = "FormElement" name = "phone" id = "phone" type="text" placeholder="XXX-XXX-XXXX"></input>
                    <br/>
                    <legend>Street Address</legend>
                    <input class = "FormElement" name = "street" id = "street" type="text"></input>
                    <br/>
                    <legend>City</legend>
                    <input class = "FormElement" name = "city" id = "city" type="text"></input>
                    <br/>
                    <legend>State</legend>
                    <input class = "FormElement" name = "state" id = "state" type="text"></input>
                    <br/>
                    <legend>Zip Code</legend>
                    <input class = "FormElement" name = "zip" id = "zip" type="text"></input>
                    <br/>
                    <legend>Password</legend>
                    <input class = "FormElement" name = "password" id = "password" type="password"></input>
                    <br/>
                    <legend>Confirm Password</legend>
                    <input class = "FormElement" name = "confirmPassword" id = "confirmPassword" type="password"></input>
                </section>
                <?php
                    //ERROR CHECKING
                    if(isset($_POST['submitButton'])) {
                        if($password == ''){
                            $error_message = "All fields are required. Please enter a password.";
                        }else if($password != $confirmPassword){
                            $error_message = "Passwords do not match.";
                        }

                        if($zip == ''){
                            $error_message = "All fields are required. Please enter a zip code.";
                        }else if(!preg_match("/^[0-9]{5}$/", $zip)){
                            $error_message = "Invalid zip code entered.";
                        }

                        if($state == ''){
                            $error_message = "All fields are required. Please enter a state.";
                        }else if(!preg_match("/^[A-Z]{2}$/", $state)){
                            $error_message = "Invalid state entered.";
                        }

                        if($city == ''){
                            $error_message = "All fields are required. Please enter a state.";
                        }

                        if($street == ''){
                            $error_message = "All fields are required. Please enter a street address.";
                        }

                        if($phone == ''){
                            $error_message = "All fields are required. Please enter a phone number.";
                        }else if(!preg_match("/^[0-9]{3}-[0-9]{3}-[0-9]{4}$/", $phone)){
                            $error_message = "Invalid phone number entered.";
                        }

                        if($email == ''){
                            $error_message = "All fields are required. Please enter an email address.";
                        }else if(!preg_match("/^[A-Za-z0-9.]*@[A-Za-z0-9.]*\.[A-Za-z0-9]*$/", $email)){
                            $error_message = "Invalid email entered.";
                        }

                        if($dob == ''){
                            $error_message = "All fields are required. Please enter a date of birth.";
                        }else if(!preg_match("/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/", $dob)){
                            $error_message = "Invalid date of birth entered.";
                        }

                        if($ssn == ''){
                            $error_message = "All fields are required. Please enter a social security number.";
                        }else if(!preg_match("/^[0-9]{3}-[0-9]{2}-[0-9]{4}$/", $ssn)){
                            $error_message = "Invalid social security number entered.";
                        }

                        if($lastName == ''){
                            $error_message = "All fields are required. Please enter a last name.";
                        }

                        if($firstName == ''){
                            $error_message = "All fields are required. Please enter a first name.";
                        }

                        //Error checking complete, process registration.
                        if($error_message==''){
                            echo "Sending data.";
                            $query = "insert into Addresses (Street, City, StateCode, ZipCode) values ('$street', '$city', '$state', '$zip')";
                            $result = mysqli_query($conn, $query);
                            $query = "select AddressId from Addresses where Street='$street' and City='$city' and StateCode='$state' and ZipCode='$zip'";
                            $result = mysqli_query($conn, $query);
                            $addressId=mysqli_fetch_assoc($result)["AddressId"];
                            $query = "insert into Patients (FirstName, LastName, SocialSecurityNumber, DateOfBirth, EmailAddress, PhoneNumber, AddressID, Password) values ('$firstName', '$lastName', '$ssn', '$dob', '$email', '$phone', '$addressId', '$password')";
                            $result = mysqli_query($conn, $query);
                            header("Location:MyHealth5.php");
                        }else{
                            echo "<span class=\"errorMessage\">$error_message</span>";
                        }
                    }
                    //Cancel button returns to login page without doing anything
                    if(isset($_POST['cancelButton'])){
                        header("Location:MyHealth5.php");
                        exit();
                    }
                ?>
                <br/>
                <section class="registerButtons">
                    <input type="submit" name="submitButton" class="button" value="Submit"/>
                    <input type="submit" name="cancelButton" class="button" value="Cancel"/>
                </section>
            </form>
        </div>
    </body>
</html>