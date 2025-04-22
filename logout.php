<?php
// Destroy the session to log the user out
session_start();
session_destroy();

// Redirect to login page
header("Location: index.html");
exit();
?>
