<?php
// Database configuration
$host = "localhost";
$dbname = "ojt_attendance";
$username = "root"; // Replace with your MySQL username
$password = ""; // Replace with your MySQL password

// Connect to the database
$conn = new mysqli($host, $username, $password, $dbname);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


