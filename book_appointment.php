<?php
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'patient') {
    header("Location: patient_login.html");
    exit();
}

$host = "localhost";
$dbname = "user_db";
$db_user = "root";
$db_pass = "";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $current_username = $_SESSION['username'];
$stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
$stmt->execute([$current_username]);
$pat_id = $stmt->fetchColumn();



    $stmt = $pdo->query("SELECT doc_id, doc_name, doc_specl FROM doctors");
    $doctors = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (isset($_GET['doctor_id'])) {
        $doctor_id = $_GET['doctor_id'];
        $stmt = $pdo->prepare("SELECT * FROM doctor_sessions WHERE doc_id = ? ORDER BY day_of_week, session_number");
        $stmt->execute([$doctor_id]);
        $sessions = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $doctor_id = $_POST['doctor_id'];
        $day_of_week = $_POST['day_of_week'];
        $start_time = $_POST['start_time'];
        $selected_date = $_POST['selected_date'];

        $date = new DateTime($selected_date);
        $actual_day = $date->format('l');

        if ($actual_day !== $day_of_week) {
            echo "<p style='color:red;'>Error: The selected date does not match the chosen session day ($day_of_week).</p>";
        } else {
            $appointment_datetime = $selected_date . ' ' . $start_time;

            $stmt = $pdo->prepare("INSERT INTO appointments (pat_id, doc_id, appointment_date, status) VALUES (?, ?, ?, 'pending')");
            $stmt->execute([$pat_id, $doctor_id, $appointment_datetime]);

            echo "<p style='color:green;'>Appointment booked successfully for $appointment_datetime</p>";
        }
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Book Appointment</title>
    <script>
    function getDayName(dateString) {
        const date = new Date(dateString);
        return date.toLocaleDateString('en-US', { weekday: 'long' });
    }

    function filterSessions() {
        const selectedDate = document.getElementById("selected_date").value;
        const selectedDay = getDayName(selectedDate);

        document.querySelectorAll(".session-row").forEach(row => {
            const sessionDay = row.dataset.day;
            if (sessionDay === selectedDay) {
                row.style.display = "";
                row.querySelector("input[type='radio']").disabled = false;
                row.querySelector("input[type='hidden']").disabled = false;
            } else {
                row.style.display = "none";
                row.querySelector("input[type='radio']").disabled = true;
                row.querySelector("input[type='hidden']").disabled = true;
            }
        });

        document.getElementById("session_note").innerText = 
            selectedDate ? "Showing sessions for: " + selectedDay : "Please select a date.";
    }
    </script>
</head>
<body>
    <h2>Book an Appointment</h2>

    <form method="get">
        <label for="doctor">Choose Doctor:</label>
        <select name="doctor_id" required>
            <option value="">-- Select Doctor --</option>
            <?php foreach ($doctors as $doc): ?>
                <option value="<?= $doc['doc_id'] ?>" <?= (isset($doctor_id) && $doctor_id == $doc['doc_id']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($doc['doc_name']) ?> (<?= $doc['doc_specl'] ?>)
                </option>
            <?php endforeach; ?>
        </select>
        <button type="submit">View Sessions</button>
    </form>

    <?php if (isset($sessions) && count($sessions) > 0): ?>
        <h3>Available Sessions</h3>
        <form method="post">
            <input type="hidden" name="doctor_id" value="<?= $doctor_id ?>">

            <label for="selected_date"><strong>Select Appointment Date:</strong></label>
<input type="date" id="selected_date" name="selected_date" onchange="filterSessions()" required>
<script>
    const today = new Date();
    const yyyy = today.getFullYear();
    const mm = String(today.getMonth() + 1).padStart(2, '0');
    const dd = String(today.getDate()).padStart(2, '0');
    const minDate = `${yyyy}-${mm}-${dd}`;
    document.getElementById('selected_date').setAttribute('min', minDate);
</script>
<p id="session_note" style="color: blue;">Please select a date to view matching sessions.</p>

            <table border="1" cellpadding="8">
                <tr>
                    <th>Select</th>
                    <th>Day</th>
                    <th>Start Time</th>
                    <th>End Time</th>
                </tr>
                <?php foreach ($sessions as $sess): ?>
                    <tr class="session-row" data-day="<?= $sess['day_of_week'] ?>" style="display: none;">
                        <td>
                            <input type="radio" name="start_time" value="<?= $sess['start_time'] ?>" disabled required>
                            <input type="hidden" name="day_of_week" value="<?= $sess['day_of_week'] ?>" disabled>
                        </td>
                        <td><?= $sess['day_of_week'] ?></td>
                        <td><?= $sess['start_time'] ?></td>
                        <td><?= $sess['end_time'] ?></td>
                    </tr>
                <?php endforeach; ?>
            </table>

            <br>
            <button type="submit">Book Appointment</button>
        </form>
    <?php endif; ?>

    <br><a href="patient_dashboard.php">Back to Dashboard</a>
</body>
</html>
