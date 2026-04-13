<?php
// Start session (only if not already started)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// MESSAGE VARIABLE (added)
if (!isset($_SESSION['message'])) {
    $_SESSION['message'] = "";
}

// Database configuration
$host = "localhost";
$user = "root";
$password = "";
$database = "eduhub";

// Create connection
$conn = new mysqli($host, $user, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Database Connection Failed: " . $conn->connect_error);
}

// Optional: Set charset (important for security & encoding)
$conn->set_charset("utf8mb4");
?>