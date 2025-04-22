<?php
session_start();
if ($_SESSION['role'] != 'patient') {
    header("Location: patient_login.html");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Dashboard</title>
</head>
<body>
    <h2>Welcome Patient!</h2>
    <p>View your profile and book appointments.</p>
    <a href="view_profile.php">View Profile</a><br>
    <a href="book_appointment.php">Book Appointment</a><br>
    <a href="logout.php">Logout</a>
</body>
</html>
