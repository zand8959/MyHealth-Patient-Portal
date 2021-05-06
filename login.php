<?php
session_start();
error_reporting(0);
include 'includes/dbconn.php';
$conn = new mysqli($servername, $username, "", "db1", $sqlport, $socket);

if($conn->connect_error)
{
	die("Connection failed: " . $conn->connect_error);
}

$_SESSION['PATIENT_ID'] = '';
$_SESSION['DOCTOR_ID'] = '';
$emailInput = '';
$passwordInput = '';
$errorMessage = '';

if(isset($_POST['loginButton']))
{
	$emailInput = $_POST['emailInput'];
	$passwordInput = $_POST['passwordInput'];
	
	if(empty($emailInput))
	{
		$errorMessage = "*All fields required. Please enter your email.";
	}
	else if(empty($passwordInput))
	{
		$errorMessage = "*All fields required. Please enter your password.";
	}
	else
	{
		$query = mysqli_query($conn, "SELECT * FROM Patients WHERE EmailAddress = '$emailInput' AND Password = '$passwordInput'");
		$result = mysqli_fetch_assoc($query);
		
		if($result)
		{
			$_SESSION['PATIENT_ID'] = $result['PatientID'];
			header("Location:patient-home.php");
			exit();
		}
		
		$query = mysqli_query($conn, "SELECT * FROM Doctors WHERE EmailAddress = '$emailInput' AND Password = '$passwordInput'");
		$result = mysqli_fetch_assoc($query);
		
		if($result)
		{
			$_SESSION['DOCTOR_ID'] = $result['DoctorID'];
			header("Location:doctor-home.php");
			exit();
		}
		
		$errorMessage = "*Invalid email or password. Please try again.";
	}
}

if(isset($_POST['registerButton']))
{
	header("Location:register.php");
	exit();
}
?>

<html>
	<head>
		<title>MyHealth Portal</title>
		<link href = 'style.css' rel = 'stylesheet'>
	</head>
	<body>
		<div class = "pageHeader">
			<h1>MyHealth Portal</h1>
		</div>
		<form name = "loginForm" class = "login" action = "login.php" method = "post">
			<section class = "formHeader">
				<h3>Log In</h3>
			</section>
			<?php echo "<span class = errorMessage>$errorMessage</span>" ?>
			<section class = "formFields">
				<legend>Email</legend>
				<input name = "emailInput" class = "formElement" type = "text" value = "<?php echo $emailInput ?>"/>
				</br>
				<legend>Password</legend>
				<input name = "passwordInput" class = "formElement" type = "password"/>
			</section>
			</br>
			<section class = "formButtons">
				<input name = "loginButton" class = "button green" type = "submit" value = "Log In"/>
				<input name = "registerButton" class = "button blue" type = "submit" value = "Register"/>
			</section>
		</form>
	</body>
</html>