<?php
// Include the database connection file
require('connect.php'); // Ensures the database connection is established if needed

// Destroy the current session
// session_destroy() is a PHP function that ends the user's session and clears session variables
if (session_destroy()) { 
    // Redirect the user to the landing page after logging out
    // header("Location: landing.html") sends an HTTP header to redirect the browser to landing.html
    header("Location: landing.html");
    exit(); // Ensures that no further code is executed after redirection
}
?>





