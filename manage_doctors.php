<?php
session_start();
if ($_SESSION['role'] != 'admin') {
    header("Location: admin_login.html");
    exit();
}
include('db.php');

// Add a new doctor
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_doctor'])) {
    $doc_name = htmlspecialchars($_POST['doctor_name']);
    $doc_specl = htmlspecialchars($_POST['doctor_specl']);
    $doc_username = htmlspecialchars($_POST['doctor_username']);
    $doctor_password = $_POST['password'];
    $hashed_password = password_hash($doctor_password, PASSWORD_DEFAULT);

    $conn->begin_transaction();
    try {
        $sql = "INSERT INTO doctors (doc_name, doc_specl, doc_username, password) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssss", $doc_name, $doc_specl, $doc_username, $hashed_password);
        $stmt->execute();

        $new_doc_id = $conn->insert_id;

        $insertSessionSql = "INSERT INTO doctor_sessions (doc_id, day_of_week, session_number, start_time, end_time) VALUES (?, ?, ?, ?, ?)";
        $insertSessionStmt = $conn->prepare($insertSessionSql);

        $daysOfWeek = ['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'];
        foreach ($daysOfWeek as $day) {
            $insertSessionStmt->bind_param("isiss", $new_doc_id, $day, $session_number, $start_time, $end_time);

            $session_number = 1;
            $start_time = "09:00:00";
            $end_time = "12:00:00";
            $insertSessionStmt->execute();

            $session_number = 2;
            $start_time = "14:00:00";
            $end_time = "15:00:00";
            $insertSessionStmt->execute();
        }

        $conn->commit();
        $success_message = "Doctor added successfully with default timings.";
    } catch (Exception $e) {
        $conn->rollback();
        $error_message = "Error: " . $e->getMessage();
    }
}

// Delete a doctor
if (isset($_GET['delete'])) {
    $doc_id = (int) $_GET['delete'];
    try {
        $delete_sessions = $conn->prepare("DELETE FROM doctor_sessions WHERE doc_id = ?");
        $delete_sessions->bind_param("i", $doc_id);
        $delete_sessions->execute();
        $delete_sessions->close();

        $delete_doctor = $conn->prepare("DELETE FROM doctors WHERE doc_id = ?");
        $delete_doctor->bind_param("i", $doc_id);
        if ($delete_doctor->execute()) {
            header("Location: manage_doctors.php");
            exit();
        } else {
            $error_message = "Error deleting doctor: " . $delete_doctor->error;
        }
        $delete_doctor->close();
    } catch (Exception $e) {
        $error_message = "Error: " . $e->getMessage();
    }
}

// Fetch all doctors
$sql = "SELECT * FROM doctors";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Doctors</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 80%;
            margin: 20px auto;
            background-color: white;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h2 {
            text-align: center;
            color: #333;
        }
        form {
            margin-bottom: 20px;
        }
        input[type="text"], input[type="password"], input[type="submit"] {
            padding: 10px;
            margin: 5px 0;
            width: 100%;
            box-sizing: border-box;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        input[type="submit"] {
            background-color: #28a745;
            color: white;
            border: none;
            cursor: pointer;
        }
        input[type="submit"]:hover {
            background-color: #218838;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table th, table td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        table th {
            background-color: #f8f9fa;
        }
        .action-link {
            color: red;
            text-decoration: none;
        }
        .action-link:hover {
            text-decoration: underline;
        }
        .alert {
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 5px;
        }
        .alert-success {
            background-color: #d4edda;
            color: #155724;
        }
        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
        }
        a {
            display: inline-block;
            margin-top: 20px;
            background-color: #007bff;
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
        }
        a:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
<div class="container">
    <h2>Manage Doctors</h2>
    <?php if (isset($success_message)): ?>
        <div class="alert alert-success">
            <?php echo $success_message; ?>
        </div>
    <?php endif; ?>

    <?php if (isset($error_message)): ?>
        <div class="alert alert-danger">
            <?php echo $error_message; ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="manage_doctors.php">
        <label for="doctor_name">Doctor Name:</label>
        <input type="text" name="doctor_name" id="doctor_name" required>

        <label for="doctor_specl">Specialization:</label>
        <input type="text" name="doctor_specl" id="doctor_specl" required>

        <label for="doctor_username">Username (e.g. email):</label>
        <input type="text" name="doctor_username" id="doctor_username" required>

        <label for="password">Password:</label>
        <input type="password" name="password" id="password" required>

        <input type="submit" name="add_doctor" value="Add Doctor">
    </form>

    <h3>Existing Doctors</h3>
    <table>
        <thead>
            <tr>
                <th>Doctor Name</th>
                <th>Specialization</th>
                <th>Username</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['doc_name']); ?></td>
                    <td><?php echo htmlspecialchars($row['doc_specl']); ?></td>
                    <td><?php echo htmlspecialchars($row['doc_username']); ?></td>
                    <td>
                        <a href="manage_doctors.php?delete=<?php echo $row['doc_id']; ?>" class="action-link" onclick="return confirm('Are you sure you want to delete this doctor?');">Delete</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <a href="admin_dashboard.php">Back to Dashboard</a>
</div>
</body>
</html>
