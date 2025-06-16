<?php
// Include the database connection file
require("connect.php"); // Ensures a connection to the database is established
// Check if the request method is POST
// $_SERVER is a PHP superglobal array that holds information about headers, paths, and script locations
if ($_SERVER["REQUEST_METHOD"] !== "POST") return;
    // Retrieve user inputs from the login form using the $_POST superglobal
    // $_POST is an associative array containing form data submitted via POST method
    $role = $_POST["role"]; // Retrieves the selected role (Student or Lecturer or HOD) from the form
    $email = $_POST["stdEmail"];
    $password = hash('sha256', $_POST["stdPassword"]);

    if ($role === "Student") {
        $stmt = "SELECT * FROM Students WHERE stdEmail = '$email' AND stdPassword = '$password'";
        $result = $con->query($stmt);
    } elseif ($role === "Lecturer") {
        $stmt = "SELECT * FROM Lecturers WHERE lecEmail = '$email' AND lecPassword = '$password'";
        $result = $con->query($stmt);
    } elseif ($role === "HOD") {
        $stmt = "SELECT * FROM HOD WHERE hodEmail = '$email' AND hodPassword = '$password'";
        $result = $con->query($stmt);
    } else {
        echo "Role does not exist";
        exit();
    }

    if ($result && $result->num_rows == 1) {
       if($role=="Student"){
            $row = $result->fetch_assoc(); // Fetches the user data as an associative array
        // Store student ID in a session for future use
        // $_SESSION is a PHP superglobal variable that is used to store data across multiple pages for a single user session
        $_SESSION['stdId'] = $row['stdId']; // Stores the student ID in the session
        $_SESSION['stdName'] = $row['stdName']; 
       
        // Redirect the user to the student dashboard upon successful login
        // header() is a PHP function used to send raw HTTP headers
        header('Location: sdashboard.php'); 
        exit();
         // Ensures that no further code is executed after redirection
    }
    elseif($role=="Lecturer"){
        $row = $result->fetch_assoc(); // Fetches the user data as an associative array
        // Store student ID in a session for future use
        // $_SESSION is a PHP superglobal variable that is used to store data across multiple pages for a single user session
    
        $_SESSION['lecId'] = $row['lecId']; // Stores the student ID in the session
        $_SESSION['lecName'] = $row['lecName'];

        

        // Redirect the user to the student dashboard upon successful login
        // header() is a PHP function used to send raw HTTP headers
        header('Location: ldashboard.php'); 
        exit(); // Ensures that no further code is executed after redirection
        
    }
        elseif($role=="HOD"){
        $row = $result->fetch_assoc(); // Fetches the user data as an associative array
        // Store HOD ID in a session for future use
        // $_SESSION is a PHP superglobal variable that is used to store data across multiple pages for a single user session

        $_SESSION['hodId'] = $row['hodId']; // Stores the HOD ID in the session
        $_SESSION['hodName'] = $row['hodName'];



        // Redirect the user to the HOD dashboard upon successful login
        // header() is a PHP function used to send raw HTTP headers
        header('Location: HODdashboard.php'); 
        exit(); // Ensures that no further code is executed after redirection
        
    }
    } else {
        if (!$result) {
            echo "Query error: " . $con->error;
        } else {
            echo "<br/><h1>Incorrect Email or Password<h1/>";
        }
    }
?>




