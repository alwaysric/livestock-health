<?php
$host = 'localhost';
$user = 'root';
$pass = '';
$dbname = 'livestock_health';

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");

// Set timezone
date_default_timezone_set('Asia/Manila'); // Change to your timezone

// Define base URL for consistent linking (only if not already defined)
if (!defined('BASE_URL')) {
    define('BASE_URL', '/lhtm_system/');
}

// Function to log admin actions
function logAction($conn, $user_id, $action, $description) {
    $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    $stmt = $conn->prepare("INSERT INTO system_logs (user_id, action, description, ip_address) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("isss", $user_id, $action, $description, $ip);
    $stmt->execute();
    $stmt->close();
}
?>