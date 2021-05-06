<?php
if(empty($_SESSION['DOCTOR_ID']))
{
	header("Location:login.php");
	exit();
}
?>

<div class = "pageHeader">
	<h1>Doctor Portal</h1>
</div>
<div class = "pageHeaderBar">
	<div class = "pageHeaderLink">
		<a href = "doctor-home.php">Home</a>
	</div>
	<div class = "pageHeaderLink">
		<a href = "doctor-appointments.php">Appointments</a>
	</div>
	<div class = "pageHeaderLink">
		<a href = "doctor-diagnoses.php">Diagnoses</a>
	</div>
	<div class = "pageHeaderLink">
		<a href = "doctor-prescriptions.php">Prescriptions</a>
	</div>
	<div class = "pageHeaderLink">
		<a href = "doctor-bills.php">Bills</a>
	</div>
	<div class = "pageHeaderLink">
		<a href = "doctor-report.php">Report</a>
	</div>
	<div class = "pageHeaderLink">
		<a href = "login.php">Log Out</a>
	</div>
</div>