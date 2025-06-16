<?php
// Database connection
require("connect.php");

// Insert form data directly into database using POST method
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $lectureName = $_POST["lectureName"];
    $lectureDate = $_POST["lectureDate"];
    $lectureTime = $_POST["lectureTime"];
    $locId = $_POST["locId"];

    // Convert date from dd-mm-yyyy to yyyy-mm-dd for MySQL storage
    $dateParts = explode('-', $lectureDate);
    if (count($dateParts) === 3) {
        // Rearrange to yyyy-mm-dd
        $formattedDate = $dateParts[2] . '-' . $dateParts[1] . '-' . $dateParts[0];
    } else {
        // Fallback to original logic if not in expected format
        $formattedDate = date("Y-m-d", strtotime(str_replace("-", "/", $lectureDate)));
    }


    $lecturesql = "INSERT INTO Lectures (lectureName, lectureDate, lectureTime, locId)
            VALUES ('$lectureName', '$formattedDate', '$lectureTime', '$locId')";

    $existingLecturesql = "SELECT * FROM Lectures WHERE lectureName = '$lectureName' AND lectureDate = '$formattedDate' AND lectureTime = '$lectureTime' AND locId = '$locId'";

    if ($con->query($lecturesql) === TRUE) {
        echo "<br><h1>Lecture added successfully.<h1>";
    } elseif ($con->query($existingLecturesql)->num_rows > 0) {
        echo "<br><h1>Lecture already exists.<h1>";
    } else {
        echo "Error: " . $con->error;
    }
    //close connection
    $con->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width:" initial-scale="1.0">
    <title>Document</title>
    <link rel="stylesheet" type="text/css" href="style.css"> <!-- Link to external CSS file -->
    <script>
        function validateForm(formId) {
    var form = document.getElementById(formId);
    var lectureName = form.lectureName.value.trim();
    var lectureDate = form.lectureDate.value.trim();
    var lectureTime = form.lectureTime.value;
    var locId = form.locId.value.trim();

    // Check for empty fields
    if (!lectureName || !lectureDate || !lectureTime || !locId) {
        alert("All fields are required.");
        return false;
    }

    // Validate Lecture Date (dd-mm-yyyy format check)
    var dateParts = lectureDate.split("-");
    if (dateParts.length !== 3) {
        alert("Lecture Date must be in the format dd-mm-yyyy.");
        return false;
    }

    var day = Number(dateParts[0]);
    var month = Number(dateParts[1]);
    var year = Number(dateParts[2]);

    if (isNaN(day) || isNaN(month) || isNaN(year)) {
        alert("Lecture Date must contain only numbers in dd-mm-yyyy format.");
        return false;
    }

    if (day < 1 || day > 31 || month < 1 || month > 12 || year < 1900) {
        alert("Invalid date. Please enter a valid dd-mm-yyyy date.");
        return false;
    }

    return true;
}

    </script>
</head>
<body>
    <h2>Lecture </h2>
    <form id="lectureForm" action="lecture.php" method="POST" onsubmit="return validateForm('lectureForm')">
        Lecture Name: <input type="text" name="lectureName"><br>
        Lecture Date: <input type="text" name="lectureDate" placeholder="dd-mm-yyyy"><br>
        Lecture Time:<select name="lectureTime">
            <option value="Morning">08:00-11:00</option>
            <option value="Mid-day">11:00-14:00</option>
            <option value="Evening">14:00-17:00</option>
        </select><br>
        Location ID: <select name="locId">
            <option value="JH Auditorium">JH Auditorium</option>
            <option value="JH Lower Lab">JH Lower Lab</option>
            <option value="JH Upper Lab">JH Upper Lab</option>
            <option value="TH Lab A">TH Lab A</option>
            <option value="TH Lab B">TH Lab B</option>
            <option value="TH01">TH01</option>
            <option value="TH02">TH02</option>
            <option value="TH03">TH03</option>
            <option value="TH04">TH04</option>
            <option value="TH05">TH05</option>
            <option value="TH06">TH06</option>
            <option value="TH07">TH07</option>
            <option value="TH08">TH08</option>
            <option value="TH09">TH09</option>
            <option value="TH10">TH10</option>
            <option value="TH11">TH11</option>
            <option value="TH12">TH12</option>
            <option value="TH13">TH13</option>
            <option value="TH14">TH14</option>
            <option value="TH15">TH15</option>
            <option value="TH16">TH16</option>
            <option value="TH17">TH17</option>
            <option value="TH18">TH18</option>
            <option value="TH19">TH19</option>
        </select><br>
        <input type="submit" value="Submit">
    </form>
</body>
</html>