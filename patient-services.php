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
$currentDate = mysqli_fetch_assoc(mysqli_query($conn, "SELECT CURDATE()"))["CURDATE()"];
$serviceInput = '';
$doctorInput = $patient['DoctorID'];
$dateInput = '';
$timeInput = '';
$errorMessage = '';
$detailsMessage = '';

if(isset($_POST['viewDetailsButton']))
{
	$serviceInput = $_POST['serviceInput'];
	$doctorInput = $_POST['doctorInput'];
	$dateInput = $_POST['dateInput'];
	$timeInput = $_POST['timeInput'];
	
	if(empty($serviceInput))
	{
		$errorMessage = "*First two fields required. Please select a service.";
	}
	else if(empty($doctorInput))
	{
		$errorMessage = "*First two fields required. Please select a doctor.";
	}
	else
	{
		$service = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM Services WHERE ServiceID = '$serviceInput'"));
		$doctor = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM Doctors WHERE DoctorID = '$doctorInput'"));
		$doctorAddress = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM Addresses WHERE AddressID = '" . $doctor['AddressID'] . "'"));
		$hospital = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM Hospitals WHERE HospitalID = '" . $doctor['HospitalID'] . "'"));
		$hospitalAddress = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM Addresses WHERE AddressID = '" . $hospital['AddressID'] . "'"));
		$detailsMessage = 	"<div class = \"infoBox patient\">
								<section class = infoHeader>
									<h3>" . $service['Name'] . " Details</h3>
								</section>
								<section class = infoFields>
									<legend>Cost</legend>
									<span>$" . $service['Cost'] . "</span>
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
											<span>" . $doctorAddress['Street'] . "</span>
											</br>
											<span>" . $doctorAddress['City'] . "</span>
											</br>
											<span>" . $doctorAddress['StateCode'] . " " . $doctorAddress['ZipCode'] . "</span>
										</td>
									</tr>
								</table>
								</br>
								<section class = infoHeader>
									<h3>" . $hospital['Name'] . " Details</h3>
								</section>
								<table class = infoFields>
									<tr>
										<td>
											<legend>Email Address</legend>
											<span>" . $hospital['EmailAddress'] . "</span>
										</td>
										<td>
											<legend>Phone Number</legend>
											<span>" . $hospital['PhoneNumber'] . "</span>
										</td>
									</tr>
									<tr>
										<td>
											<legend>Street Address</legend>
											<span>" . $hospitalAddress['Street'] . "</span>
											</br>
											<span>" . $hospitalAddress['City'] . "</span>
											</br>
											<span>" . $hospitalAddress['StateCode'] . " " . $hospitalAddress['ZipCode'] . "</span>
										</td>
									</tr>
								</table>
							</div>";
	}
}

if(isset($_POST['scheduleButton']))
{
	$serviceInput = $_POST['serviceInput'];
	$doctorInput = $_POST['doctorInput'];
	$dateInput = $_POST['dateInput'];
	$timeInput = $_POST['timeInput'];
	
	if(empty($serviceInput))
	{
		$errorMessage = "*All fields required. Please select a service.";
	}
	else if(empty($doctorInput))
	{
		$errorMessage = "*All fields required. Please select a doctor.";
	}
	else if(empty($dateInput))
	{
		$errorMessage = "*All fields required. Please select a date.";
	}
	else if(empty($timeInput))
	{
		$errorMessage = "*All fields required. Please select a time.";
	}
	else
	{
		$query = mysqli_query($conn, "INSERT INTO ServiceAppointments (PatientID, ServiceID, DoctorID, AppointmentDate, AppointmentTime) VALUES ('$patientID','$serviceInput','$doctorInput','$dateInput','$timeInput')");
		$serviceInput = '';
		$doctorInput = $patient['DoctorID'];
		$dateInput = '';
		$timeInput = '';
	}
}

$query = mysqli_query($conn, "SELECT * FROM ServiceAppointments WHERE PatientID = '$patientID' AND AppointmentDate >= CURDATE() ORDER BY AppointmentDate, AppointmentTime");
$result = mysqli_fetch_assoc($query);
$cancelButtons = [];

while($result)
{
	$cancelButtons[] = $result['ServiceAppointmentID'];
	$result = mysqli_fetch_assoc($query);
}

foreach($cancelButtons as $cancel)
{
	if(isset($_POST["$cancel"]))
	{
		$query = mysqli_query($conn, "DELETE FROM ServiceAppointments WHERE ServiceAppointmentID = '$cancel'");
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
		<form name = "scheduleServicesForm" class = "patient" action = "patient-services.php" method = "post">
			<section class = "formHeader">
				<h3>Schedule Services</h3>
			</section>
			<?php echo "<span class = errorMessage>$errorMessage</span>" ?>
			<section class = "formFields">
				<legend>Service</legend>
				<select name = "serviceInput" class = "formElement">
					<option value = "0" disabled selected>Select Service</option>
					<?php
					$query = mysqli_query($conn, "SELECT * FROM Services");
					$result = mysqli_fetch_assoc($query);

					while($result)
					{
						echo "<option value = \"" . $result['ServiceID'] . "\"";
						
						if($result['ServiceID'] == $serviceInput)
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
				<legend>Appointment Date</legend>
				<input name="dateInput" class = "formElement" type="date" value = "<?php echo $currentDate ?>" min = "<?php echo $currentDate ?>"/>
				</br>
				<legend>Appointment Time</legend>
				<select name = "timeInput" class = "formElement">
					<option value="0" disabled selected>Select Time</option>
					<?php
					$timeStart = 8;
					$timeEnd = 16;
						
					for($i = $timeStart; $i <= $timeEnd; $i++)
					{
						if($i > 12)
						{
							$temp = $i - 12;
						}
						else
						{
							$temp = $i;
						}
						
						if($i > 11 && $i < 24)
						{
							$midday = "PM";
						}
						else
						{
							$midday = "AM";
						}
						
						echo "<option value = \"$i\"";
						
						if($i == $timeInput)
						{
							echo " selected";
						}
						
						echo ">$temp $midday</option>";
					}
					?>
				</select>
			</section>
			</br>
			<section class "formButtons">
				<input name = "viewDetailsButton" class = "button blue" type = "submit" value = "View Details"/>
				<input name = "scheduleButton" class = "button green" type = "submit" value = "Schedule"/>
			</section>
		</form>
		<?php
		echo $detailsMessage;
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
				
				echo	"<form name = cancelServiceAppointmentsForm class = patient action = patient-services.php method = post>
							<section class = formHeader>
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
							</br>
							<section class = formButtons>
								<input name = " . $result['ServiceAppointmentID'] . " class = \"button red\" type = submit value = Cancel />
							</section>
						</form>";
				$result = mysqli_fetch_assoc($query);
			}
		}
		?>
	</body>
</html>