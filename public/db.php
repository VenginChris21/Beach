<?php


$servername = "localhost";
$username = "root";     
$password = "";         
$dbname = "beach_employee_system";



$conn = new mysqli($servername, $username, $password, $dbname);



if ($conn->connect_error) {
  
    error_log("Database Connection Failed: " . $conn->connect_error);
    
    $conn = null; 
}
?>