<?php
session_start();

if (!isset($_SESSION['doctor_id'])) {
    header("Location: login.php");
    exit();
}

$host = 'localhost';
$dbname = 'user_db';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if (isset($_GET['appointment_id']) && is_numeric($_GET['appointment_id'])) {
        $appointment_id = $_GET['appointment_id'];

        // Fetch the appointment
       $stmt = $pdo->prepare("SELECT * FROM appointments WHERE appointment_id = :id");

        $stmt->execute([':id' => $appointment_id]);
        $appointment = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$appointment) {
            echo "❌ Appointment not found.";
            exit();
        }

        $current_status = $appointment['status'];

        // Only allow update if current status is pending
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && $current_status === 'pending') {
            $new_status = $_POST['status'];

            if (in_array($new_status, ['completed', 'cancelled'])) {
                $stmt = $pdo->prepare("UPDATE appointments SET status = :status WHERE appointment_id = :id");

                $stmt->execute([':status' => $new_status, ':id' => $appointment_id]);
                echo "✅ Appointment updated to <strong>$new_status</strong>.<br><br><a href='appointments.php'>Back to Appointments</a>";
                exit();
            } else {
                echo "❌ Invalid status change.";
                exit();
            }
        }

    } else {
        echo "❌ Invalid appointment ID.";
        exit();
    }

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Appointment</title>
    <style>
        body {
            font-family: Arial;
            padding: 30px;
            background-color: #f4f8f9;
        }
        h2 {
            color: #007bff;
        }
        form {
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            width: 300px;
        }
        select, button {
            padding: 10px;
            width: 100%;
            margin-top: 10px;
        }
        .disabled-message {
            color: red;
            font-weight: bold;
        }
        a {
            display: block;
            margin-top: 20px;
            color: #007bff;
        }
    </style>
</head>
<body>

<h2>Edit Appointment Status</h2>

<?php if ($current_status === 'pending'): ?>
    <form method="post">
        <label for="status">Change status:</label>
        <select name="status" required>
            <option value="completed">Mark as Completed</option>
            <option value="cancelled">Cancel Appointment</option>
        </select>
        <button type="submit">Update</button>
    </form>
<?php else: ?>
    <p class="disabled-message">This appointment is already <strong><?php echo htmlspecialchars($current_status); ?></strong> and cannot be changed.</p>
<?php endif; ?>

<a href="appointments.php">Back to Appointments</a>

</body>
</html>
