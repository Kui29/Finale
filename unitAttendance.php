<?php
// Database connection
require("connect.php");

$message = "";

// Insert form data using prepared statements
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $stdId = trim($_POST["stdId"]);
    $unitCode = trim($_POST["unitCode"]);
    $lectureName = trim($_POST["lectureName"]);
    $lecId = trim($_POST["lecId"]);
    $locId = trim($_POST["locId"]);

    if ($stdId && $unitCode && $lectureName && $lecId && $locId) {
        $stmt = $con->prepare("INSERT INTO Attendance (stdId, unitCode, lectureName, lecId, locId) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("issss", $stdId, $unitCode, $lectureName, $lecId, $locId);

        if ($stmt->execute()) {
            $message = "<span style='color:green;'>Unit attendance added successfully.</span>";
        } else {
            $message = "<span style='color:red;'>Error: " . $stmt->error . "</span>";
        }
        $stmt->close();
    } else {
        $message = "<span style='color:red;'>All fields are required.</span>";
    }
}

// Fetch all attendance records
$attendances = [];
$result = $con->query("SELECT * FROM Attendance ORDER BY AttendanceId DESC");
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $attendances[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Unit Attendance</title>
    <link rel="stylesheet" type="text/css" href="style.css">
    <style>
        table { border-collapse: collapse; width: 90%; margin-top: 20px; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align: left; }
        th { background: #f2f2f2; }
        .message { margin: 10px 0; }
    </style>
    <script>
        function validateForm(formId) {
            var form = document.getElementById(formId);
            var stdId = form.stdId ? form.stdId.value.trim() : "";
            var unitCode = form.unitCode.value.trim();
            var lectureName = form.lectureName ? form.lectureName.value.trim() : "";
            var lecId = form.lecId.value.trim();
            var locId = form.locId.value.trim();

            if (stdId === "") {
                alert("Student ID is required");
                return false;
            }
            if (unitCode === "") {
                alert("Unit Code is required");
                return false;
            }
            if (lectureName === ""  || !isNaN(lectureName)) {
                alert("Lecture Name is required");
                return false;
            }
            if (lecId === "") {
                alert("Enter a valid Lecturer ID");
                return false;
            }
            if (locId === "" ) {
                alert("Enter a valid Location ID");
                return false;
            }
            return true;
        }
    </script>
</head>
<body>
    <h2>Unit Attendance</h2>
    <div class="message"><?php echo $message; ?></div>
    <form id="unitAttendanceForm" action="unitAttendance.php" method="post" onsubmit="return validateForm('unitAttendanceForm')">
        Student ID: <input type="text" name="stdId"><br>
        Unit Code: <input type="text" name="unitCode"><br>
        Lecture Name: <input type="text" name="lectureName"><br>
        Lecturer ID: <input type="text" name="lecId"><br>
        Location ID: <input type="text" name="locId"><br>
        <input type="submit" value="Submit">
    </form>

    <h3>Attendance Records</h3>
    <table>
        <tr>
            <th>ID</th>
            <th>Student ID</th>
            <th>Unit Code</th>
            <th>Lecture Name</th>
            <th>Lecturer ID</th>
            <th>Location ID</th>
        </tr>
        <?php if (count($attendances) > 0): ?>
            <?php foreach ($attendances as $attendance): ?>
                <tr>
                    <td><?php echo htmlspecialchars($attendance['attendanceId']); ?></td>
                    <td><?php echo htmlspecialchars($attendance['stdId']); ?></td>
                    <td><?php echo htmlspecialchars($attendance['unitCode']); ?></td>
                    <td><?php echo htmlspecialchars($attendance['lectureName']); ?></td>
                    <td><?php echo htmlspecialchars($attendance['lecId']); ?></td>
                    <td><?php echo htmlspecialchars($attendance['locId']); ?></td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr><td colspan="6">No attendance records found.</td></tr>
        <?php endif; ?>
    </table>
</body>
</html>