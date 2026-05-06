<?php
// Database Configuration
$host     = 'localhost';
$username = 'root';
$password = '';
$db_name  = 'eproject';

// Create connection
$conn = mysqli_connect($host, $username, $password, $db_name);

// Check connection
if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

// Set charset
mysqli_set_charset($conn, "utf8mb4");

// Alias for older scripts
$con = &$conn;
?>
