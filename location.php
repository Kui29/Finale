<?php
// Database connection
require("connect.php");

// Insert form data directly into database using POST method
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $locId = $_POST["locId"];
    $locName = $_POST["locName"];
    $locCoordinates = $_POST["locCoordinates"];

    $locationsql = "INSERT INTO Location (locId, locName, locCoordinates)
            VALUES ('$locId', '$locName', '$locCoordinates')";

    if ($con->query($locationsql) === TRUE) {
        echo "<br><h1>Location added successfully.<h1>";
    } else {
        echo "Error: " . $con->error;
    }
    // Close connection
    $con->close();
}
?>