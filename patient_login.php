<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "user_db";

// Create a connection
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT password, role FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($hashed_password, $role);
        $stmt->fetch();
        if (password_verify($password, $hashed_password) && $role == 'patient') {
            session_start();
			$_SESSION['pat_id'] = $user['pat_id'];

            $_SESSION['username'] = $username;
            $_SESSION['role'] = $role;
            header("Location: patient_dashboard.php");
        } else {
            echo "Invalid credentials or not a patient!";
        }
    } else {
        echo "User not found!";
    }

    $stmt->close();
}
$conn->close();
?>
