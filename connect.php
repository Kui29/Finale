<?php
//Function to establish a connection to MySQL Database using mysqli extension
require_once 'config.php';
$con = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if(!$con){
	//Built-in PHP function that returns the error message from the last connection attempt using MySQLI 
	echo mysqli_connect_error();
	die();
}

session_start();




