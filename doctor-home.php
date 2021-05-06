<?php
session_start();
error_reporting(0);
include 'includes/dbconn.php';
$conn = new mysqli($servername, $username, "", "db1", $sqlport, $socket);

if($conn->connect_error)
{
	die("Connection failed: " . $conn->connect_error);
}

$doctorID = $_SESSION['DOCTOR_ID'];
$doctor = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM Doctors WHERE DoctorID = '$doctorID'"));
$address = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM Addresses WHERE AddressID = '" . $doctor['AddressID'] . "'"));
?>

<html>
	<head>
		<title>MyHealth Portal</title>
		<link href = 'style.css' rel = 'stylesheet'>
	</head>
	<body>
		<?php require "doctor-header.php" ?>
		<div class = "infoBox doctor">
			<section class = "infoHeader">
				<h3>Account Information</h3>
			</section>
			<table class = "infoFields">
				<tr>
					<td>
						<legend>First Name</legend>
						<span><?php echo $doctor['FirstName'] ?></span>
					</td>
					<td>
						<legend>Last Name</legend>
						<span><?php echo $doctor['LastName'] ?></span>
					</td>
				</tr>
				<tr>
					<td>
						<legend>Email Address</legend>
						<span><?php echo $doctor['EmailAddress'] ?></span>
					</td>
					<td>
						<legend>Phone Number</legend>
						<span><?php echo $doctor['PhoneNumber'] ?></span>
					</td>
				</tr>
				<tr>
					<td>
						<legend>Street Address</legend>
						<span><?php echo $address['Street'] ?></span>
						</br>
						<span><?php echo $address['City'] ?></span>
						</br>
						<span><?php echo $address['StateCode'] . " " . $address['ZipCode'] ?></span>
					</td>
				</tr>
			</table>
		</div>
	</body>
</html>