<?php
require_once("connect.php");

// Use hodId for both GET and POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['hodId'])) {
    $hodId = filter_var($_POST['hodId'], FILTER_VALIDATE_INT);
    $hodName = $_POST['name'];
    $hodEmail = $_POST['email'];
    $hodContact = $_POST['contact'];
    $hodPassword = $_POST['password'];
    if($hodPassword === null) {
        $hodPassword = ""; // Set to empty string if password is null
    }
    $updateStmt = $con->prepare("UPDATE HOD SET hodName=?, hodEmail=?, hodContact=?, hodPassword=? WHERE hodId=?");
    $updateStmt->bind_param("ssssi", $hodName, $hodEmail, $hodContact, $hodPassword, $hodId);
    if ($updateStmt->execute()) {
        // Update session values if this is the logged-in HOD
        if (isset($_SESSION['hodId']) && $_SESSION['hodId'] == $hodId) {
            $_SESSION['hodName'] = $hodName;
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
        die("Error updating HOD: " . $updateStmt->error);
    }
}
 elseif (isset($_SESSION['hodId'])) {
    $hodId = $_SESSION['hodId'];
} else {
    $hodId = null;
}
if(!$hodId || $hodId <= 0) {
    die("Invalid HOD ID.");
}
$stmt = $con->prepare("SELECT * FROM HOD WHERE hodId = ?");
$stmt->bind_param("i", $hodId);
if($stmt->execute()) {
    $result = $stmt->get_result();
    if($result && $row = $result->fetch_assoc()) {
        $hodId = $row['hodId'];
        $hodName = $row['hodName'];
        $hodEmail = $row['hodEmail'];
        $hodContact = $row['hodContact'];
        $hodPassword = $row['hodPassword'];
    } 
     else {
        die("HOD not found.");
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
    <title>Edit HOD</title>
</head>
<body>
    <div class="main-container">

        <div class="form-container">
            <h2>Edit Your Profile Here</h2>
            <form action="edit_HOD.php" method="post" id="editHODForm" enctype="application/x-www-form-urlencoded">
                <div class="form-input">
                    <label for="hodId">HOD ID:</label>
                    <input type="hidden" name="hodId" id="hodId" value="<?php echo htmlspecialchars($hodId); ?>">
                    <span id="idDisplay"><?php echo htmlspecialchars($hodId); ?></span>
                </div>
                <div class="form-input">
                    <label for="name">Name:</label>
                    <input type="text" name="name" id="name" value="<?php echo htmlspecialchars($hodName); ?>">
                </div>
                <div class="form-input">
                    <label for="email">Email:</label>
                    <input type="text" name="email" id="email" value="<?php echo htmlspecialchars($hodEmail); ?>">
                </div>
                <div class="form-input">
                    <label for="contact">Contact:</label>
                    <input type="text" name="contact" id="contact" value="<?php echo htmlspecialchars($hodContact); ?>">
                </div>
                <div class="form-input">
                    <label for="password">Password:</label>
                    <input type="text" name="password" id="password" value="<?php echo htmlspecialchars($hodPassword); ?>">
                </div>
                <div class="form-input" style="display: flex; justify-content: center; align-items: center;">
                    <input type="submit" name="submit" value="Save Changes" style="background-color: #4CAF50; color: #fff; border: none; border-radius: 5px; padding: 10px 10px; cursor: pointer; font-weight: bold; width: auto; min-width: 120px; text-align: center;">
                </div>
            </form>
        </div>
    </div>
</body>
</html>