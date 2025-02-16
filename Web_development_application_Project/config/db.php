<?php
$host = "localhost"; // Change if using a remote server
$user = "root";      // Database username
$pass = "";          // Database password (leave empty for XAMPP)
$dbname = "irrigation_hub"; // Your database name

// Create a connection
$conn = new mysqli($host, $user, $pass, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
