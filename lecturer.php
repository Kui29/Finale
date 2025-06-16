<?php
// Database connection
require("connect.php");

// Insert form data directly into database using POST method
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $lecId = $_POST["lecId"];
    $lecName = $_POST["lecName"];
    $lecEmail = $_POST["lecEmail"];
    $lecContact = $_POST["lecContact"];
    $lecPassword = mysqli_real_escape_string($con,$_POST["lecPassword"]);

    $lecturersql = "INSERT INTO Lecturers (lecId, lecName, lecEmail, lecContact, lecPassword)
            VALUES ('$lecId', '$lecName', '$lecEmail', '$lecContact', '$lecPassword')";

    if ($con->query($lecturersql) === TRUE) {
        echo "<br><h1>Lecturer added successfully.<h1>";
    } else {
        echo "Error: " . $con->error;
    }
    // Close connection
    $con->close();
}
?>
