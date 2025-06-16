<?php
require_once("connect.php");

// Use lecId for both GET and POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['lecId'])) {
    $lecId = filter_var($_POST['lecId'], FILTER_VALIDATE_INT);
    $lecName = $_POST['name'];
    $lecEmail = $_POST['email'];
    $lecContact = $_POST['contact'];
    $lecPassword = $_POST['password'];
    if($lecPassword === null) {
        $lecPassword = ""; // Set to empty string if password is null
    }
    $updateStmt = $con->prepare("UPDATE Lecturers SET lecName=?, lecEmail=?, lecContact=?, lecPassword=? WHERE lecId=?");
    $updateStmt->bind_param("ssssi", $lecName, $lecEmail, $lecContact, $lecPassword, $lecId);
    if ($updateStmt->execute()) {
        // Update session values if this is the logged-in lecturer
        if (isset($_SESSION['lecId']) && $_SESSION['lecId'] == $lecId) {
            $_SESSION['lecName'] = $lecName;
        }
        // Success notification with correct.gif
        echo '<!DOCTYPE html>
        <html>
        <head>
        <style>
            .success-popup {
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                height: 50vh;
            }
            .success-popup img {
                width: 100px;
                height: 100px;
            }
            .success-popup span {
                font-size: 1.5rem;
                color: #2e7d32;
                margin-top: 10px;
            }
        </style>
        </head>
        <body>
        <div class="success-popup">
            <img src="correct.gif" alt="Success">
            <span>Changes Saved Successfully</span>
        </div>
        </body>
        </html>';
        exit();
    } else {
        die("Error updating lecturer: " . $updateStmt->error);
    }
} elseif (isset($_SESSION['lecId'])) {
    $lecId = $_SESSION['lecId'];
} else {
    $lecId = null;
}

if(!$lecId || $lecId <= 0) {
    die("Invalid lecturer ID.");
}

$stmt = $con->prepare("SELECT * FROM Lecturers WHERE lecId = ?");
$stmt->bind_param("i", $lecId);

$lecName = "";
$lecEmail = "";
$lecContact = "";
$lecPassword = "";

if($stmt->execute()) {
    $result = $stmt->get_result();
    if($result && $row = $result->fetch_assoc()) {
        $lecId = $row['lecId'];
        $lecName = $row['lecName'];
        $lecEmail = $row['lecEmail'];
        $lecContact = $row['lecContact'];
        $lecPassword = $row['lecPassword'];
    } 
     else {
        die("Lecturer not found.");
    }
} else {
    die("Error executing query: " . $stmt->error);
}

$stmt->close();
$con->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="style2.css">
    <title>Edit Lecturer</title>
</head>
<body>
    <div class="main-container">

        <div class="form-container">
            <h2>Edit Your Profile Here</h2>
            <form action="edit_lecturer.php" method="post" id="editLecturerForm" enctype="application/x-www-form-urlencoded">
                <div class="form-input">
                    <label for="lecId">Lecturer ID:</label>
                    <input type="hidden" name="lecId" id="lecId" value="<?php echo htmlspecialchars($lecId); ?>">
                    <span id="idDisplay"><?php echo htmlspecialchars($lecId); ?></span>
                </div>
                <div class="form-input">
                    <label for="name">Name:</label>
                    <input type="text" name="name" id="name" value="<?php echo htmlspecialchars($lecName); ?>">
                </div>

                <div class="form-input">
                    <label for="email">Email:</label>
                    <input type="text" name="email" id="email" value="<?php echo htmlspecialchars($lecEmail); ?>">
                </div>
                <div class="form-input">
                    <label for="contact">Contact:</label>
                    <input type="text" name="contact" id="contact" value="<?php echo htmlspecialchars($lecContact); ?>">
                </div>
                <div class="form-input">
                    <label for="password">Password:</label>
                    <input type="text" name="password" id="password" value="<?php echo htmlspecialchars($lecPassword); ?>">
                </div>
                <div class="form-input" style="display: flex; justify-content: center; align-items: center;">
                    <input type="submit" name="submit" value="Save Changes" style="background-color: #4CAF50; color: #fff; border: none; border-radius: 5px; padding: 10px 10px; cursor: pointer; font-weight: bold; width: auto; min-width: 120px; text-align: center;">
                </div>
            </form>
        </div>
    </div>
</body>
</html>