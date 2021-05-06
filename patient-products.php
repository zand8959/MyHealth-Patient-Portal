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
$productInput = '';
$pharmacyInput = $patient['PharmacyID'];
$quantityInput = '';
$errorMessage = '';
$detailsMessage = '';

if(isset($_POST['viewDetailsButton']))
{
	$productInput = $_POST['productInput'];
	$pharmacyInput = $_POST['pharmacyInput'];
	$quantityInput = $_POST['quantityInput'];
	
	if(empty($productInput))
	{
		$errorMessage = "*All fields required. Please select a product.";
	}
	else if(empty($pharmacyInput))
	{
		$errorMessage = "*All fields required. Please select a pharmacy.";
	}
	else if(empty($quantityInput))
	{
		$errorMessage = "*All fields required. Please enter a quantity.";
	}
	else
	{
		$product = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM Products WHERE ProductID = '$productInput'"));
		$pharmacy = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM Pharmacies WHERE PharmacyID = '$pharmacyInput'"));
		$address = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM Addresses WHERE AddressID = '" . $pharmacy['AddressID'] . "'"));
		$detailsMessage =	"<div class = \"infoBox patient\">
								<section class = infoHeader>
									<h3>" . $product['Name'] . " Details</h3>
								</section>
								<table class = infoFields>
									<tr>
										<td>
											<legend>Cost for 1</legend>
											<span>$" . $product['Cost'] . "</span>
										</td>
										<td>
											<legend>Total Cost</legend>
											<span>$" . $product['Cost'] * $quantityInput . "</span>
										</td>
									</tr>
								</table>
								</br>
								<section class = infoHeader>
									<h3>" . $pharmacy['Name'] . " Details</h3>
								</section>
								<table class = infoFields>
									<tr>
										<td>
											<legend>Email Address</legend>
											<span>" . $pharmacy['EmailAddress'] . "</span>
										</td>
										<td>
											<legend>Phone Number</legend>
											<span>" . $pharmacy['PhoneNumber'] . "</span>
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
							</section>";
	}
}

if(isset($_POST['orderButton']))
{
	$productInput = $_POST['productInput'];
	$pharmacyInput = $_POST['pharmacyInput'];
	$quantityInput = $_POST['quantityInput'];
	
	if(empty($productInput))
	{
		$errorMessage = "*All fields required. Please select a product.";
	}
	else if(empty($pharmacyInput))
	{
		$errorMessage = "*All fields required. Please select a pharmacy.";
	}
	else if(empty($quantityInput))
	{
		$errorMessage = "*All fields required. Please specify a quantity.";
	}
	else
	{
		$query = mysqli_query($conn, "INSERT INTO ProductOrders (PatientID, ProductID, PharmacyID, Quantity, OrderDate) VALUES ('$patientID','$productInput','$pharmacyInput','$quantityInput','$currentDate')");
		$productInput = '';
		$pharmacy = $patient['PharmacyID'];
		$quantityInput = '';
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
		<form name = "orderProductsForm" class = "patient" action = "patient-products.php" method = "post">
			<section class = "formHeader">
				<h3>Order Products</h3>
			</section>
			<?php echo "<span class = errorMessage>$errorMessage</span>" ?>
			<section class = "formFields">
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
				<legend>Pharmacy</legend>
				<select name = "pharmacyInput" class = "formElement">
					<option value = "0" disabled selected>Select Pharmacy</option>
					<?php
					$query = mysqli_query($conn, "SELECT * FROM Pharmacies");
					$result = mysqli_fetch_assoc($query);

					while($result)
					{
						echo "<option value = \"" . $result['PharmacyID'] . "\"";
						
						if($result['PharmacyID'] == $pharmacyInput)
						{
							echo " selected";
						}
						
						echo ">" . $result['Name'] . "</option>";
						$result = mysqli_fetch_assoc($query);
					}
					?>
				</select>
				</br>
				<legend>Quantity</legend>
				<input name = "quantityInput" class = "formElement" type="text" placeholder="1" value = "<?php echo $quantityInput ?>"/>
			</section>
			</br>
			<section class = "formButtons">
				<input name = "viewDetailsButton" class = "button blue" type = "submit" value = "View Details"/>
				<input name = "orderButton" class = "button green" type = "submit" value = "Order"/>
			</section>
		</form>
		<?php echo $detailsMessage ?>
	</body>
</html>