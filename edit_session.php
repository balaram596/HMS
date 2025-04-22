<?php
session_start();

if (!isset($_SESSION['doctor_id'])) {
    header("Location: login.php");
    exit();
}

$doctor_id = $_SESSION['doctor_id'];

$host = 'localhost'; 
$dbname = 'user_db';  
$username = 'root';   
$password = '';        

$day_of_week = $_GET['day_of_week'];

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $pdo->prepare("SELECT * FROM doctor_sessions WHERE doc_id = :doctor_id AND day_of_week = :day_of_week ORDER BY session_number");
    $stmt->bindParam(':doctor_id', $doctor_id);
    $stmt->bindParam(':day_of_week', $day_of_week);
    $stmt->execute();
    $sessions = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
    exit();
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $start_time_1 = $_POST['start_time_1'];
    $end_time_1 = $_POST['end_time_1'];
    $start_time_2 = $_POST['start_time_2'];
    $end_time_2 = $_POST['end_time_2'];

    try {
      
        $updateStmt1 = $pdo->prepare("UPDATE doctor_sessions SET start_time = :start_time_1, end_time = :end_time_1 WHERE doc_id = :doctor_id AND day_of_week = :day_of_week AND session_number = 1");
        $updateStmt1->bindParam(':doctor_id', $doctor_id);
        $updateStmt1->bindParam(':day_of_week', $day_of_week);
        $updateStmt1->bindParam(':start_time_1', $start_time_1);
        $updateStmt1->bindParam(':end_time_1', $end_time_1);
        $updateStmt1->execute();

      
        $updateStmt2 = $pdo->prepare("UPDATE doctor_sessions SET start_time = :start_time_2, end_time = :end_time_2 WHERE doc_id = :doctor_id AND day_of_week = :day_of_week AND session_number = 2");
        $updateStmt2->bindParam(':doctor_id', $doctor_id);
        $updateStmt2->bindParam(':day_of_week', $day_of_week);
        $updateStmt2->bindParam(':start_time_2', $start_time_2);
        $updateStmt2->bindParam(':end_time_2', $end_time_2);
        $updateStmt2->execute();

        header("Location: session_timings.php"); 
        exit();
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Session Timings</title>
</head>
<body>

<h2>Edit Session Timings for <?php echo htmlspecialchars($day_of_week); ?></h2>

<form action="" method="post">
    <h3>Session 1</h3>
    <label for="start_time_1">Start Time:</label>
    <input type="time" id="start_time_1" name="start_time_1" value="<?php echo htmlspecialchars($sessions[0]['start_time']); ?>" required><br><br>

    <label for="end_time_1">End Time:</label>
    <input type="time" id="end_time_1" name="end_time_1" value="<?php echo htmlspecialchars($sessions[0]['end_time']); ?>" required><br><br>

    <h3>Session 2</h3>
    <label for="start_time_2">Start Time:</label>
    <input type="time" id="start_time_2" name="start_time_2" value="<?php echo htmlspecialchars($sessions[1]['start_time']); ?>" required><br><br>

    <label for="end_time_2">End Time:</label>
    <input type="time" id="end_time_2" name="end_time_2" value="<?php echo htmlspecialchars($sessions[1]['end_time']); ?>" required><br><br>

    <input type="submit" value="Update Timings">
</form>

<a href="session_timings.php">Back to Session Timings</a>

</body>
</html>
