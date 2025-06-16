<?php
require("connect.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Capture POST data
    $qrLabel = $_POST["qrLabel"];
    $qrPhoto = $_POST["qrPhoto"];

    // Prepare SQL query
    $qrSQL = "INSERT INTO QRCode (qrLabel, qrPhoto) VALUES ('$qrLabel', '$qrPhoto')";

    // Execute query and check result
    if ($con->query($qrSQL) === TRUE) {
        echo "<h1>Successful Entry</h1>";
    } else {
        echo "Error: " . $con->error;
    }

    // Close connection
    $con->close();
}
?>
