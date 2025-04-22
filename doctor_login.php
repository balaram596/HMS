<?php
// Start the session
session_start();

// Include database connection
include('db.php');

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Sanitize and validate inputs
    $username = htmlspecialchars($_POST['username']);
    $password = $_POST['password'];

    // Prepare the SQL query to check if the doctor exists in the database
    $sql = "SELECT * FROM doctors WHERE doc_username = ?";

    if ($stmt = $conn->prepare($sql)) {
        // Bind the parameters
        $stmt->bind_param("s", $username); // "s" indicates that the parameter is a string

        // Execute the query
        $stmt->execute();
        $result = $stmt->get_result();

        // Check if a record is found
        if ($result->num_rows == 1) {
            // Fetch the doctor's data
            $doctor = $result->fetch_assoc();

            // Verify the password
            if (password_verify($password, $doctor['password'])) {
                // Password is correct, set session variables
                $_SESSION['doctor_id'] = $doctor['doc_id'];
                $_SESSION['doctor_name'] = $doctor['doc_name'];
                $_SESSION['doctor_username'] = $doctor['doc_username'];
                $_SESSION['doctor_specl'] = $doctor['doc_specl'];

                // Redirect to doctor dashboard
                header("Location: doctor_dashboard.php");
                exit(); // Make sure no code is executed after this
            } else {
                // If password doesn't match
                $error_message = "Incorrect password.";
            }
        } else {
            // If username doesn't exist in the database
            $error_message = "Username not found.";
        }

        // Close the prepared statement
        $stmt->close();
    } else {
        // If the query preparation fails
        $error_message = "Database query failed.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Doctor Login</title>
    <style>
        /* Styling for the form and page */
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f8f9;
            color: #333;
            padding: 50px;
        }
        .form-container {
            max-width: 500px;
            margin: 0 auto;
            background-color: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        h2 {
            text-align: center;
            color: #007bff;
        }
        label {
            font-weight: bold;
            display: block;
            margin-bottom: 6px;
        }
        input {
            width: 100%;
            padding: 12px;
            margin-bottom: 15px;
            border-radius: 4px;
            border: 1px solid #ccc;
            box-sizing: border-box;
        }
        button {
            width: 100%;
            padding: 12px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        button:hover {
            background-color: #0056b3;
        }
        .error-message {
            color: red;
            font-size: 14px;
            text-align: center;
        }
    </style>
</head>
<body>

    <h2>Doctor Login</h2>

    <div class="form-container">
        <form action="doctor_login.php" method="POST">
            <!-- Display error message if login fails -->
            <?php if (isset($error_message)) { ?>
                <p class="error-message"><?php echo $error_message; ?></p>
            <?php } ?>

            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required>

            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>

            <button type="submit">Login</button>
        </form>
    </div>

</body>
</html>
