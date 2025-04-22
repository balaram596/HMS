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
    $username = $_POST['username'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $specialization = $_POST['specialization'];

    // Password validation
    if ($password !== $confirm_password) {
        echo "Passwords do not match!";
        exit();
    }

    // Hash the password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Insert into database
    $stmt = $conn->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, 'doctor')");
    $stmt->bind_param("ss", $username, $hashed_password);

    if ($stmt->execute()) {
        // Get the user ID of the newly created doctor
        $doctor_id = $conn->insert_id;

        // Insert specialization into doctor info table (optional)
        $stmt_specialization = $conn->prepare("INSERT INTO doctor_info (doctor_id, specialization) VALUES (?, ?)");
        $stmt_specialization->bind_param("is", $doctor_id, $specialization);
        $stmt_specialization->execute();
        $stmt_specialization->close();

        echo "Doctor registration successful!";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();
?>
