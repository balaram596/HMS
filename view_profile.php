<?php
session_start();

// Ensure the user is logged in and has the correct role
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'patient') {
    header("Location: patient_login.html");
    exit();
}

// Database connection
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

// Get the username from the session
$current_username = $_SESSION['username']; // Assuming the session stores the username

// Query the database to get patient details from the patients table only
$sql = "SELECT pat_name, pat_gender, pat_age, pat_phone, pat_username FROM patients WHERE pat_username = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $current_username);
$stmt->execute();
$result = $stmt->get_result();

// Check if the user was found
if ($result->num_rows > 0) {
    // Fetch user data
    $row = $result->fetch_assoc();
    $pat_name = $row['pat_name']; 
    $pat_gender = $row['pat_gender']; // Updated to fetch 'pat_gender'
    $pat_age = $row['pat_age']; // Updated to fetch 'pat_age'
    $pat_phone = $row['pat_phone'];
    $pat_username = $row['pat_username']; // Updated to fetch 'pat_username'
} else {
    echo "No profile found!";
    exit();
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Profile</title>
</head>
<body>
    <h2>Patient Profile</h2>
    <p><strong>Name:</strong> <?php echo htmlspecialchars($pat_name); ?></p> <!-- Updated to pat_name -->
    <p><strong>Gender:</strong> <?php echo htmlspecialchars($pat_gender); ?></p> <!-- Updated to pat_gender -->
    <p><strong>Age:</strong> <?php echo htmlspecialchars($pat_age); ?></p> <!-- Updated to pat_age -->
    <p><strong>Phone Number:</strong> <?php echo htmlspecialchars($pat_phone); ?></p>
    <p><strong>Username:</strong> <?php echo htmlspecialchars($pat_username); ?></p> <!-- Updated to pat_username -->

    <br>
    <a href="edit_profile.php">Edit Profile</a><br>
    <a href="patient_dashboard.php">Back to Dashboard</a><br>
    <a href="logout.php">Logout</a>
</body>
</html>
