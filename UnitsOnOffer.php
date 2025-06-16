<?php
// Database connection
require("connect.php");

// Insert form data directly into database using POST method
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $unitCode = $_POST["unitCode"];
    $academicYear = $_POST["academicYear"];
    $semester = $_POST["semester"];
    $cohort = $_POST["cohort"];
    $lecId = $_POST["lecId"];
    $locId = $_POST["locId"];

    $UnitsOnOffersql = "INSERT INTO UnitsOnOffer (unitCode, academicYear, semester, cohort, lecId,locId)
            VALUES ('$unitCode', '$academicYear', '$semester', '$cohort', '$lecId', '$locId')";

    if ($con->query($UnitsOnOffersql) === TRUE) {
        echo "<br><h1>Unit added successfully.<h1>";
    } else {
        echo "Error: " . $con->error;
    }
    //close connection
    $con->close();
}