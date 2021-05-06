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
				echo	"<div class = \"infoBox patient\">
							<section class = infoHeader>
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
						</div>";
				$result = mysqli_fetch_assoc($query);
			}
		}
		
		echo "<h2>Service Appointments</h2>";
		$query = mysqli_query($conn, "SELECT * FROM ServiceAppointments WHERE PatientID = '$patientID' AND AppointmentDate >= CURDATE() ORDER BY AppointmentDate, AppointmentTime");
		$result = mysqli_fetch_assoc($query);
		
		if(!$result)
		{
			echo	"<div class = \"infoBox patient\">
						<span>You don't have any service appointments.</span>
					</div>";
		}
		else
		{
			while($result)
			{
				$service = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM Services WHERE ServiceID = '" . $result['ServiceID'] . "'"));
				$doctor = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM Doctors WHERE DoctorID = '" . $result['DoctorID'] . "'"));
				$hospital = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM Hospitals WHERE HospitalID = '" . $doctor['HospitalID'] . "'"));
				
				if($result['AppointmentTime'] > 12)
				{
					$temp = $result['AppointmentTime'] - 12;
				}
				else
				{
					$temp = $result['AppointmentTime'];
				}
				
				if($result['AppointmentTime'] > 11 && $result['AppointmentTime'] < 24)
				{
					$midday = "PM";
				}
				else
				{
					$midday = "AM";
				}
				
				echo	"<div class = \"infoBox patient\">
							<section class = infoHeader>
								<h3>" . $service['Name'] . " Appointment Details</h3>
							</section>
							<table class = infoFields>
								<tr>
									<td>
										<legend>Doctor</legend>
										<span>" . $doctor['FirstName'] . " " . $doctor['LastName'] . "</span>
									</td>
									<td>
										<legend>Hospital</legend>
										<span>" . $hospital['Name'] . "</span>
									</td>
								</tr>
								<tr>
									<td>
										<legend>Appointment Date</legend>
										<span>" . $result['AppointmentDate'] . "</span>
									</td>
									<td>
										<legend>Appointment Time</legend>
										<span>$temp $midday</span>
									</td>
								</tr>
							</table>
						</div>";
				$result = mysqli_fetch_assoc($query);
			}
		}
		
		/*echo "<h2>Test Appointments</h2>";
		$query = mysqli_query($conn, "SELECT * FROM TestAppointments WHERE PatientID = '$patientID' AND AppointmentDate >= CURDATE() ORDER BY AppointmentDate, AppointmentTime");
		$result = mysqli_fetch_assoc($query);
		
		if(!$result)
		{
			echo	"<div class = \"infoBox patient\">
						<span>You don't have any test appointments.</span>
					</div>";
		}
		else
		{
			while($result)
			{
				$test = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM Tests WHERE TestID = '" . $result['TestID'] . "'"));
				$lab = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM Labs WHERE LabID = '" . $result['LabID'] . "'"));
				
				if($result['AppointmentTime'] > 12)
				{
					$temp = $result['AppointmentTime'] - 12;
				}
				else
				{
					$temp = $result['AppointmentTime'];
				}
				
				if($result['AppointmentTime'] > 11 && $result['AppointmentTime'] < 24)
				{
					$midday = "PM";
				}
				else
				{
					$midday = "AM";
				}
				
				echo	"<div class = \"infoBox patient\">
							<section class = infoHeader>
								<h3>" . $test['Name'] . " Appointment Details</h3>
							</section>
							<table class = infoFields>
								<tr>
									<td>
										<legend>Lab</legend>
										<span>" . $lab['Name'] . "</span>
									</td>
								</tr>
								<tr>
									<td>
										<legend>Appointment Date</legend>
										<span>" . $result['AppointmentDate'] . "</span>
									</td>
									<td>
										<legend>Appointment Time</legend>
										<span>$temp $midday</span>
									</td>
								</tr>
							</table>
						</div>";
				$result = mysqli_fetch_assoc($query);
			}
		}*/
		
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
		
		echo "<h2>Prescriptions</h2>";
		$query = mysqli_query($conn, "SELECT * FROM PatientsPrescriptions WHERE PatientID = '$patientID' ORDER BY PrescriptionDate");
		$result = mysqli_fetch_assoc($query);
		
		if(!$result)
		{
			echo	"<div class = \"infoBox patient\">
						<span>You don't have any prescriptions.</span>
					</div>";
		}
		else
		{
			while($result)
			{
				$product = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM Products WHERE ProductID = '" . $result['ProductID'] . "'"));
				$doctor = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM Doctors WHERE DoctorID = '" . $result['DoctorID'] . "'"));
				$address = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM Addresses WHERE AddressID = '" . $doctor['AddressID'] . "'"));
				echo	"<div class = \"infoBox patient\">
							<section class = infoHeader>
								<h3>" . $product['Name'] . " Details</h3>
							</section>
							<section class = infoFields>
								<legend>Note</legend>
								<span>" . $result['Note'] . "</span>
								<legend>Prescription Date</legend>
								<span>" . $result['PrescriptionDate'] . "</span>
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
		
		echo "<h2>Bills</h2>";
		$query = mysqli_query($conn, "SELECT * FROM ServiceBills WHERE PatientID = '$patientID' AND DueDate >= CURDATE() ORDER BY DueDate");
		$result = mysqli_fetch_assoc($query);
		
		if(!$result)
		{
			echo	"<div class = \"infoBox patient\">
						<span>You don't have any bills to pay.</span>
					</div>";
		}
		else
		{
			while($result)
			{
				$service = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM Services WHERE ServiceID = '" . $result['ServiceID'] . "'"));
				$doctor = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM Doctors WHERE DoctorID = '" . $result['DoctorID'] . "'"));
				$address = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM Addresses WHERE AddressID = '" . $doctor['AddressID'] . "'"));
				echo	"<div class = \"infoBox patient\">
							<section class = infoHeader>
								<h3>" . $service['Name'] . " Details</h3>
							</section>
							<section class = infoFields>
								<legend>Cost</legend>
								<span>$" . $service['Cost'] . "</span>
								<legend>Due Date</legend>
								<span>" . $result['DueDate'] . "</span>
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