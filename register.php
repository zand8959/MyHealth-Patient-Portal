<?php
session_start();
error_reporting(0);
include 'includes/dbconn.php';
$conn = new mysqli($servername, $username, "", "db1", $sqlport, $socket);

if($conn->connect_error)
{
	die("Connection failed: " . $conn->connect_error);
}

$currentDate = mysqli_fetch_assoc(mysqli_query($conn, "SELECT CURDATE()"))["CURDATE()"];
$firstNameInput = '';
$lastNameInput = '';
$ssnInput = '';
$dobInput = '';
$emailInput = '';
$phoneInput = '';
$streetInput = '';
$cityInput = '';
$stateInput = '';
$zipInput = '';
$pharmacyInput = '';
$doctorInput = '';
$labInput = '';
$passwordInput = '';
$confirmPasswordInput = '';
$errorMessage = '';

if(isset($_POST['registerButton']))
{
	$firstNameInput = $_POST['firstNameInput'];
	$lastNameInput = $_POST['lastNameInput'];
	$ssnInput = $_POST['ssnInput'];
	$dobInput = $_POST['dobInput'];
	$emailInput = $_POST['emailInput'];
	$phoneInput = $_POST['phoneInput'];
	$streetInput = $_POST['streetInput'];
	$cityInput = $_POST['cityInput'];
	$stateInput = $_POST['stateInput'];
	$zipInput = $_POST['zipInput'];
	$pharmacyInput = $_POST['pharmacyInput'];
	$doctorInput = $_POST['doctorInput'];
	$labInput = $_POST['labInput'];
	$passwordInput = $_POST['passwordInput'];
	$confirmPasswordInput = $_POST['confirmPasswordInput'];
	
	if(empty($firstNameInput))
	{
		$errorMessage = "*All fields required. Please enter your first name.";
	}
	else if(empty($lastNameInput))
	{
		$errorMessage = "*All fields required. Please enter your last name.";
	}
	else if(empty($ssnInput))
	{
		$errorMessage = "*All fields required. Please enter your social security number.";
	}
	else if(!preg_match("/^[0-9]{3}-[0-9]{2}-[0-9]{4}$/", $ssnInput))
	{
		$errorMessage = "*Invalid social security number. Please try again.";
		$ssnInput = '';
	}
	else if(empty($dobInput))
	{
		$errorMessage = "*All fields required. Please enter your date of birth.";
	}
	else if(!preg_match("/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/", $dobInput))
	{
		$errorMessage = "*Invalid date of birth. Please try again.";
		$dobInput = '';
	}
	else if(empty($emailInput))
	{
		$errorMessage = "*All fields required. Please enter your email address.";
	}
	else if(!preg_match("/^[A-Za-z0-9.]*@[A-Za-z0-9.]*\.[A-Za-z0-9]*$/", $emailInput))
	{
		$errorMessage = "*Invalid email address. Please try again.";
		$emailInput = '';
	}
	else if(empty($phoneInput))
	{
		$errorMessage = "*All fields required. Please enter your phone number.";
	}
	else if(!preg_match("/^[0-9]{3}-[0-9]{3}-[0-9]{4}$/", $phoneInput))
	{
		$errorMessage = "*Invalid phone number. Please try again.";
		$phoneInput = '';
	}
	else if(empty($streetInput))
	{
		$errorMessage = "*All fields required. Please enter your street address.";
	}
	else if(empty($cityInput))
	{
		$errorMessage = "*All fields required. Please enter your city.";
	}
	else if(empty($stateInput))
	{
		$errorMessage = "*All fields required. Please enter your state.";
	}
	else if(!preg_match("/^[A-Z]{2}$/", $stateInput))
	{
		$errorMessage = "*Invalid state. Please try again.";
		$stateInput = '';
	}
	else if(empty($zipInput))
	{
		$errorMessage = "*All fields required. Please enter your zip code.";
	}
	else if(!preg_match("/^[0-9]{5}$/", $zipInput))
	{
		$errorMessage = "*Invalid zip code. Please try again.";
		$zipInput = '';
	}
	else if(empty($pharmacyInput))
	{
		$errorMessage = "*All fields required. Please select a primary pharmacy.";
	}
	else if(empty($doctorInput))
	{
		$errorMessage = "*All fields required. Please select a primary doctor.";
	}
	else if(empty($labInput))
	{
		$errorMessage = "*All fields required. Please select a primary lab.";
	}
	else if(empty($passwordInput))
	{
		$errorMessage = "*All fields required. Please enter your password.";
	}
	else if(empty($confirmPasswordInput))
	{
		$errorMessage = "*All fields required. Please confirm your password.";
	}
	else if($passwordInput != $confirmPasswordInput)
	{
		$errorMessage = "*Passwords do not match. Please try again.";
		$passwordInput = '';
		$confirmPasswordInput = '';
	}
	else
	{
		$query = mysqli_query($conn, "SELECT * FROM Addresses WHERE Street = '$streetInput' AND City = '$cityInput' AND StateCode = '$stateInput' AND ZipCode = '$zipInput'");
		$result = mysqli_fetch_assoc($query);
		
		if(!$result)
		{
			$query = mysqli_query($conn, "INSERT INTO Addresses (Street, City, StateCode, ZipCode) VALUES ('$streetInput','$cityInput','$stateInput','$zipInput')");
			$query = mysqli_query($conn, "SELECT * FROM Addresses WHERE Street = '$streetInput' AND City = '$cityInput' AND StateCode = '$stateInput' AND ZipCode = '$zipInput'");
			$result = mysqli_fetch_assoc($query);
		}
		
		$query = mysqli_query($conn, "INSERT INTO Patients (FirstName, LastName, SocialSecurityNumber, DateOfBirth, EmailAddress, PhoneNumber, AddressID, PharmacyID, DoctorID, LabID, Password) VALUES ('$firstNameInput','$lastNameInput','$ssnInput','$dobInput','$emailInput','$phoneInput','" . $result['AddressID'] . "','$pharmacyInput','$doctorInput','$labInput','$passwordInput')");
		header("Location:login.php");
		exit();
	}
}

if(isset($_POST['cancelButton']))
{
	header("Location:login.php");
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
		<form name = "registerForm" class = "register" action = "register.php" method = "post">
			<section class = "formHeader">
				<h3>Register</h3>
			</section>
			<?php echo "<span class = errorMessage>$errorMessage</span>" ?>
			<section class = "formFields">
				<legend>First Name</legend>
				<input name = "firstNameInput" class = "formElement" type = "text" value = "<?php echo $firstNameInput ?>"/>
				</br>
				<legend>Last Name</legend>
				<input name = "lastNameInput" class = "formElement" type = "text" value = "<?php echo $lastNameInput ?>"/>
				</br>
				<legend>Social Security Number</legend>
				<input name = "ssnInput" class = "formElement" type = "text" placeholder = "XXX-XX-XXXX" value = "<?php echo $ssnInput ?>"/>
				</br>
				<legend>Date of Birth</legend>
				<input name = "dobInput" class = "formElement" type = "date" value = "<?php echo $dobInput ?>" max = "<?php echo $currentDate ?>"/>
				</br>
				<legend>Email Address</legend>
				<input name = "emailInput" class = "formElement" type = "text" value = "<?php echo $emailInput ?>"/>
				</br>
				<legend>Phone Number</legend>
				<input name = "phoneInput" class = "formElement" type = "text" value = "<?php echo $phoneInput ?>"/>
				</br>
				<legend>Street Address</legend>
				<input name = "streetInput" class = "formElement" type = "text" value = "<?php echo $streetInput ?>"/>
				</br>
				<legend>City</legend>
				<input name = "cityInput" class = "formElement" type = "text" value = "<?php echo $cityInput ?>"/>
				</br>
				<legend>State</legend>
				<input name = "stateInput" class = "formElement" type = "text" placeholder = "ID" value = "<?php echo $stateInput ?>"/>
				</br>
				<legend>Zip Code</legend>
				<input name = "zipInput" class = "formElement" type = "text" placeholder = "XXXXX" value = "<?php echo $zipInput ?>"/>
				</br>
				<legend>Pharmacy</legend>
				<select name = "pharmacyInput" class = "formElement">
					<option value = "0" disabled selected>Select Pharmacy</option>
					<?php
					$query = mysqli_query($conn, "SELECT * FROM Pharmacies");
					$result = mysqli_fetch_assoc($query);

					while($result)
					{
						echo "<option value = \"" . $result['PharmacyID'] . "\"";
						
						if($result['PharmacyID'] == $pharmacyInput)
						{
							echo " selected";
						}
						
						echo ">" . $result['Name'] . "</option>";
						$result = mysqli_fetch_assoc($query);
					}
					?>
				</select>
				</br>
				<legend>Doctor</legend>
				<select name = "doctorInput" class = "formElement">
					<option value = "0" disabled selected>Select Doctor</option>
					<?php
					$query = mysqli_query($conn, "SELECT * FROM Doctors");
					$result = mysqli_fetch_assoc($query);

					while($result)
					{
						echo "<option value = \"" . $result['DoctorID'] . "\"";
						
						if($result['DoctorID'] == $doctorInput)
						{
							echo " selected";
						}
						
						echo ">" . $result['FirstName'] . " " . $result['LastName'] . "</option>";
						$result = mysqli_fetch_assoc($query);
					}
					?>
				</select>
				</br>
				<legend>Lab</legend>
				<select name = "labInput" class = "formElement">
					<option value = "0" disabled selected>Select Lab</option>
					<?php
					$query = mysqli_query($conn, "SELECT * FROM Labs");
					$result = mysqli_fetch_assoc($query);

					while($result)
					{
						echo "<option value = \"" . $result['LabID'] . "\"";
						
						if($result['LabID'] == $labInput)
						{
							echo " selected";
						}
						
						echo ">" . $result['Name'] . "</option>";
						$result = mysqli_fetch_assoc($query);
					}
					?>
				</select>
				</br>
				<legend>Password</legend>
				<input name = "passwordInput" class = "formElement" type = "password" value = "<?php echo $passwordInput ?>"/>
				</br>
				<legend>Confirm Password</legend>
				<input name = "confirmPasswordInput" class = "formElement" type = "password" value = "<?php echo $confirmPasswordInput ?>"/>
			</section>
			</br>
			<section class = "formButtons">
				<input name = "registerButton" class = "button green" type = "submit" value = "Register"/>
				<input name = "cancelButton" class = "button red" type = "submit" value = "Cancel"/>
			</section>
		</form>
	</body>
</html>