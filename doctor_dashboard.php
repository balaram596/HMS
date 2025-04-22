<?php
session_start();

// Check if the doctor is logged in
if (!isset($_SESSION['doctor_id'])) {
    header("Location: login.php");
    exit();
}

$doctor_id = $_SESSION['doctor_id'];
$doctor_name = $_SESSION['doctor_name'];
$doctor_specl = $_SESSION['doctor_specl'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Doctor Dashboard</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f8f9;
            color: #333;
            padding: 50px;
        }

        .dashboard-container {
            max-width: 600px;
            margin: auto;
            background-color: #fff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        h2 {
            color: #007bff;
            text-align: center;
            font-size: 2em;
            margin-bottom: 10px;
        }

        p {
            text-align: center;
            font-size: 1.1em;
            color: #555;
        }

        nav ul {
            list-style: none;
            padding: 0;
            margin-top: 30px;
        }

        nav ul li {
            margin: 15px 0;
        }

        nav ul li a {
            display: block;
            padding: 12px;
            text-decoration: none;
            color: #fff;
            background-color: #007bff;
            border-radius: 6px;
            font-weight: bold;
            transition: background-color 0.3s ease;
        }

        nav ul li a:hover {
            background-color: #0056b3;
        }

        .logout {
            display: block;
            text-align: center;
            margin-top: 30px;
            text-decoration: none;
            color: #007bff;
        }

        .logout:hover {
            text-decoration: underline;
        }

        .icon {
            margin-right: 8px;
        }
    </style>
</head>
<body>

<div class="dashboard-container">
    <h2><i class="fas fa-user-md icon"></i>Welcome, Dr. <?php echo htmlspecialchars($doctor_name); ?></h2>
    <p><i class="fas fa-stethoscope icon"></i>Specialization: <?php echo htmlspecialchars($doctor_specl); ?></p>

    <nav>
        <ul>
            <li><a href="appointments.php"><i class="fas fa-calendar-check icon"></i>Manage Appointments</a></li>
            <li><a href="session_timings.php"><i class="fas fa-clock icon"></i>Manage Session Timings</a></li>
            <li><a href="prescriptions.php"><i class="fas fa-prescription icon"></i>Manage Prescriptions</a></li>
        </ul>
    </nav>

    <a class="logout" href="logout.php"><i class="fas fa-sign-out-alt icon"></i>Logout</a>
</div>

</body>
</html>
