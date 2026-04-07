<?php
session_start();
require_once '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: forgot_password.php');
    exit;
}

$email = $_POST['email'] ?? '';
$token = $_POST['token'] ?? '';
$password = $_POST['password'] ?? '';
$confirm = $_POST['confirm_password'] ?? '';

// Basic validation
if (empty($email) || empty($token) || empty($password)) {
    header('Location: reset_password.php?error=invalid&email=' . urlencode($email) . '&token=' . $token);
    exit;
}

// Check password length
if (strlen($password) < 6) {
    header('Location: reset_password.php?error=weak_password&email=' . urlencode($email) . '&token=' . $token);
    exit;
}

// Check if passwords match
if ($password !== $confirm) {
    header('Location: reset_password.php?error=password_mismatch&email=' . urlencode($email) . '&token=' . $token);
    exit;
}

// Verify token is valid
$stmt = $conn->prepare("SELECT * FROM password_resets WHERE email = ? AND token = ? AND used = 0 AND expires > NOW()");
$stmt->bind_param("ss", $email, $token);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header('Location: forgot_password.php?error=invalid_token');
    exit;
}

$reset_data = $result->fetch_assoc();

// Get user ID
$user_stmt = $conn->prepare("SELECT user_id FROM users WHERE email = ?");
$user_stmt->bind_param("s", $email);
$user_stmt->execute();
$user_result = $user_stmt->get_result();
$user = $user_result->fetch_assoc();

if (!$user) {
    header('Location: forgot_password.php?error=email_not_found');
    exit;
}

// Hash the new password
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// Update password in database
$update = $conn->prepare("UPDATE users SET password = ? WHERE email = ?");
$update->bind_param("ss", $hashed_password, $email);

if ($update->execute()) {
    // Mark token as used
    $mark_used = $conn->prepare("UPDATE password_resets SET used = 1 WHERE id = ?");
    $mark_used->bind_param("i", $reset_data['id']);
    $mark_used->execute();
    $mark_used->close();
    
    // Log the action
    if (function_exists('logAction')) {
        logAction($conn, $user['user_id'], 'PASSWORD_RESET', "Password reset successful for email: $email");
    }
    
    // Redirect to login with success message
    header('Location: index.php?reset=success');
} else {
    header('Location: reset_password.php?error=update_failed&email=' . urlencode($email) . '&token=' . $token);
}
exit;
?>