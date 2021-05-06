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
$patient = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM Patients WHERE PatientID = '$patientID'"));
$address = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM Addresses WHERE AddressID = '" . $patient['AddressID'] . "'"));
$pharmacy = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM Pharmacies WHERE PharmacyID = '" . $patient['PharmacyID'] . "'"));
$doctor = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM Doctors WHERE DoctorID = '" . $patient['DoctorID'] . "'"));
$lab = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM Labs WHERE LabID = '" . $patient['LabID'] . "'"));
$query = mysqli_query($conn, "SELECT * FROM PatientsInsurancePlansAndCoverage WHERE PatientID = '$patientID'");
$result = mysqli_fetch_assoc($query);
$cancelButtons = [];

while($result)
{
	$cancelButtons[] = $result['InsurancePlanID'];
	$result = mysqli_fetch_assoc($query);
}

foreach($cancelButtons as $cancel)
{
	if(isset($_POST["$cancel"]))
	{
		$query = mysqli_query($conn, "DELETE FROM PatientsInsurancePlansAndCoverage WHERE PatientID = '$patientID' AND InsurancePlanID = '$cancel'");
	}
}
?>

<html>
	<head>
		<title>MyHealth Portal</title>
		<link href = 'style.css' rel = 'stylesheet'>
	</head>
	<body>
		<?php require "patient-header.php" ?>
		<div class = "infoBox patient">
			<section class = "infoHeader">
				<h3>Account Information</h3>
			</section>
			<table class = "infoFields">
				<tr>
					<td>
						<legend>First Name</legend>
						<span><?php echo $patient['FirstName'] ?></span>
					</td>
					<td>
						<legend>Last Name</legend>
						<span><?php echo $patient['LastName'] ?></span>
					</td>
				</tr>
				<tr>
					<td>
						<legend>Social Security Number</legend>
						<span><?php echo $patient['SocialSecurityNumber'] ?></span>
					</td>
					<td>
						<legend>Date of Birth</legend>
						<span><?php echo $patient['DateOfBirth'] ?></span>
					</td>
				</tr>
				<tr>
					<td>
						<legend>Email Address</legend>
						<span><?php echo $patient['EmailAddress'] ?></span>
					</td>
					<td>
						<legend>Phone Number</legend>
						<span><?php echo $patient['PhoneNumber'] ?></span>
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
					<td>
						<legend>Primary Pharmacy</legend>
						<span><?php echo $pharmacy['Name'] ?></span>
						</br>
						<span><?php echo $pharmacy['EmailAddress'] ?></span>
						</br>
						<span><?php echo $pharmacy['PhoneNumber'] ?></span>
					</td>
				</tr>
				<tr>
					<td>
						<legend>Primary Doctor</legend>
						<span><?php echo $doctor['FirstName'] . " " . $doctor['LastName'] ?></span>
						</br>
						<span><?php echo $doctor['EmailAddress'] ?></span>
						</br>
						<span><?php echo $doctor['PhoneNumber'] ?></span>
					</td>
					<td>
						<legend>Primary Lab</legend>
						<span><?php echo $lab['Name'] ?></span>
						</br>
						<span><?php echo $lab['EmailAddress'] ?></span>
						</br>
						<span><?php echo $lab['PhoneNumber'] ?></span>
					</td>
				</tr>
			</table>
		</div>
		<?php
		echo "<h2>Insurance Plans</h2>";
		$query = mysqli_query($conn, "SELECT * FROM PatientsInsurancePlansAndCoverage WHERE PatientID = '$patientID'");
		$result = mysqli_fetch_assoc($query);
		
		if(!$result)
		{
			echo	"<div class = \"infoBox patient\">
						<span>You aren't signed up for any insurance plans.</span>
					</div>";
		}
		else
		{
			while($result)
			{
				$insurancePlan = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM InsurancePlans WHERE InsurancePlanID = '" . $result['InsurancePlanID'] . "'"));
				echo	"<form name = cancelInsurancePlansForm class = patient action = patient-home.php method = post>
							<section class = formHeader>
								<h3>" . $insurancePlan['Name'] . " Details</h3>
							</section>
							<table class = infoFields>
								<tr>
									<td>
										<legend>Annual Premium</legend>
										<span>$" . $insurancePlan['AnnualPremium'] . "</span>
									</td>
									<td>
										<legend>Product Deductible</legend>
										<span>$" . $insurancePlan['ProductDeductible'] . "</span>
									</td>
									<td>
										<legend>Service Deductible</legend>
										<span>$" . $insurancePlan['ServiceDeductible'] . "</span>
									</td>
								</tr>
								<tr>
									<td>
										<legend>Test Deductible</legend>
										<span>$" . $insurancePlan['TestDeductible'] . "</span>
									</td>
									<td>
										<legend>Plan Contribution</legend>
										<span>$" . $insurancePlan['PlanContribution'] . "</span>
									</td>
									<td>
										<legend>Max Coverage</legend>
										<span>$" . $insurancePlan['MaxCoverage'] . "</span>
									</td>
								</tr>
							</table>
							</br>
							<section class = formButtons>
								<input name = " . $insurancePlan['InsurancePlanID'] . " class = \"button red\" type = submit value = Cancel />
							</section>
						</form>";
				$result = mysqli_fetch_assoc($query);
			}
		}
		?>
	</body>
</html>