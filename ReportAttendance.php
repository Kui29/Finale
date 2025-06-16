<?php
// ReportAttendance.php
// Displays a student's attendance in a table format with search functionality for unit code.

// Create connection
require("connect.php");

// Get student ID and name from session
$stdId = $_SESSION['stdId'];
$stdName = isset($_SESSION['stdName']) ? $_SESSION['stdName'] : 'Student';

// Fetch all attended lectures for this student
$sql = "SELECT lectureName FROM attendance WHERE stdId = ?";
$stmt = $con->prepare($sql);
$stmt->bind_param("i", $stdId);
$stmt->execute();
$result = $stmt->get_result();
$attendance = [];
while ($row = $result->fetch_assoc()) {
    // Extract unit code (first two words of lectureName)
    $labelWords = preg_split('/\s+/', $row['lectureName']);
    $unit = isset($labelWords[0], $labelWords[1]) ? $labelWords[0] . ' ' . $labelWords[1] : $row['lectureName'];
    // Extract lecture number from label (e.g., 'Lecture 1')
    preg_match('/Lecture\s+(\d+)/i', $row['lectureName'], $match);
    $lectureNum = isset($match[1]) ? (int)$match[1] : null;
    // Only consider lectures 1-13
    if ($lectureNum !== null && $lectureNum >= 1 && $lectureNum <= 13) {
        $attendance[$unit][$lectureNum] = true;
    }
}
$stmt->close();

// Get count of attended lectures (distinct lectureName)
$countSql = "SELECT COUNT(DISTINCT lectureName) as attended_count FROM attendance WHERE stdId = ?";
$countStmt = $con->prepare($countSql);
$countStmt->bind_param("i", $stdId);
$countStmt->execute();
$countResult = $countStmt->get_result();
$countRow = $countResult->fetch_assoc();
$attendedCount = $countRow['attended_count'];
$countStmt->close();

// Get all unit codes for table rows
$unitCodes = array_keys($attendance);

// Output: Student name and total attended lectures
echo '<h2 style="text-align:center;">' . htmlspecialchars($stdName) . '</h2>';
echo '<h3 style="text-align:center;">Total Attended Lectures: ' . $attendedCount . '</h3>';

// Output: Attendance table (Unit Code x Lecture 1-13)
echo '<table border="1" style="border-collapse:collapse;margin:auto;">';
echo '<tr><th>Unit Code</th>';
for ($i = 1; $i <= 13; $i++) {
    echo '<th>Lecture ' . $i . '</th>';
}
echo '</tr>';
foreach ($unitCodes as $unit) {
    echo '<tr>';
    echo '<td>' . htmlspecialchars($unit) . '</td>';
    for ($i = 1; $i <= 13; $i++) {
        // Mark attended with ✅, not attended with ❌
        if (isset($attendance[$unit][$i])) {
            echo '<td style="background:#c8e6c9;text-align:center;">✅</td>';
        } else {
            echo '<td style="background:#ffcdd2;text-align:center;">❌</td>';
        }
    }
    echo '</tr>';
}
echo '</table>';
?>
<!-- Search box just below the attendance table -->
<div id="unitSearchBox" style="margin:auto;margin-top:30px;display:flex;align-items:right;gap:8px;justify-content:center;">
    <input type="text" id="unitSearchInput" placeholder="Search unit code...">
    <button id="unitSearchBtn"><img src="search.jpg" alt="Search" style="width:28px;height:28px;"></button>
</div>
<div id="unitSearchResult" style="margin:auto;margin-top:10px;max-width:700px;display:none;"></div>
<script>
// Pass PHP attendance data to JavaScript for searching
const attendanceData = <?php echo json_encode($attendance); ?>;
// Handle search button click
// Shows attendance for the searched unit code (lectures 1-13)
document.getElementById('unitSearchBtn').onclick = function(e) {
    e.preventDefault();
    const code = document.getElementById('unitSearchInput').value.trim().toUpperCase();
    const resultDiv = document.getElementById('unitSearchResult');
    if (!code) {
        resultDiv.style.display = 'none';
        return;
    }
    let found = false;
    for (const unit in attendanceData) {
        if (unit.toUpperCase() === code) {
            found = true;
            let html = `<b>Attendance for ${unit}</b><table border='1' style='margin-top:8px;
                                                                              border-collapse:collapse;
                                                                              width:100%;'><tr>`;
            for (let i = 1; i <= 13; i++) html += `<th>Lecture ${i}</th>`;
            html += '</tr><tr>';
            for (let i = 1; i <= 13; i++) {
                html += `<td style='text-align:center;'>${attendanceData[unit][i] ? '✅' : '❌'}</td>`;
            }
            html += '</tr></table>';
            resultDiv.innerHTML = html;
            resultDiv.style.display = 'block';
            return;
        }
    }
    // If not found, show error
    resultDiv.innerHTML = `<span style='color:red;'>Unit code not found or no attendance.</span>`;
    resultDiv.style.display = 'block';
};
// Hide result if search box is cleared
document.getElementById('unitSearchInput').oninput = function() {
    if (!this.value.trim()) document.getElementById('unitSearchResult').style.display = 'none';
};
</script>