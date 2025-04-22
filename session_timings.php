<?php
// Start the session
session_start();

// Check if the doctor is logged in
if (!isset($_SESSION['doctor_id'])) {
    header("Location: login.php");
    exit();
}

$doctor_id = $_SESSION['doctor_id'];

// Database connection
$host = 'localhost';  // Change to your database host
$dbname = 'user_db';  // Change to your database name
$username = 'root';    // Change to your database username
$password = '';        // Change to your database password

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Fetch doctor session timings
    $stmt = $pdo->prepare("SELECT * FROM doctor_sessions WHERE doc_id = :doctor_id ORDER BY FIELD(day_of_week, 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'), session_number");
    $stmt->bindParam(':doctor_id', $doctor_id);
    $stmt->execute();
    $sessions = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Session Timings</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }

        table, th, td {
            border: 1px solid black;
        }

        th, td {
            padding: 10px;
            text-align: center;
        }

        th {
            background-color: #f2f2f2;
        }

        td a {
            text-decoration: none;
            color: blue;
        }
    </style>
</head>
<body>

<h2>Your Session Timings</h2>

<table>
    <thead>
        <tr>
            <th>Day</th>
            <th>Session 1</th>
            <th>Session 2</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <?php
        // Group sessions by weekday
        $weekdays = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];

        foreach ($weekdays as $day) {
            // Get sessions for this specific day
            $sessions_today = array_filter($sessions, function($session) use ($day) {
                return $session['day_of_week'] === $day;
            });

            // Default empty session times
            $session1 = $session2 = '';

            // Assign sessions to session1 and session2
            $i = 0;
            foreach ($sessions_today as $session) {
                if ($i == 0) {
                    $session1 = htmlspecialchars($session['start_time']) . " - " . htmlspecialchars($session['end_time']);
                } elseif ($i == 1) {
                    $session2 = htmlspecialchars($session['start_time']) . " - " . htmlspecialchars($session['end_time']);
                }
                $i++;
            }

            echo "<tr>
                    <td>" . $day . "</td>
                    <td>" . $session1 . "</td>
                    <td>" . $session2 . "</td>
                    <td>
                        <a href='edit_session.php?day_of_week=" . $day . "'>Edit</a> 
                        
                    </td>
                </tr>";
        }
        ?>
    </tbody>
</table>

<a href="doctor_dashboard.php">Back to Dashboard</a>

</body>
</html>
