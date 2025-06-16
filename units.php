<?php

require("connect.php");

$message = "";

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $unitCode = trim($_POST["unitCode"]);
    $unitName = trim($_POST["unitName"]);
    $progId = trim($_POST["progId"]);
    $creditHours = trim($_POST["creditHours"]);

    if ($unitCode && $unitName && $progId && is_numeric($creditHours) && $creditHours > 0) {
        $stmt = $con->prepare("INSERT INTO Units (unitCode, unitName, progId, creditHours) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("sssi", $unitCode, $unitName, $progId, $creditHours);

        if ($stmt->execute()) {
            $message = "<span style='color:green;'>Unit added successfully.</span>";
        } else {
            $message = "<span style='color:red;'>Error: " . $stmt->error . "</span>";
        }
        $stmt->close();
    } else {
        $message = "<span style='color:red;'>All fields are required and Credit Hours must be a positive number.</span>";
    }
}

// Fetch all units
$units = [];
$result = $con->query("SELECT * FROM Units ORDER BY unitCode ASC");
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $units[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Unit Management</title>
    <link rel="stylesheet" type="text/css" href="style.css">
    <style>
        table { border-collapse: collapse; width: 80%; margin-top: 20px; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align: left; }
        th { background: #f2f2f2; }
        .message { margin: 10px 0; }
    </style>
    <script>
        function validateForm(formId) {
            var form = document.getElementById(formId);
            var unitCode = form.unitCode.value.trim();
            var unitName = form.unitName.value.trim();
            var progId = form.progId.value.trim();
            var creditHours = form.creditHours.value;

            if (!unitCode || !unitName || !progId || !creditHours) {
                alert("All fields are required.");
                return false;
            }
            if (isNaN(creditHours) || creditHours <= 0) {
                alert("Credit Hours must be a positive number.");
                return false;
            }
            return true;
        }
    </script>
</head>
<body>
    <h2>Units Management</h2>
    <div class="message"><?php echo $message; ?></div>
    <form id="unitsForm" method="post" onsubmit="return validateForm('unitsForm')">
        <label>Unit Code: <input type="text" name="unitCode" required></label><br>
        <label>Unit Name: <input type="text" name="unitName" required></label><br>
        <label>Programme ID: <input type="text" name="progId" required></label><br>
        <label>Credit Hours: <input type="number" name="creditHours" min="1" required></label><br>
        <input type="submit" value="Add Unit">
    </form>

    <h3>All Units</h3>
    <table>
        <tr>
            <th>Unit Code</th>
            <th>Unit Name</th>
            <th>Programme ID</th>
            <th>Credit Hours</th>
        </tr>
        <?php if (count($units) > 0): ?>
            <?php foreach ($units as $unit): ?>
                <tr>
                    <td><?php echo htmlspecialchars($unit['unitCode']); ?></td>
                    <td><?php echo htmlspecialchars($unit['unitName']); ?></td>
                    <td><?php echo htmlspecialchars($unit['progId']); ?></td>
                    <td><?php echo htmlspecialchars($unit['creditHours']); ?></td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr><td colspan="4">No units found.</td></tr>
        <?php endif; ?>
    </table>
</body>
</html>