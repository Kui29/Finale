<?php
// Database connection
require("connect.php");

// Insert form data directly into database using POST method
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $progId = $_POST["progId"];
    $progName = $_POST["progName"];
    $progDuration = $_POST["progDuration"];
    $dptId = $_POST["dptId"];

    $programmesql = "INSERT INTO programmes (progId, progName, progDuration, dptId)
            VALUES ('$progId', '$progName', '$progDuration', '$dptId')";

    if ($con->query($programmesql) === TRUE) {
        echo "<br><h1>Programme added successfully.<h1>";
    } else {
        echo "Error: " . $con->error;
    }
    // Close connection
    $con->close();
}
?>