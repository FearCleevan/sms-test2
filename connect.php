<?php

$host = "localhost";       
$user = "root";            
$password = "";            
$dbname = "samson_management_system";

$conn = new mysqli($host, $user, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Remove or comment out this line
// echo "Connected successfully";

?>
