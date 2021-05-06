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
$currentDate = mysqli_fetch_assoc(mysqli_query($conn, "SELECT CURDATE()"))["CURDATE()"];
$patientInput = '';
$serviceInput = '';
$dateInput = '';
$errorMessage = '';
$detailsMessage = '';

if(isset($_POST['viewDetailsButton']))
{
	$patientInput = $_POST['patientInput'];
	$serviceInput = $_POST['serviceInput'];
	$dateInput = $_POST['dateInput'];
	
	if(empty($patientInput))
	{
		$errorMessage = "*First two fields required. Please select a patient.";
	}
	else if(empty($serviceInput))
	{
		$errorMessage = "*First two fields required. Please select a service.";
	}
	else
	{
		$patient = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM Patients WHERE PatientID = '$patientInput'"));
		$doctor = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM Doctors WHERE DoctorID = '" . $patient['DoctorID'] . "'"));
		$service = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM Services WHERE ServiceID = '$serviceInput'"));
		$detailsMessage = 	"<div class = \"infoBox doctor\">
								<section class = infoHeader>
									<h3>" . $patient['FirstName'] . " " . $patient['LastName'] . " Details</h3>
								</section>
								<table class = infoFields>
									<tr>
										<td>
											<legend>Date of Birth</legend>
											<span>" . $patient['DateOfBirth'] . "</span>
										</td>
										<td>
											<legend>Email Address</legend>
											<span>" . $patient['EmailAddress'] . "</span>
										</td>
									</tr>
									<tr>
										<td>
											<legend>Phone Number</legend>
											<span>" . $patient['PhoneNumber'] . "</span>
										</td>
										<td>
											<legend>Primary Doctor</legend>
											<span>" . $doctor['FirstName'] . " " . $doctor['LastName'] . "</span>
										</td>
									</tr>
								</table>
								</br>
								<section class = infoHeader>
									<h3>" . $service['Name'] . " Details</h3>
								<section>
								<section class = infoFields>
									<legend>Cost</legend>
									<span>$" . $service['Cost'] . "</span>
								</section>
							</div>";
		$query = mysqli_query($conn, "SELECT * FROM ServiceBills WHERE PatientID = '$patientInput' AND DueDate >= CURDATE() ORDER BY DueDate");
		$result = mysqli_fetch_assoc($query);
		
		if(!$result)
		{
			$detailsMessage .=	"<div class = \"infoBox doctor\">
									<section class = infoFields>
										<span>This patient doesn't have any bills to pay.</span>
									</section>
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
	}
}

if(isset($_POST['billButton']))
{
	$patientInput = $_POST['patientInput'];
	$serviceInput = $_POST['serviceInput'];
	$dateInput = $_POST['dateInput'];
	
	if(empty($patientInput))
	{
		$errorMessage = "*All fields required. Please select a patient.";
	}
	else if(empty($serviceInput))
	{
		$errorMessage = "*All fields required. Please select a service.";
	}
	else if(empty($dateInput))
	{
		$errorMessage = "*All fields required. Please select a date.";
	}
	else
	{
		$query = mysqli_query($conn, "INSERT INTO ServiceBills (PatientID, ServiceID, DoctorID, DueDate) VALUES ('$patientInput','$serviceInput','$doctorID','$dateInput')");
		$patientInput = '';
		$serviceInput = '';
		$dateInput = '';
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
		<form name = "billPatientsForm" class = "doctor" action = "doctor-bills.php" method = "post">
			<section class = "formHeader">
				<h3>Bill Patients</h3>
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
				</br>
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
				<legend>Due Date</legend>
				<input name="dateInput" class = "formElement" type="date" value = "<?php echo $currentDate ?>" min = "<?php echo $currentDate ?>"/>
			</section>
			</br>
			<section class = "formButtons">
				<input name = "viewDetailsButton" class = "button blue" type = "submit" value = "View Details"/>
				<input name = "billButton" class = "button green" type = "submit" value = "Bill"/>
			</section>
		</form>
		<?php echo $detailsMessage ?>
	</body>
</html>