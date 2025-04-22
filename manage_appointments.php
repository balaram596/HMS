<?php
session_start();
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['doctor', 'admin'])) {
    header("Location: login.html");
    exit();
}

$role = $_SESSION['role'];
$username = $_SESSION['username'];

$pdo = new PDO("mysql:host=localhost;dbname=user_db", "root", "");
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Handle status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['appointment_id'], $_POST['action'])) {
    $appointment_id = $_POST['appointment_id'];
    $new_status = $_POST['action'] === 'complete' ? 'completed' : 'cancelled';
    $prescription = $_POST['prescription'] ?? null;

    $stmt = $pdo->prepare("UPDATE appointments SET status = :status, prescription = :prescription WHERE id = :id");
    $stmt->execute([':status' => $new_status, ':prescription' => $prescription, ':id' => $appointment_id]);
}

// Get appointments
if ($role === 'doctor') {
    $stmt = $pdo->prepare("SELECT doc_id FROM doctors WHERE doc_username = ?");
    $stmt->execute([$username]);
    $doctor_id = $stmt->fetchColumn();

    $stmt = $pdo->prepare("
        SELECT a.*, u.fullname AS patient_name, ds.day_of_week, ds.start_time, ds.end_time
        FROM appointments a
        JOIN users u ON a.pat_id = u.id
        JOIN doctor_sessions ds ON a.session_id = ds.id
        WHERE a.doctor_id = ?
        ORDER BY a.appointment_date ASC
    ");
    $stmt->execute([$doctor_id]);
} else {
    $stmt = $pdo->query("
        SELECT a.*, u.fullname AS patient_name, d.doc_name AS doctor_name,
               ds.day_of_week, ds.start_time, ds.end_time
        FROM appointments a
        JOIN users u ON a.pat_id = u.id
        JOIN doctors d ON a.doctor_id = d.doc_id
        JOIN doctor_sessions ds ON a.session_id = ds.id
        ORDER BY a.appointment_date DESC
    ");
}
$appointments = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Appointment Management</title>
    <style>
        select[disabled] {
            background-color: #f5f5f5;
            color: #333;
        }
        textarea {
            resize: vertical;
            width: 100%;
        }
    </style>
</head>
<body>
    <h2><?= ucfirst($role) ?> Appointment Dashboard</h2>

    <table border="1" cellpadding="8">
        <tr>
            <?php if ($role === 'admin'): ?>
                <th>Doctor</th>
            <?php endif; ?>
            <th>Patient</th>
            <th>Date</th>
            <th>Session</th>
            <th>Token</th>
            <th>Status</th>
            <th>Prescription</th>
            <th>Action</th>
        </tr>

        <?php foreach ($appointments as $appt): ?>
            <tr>
                <?php if ($role === 'admin'): ?>
                    <td><?= htmlspecialchars($appt['doctor_name']) ?></td>
                <?php endif; ?>
                <td><?= htmlspecialchars($appt['patient_name']) ?></td>
                <td><?= $appt['appointment_date'] ?></td>
                <td><?= $appt['day_of_week'] . ' - ' . $appt['start_time'] . ' to ' . $appt['end_time'] ?></td>
                <td><?= $appt['token_id'] ?></td>
                <td>
                    <select disabled>
                        <option value="pending" <?= $appt['status'] === 'pending' ? 'selected' : '' ?>>Pending</option>
                        <option value="completed" <?= $appt['status'] === 'completed' ? 'selected' : '' ?>>Completed</option>
                        <option value="cancelled" <?= $appt['status'] === 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                    </select>
                </td>
                <td>
                    <?= $appt['status'] === 'pending' ? '' : htmlspecialchars($appt['prescription']) ?>
                </td>
                <td>
                    <?php if ($appt['status'] === 'pending'): ?>
                        <form method="POST">
                            <input type="hidden" name="appointment_id" value="<?= $appt['id'] ?>">
                            <textarea name="prescription" placeholder="Enter prescription..." rows="2"><?= htmlspecialchars($appt['prescription'] ?? '') ?></textarea><br>
                            <button name="action" value="complete">Complete</button>
                            <button name="action" value="cancel">Cancel</button>
                        </form>
                    <?php else: ?>
                        <em><?= ucfirst($appt['status']) ?></em>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>

    <br><a href="dashboard.php">Back to Dashboard</a>
</body>
</html>
