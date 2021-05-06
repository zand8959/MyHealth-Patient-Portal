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
$defaultMinimumAnnualPremium = '20000';
$defaultMaximumAnnualPremium = '40000';
$defaultMinimumProductDeductible = '50';
$defaultMaximumProductDeductible = '250';
$defaultMinimumServiceDeductible = '1000';
$defaultMaximumServiceDeductible = '5000';
$defaultMinimumTestDeductible = '250';
$defaultMaximumTestDeductible = '2500';
$defaultMinimumPlanContribution = '0';
$defaultMaximumPlanContribution = '10000';
$defaultMinimumMaxCoverage = '10000';
$defaultMaximumMaxCoverage = '20000';
$minimumAnnualPremiumValue = '';
$maximumAnnualPremiumValue = '';
$minimumProductDeductibleValue = '';
$maximumProductDeductibleValue = '';
$minimumServiceDeductibleValue = '';
$maximumServiceDeductibleValue = '';
$minimumTestDeductibleValue = '';
$maximumTestDeductibleValue = '';
$minimumPlanContributionValue = '';
$maximumPlanContributionValue = '';
$minimumMaxCoverageValue = '';
$maximumMaxCoverageValue = '';
$minimumAnnualPremiumInput = '';
$maximumAnnualPremiumInput = '';
$minimumProductDeductibleInput = '';
$maximumProductDeductibleInput = '';
$minimumServiceDeductibleInput = '';
$maximumServiceDeductibleInput = '';
$minimumTestDeductibleInput = '';
$maximumTestDeductibleInput = '';
$minimumPlanContributionInput = '';
$maximumPlanContributionInput = '';
$minimumMaxCoverageInput = '';
$maximumMaxCoverageInput = '';

if(isset($_POST['browseButton']))
{
	$minimumAnnualPremiumInput = $_POST['minimumAnnualPremiumInput'];
	$maximumAnnualPremiumInput = $_POST['maximumAnnualPremiumInput'];
	$minimumProductDeductibleInput = $_POST['minimumProductDeductibleInput'];
	$maximumProductDeductibleInput = $_POST['maximumProductDeductibleInput'];
	$minimumServiceDeductibleInput = $_POST['minimumServiceDeductibleInput'];
	$maximumServiceDeductibleInput = $_POST['maximumServiceDeductibleInput'];
	$minimumTestDeductibleInput = $_POST['minimumTestDeductibleInput'];
	$maximumTestDeductibleInput = $_POST['maximumTestDeductibleInput'];
	$minimumPlanContributionInput = $_POST['minimumPlanContributionInput'];
	$maximumPlanContributionInput = $_POST['maximumPlanContributionInput'];
	$minimumMaxCoverageInput = $_POST['minimumMaxCoverageInput'];
	$maximumMaxCoverageInput = $_POST['maximumMaxCoverageInput'];
	
	empty($minimumAnnualPremiumInput) ? $minimumAnnualPremiumValue = $defaultMinimumAnnualPremium : $minimumAnnualPremiumValue = $minimumAnnualPremiumInput;
	empty($maximumAnnualPremiumInput) ? $maximumAnnualPremiumValue = $defaultMaximumAnnualPremium : $maximumAnnualPremiumValue = $maximumAnnualPremiumInput;
	empty($minimumProductDeductibleInput) ? $minimumProductDeductibleValue = $defaultMinimumProductDeductible : $minimumProductDeductibleValue = $minimumProductDeductibleInput;
	empty($maximumProductDeductibleInput) ? $maximumProductDeductibleValue = $defaultMaximumProductDeductible : $maximumProductDeductibleValue = $maximumProductDeductibleInput;
	empty($minimumServiceDeductibleInput) ? $minimumServiceDeductibleValue = $defaultMinimumServiceDeductible : $minimumServiceDeductibleValue = $minimumServiceDeductibleInput;
	empty($maximumServiceDeductibleInput) ? $maximumServiceDeductibleValue = $defaultMaximumServiceDeductible : $maximumServiceDeductibleValue = $maximumServiceDeductibleInput;
	empty($minimumTestDeductibleInput) ? $minimumTestDeductibleValue = $defaultMinimumTestDeductible : $minimumTestDeductibleValue = $minimumTestDeductibleInput;
	empty($maximumTestDeductibleInput) ? $maximumTestDeductibleValue = $defaultMaximumTestDeductible : $maximumTestDeductibleValue = $maximumTestDeductibleInput;
	empty($minimumPlanContributionInput) ? $minimumPlanContributionValue = $defaultMinimumPlanContribution : $minimumPlanContributionValue = $minimumPlanContributionInput;
	empty($maximumPlanContributionInput) ? $maximumPlanContributionValue = $defaultMaximumPlanContribution : $maximumPlanContributionValue = $maximumPlanContributionInput;
	empty($minimumMaxCoverageInput) ? $minimumMaxCoverageValue = $defaultMinimumMaxCoverage : $minimumMaxCoverageValue = $minimumMaxCoverageInput;
	empty($maximumMaxCoverageInput) ? $maximumMaxCoverageValue = $defaultMaximumMaxCoverage : $maximumMaxCoverageValue = $maximumMaxCoverageInput;
}

$query = mysqli_query($conn, "SELECT * FROM InsurancePlans");
$result = mysqli_fetch_assoc($query);
$signupButtons = [];

while($result)
{
	$signupButtons[] = $result['InsurancePlanID'];
	$result = mysqli_fetch_assoc($query);
}

foreach($signupButtons as $signup)
{
	if(isset($_POST["$signup"]))
	{
		$query = mysqli_query($conn, "INSERT INTO PatientsInsurancePlansAndCoverage (PatientID, InsurancePlanID) VALUES ('$patientID','$signup')");
		header("Refresh:0");
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
		<form name = "browseInsurancePlansForm" class = "patient" action = "patient-insurance-plans.php" method = "post">
			<section class = "formHeader">
				<h3>Browse Insurance Plans</h3>
			</section>
			<table class = "formFields">
				<tr>
					<legend>Annual Premium</legend>
				</tr>
				<tr>
					<span>Minimum: $</span>
					<input name = "minimumAnnualPremiumInput" type="text" placeholder = "<?php echo $defaultMinimumAnnualPremium ?>" value = "<?php echo $minimumAnnualPremiumInput ?>"/>
					<span>Maximum: $</span>
					<input name = "maximumAnnualPremiumInput" type="text" placeholder = "<?php echo $defaultMaximumAnnualPremium ?>" value = "<?php echo $maximumAnnualPremiumInput ?>"/>
				</tr>
				<tr>
					<legend>Product Deductible</legend>
				</tr>
				<tr>
					<span>Minimum: $</span>
					<input name = "minimumProductDeductibleInput" type="text" placeholder = "<?php echo $defaultMinimumProductDeductible ?>" value = "<?php echo $minimumProductDeductibleInput ?>"/>
					<span>Maximum: $</span>
					<input name = "maximumProductDeductibleInput" type="text" placeholder = "<?php echo $defaultMaximumProductDeductible ?>" value = "<?php echo $maximumProductDeductibleInput ?>"/>
				</tr>
				<tr>
					<legend>Service Deductible</legend>
				</tr>
				<tr>
					<span>Minimum: $</span>
					<input name = "minimumServiceDeductibleInput" type="text" placeholder = "<?php echo $defaultMinimumServiceDeductible ?>" value = "<?php echo $minimumServiceDeductibleInput ?>"/>
					<span>Maximum: $</span>
					<input name="maximumServiceDeductibleInput" type="text" placeholder = "<?php echo $defaultMaximumServiceDeductible ?>" value = "<?php echo $maximumServiceDeductibleInput ?>"/>
				</tr>
				<tr>
					<legend>Test Deductible</legend>
				</tr>
				<tr>
					<span>Minimum: $</span>
					<input name = "minimumTestDeductibleInput" type="text" placeholder = "<?php echo $defaultMinimumTestDeductible ?>" value = "<?php echo $minimumTestDeductibleInput ?>"/>
					<span>Maximum: $</span>
					<input name = "maximumTestDeductibleInput" type="text" placeholder = "<?php echo $defaultMaximumTestDeductible ?>" value = "<?php echo $maximumTestDeductibleInput ?>"/>
				</tr>
				<tr>
					<legend>Plan Contribution</legend>
				</tr>
					<span>Minimum: $</span>
					<input name = "minimumPlanContributionInput" type="text" placeholder = "<?php echo $defaultMinimumPlanContribution ?>" value = "<?php echo $minimumPlanContributionInput ?>"/>
					<span>Maximum: $</span>
					<input name = "maximumPlanContributionInput" type="text" placeholder = "<?php echo $defaultMaximumPlanContribution ?>" value = "<?php echo $maximumPlanContributionInput ?>"/>
				</tr>
				<tr>
					<legend>Max Coverage</legend>
				</tr>
				<tr>
					<span>Minimum: $</span>
					<input name = "minimumMaxCoverageInput" type="text" placeholder = "<?php echo $defaultMinimumMaxCoverage ?>" value = "<?php echo $minimumMaxCoverageInput ?>"/>
					<span>Maximum: $</span>
					<input name = "maximumMaxCoverageInput" type="text" placeholder = "<?php echo $defaultMaximumMaxCoverage ?>" value = "<?php echo $maximumMaxCoverageInput ?>"/>
				</tr>
			</table>
			<section class = "formButtons">
				<input name = "browseButton" class = "button blue" type = "submit" value = "Browse"/>
			</section>
		</form>
		<?php
		$query = mysqli_query($conn, "SELECT * FROM InsurancePlans WHERE AnnualPremium >= '$minimumAnnualPremiumValue' AND AnnualPremium <= '$maximumAnnualPremiumValue' AND ProductDeductible >= '$minimumProductDeductibleValue' AND ProductDeductible <= '$maximumProductDeductibleValue' AND ServiceDeductible >= '$minimumServiceDeductibleValue' AND ServiceDeductible <= '$maximumServiceDeductibleValue' AND TestDeductible >= '$minimumTestDeductibleValue' AND TestDeductible <= '$maximumTestDeductibleValue' AND PlanContribution >= '$minimumPlanContributionValue' AND PlanContribution <= '$maximumPlanContributionValue' AND MaxCoverage >= '$minimumMaxCoverageValue' AND MaxCoverage <= '$maximumMaxCoverageValue'");
		$result = mysqli_fetch_assoc($query);
		
		while($result)
		{
			echo	"<form name = signupInsurancePlansForm class = patient action = patient-insurance-plans.php method = post>
						<section class = formHeader>
							<h3>" . $result['Name'] . " Details</h3>
						</section>
						<table class = infoFields>
							<tr>
								<td>
									<legend>Annual Premium</legend>
									<span>$" . $result['AnnualPremium'] . "</span>
								</td>
								<td>
									<legend>Product Deductible</legend>
									<span>$" . $result['ProductDeductible'] . "</span>
								</td>
								<td>
									<legend>Service Deductible</legend>
									<span>$" . $result['ServiceDeductible'] . "</span>
								</td>
							</tr>
							<tr>
								<td>
									<legend>Test Deductible</legend>
									<span>$" . $result['TestDeductible'] . "</span>
								</td>
								<td>
									<legend>Plan Contribution</legend>
									<span>$" . $result['PlanContribution'] . "</span>
								</td>
								<td>
									<legend>Max Coverage</legend>
									<span>$" . $result['MaxCoverage'] . "</span>
								</td>
							</tr>
						</table>
						<section class = formButtons>
							<input name = " . $result['InsurancePlanID'] . " class = \"button green\" type = submit value = Signup />
						</section>
					</form>";
			$result = mysqli_fetch_assoc($query);
		}
		?>
	</body>
</html>