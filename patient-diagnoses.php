<?php
session_start();
error_reporting(0);
include 'includes/dbconn.php';
$conn = new mysqli($servername, $username, "", "db1", $sqlport, $socket);

if($conn->connect_error)
{
	die("Connection failed: " . $conn->connect_error);
}

$patientID = $_SESSION['PATIENT_ID'];
?>

<html>
	<head>
		<title>MyHealth Portal</title>
		<link href = 'style.css' rel = 'stylesheet'>
	</head>
	<body>
		<?php
		require "patient-header.php";
		echo "<h2>Diagnoses</h2>";
		$query = mysqli_query($conn, "SELECT * FROM PatientsDiagnoses WHERE PatientID = '$patientID' ORDER BY DiagnosisDate");
		$result = mysqli_fetch_assoc($query);
		
		if(!$result)
		{
			echo	"<div class = \"infoBox patient\">
						<span>You don't have any diagnoses.</span>
					</div>";
		}
		else
		{
			while($result)
			{
				$diagnosis = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM Diagnoses WHERE DiagnosisID = '" . $result['DiagnosisID'] . "'"));
				$doctor = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM Doctors WHERE DoctorID = '" . $result['DoctorID'] . "'"));
				$address = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM Addresses WHERE AddressID = '" . $doctor['AddressID'] . "'"));
				echo	"<div class = \"infoBox patient\">
							<section class = infoHeader>
								<h3>" . $diagnosis['Name'] . " Details</h3>
							</section>
							<section class = infoFields>
								<legend>Note</legend>
								<span>" . $result['Note'] . "</span>
								<legend>Diagnosis Date</legend>
								<span>" . $result['DiagnosisDate'] . "</span>
							</section>
							</br>
							<section class = infoHeader>
								<h3>" . $doctor['FirstName'] . " " . $doctor['LastName'] . " Details</h3>
							</section>
							<table class = infoFields>
								<tr>
									<td>
										<legend>Email Address</legend>
										<span>" . $doctor['EmailAddress'] . "</span>
									</td>
									<td>
										<legend>Phone Number</legend>
										<span>" . $doctor['PhoneNumber'] . "</span>
									</td>
								</tr>
								<tr>
									<td>
										<legend>Street Address</legend>
										<span>" . $address['Street'] . "</span>
										</br>
										<span>" . $address['City'] . "</span>
										</br>
										<span>" . $address['StateCode'] . " " . $address['ZipCode'] . "</span>
									</td>
								</tr>
							</table>
						</div>";
				$result = mysqli_fetch_assoc($query);
			}
		}
		?>
	</body>
</html>