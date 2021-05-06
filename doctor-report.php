<?php
session_start();
error_reporting(0);
include 'includes/dbconn.php';
$conn = new mysqli($servername, $username, "", "db1", $sqlport, $socket);

if($conn->connect_error)
{
	die("Connection failed: " . $conn->connect_error);
}

$patientInput = '';
$errorMessage = '';
$detailsMessage = '';

if(isset($_POST['viewButton']))
{
	$patientInput = $_POST['patientInput'];
	
	if(empty($patientInput))
	{
		$errorMessage = "*All fields required. Please select a patient.";
	}
	else
	{
		$patient = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM Patients WHERE PatientID = '$patientInput'"));
		$address = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM Addresses WHERE AddressID = '" . $patient['AddressID'] . "'"));
		$pharmacy = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM Pharmacies WHERE PharmacyID = '" . $patient['PharmacyID'] . "'"));
		$doctor = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM Doctors WHERE DoctorID = '" . $patient['DoctorID'] . "'"));
		$lab = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM Labs WHERE LabID = '" . $patient['LabID'] . "'"));
		$detailsMessage =	"<div class = \"infoBox doctor\">
								<section class = infoHeader>
									<h3>Account Information</h3>
								</section>
								<table class = infoFields>
									<tr>
										<td>
											<legend>First Name</legend>
											<span>" . $patient['FirstName'] . "</span>
										</td>
										<td>
											<legend>Last Name</legend>
											<span>" . $patient['LastName'] . "</span>
										</td>
									</tr>
									<tr>
										<td>
											<legend>Social Security Number</legend>
											<span>" . $patient['SocialSecurityNumber'] . "</span>
										</td>
										<td>
											<legend>Date of Birth</legend>
											<span>" . $patient['DateOfBirth'] . "</span>
										</td>
									</tr>
									<tr>
										<td>
											<legend>Email Address</legend>
											<span>" . $patient['EmailAddress'] . "</span>
										</td>
										<td>
											<legend>Phone Number</legend>
											<span>" . $patient['PhoneNumber'] . "</span>
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
										<td>
											<legend>Primary Pharmacy</legend>
											<span>" . $pharmacy['Name'] . "</span>
											</br>
											<span>" . $pharmacy['EmailAddress'] . "</span>
											</br>
											<span>" . $pharmacy['PhoneNumber'] . "</span>
										</td>
									</tr>
									<tr>
										<td>
											<legend>Primary Doctor</legend>
											<span>" . $doctor['FirstName'] . " " . $doctor['LastName'] . "</span>
											</br>
											<span>" . $doctor['EmailAddress'] . "</span>
											</br>
											<span>" . $doctor['PhoneNumber'] . "</span>
										</td>
										<td>
											<legend>Primary Lab</legend>
											<span>" . $lab['Name'] . "</span>
											</br>
											<span>" . $lab['EmailAddress'] . "</span>
											</br>
											<span>" . $lab['PhoneNumber'] . "</span>
										</td>
									</tr>
								</table>
							</div>";
		$detailsMessage .= "<h2>Insurance Plans</h2>";
		$query = mysqli_query($conn, "SELECT * FROM PatientsInsurancePlansAndCoverage WHERE PatientID = '$patientInput'");
		$result = mysqli_fetch_assoc($query);
		
		if(!$result)
		{
			$detailsMessage .=	"<div class = \"infoBox doctor\">
									<span>This patient isn't signed up for any insurance plans.</span>
								</div>";
		}
		else
		{
			while($result)
			{
				$insurancePlan = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM InsurancePlans WHERE InsurancePlanID = '" . $result['InsurancePlanID'] . "'"));
				$detailsMessage .=	"<div class = \"infoBox doctor\">
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
		
		$detailsMessage .= "<h2>Service Appointments</h2>";
		$query = mysqli_query($conn, "SELECT * FROM ServiceAppointments WHERE PatientID = '$patientInput' AND AppointmentDate >= CURDATE() ORDER BY AppointmentDate, AppointmentTime");
		$result = mysqli_fetch_assoc($query);
		
		if(!$result)
		{
			$detailsMessage .=	"<div class = \"infoBox doctor\">
									<span>This patient doesn't have any service appointments.</span>
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
				
				$detailsMessage .=	"<div class = \"infoBox doctor\">
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
		
		/*$detailsMessage .= "<h2>Test Appointments</h2>";
		$query = mysqli_query($conn, "SELECT * FROM TestAppointments WHERE PatientID = '$patientInput' AND AppointmentDate >= CURDATE() ORDER BY AppointmentDate, AppointmentTime");
		$result = mysqli_fetch_assoc($query);
		
		if(!$result)
		{
			$detailsMessage .=	"<div class = \"infoBox doctor\">
									<span>This patient doesn't have any test appointments.</span>
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
				
				$detailsMessage .=	"<div class = \"infoBox doctor\">
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
		
		$detailsMessage .= "<h2>Diagnoses</h2>";
		$query = mysqli_query($conn, "SELECT * FROM PatientsDiagnoses WHERE PatientID = '$patientInput' ORDER BY DiagnosisDate");
		$result = mysqli_fetch_assoc($query);
		
		if(!$result)
		{
			$detailsMessage .=	"<div class = \"infoBox doctor\">
									<span>This patient doesn't have any diagnoses.</span>
								</div>";
		}
		else
		{
			while($result)
			{
				$diagnosis = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM Diagnoses WHERE DiagnosisID = '" . $result['DiagnosisID'] . "'"));
				$doctor = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM Doctors WHERE DoctorID = '" . $result['DoctorID'] . "'"));
				$address = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM Addresses WHERE AddressID = '" . $doctor['AddressID'] . "'"));
				$detailsMessage .=	"<div class = \"infoBox doctor\">
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
		
		$detailsMessage .= "<h2>Prescriptions</h2>";
		$query = mysqli_query($conn, "SELECT * FROM PatientsPrescriptions WHERE PatientID = '$patientInput' ORDER BY PrescriptionDate");
		$result = mysqli_fetch_assoc($query);
		
		if(!$result)
		{
			$detailsMessage .=	"<div class = \"infoBox doctor\">
									<span>This patient doesn't have any prescriptions.</span>
								</div>";
		}
		else
		{
			while($result)
			{
				$product = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM Products WHERE ProductID = '" . $result['ProductID'] . "'"));
				$doctor = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM Doctors WHERE DoctorID = '" . $result['DoctorID'] . "'"));
				$address = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM Addresses WHERE AddressID = '" . $doctor['AddressID'] . "'"));
				$detailsMessage .=	"<div class = \"infoBox doctor\">
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
		
		$detailsMessage .= "<h2>Bills</h2>";
		$query = mysqli_query($conn, "SELECT * FROM ServiceBills WHERE PatientID = '$patientInput' AND DueDate >= CURDATE() ORDER BY DueDate");
		$result = mysqli_fetch_assoc($query);
		
		if(!$result)
		{
			$detailsMessage .=	"<div class = \"infoBox doctor\">
									<span>This patient doesn't have any bills to pay.</span>
								</div>";
		}
		else
		{
			while($result)
			{
				$service = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM Services WHERE ServiceID = '" . $result['ServiceID'] . "'"));
				$doctor = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM Doctors WHERE DoctorID = '" . $result['DoctorID'] . "'"));
				$address = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM Addresses WHERE AddressID = '" . $doctor['AddressID'] . "'"));
				$detailsMessage .=	"<div class = \"infoBox doctor\">
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
		
		$patientInput = '';
	}
}
?>

<html>
	<head>
		<title>MyHealth Portal</title>
		<link href = 'style.css' rel = 'stylesheet'>
	</head>
	<body>
		<?php require "doctor-header.php" ?>
		<form name = "viewPatientsForm" class = "doctor" action = "doctor-report.php" method = "post">
			<section class = "formHeader">
				<h3>View Patients</h3>
			</section>
			<?php echo "<span class = errorMessage>$errorMessage</span>" ?>
			<section class = "formFields">
				<legend>Patient</legend>
				<select name = "patientInput" class = "formElement">
					<option value = "0" disabled selected>Select Patient</option>
					<?php
					$query = mysqli_query($conn, "SELECT * FROM Patients");
					$result = mysqli_fetch_assoc($query);

					while($result)
					{
						echo "<option value = \"" . $result['PatientID'] . "\"";
						
						if($result['PatientID'] == $patientInput)
						{
							echo " selected";
						}
						
						echo ">" . $result['FirstName'] . " " . $result['LastName'] . "</option>";
						$result = mysqli_fetch_assoc($query);
					}
					?>
				</select>
			</section>
			</br>
			<section class = "formButtons">
				<input name = "viewButton" class = "button green" type = "submit" value = "View"/>
			</section>
		</form>
		<?php echo $detailsMessage ?>
	</body>
</html>