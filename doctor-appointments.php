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
$query = mysqli_query($conn, "SELECT * FROM ServiceAppointments WHERE DoctorID = '$doctorID' AND AppointmentDate >= CURDATE() ORDER BY AppointmentDate, AppointmentTime");
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
		<?php
		require "doctor-header.php";
		echo "<h2>Appointments</h2>";
		$query = mysqli_query($conn, "SELECT * FROM ServiceAppointments WHERE DoctorID = '$doctorID' AND AppointmentDate >= CURDATE() ORDER BY AppointmentDate, AppointmentTime");
		$result = mysqli_fetch_assoc($query);
		
		if(!$result)
		{
			echo	"<div class = \"infoBox doctor\">
						<span>You don't have any appointments.</span>
					</div>";
		}
		else
		{
			while($result)
			{
				$patient = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM Patients WHERE PatientID = '" . $result['PatientID'] . "'"));
				$service = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM Services WHERE ServiceID = '" . $result['ServiceID'] . "'"));
				
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
				
				echo	"<form name = cancelServiceAppointmentsForm class = doctor action = doctor-appointments.php method = post>
							<section class = formHeader>
								<h3>" . $service['Name'] . " Appointment Details</h3>
							</section>
							<table class = infoFields>
								<tr>
									<td>
										<legend>Patient</legend>
										<span>" . $patient['FirstName'] . " " . $patient['LastName'] . "</span>
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