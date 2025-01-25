<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Generated Reports</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        h1 {
            text-align: center;
            color: #4CAF50;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        a {
            color: #4CAF50;
            text-decoration: none;
        }
        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

    <h1>Generated Reports</h1>
    
    <table>
        <thead>
            <tr>
                <th>Attendance ID</th>
                <th>Student ID</th>
                <th>Lecturer ID</th>
                <th>Location ID</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // Database connection
           require("connect.php");

            // Query to fetch reports
             $sql = "SELECT 
                a.attendanceId,
                a.stdId,
                a.attendanceTime,
                a.locId,
                l.lecId
            FROM 
                Attendance as a
            INNER JOIN 
                Lecturers as l
            ON 
                a.lecId = l.lecId";

            $result = $con->query($sql);

            if ($result->num_rows > 0) {
                // Output data for each row
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>
                            <td>{$row['attendanceId']}</td>
                            <td>{$row['stdId']}</td>
                            <td>{$row['lecId']}</td>
                            <td>{$row['locId']}</td>
                          </tr>";
                }
            } else {
                echo "<tr><td colspan='3'>No reports found</td></tr>";
            }

            // Close the connection
            $con->close();
            ?>
        </tbody>
    </table>

</body>
</html>
