<?php
session_start();
require_once '../config/db.php';

// Check if form was submitted
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: register.php');
    exit;
}

// Get and sanitize inputs
$name = trim($_POST['name'] ?? '');
$role = $_POST['role'] ?? 'worker'; // Default to worker if not set
$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

// Validate inputs
if (empty($name) || empty($email) || empty($password)) {
    header('Location: register.php?error=invalid');
    exit;
}

// Validate email format
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header('Location: register.php?error=invalid');
    exit;
}

// Force role to be worker (security measure)
$role = 'worker';

// Check if email already exists
$check_email = $conn->prepare("SELECT user_id FROM users WHERE email = ?");
$check_email->bind_param("s", $email);
$check_email->execute();
$check_email->store_result();

if ($check_email->num_rows > 0) {
    header('Location: register.php?error=email_exists');
    exit;
}
$check_email->close();

// Hash password
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// Insert user
$stmt = $conn->prepare("INSERT INTO users (name, role, email, password, status) VALUES (?, ?, ?, ?, 'active')");
$stmt->bind_param("ssss", $name, $role, $email, $hashed_password);

if ($stmt->execute()) {
    // Get the new user ID
    $user_id = $stmt->insert_id;
    
    // Log the registration
    if (function_exists('logAction')) {
        logAction($conn, $user_id, 'REGISTER', "New user registered as worker: $name");
    }
    
    // Automatically log the user in
    $_SESSION['user_id'] = $user_id;
    $_SESSION['name'] = $name;
    $_SESSION['role'] = $role;
    
    // Redirect to dashboard
    header('Location: /lhtm_system/dashboard.php');
    exit;
} else {
    // Registration failed
    header('Location: register.php?error=insert_failed');
    exit;
}

$stmt->close();
$conn->close();
?>