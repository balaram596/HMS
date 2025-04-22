<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <style>
        /* Base Styles */
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f7fc;
            color: #333;
        }

        header {
            background-color: #2c3e50;
            color: white;
            padding: 20px 0;
            text-align: center;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        header h1 {
            font-size: 2.5rem;
            margin: 0;
            font-weight: 600;
        }

        .container {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 80vh;
            padding: 30px;
        }

        .container h2 {
            margin-bottom: 30px;
            font-size: 2rem;
            color: #34495e;
            text-align: center;
        }

        /* Menu Styles */
        .menu {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .menu a {
            text-decoration: none;
            padding: 15px 25px;
            background-color: #3498db;
            color: white;
            border-radius: 8px;
            width: 250px;
            text-align: center;
            transition: all 0.3s ease;
            font-size: 18px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            margin: 5px 0;
        }

        .menu a:hover {
            background-color: #2980b9;
            transform: translateY(-3px);
        }

        .menu a:active {
            background-color: #1f5a80;
            transform: translateY(0);
        }

        /* Footer Styling */
        footer {
            text-align: center;
            padding: 15px;
            background-color: #2c3e50;
            color: white;
            position: fixed;
            bottom: 0;
            width: 100%;
            box-shadow: 0 -4px 8px rgba(0, 0, 0, 0.1);
        }

        footer p {
            margin: 0;
            font-size: 14px;
        }

        /* Logout Link */
        .logout-link {
            font-size: 16px;
            color: #3498db;
            text-decoration: none;
            transition: color 0.3s ease;
            margin-top: 20px;
        }

        .logout-link:hover {
            color: #2980b9;
        }
    </style>
</head>
<body>

    <header>
        <h1>Admin Dashboard</h1>
    </header>
    
    <div class="container">
        <h2>Welcome, Admin!</h2>

        <div class="menu">
            <a href="manage_doctors.php">Manage Doctors</a>
            <a href="manage_patients.php">Manage Patients</a>
            <a href="manage_appointments.php">Manage Appointments</a>
            <a href="view_reports.php">View Reports</a>
        </div>

        <a href="logout.php" class="logout-link">Logout</a>
    </div>

    <footer>
        <p>&copy; 2025 Hospital Management System. All rights reserved.</p>
    </footer>

</body>
</html>
