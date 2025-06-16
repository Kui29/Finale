<?php
require_once("connect.php");

// Use stdId for both GET and POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['stdId'])) {
    $studentId = filter_var($_POST['stdId'], FILTER_VALIDATE_INT);
    $stdName = $_POST['name'];
    $stdGender = $_POST['gender'];
    $stdEmail = $_POST['email'];
    $stdContact = $_POST['contact'];
    $stdPassword = $_POST['password'];
    if($stdPassword === null) {
        $stdPassword = ""; // Set to empty string if password is null
    }
    $updateStmt = $con->prepare("UPDATE Students SET stdName=?, stdGender=?, stdEmail=?, stdContact=?, stdPassword=? WHERE stdId=?");
    $updateStmt->bind_param("sssssi", $stdName, $stdGender, $stdEmail, $stdContact, $stdPassword, $studentId);
    if ($updateStmt->execute()) {
        // Update session values if this is the logged-in student
        if (isset($_SESSION['stdId']) && $_SESSION['stdId'] == $studentId) {
            $_SESSION['stdName'] = $stdName;
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
        die("Error updating student: " . $updateStmt->error);
    }
}
elseif (isset($_GET['id'])) {
    $studentId = filter_var($_GET['id'], FILTER_VALIDATE_INT);
} else {
    $studentId = null;
}

if(!$studentId || $studentId <= 0) {
    die("Invalid student ID.");
}

$stmt = $con->prepare("SELECT * FROM Students WHERE stdId = ?");
$stmt->bind_param("i", $studentId);

$stdId = "";
$stdName = "";
$stdGender = "";
$stdEmail = "";
$stdContact = "";
$stdPassword = "";

if($stmt->execute()) {
    $result = $stmt->get_result();
    if($result && $row = $result->fetch_assoc()) {
        $stdId = $row['stdId'];
        $stdName = $row['stdName'];
        $stdGender = $row['stdGender'];
        $stdEmail = $row['stdEmail'];
        $stdContact = $row['stdContact'];
        $stdPassword = $row['stdPassword'];
    } 
     else {
        die("Student not found.");
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
    <title>Edit Student</title>
</head>
<body>
    <div class="main-container">

        <div class="form-container">
            <h2>Edit Your Profile Here</h2>
            <form action="edit_profile.php" method="post" id="editStudentForm" enctype="application/x-www-form-urlencoded">
                <div class="form-input">
                    <label for="stdId">Student ID:</label>
                    <input type="hidden" name="stdId" id="stdId" value="<?php echo htmlspecialchars($stdId); ?>">
                    <span id="idDisplay"><?php echo htmlspecialchars($stdId); ?></span>
                </div>
                <div class="form-input">
                    <label for="name">Name:</label>
                    <input type="text" name="name" id="name" value="<?php echo htmlspecialchars($stdName); ?>">
                </div>
                <div class="form-input">
                    <label for="gender">Gender:</label>
                    <input type="radio" id="genderMale" name="gender" value="Male" <?php if($stdGender==="Male") echo "checked"; ?>>
                    <label for="genderMale" style="display:inline; margin-right:10px;">Male</label>
                    <input type="radio" id="genderFemale" name="gender" value="Female" <?php if($stdGender==="Female") echo "checked"; ?>>
                    <label for="genderFemale" style="display:inline;">Female</label>
                </div>
                <div class="form-input">
                    <label for="email">Email:</label>
                    <input type="text" name="email" id="email" value="<?php echo htmlspecialchars($stdEmail); ?>">
                </div>
                <div class="form-input">
                    <label for="contact">Contact:</label>
                    <input type="text" name="contact" id="contact" value="<?php echo htmlspecialchars($stdContact); ?>">
                </div>
                <div class="form-input">
                    <label for="password">Password:</label>
                    <input type="text" name="password" id="password" value="<?php echo htmlspecialchars($stdPassword); ?>">
                </div>
                <div class="form-input" style="display: flex; justify-content: center; align-items: center;">
                    <input type="submit" name="submit" value="Save Changes" style="background-color: #4CAF50; color: #fff; border: none; border-radius: 5px; padding: 10px 10px; cursor: pointer; font-weight: bold; width: auto; min-width: 120px; text-align: center;">
                </div>
            </form>
        </div>
    </div>
</body>
</html>