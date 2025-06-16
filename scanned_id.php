<?php

require('connect.php');

//check if request is valid
if (!isset($_GET['id']) || empty($_GET['id'])) exit('No lecture id provided');

//grab the scanned id from the request
$lecture = trim($_GET['id']);

//check login status
if (!isset($_SESSION['stdId'])) exit('Not logged in');

// check if the lecture exists
$checkLecture = $con->prepare("SELECT * FROM LectureDetails WHERE lectureName = ?");
$checkLecture->bind_param("s", $lecture);
$checkLecture->execute();
$result = $checkLecture->get_result();
if ($result->num_rows == 0) {
    // Debug: show what was searched for
    echo "Lecture not found: '" . htmlspecialchars($lecture) . "'";
    exit;
}
$lectureRow = $result->fetch_assoc();
$lecId = $lectureRow['lecId'];
$locId = $lectureRow['locId'];

//update for session attendance
$attendanceTime = date('Y-m-d H:i:s');
$updateAttendance = $con->prepare("INSERT INTO attendance (stdId,attendanceTime,lectureName,lecId,locId) VALUES (?,?,?,?,?)");
$updateAttendance->bind_param("issss", $_SESSION['stdId'], $attendanceTime, $lecture, $lecId, $locId);
$updateAttendance->execute();

//check if the update was successful
if ($updateAttendance->affected_rows > 0) {
    echo '<!DOCTYPE html>
        <html>
        <head>
        <style>
#backBtn {
    position: fixed;
    top: 20%;
    left: 24px;
    z-index: 100;
    background: none;
    border: none;
    cursor: pointer;
    outline: none;
    animation: bounce 1.2s infinite;
}
#backBtn img {
    width: 48px;
    height: 48px;
}
/*
@keyframes bounce animates the back button to move up and down, creating a bouncing effect.
0% and 100%: at rest
20%: moves up slightly
80%: returns to rest
*/
@keyframes bounce {
    0%, 100% { transform: translateY(0); }
    20% { transform: translateY(-10px); }
    80% { transform: translateY(0); }
}
  
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
<button id="backBtn" onclick="window.location.href=\'sdashboard.php\'" title="Back">
    <img src="back.png" alt="Back">
</button>
        <div class="success-popup">
            <img src="correct.gif" alt="Success">
        <span>Attendance Successfully Recorded</span>
        </div>
        </body>
        </html>';
} else {
    echo  'Failed to record attendance: ' . $updateAttendance->error;
}