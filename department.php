<?php
// Database connection
require("connect.php");

// Insert form data directly into database using POST method
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $dptId = $_POST["dptId"];
    $dptFaculty = $_POST["dptFaculty"];
    $dptName = $_POST["dptName"];

    $departmentsql = "INSERT INTO department (dptId, dptFaculty, dptName)
            VALUES ('$dptId', '$dptFaculty', '$dptName')";

    if ($con->query($departmentsql) === TRUE) {
        echo "<br><h1>Department added successfully.<h1>";
    } else {
        echo "Error: " . $con->error;
    }
    // Close connection
    $con->close();
}
?>