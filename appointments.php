<?php
session_start();

if (!isset($_SESSION['doctor_id'])) {
    header("Location: login.php");
    exit();
}

$doctor_id = $_SESSION['doctor_id'];

// Database connection
$host = 'localhost';
$dbname = 'user_db';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Fetch unique doctor appointments with patient info
 $stmt = $pdo->prepare("
    SELECT DISTINCT a.appointment_id AS id, u.username AS patient_name, a.appointment_date, a.status
    FROM appointments a
    JOIN users u ON a.pat_id = u.id 
    WHERE a.doc_id = :doctor_id 
    ORDER BY a.appointment_date DESC
");



    $stmt->bindParam(':doctor_id', $doctor_id);
    $stmt->execute();
    $appointments = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Doctor Appointments</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f8f9;
            padding: 40px;
        }

        h2 {
            text-align: center;
            color: #007bff;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 25px;
        }

        th, td {
            border: 1px solid #ccc;
            padding: 12px;
            text-align: center;
        }

        th {
            background-color: #007bff;
            color: white;
        }

        td {
            background-color: #fff;
        }

        .btn {
            padding: 6px 12px;
            border-radius: 4px;
            font-size: 14px;
            cursor: pointer;
            margin: 0 2px;
            text-decoration: none;
            display: inline-block;
        }

        .btn-edit {
            background-color: #ffc107;
            color: white;
        }

        .btn-prescription {
            background-color: #28a745;
            color: white;
        }

        .status-pending {
            color: orange;
            font-weight: bold;
        }

        .status-completed {
            color: green;
            font-weight: bold;
        }

        .status-cancelled {
            color: red;
            font-weight: bold;
        }

        .back-link {
            display: block;
            text-align: center;
            margin-top: 30px;
            color: #007bff;
        }

        .back-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

    <h2><i class="fas fa-calendar-check"></i> Doctor Appointments</h2>

    <table>
        <thead>
            <tr>
                <th>Patient Name</th>
                <th>Appointment Date</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php if (!empty($appointments)) : ?>
            <?php foreach ($appointments as $appointment): ?>
            <tr>
                <td><?= htmlspecialchars($appointment['patient_name']); ?></td>
                <td><?= htmlspecialchars($appointment['appointment_date']); ?></td>
                <td>
    <?php
        $status = $appointment['status'];
        if ($status === 'pending') {
            echo '<span class="status-pending">Pending</span>';
        } elseif ($status === 'completed') {
            echo '<span class="status-completed">Completed</span>';
        } elseif ($status === 'cancelled') {
            echo '<span class="status-cancelled">Cancelled</span>';
        } else {
            echo htmlspecialchars($status);
        }
    ?>
</td>

              <td>
    <?<?php if ($appointment['status'] === 'pending'): ?>
    <a class="btn btn-edit" href="edit_appointment.php?appointment_id=<?= $appointment['id']; ?>"><i class="fas fa-edit"></i> Edit</a>
<?php endif; ?>

<?php if (in_array($appointment['status'], ['pending', 'completed'])): ?>
    <a class="btn btn-prescription" href="prescription.php?appointment_id=<?= $appointment['id']; ?>"><i class="fas fa-notes-medical"></i> Prescription</a>
<?php else: ?>
    <em>No actions</em>
<?php endif; ?>

</td>

            </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr><td colspan="4">No appointments found.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>

    <a class="back-link" href="doctor_dashboard.php"><i class="fas fa-arrow-left"></i> Back to Dashboard</a>

</body>
</html>
