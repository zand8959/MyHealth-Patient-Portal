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
$testInput = '';
$labInput = $patient['LabID'];
$dateInput = '';
$timeInput = '';
$errorMessage = '';
$detailsMessage = '';

if(isset($_POST['viewDetailsButton']))
{
	$testInput = $_POST['testInput'];
	$labInput = $_POST['labInput'];
	$dateInput = $_POST['dateInput'];
	$timeInput = $_POST['timeInput'];
	
	if(empty($testInput))
	{
		$errorMessage = "*First two fields required. Please select a test.";
	}
	else if(empty($labInput))
	{
		$errorMessage = "*First two fields required. Please select a lab.";
	}
	else
	{
		$test = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM Tests WHERE TestID = '$testInput'"));
		$lab = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM Labs WHERE LabID = '$labInput'"));
		$address = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM Addresses WHERE AddressID = '" . $lab['AddressID'] . "'"));
		$detailsMessage =	"<div class = \"infoBox patient\">
								<section class = infoHeader>
									<h3>" . $test['Name'] . " Details</h3>
								</section>
								<section class = infoFields>
									<legend>Cost</legend>
									<span>$" . $test['Cost'] . "</span>
								</section>
								</br>
								<section class = infoHeader>
									<h3>" . $lab['Name'] . " Details</h3>
								</section>
								<table class = infoFields>
									<tr>
										<td>
											<legend>Email Address</legend>
											<span>" . $lab['EmailAddress'] . "</span>
										</td>
										<td>
											<legend>Phone Number</legend>
											<span>" . $lab['PhoneNumber'] . "</span>
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
	}
}

if(isset($_POST['scheduleButton']))
{
	$testInput = $_POST['testInput'];
	$labInput = $_POST['labInput'];
	$dateInput = $_POST['dateInput'];
	$timeInput = $_POST['timeInput'];
	
	if(empty($testInput))
	{
		$errorMessage = "*All fields required. Please select a test.";
	}
	else if(empty($labInput))
	{
		$errorMessage = "*All fields required. Please select a lab.";
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
		$query = mysqli_query($conn, "INSERT INTO TestAppointments (PatientID, TestID, LabID, AppointmentDate, AppointmentTime) VALUES ('$patientID','$testInput','$labInput','$dateInput','$timeInput')");
		$testInput = '';
		$labInput = $patient['LabID'];
		$dateInput = '';
		$timeInput = '';
	}
}

$query = mysqli_query($conn, "SELECT * FROM TestAppointments WHERE PatientID = '$patientID' AND AppointmentDate >= CURDATE() ORDER BY AppointmentDate, AppointmentTime");
$result = mysqli_fetch_assoc($query);
$cancelButtons = [];

while($result)
{
	$cancelButtons[] = $result["TestAppointmentID"];
	$result = mysqli_fetch_assoc($query);
}

foreach($cancelButtons as $cancel)
{
	if(isset($_POST["$cancel"]))
	{
		$query = mysqli_query($conn, "DELETE FROM TestAppointments WHERE TestAppointmentID = '$cancel'");
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
		<form name = "scheduleTestsForm" class = "patient" action = "patient-tests.php" method = "post">
			<section class = "formHeader">
				<h3>Schedule Tests</h3>
			</section>
			<?php echo "<span class = errorMessage>$errorMessage</span>" ?>
			<section class = "formFields">
				<legend>Test</legend>
				<select name = "testInput" class = "formElement">
					<option value = "0" disabled selected>Select Test</option>
					<?php
					$query = mysqli_query($conn, "SELECT * FROM Tests");
					$result = mysqli_fetch_assoc($query);

					while($result)
					{
						echo "<option value = \"" . $result['TestID'] . "\"";
						
						if($result['TestID'] == $testInput)
						{
							echo " selected";
						}
						
						echo ">" . $result['Name'] . "</option>";
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
			<section class = "formButtons">
				<input name = "viewDetailsButton" class = "button blue" type = "submit" value = "View Details"/>
				<input name = "scheduleButton" class = "button green" type = "submit" value = "Schedule"/>
			</section>
		</form>
		<?php
		echo $detailsMessage;
		echo "<h2>Test Appointments</h2>";
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
				
				echo	"<form name = cancelTestAppointmentsForm class = patient action = patient-tests.php method = post>
							<section class = formHeader>
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
							</br>
							<section class = formButtons>
								<input name = " . $result['TestAppointmentID'] . " class = \"button red\" type = submit value = Cancel />
							</section>
						</form>";
				$result = mysqli_fetch_assoc($query);
			}
		}
		?>
	</body>
</html>