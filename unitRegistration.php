<?php
// Database connection
require("connect.php");

// Insert form data directly into database using POST method
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $stdId = $_POST["stdId"];
    $unitCode = $_POST["unitCode"];
    $academicYear = $_POST["academicYear"];
    $semester = $_POST["semester"];
    $academicStage = $_POST["academicStage"];
    $registrationStatus = $_POST["registrationStatus"];

    $UnitRegistrationsql = "INSERT INTO UnitRegistration (stdId, unitCode, academicYear, semester, academicStage, registrationStatus)
            VALUES ('$stdId', '$unitCode', '$academicYear', '$semester', '$academicStage', '$registrationStatus')";

    if ($con->query($UnitRegistrationsql) === TRUE) {
        echo "<br><h1>Unit Registered successfully.<h1>";
    } else {
        echo "Error: " . $con->error;
    }
    //close connection
    $con->close();
}
?>