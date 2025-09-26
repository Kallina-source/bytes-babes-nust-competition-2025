<?php
$servername = "localhost";
$username = "root"; 
$password = "";     
$dbname = "mesmtf"; 

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}
//testing
//echo "Connected successfully";
?>