<?php
// Database configuration
$host = "localhost";       // Usually localhost in XAMPP
$user = "root";            // Default XAMPP MySQL username
$password = "";            // Default XAMPP MySQL password is empty
$dbname = "sk_voting_db";     // Your database name

// Create connection
$conn = new mysqli($host, $user, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set character set to UTF-8
$conn->set_charset("utf8");
?>
