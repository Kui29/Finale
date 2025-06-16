<?php
require("connect.php");


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
            display: flex;
        }
        .sidebar {
            width: 250px;
            background: #4CAF50;;
            color: white;
            height: 100vh;
            padding-top: 20px;
            position: fixed;
        }
        .sidebar ul {
            list-style: none;
            padding: 0;
        }
        .sidebar ul li {
            padding: 15px;
            text-align: center;
            cursor: pointer;
        }
        .sidebar ul li a {
            color: white;
            text-decoration: none;
            display: block;
        }
        .sidebar ul li:hover {
            background: #555;
        }
        .main-content {
            margin-left: 250px;
            padding: 20px;
            width: 100%;
        }
        .logout {
            position: absolute;
            top: 10px;
            right: 10px;
            background: red;
            color: white;
            padding: 10px;
            border: none;
            cursor: pointer;
            border-radius: 5px;
        }
        .logout:hover {
            background: darkred;
        }
        .content {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            max-width: 900px;
            margin: auto;
        }
    </style>
    
</head>
<body onload="setGreeting('<?php echo $_SESSION['stdName'];?>')">
    <div class="sidebar">
        <ul>
            <li onclick="showContent('dashboard')">Dashboard</li>
            <li onclick="showContent('profile')">Profile</li>
            <li onclick="showContent('unitreg')">Unit Registration</li>
            <li onclick="showContent('attendance')">Attendance</li>
            <li onclick="showContent('scanner')">Scanner</li>
        </ul>
    </div>

    <div class="main-content">
        <button class="logout" onclick="logout()">Logout</button>
        <div id="content" class="content">
            <h2>Student Dashboard</h2>
            <p id="greeting"></p>
            <?php

            if (isset($_SESSION['stdId'])) {
                $stdId = $_SESSION['stdId'];
                $stdName = $_SESSION['stdName'];
                $query = "SELECT stdName, stdGender, stdEmail, stdContact FROM Students WHERE stdId = '$stdId'";
                $result = $con->query($query);

                if ($result->num_rows > 0) {
                    $row = $result->fetch_assoc();
                    echo "<p> id: ".$_SESSION['stdId']; 
                    echo "<p>Name: " . htmlspecialchars($row['stdName']) . "</p>";
                    echo "<p>Gender: " . htmlspecialchars($row['stdGender']) . "</p>";
                    echo "<p>Email: " . htmlspecialchars($row['stdEmail']) . "</p>";
                    echo "<p>Contact: " . htmlspecialchars($row['stdContact']) . "</p>";
                } else {
                    echo "<p>No student details found.</p>";
                }

                $con->close();
            } else {
                echo "<p>Please log in to see your details.</p>";
            }

            ?>

        </div>
    </div>

    <script>
        function setGreeting(username) {
            const hour = new Date().getHours();
            const greeting = `<b>${hour < 12 ? "Good Morning, " : hour < 18? "Good Afternoon, ": "Good Evening, "}</b>`; 
            let greetingElement = document.getElementById("greeting");
            greetingElement.innerHTML = `${greeting} ${username.trim()}.`;
        }
    
        function logout() {
            window.location.href = 'logOut.php'; // Redirect to landing page
        }

        function showContent(section) {
            const content = {
                dashboard: document.querySelector('.content').innerHTML, // reload dashboard content
                profile: `<iframe src="edit_profile.php?id=<?php echo isset($_SESSION['stdId']) ? urlencode($_SESSION['stdId']) : ''; ?>" style="width:100%;height:600px;border:none;"></iframe>`,
                unitreg: `<iframe src="unit_registration.php" style="width:100%;height:600px;border:none;"></iframe>`,
                attendance: `<iframe src="ReportAttendance.php" style="width:100%;height:600px;border:none;"></iframe>`,
                scanner: `<h2>Scanner</h2><a href="index.html">Click here to scan</a>`
            };
            if (section === 'dashboard') {
                location.reload();
                return;
            }
            document.getElementById('content').innerHTML = content[section] || `<h2>Not Found</h2><p>The requested section does not exist.</p>`;
        }
    </script>

</body>
</html>


