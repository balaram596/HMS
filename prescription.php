<?php
session_start();
if (!isset($_SESSION['doctor_id'])) {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['appointment_id'])) {
    echo "Invalid appointment ID.";
    exit();
}

$appointment_id = intval($_GET['appointment_id']);
$host = 'localhost';
$dbname = 'user_db';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Handle form submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['prescription'])) {
        $prescription = trim($_POST['prescription']);

        $stmt = $pdo->prepare("UPDATE appointments SET prescription = :prescription, status = 'completed' WHERE id = :id");
        $stmt->execute([
            ':prescription' => $prescription,
            ':id' => $appointment_id
        ]);

        echo "<p style='color: green;'>Prescription saved successfully.</p>";
        echo "<a href='appointments_view.php'>Back to Appointments</a>"; // Replace with your actual view page
        exit();
    }

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Write Prescription</title>
</head>
<body>
    <h2>Write Prescription</h2>
    <form method="POST">
        <label for="prescription">Prescription:</label><br>
        <textarea name="prescription" id="prescription" rows="6" cols="50" placeholder="Enter prescription here..." required></textarea><br><br>
        <button type="submit">Submit Prescription</button>
    </form>
    <br>
    <a href="appointments_view.php">Cancel and Go Back</a> <!-- Adjust as needed -->
</body>
</html>
