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
$productInput = '';
$noteInput = '';
$errorMessage = '';
$detailsMessage = '';

if(isset($_POST['viewDetailsButton']))
{
	$patientInput = $_POST['patientInput'];
	$productInput = $_POST['productInput'];
	$noteInput = $_POST['noteInput'];
	
	if(empty($patientInput))
	{
		$errorMessage = "*First field required. Please select a patient.";
	}
	else
	{
		$patient = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM Patients WHERE PatientID = '$patientInput'"));
		$doctor = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM Doctors WHERE DoctorID = '" . $patient['DoctorID'] . "'"));
		$detailsMessage =	"<div class = \"infoBox doctor\">
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
							</div>";
		$query = mysqli_query($conn, "SELECT * FROM PatientsPrescriptions WHERE PatientID = '$patientInput' ORDER BY PrescriptionDate");
		$result = mysqli_fetch_assoc($query);
		
		if(!$result)
		{
			$detailsMessage .=	"<div class = \"infoBox doctor\">
									<section class = infoFields>
										<span>This patient doesn't have any prescriptions.</span>
									</section>
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
	}
}

if(isset($_POST['prescribeButton']))
{
	$patientInput = $_POST['patientInput'];
	$productInput = $_POST['productInput'];
	$noteInput = $_POST['noteInput'];
	
	if(empty($patientInput))
	{
		$errorMessage = "*All fields required. Please select a patient.";
	}
	else if(empty($productInput))
	{
		$errorMessage = "*All fields required. Please select a product.";
	}
	else if(empty($noteInput))
	{
		$errorMessage = "*All fields required. Please fill in the note.";
	}
	else
	{
		$query = mysqli_query($conn, "INSERT INTO PatientsPrescriptions (PatientID, ProductID, DoctorID, Note, PrescriptionDate) VALUES ('$patientInput','$productInput','$doctorID','$noteInput','$currentDate')");
		$patientInput = '';
		$productInput = '';
		$noteInput = '';
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
		<form name = "prescribePatientsForm" class = "doctor" action = "doctor-prescriptions.php" method = "post">
			<section class = "formHeader">
				<h3>Prescribe Patients</h3>
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
				<legend>Product</legend>
				<select name = "productInput" class = "formElement">
					<option value = "0" disabled selected>Select Product</option>
					<?php
					$query = mysqli_query($conn, "SELECT * FROM Products");
					$result = mysqli_fetch_assoc($query);

					while($result)
					{
						echo "<option value = \"" . $result['ProductID'] . "\"";
						
						if($result['ProductID'] == $productInput)
						{
							echo " selected";
						}
						
						echo ">" . $result['Name'] . "</option>";
						$result = mysqli_fetch_assoc($query);
					}
					?>
				</select>
				</br>
				<legend>Note</legend>
				<input name="noteInput" class = "formElement" type="text" placeholder = "Reason for prescription." value = "<?php echo $noteInput ?>"/>
			</section>
			</br>
			<section class = "formButtons">
				<input name = "viewDetailsButton" class = "button blue" type = "submit" value = "View Details"/>
				<input name = "prescribeButton" class = "button green" type = "submit" value = "Prescribe"/>
			</section>
		</form>
		<?php echo $detailsMessage ?>
	</body>
</html>