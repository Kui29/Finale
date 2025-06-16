<?php

// Create connection
require("connect.php");

$stdId = $_SESSION['stdId']; // e.g. 104532

$sql =
   "SELECT DISTINCT r.unitCode, u.unitName
    FROM registeredUnit r
    JOIN units u ON r.unitCode = u.unitCode
    WHERE r.stdId = ?";

$stmt = $con->prepare($sql);
$stmt->bind_param("s", $stdId);
$stmt->execute();

$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    echo "<br>" . $row["unitCode"] . " - " . $row["unitName"] . "<br>";
}

// Close the connection
$con->close();
?>