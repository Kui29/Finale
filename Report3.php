<?php

// Create connection
require("connect.php");

// Query to select data from the failed_registrations view
 $sql = "SELECT unitCode, COUNT(*) AS failed_registrations 
FROM UnitRegistration 
WHERE registrationStatus = 'Failed' 
GROUP BY unitCode";
$result = $con->query($sql);

// Check if there are any results
if ($result->num_rows > 0) {
    // Output data of each row
    while($row = $result->fetch_assoc()) {
        echo "<br>". $row["unitCode"] . " - Failed Registrations: " . $row["failed_registrations"] . "<br>";
    }
} else {
    echo "0 results";
}

// Close the connection
$con->close();
?>
