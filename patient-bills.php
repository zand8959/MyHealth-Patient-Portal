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
$query = mysqli_query($conn, "SELECT * FROM ServiceBills WHERE PatientID = '$patientID' AND DueDate >= CURDATE() ORDER BY DueDate");
$result = mysqli_fetch_assoc($query);
$payButtons = [];

while($result)
{
	$payButtons[] = $result['ServiceBillID'];
	$result = mysqli_fetch_assoc($query);
}

foreach($payButtons as $pay)
{
	if(isset($_POST["$pay"]))
	{
		$query = mysqli_query($conn, "DELETE FROM ServiceBills WHERE ServiceBillID = '$pay'");
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
		require "patient-header.php";
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
				echo	"<form name = payBillsForm class = patient action = patient-bills.php method = post>
							<section class = formHeader>
								<h3>" . $service['Name'] . " Details</h3>
							</section>
							<section class = infoFields>
								<legend>Cost</legend>
								<span>$" . $service['Cost'] . "</span>
								<legend>Due Date</legend>
								<span>" . $result['DueDate'] . "</span>
							</section>
							</br>
							<section class = formHeader>
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
							</br>
							<section class = formButtons>
								<input name = " . $result['ServiceBillID'] . " class = \"button red\" type = submit value = Pay />
							</section>
						</form>";
				$result = mysqli_fetch_assoc($query);
			}
		}
		?>
	</body>
</html>