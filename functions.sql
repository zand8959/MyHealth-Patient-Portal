/* SCHEDULING SERVICE APPOINTMENT */
-- 1) Patient selects the service they want performed.
-- 2) Patient selects which doctor they want to perform the service.
-- 3) Patient selects a date and time that the doctor has available.

SELECT Name, Cost
FROM Services
-- Finds all available services.

/* Once they find the service they want, the ServiceID will be stored as Selected_Service */

SELECT FirstName, LastName
FROM Doctors
WHERE DoctorID IN
	(SELECT DoctorID
	FROM ServicesPerformedByDoctors
	WHERE ServiceID=Selected_Service
AND HospitalID IN
	(SELECT HospitalID
	FROM ServicesProvidedByHospitals
	WHERE ServiceID=Selected_Service)
-- Finds all doctors that perform the selected service at the hospital they work at.

/* Once they find the doctor they want, the DoctorID will be sotred as Selected_Doctor */

SELECT TimeStart, TimeEnd
FROM Doctors
WHERE DoctorID=Selected_Doctor
-- Finds the work hours of the selected doctor.

SELECT AppointmentTime
FROM ServiceAppointments
WHERE AppointmentDate=Selected_Date AND DoctorID=Selected_Doctor
-- Finds the times of the appointments already scheduled with the selected doctor on the selected date.

/* Once they find the date they want, it will be stored as Selected_Date. Then, a list of the available times the Selected_Doctor has will be listed. */

SELECT Cost
FROM Services
WHERE ServiceID=Selected_Service
-- Finds the cost of the selected service.

SELECT ServiceDeductible
FROM InsurancePlans
WHERE InsurancePlanID IN
	(SELECT InsurancePlanID
	FROM PatientsInsurancePlansAndCoverage
	WHERE PatientID=Current_Patient)
-- Finds the service deductible for the current patient's insurance plan.

SELECT Cost
FROM Products
WHERE ProductID IN
	(SELECT ProductID
	FROM ProductsUsedByServices
	WHERE ServiceID=Selected_Service)
-- Finds the cost of each product used by the selected service.

SELECT Name
FROM Products
WHERE ProductID IN
	(SELECT ProductID
	FROM ProductsCoveredByInsurancePlans
	WHERE InsurancePlanID IN
		(SELECT InsurancePlanID
		FROM PatientsInsurancePlansAndCoverage
		WHERE PatientID=Current_Patient))
-- Finds the products covered by the current patient's insurance plan.

SELECT ProductDeductible
FROM InsurancePlans
WHERE InsurancePlanID IN
	(SELECT InsurancePlanID
	FROM PatientsInsurancePlansAndCoverage
	WHERE PatientID=Current_Patient)
-- Finds the product deductible for the current patient's insurance plan.

SELECT Cost
FROM Tests
WHERE TestID IN
	(SELECT TestID
	FROM TestsUsedByServices
	WHERE ServiceID=Selected_Service)
-- Finds the cost of each test used by the selected service.

SELECT Name
FROM Tests
WHERE TestID IN
	(SELECT TestID
	FROM TestsCoveredByInsurancePlans
	WHERE InsurancePlanID IN
		(SELECT InsurancePlanID
		FROM PatientsInsurancePlansAndCoverage
		WHERE PatientID=Current_Patient))
-- Finds the tests covered by the current patient's insurance plan.

SELECT TestDeductible
FROM InsurancePlans
WHERE InsurancePlanID IN
	(SELECT InsurancePlanID
	FROM PatientsInsurancePlansAndCoverage
	WHERE PatientID=Current_Patient)
-- Finds the test deductible for the current patient's insurance plan.

/* Once the patient has scheduled an appointment, the database will calculate the total price of the service.
It will check the cost of the service, the cost of the products used, and the cost of the tests used against
the service deductible, product deductible, and test deductible respectively and subtract the cost if the costs
exceed the deductibles (unless the patient is already at their maximum coverage for the year). */

