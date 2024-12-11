<?php
// Database connection 
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'billingsystem';

// Establish the connection
$conn = new mysqli($host, $username, $password, $database);

// Check the connection
if ($conn->connect_error) {
    die("ERROR: Could not connect. " . $conn->connect_error);
}
?>
