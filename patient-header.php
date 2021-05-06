<?php
if(empty($_SESSION['PATIENT_ID']))
{
	header("Location:login.php");
	exit();
}
?>

<div class = "pageHeader">
	<h1>Patient Portal</h1>
</div>
<div class = "pageHeaderBar">
	<div class = "pageHeaderLink">
		<a href = "patient-home.php">Home</a>
	</div>
	<div class = "pageHeaderLink">
		<a href = "patient-insurance-plans.php">Insurance Plans</a>
	</div>
	<div class = "pageHeaderLink">
		<a href = "patient-services.php">Services</a>
	</div>
	<div class = "pageHeaderLink">
		<a href = "patient-diagnoses.php">Diagnoses</a>
	</div>
	<div class = "pageHeaderLink">
		<a href = "patient-prescriptions.php">Prescriptions</a>
	</div>
	<div class = "pageHeaderLink">
		<a href = "patient-bills.php">Bills</a>
	</div>
	<div class = "pageHeaderLink">
		<a href = "patient-report.php">Report</a>
	</div>
	<div class = "pageHeaderLink">
		<a href = "login.php">Log Out</a>
	</div>
</div>