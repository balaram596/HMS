<?php
// db.php - A file to establish and handle the database connection

// Database connection credentials
$servername = "localhost";  // Your database server (usually "localhost")
$username = "root";         // Your MySQL username
$password = "";             // Your MySQL password (leave empty for default local server)
$dbname = "user_db";  // Your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    // If connection fails, output a detailed error message and exit
    die("Connection failed: " . $conn->connect_error);
}

// Set character set to UTF-8 to avoid encoding issues
$conn->set_charset("utf8");

// Optionally, you can handle and log the connection here for debugging purposes
// error_log("Connected to the database successfully");

// The $conn object can now be used throughout your PHP files to interact with the database.

?>
