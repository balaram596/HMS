<?php
session_start();
if ($_SESSION['role'] != 'admin') {
    header("Location: admin_login.html");
    exit();
}

include('db.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_patient'])) {
    if (empty($_POST['username'])) {
        echo "<div class='alert error'>Username cannot be empty.</div>";
    } else {
        $username = $_POST['username'];
        $default_password = password_hash("admin", PASSWORD_DEFAULT); // Secure password hash
        $role = 'patient';

        // Insert into patients table
        $stmt = $conn->prepare("INSERT INTO patients (pat_name, pat_age, pat_gender, pat_phone, pat_username) 
                                VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sisss", $_POST['patient_name'], $_POST['age'], $_POST['gender'], $_POST['contact_number'], $username);

        if ($stmt->execute()) {
            // Now insert into users table
            $userStmt = $conn->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
            $userStmt->bind_param("sss", $username, $default_password, $role);

            if ($userStmt->execute()) {
                $_SESSION['message'] = 'Patient and login user created successfully with default password "admin".';
            } else {
                $_SESSION['message'] = 'Patient added, but failed to create user: ' . $userStmt->error;
            }
            $userStmt->close();
        } else {
            $_SESSION['message'] = 'Error adding patient: ' . $stmt->error;
        }

        $stmt->close();
        header("Location: manage_patients.php");
        exit();
    }
}


if (isset($_GET['delete'])) {
    $patient_id = intval($_GET['delete']);
    $stmt = $conn->prepare("DELETE FROM patients WHERE pat_id = ?");
    $stmt->bind_param("i", $patient_id);

    if ($stmt->execute()) {
        $_SESSION['message'] = 'Patient deleted successfully.';
    } else {
        $_SESSION['message'] = 'Error deleting patient: ' . $stmt->error;
    }
    $stmt->close();
    header("Location: manage_patients.php");
    exit();
}

$result = $conn->query("SELECT * FROM patients");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Patients</title>
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #eef2f5;
            margin: 0;
            padding: 40px;
            color: #333;
        }

        h2, h3 {
            color: #2c3e50;
        }

        .container {
            max-width: 960px;
            margin: auto;
            background-color: #ffffff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }

        form {
            margin-bottom: 30px;
        }

        label {
            display: block;
            margin-top: 15px;
            font-weight: 600;
        }

        input[type="text"],
        input[type="number"],
        select {
            width: 100%;
            padding: 10px;
            margin-top: 6px;
            border: 1px solid #ccc;
            border-radius: 6px;
            transition: border-color 0.2s;
        }

        input[type="text"]:focus,
        input[type="number"]:focus,
        select:focus {
            border-color: #007bff;
            outline: none;
        }

        input[type="submit"] {
            margin-top: 20px;
            background-color: #007bff;
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        input[type="submit"]:hover {
            background-color: #0056b3;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        th, td {
            padding: 12px;
            border-bottom: 1px solid #ddd;
            text-align: center;
        }

        th {
            background-color: #007bff;
            color: white;
        }

        tr:hover {
            background-color: #f1f1f1;
        }

        .delete-link {
            color: #e74c3c;
            text-decoration: none;
            font-weight: 500;
        }

        .delete-link:hover {
            text-decoration: underline;
        }

        .back-link {
            display: inline-block;
            margin-top: 30px;
            color: #007bff;
            text-decoration: none;
            font-size: 16px;
        }

        .back-link:hover {
            text-decoration: underline;
        }

        .alert {
            padding: 12px 15px;
            margin-bottom: 20px;
            border-radius: 6px;
        }

        .alert.error {
            background-color: #f8d7da;
            color: #721c24;
        }

        .alert.success {
            background-color: #d4edda;
            color: #155724;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Manage Patients</h2>

    <?php
    if (isset($_SESSION['message'])) {
        $msg = $_SESSION['message'];
        $type = strpos($msg, 'successfully') !== false ? 'success' : 'error';
        echo "<div class='alert $type'>{$msg}</div>";
        unset($_SESSION['message']);
    }
    ?>

    <form method="POST" action="manage_patients.php">
        <label for="patient_name">Patient Name</label>
        <input type="text" name="patient_name" required>

        <label for="age">Age</label>
        <input type="number" name="age" required>

        <label for="gender">Gender</label>
        <select name="gender" required>
            <option value="">Select</option>
            <option value="Male">Male</option>
            <option value="Female">Female</option>
            <option value="Other">Other</option>
        </select>

        <label for="contact_number">Contact Number</label>
        <input type="text" name="contact_number" required>

        <label for="username">Username</label>
        <input type="text" name="username" required>

        <input type="submit" name="add_patient" value="Add Patient">
    </form>

    <h3>Existing Patients</h3>
    <table>
        <thead>
        <tr>
            <th>Username</th>
            <th>Name</th>
            <th>Age</th>
            <th>Gender</th>
            <th>Phone</th>
            <th>Action</th>
        </tr>
        </thead>
        <tbody>
        <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($row['pat_username']) ?></td>
                <td><?= htmlspecialchars($row['pat_name']) ?></td>
                <td><?= htmlspecialchars($row['pat_age']) ?></td>
                <td><?= htmlspecialchars($row['pat_gender']) ?></td>
                <td><?= htmlspecialchars($row['pat_phone']) ?></td>
                <td>
                    <a class="delete-link" href="?delete=<?= $row['pat_id'] ?>" onclick="return confirm('Are you sure you want to delete this patient?');">Delete</a>
                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>

    <a class="back-link" href="admin_dashboard.php">← Back to Dashboard</a>
</div>

</body>
</html>
```````````