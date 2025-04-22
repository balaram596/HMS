<?php
$servername = "localhost";
$username = "root"; // Default MySQL username in XAMPP
$password = ""; // Default MySQL password in XAMPP
$dbname = "user_db"; // Your database name

// Create a connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect form data
	$name = $_POST['name'];
    $phone = $_POST['phone'];
    $age = $_POST['age'];
    $gender = $_POST['gender'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];


    // Password validation
    if ($password !== $confirm_password) {
        echo "Passwords do not match!";
        exit();
    }

    // Hash the password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Insert into database (users table)
    $stmt = $conn->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, 'patient')");
    $stmt->bind_param("ss", $username, $hashed_password);

    if ($stmt->execute()) {
        // Get the user ID of the newly created patient
        $patient_id = $conn->insert_id;

        // Insert patient details into patients table
        $stmt_info = $conn->prepare("INSERT INTO patients (pat_username,pat_name,pat_age,pat_gender, pat_phone) VALUES (?, ?,?,?,?)");
        $stmt_info->bind_param("sssss", $username,$name,$age,$gender, $phone); // Correcting variable names and data types

        if ($stmt_info->execute()) {
            echo "Patient registration successful!";
        } else {
            echo "Error: " . $stmt_info->error;
        }

        $stmt_info->close();
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();
?>
