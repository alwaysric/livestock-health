<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../config/db.php';
require_once '../config/mail_config.php';

// Enable debug mode
define('DEBUG_MODE', true);

// Function to redirect with error and debug info
function redirectWithError($error_code, $debug_info = '') {
    $_SESSION['debug_error'] = [
        'code' => $error_code,
        'info' => $debug_info,
        'time' => date('Y-m-d H:i:s')
    ];
    
    // Build URL with error
    $url = "forgot_password.php?error=" . $error_code;
    if (!empty($debug_info) && DEBUG_MODE) {
        $url .= "&debug=" . urlencode(substr($debug_info, 0, 500));
    }
    
    header('Location: ' . $url);
    exit;
}

// Check if form was submitted
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirectWithError('invalid', 'Form not submitted via POST');
}

$email = $_POST['email'] ?? '';

// Validate email
if (empty($email)) {
    redirectWithError('invalid', 'Email is empty');
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    redirectWithError('invalid', 'Invalid email format: ' . $email);
}

// Check database connection
if (!$conn || $conn->connect_error) {
    redirectWithError('db_error', 'Database connection failed: ' . ($conn->connect_error ?? 'Unknown'));
}

// Check if email exists
try {
    $stmt = $conn->prepare("SELECT user_id, name FROM users WHERE email = ?");
    if (!$stmt) {
        redirectWithError('db_error', 'Prepare failed: ' . $conn->error);
    }
    
    $stmt->bind_param("s", $email);
    if (!$stmt->execute()) {
        redirectWithError('db_error', 'Execute failed: ' . $stmt->error);
    }
    
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        redirectWithError('email_not_found', "Email not found in database: $email");
    }
    
    $user = $result->fetch_assoc();
    $stmt->close();
    
} catch (Exception $e) {
    redirectWithError('db_error', 'Exception: ' . $e->getMessage());
}

// Generate token
$token = bin2hex(random_bytes(32));
$expires = date('Y-m-d H:i:s', strtotime('+1 hour'));

// Create password_resets table if not exists
try {
    $conn->query("CREATE TABLE IF NOT EXISTS password_resets (
        id INT AUTO_INCREMENT PRIMARY KEY,
        email VARCHAR(100) NOT NULL,
        token VARCHAR(100) NOT NULL,
        expires DATETIME NOT NULL,
        used TINYINT DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        INDEX idx_email (email),
        INDEX idx_token (token)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    
} catch (Exception $e) {
    redirectWithError('db_error', 'Failed to create table: ' . $e->getMessage());
}

// Delete old tokens
try {
    $delete = $conn->prepare("DELETE FROM password_resets WHERE email = ?");
    if ($delete) {
        $delete->bind_param("s", $email);
        $delete->execute();
        $delete->close();
    }
} catch (Exception $e) {
    // Non-critical error, continue
}

// Insert new token
try {
    $insert = $conn->prepare("INSERT INTO password_resets (email, token, expires) VALUES (?, ?, ?)");
    if (!$insert) {
        redirectWithError('db_error', 'Insert prepare failed: ' . $conn->error);
    }
    
    $insert->bind_param("sss", $email, $token, $expires);
    if (!$insert->execute()) {
        redirectWithError('db_error', 'Insert execute failed: ' . $insert->error);
    }
    $insert->close();
    
} catch (Exception $e) {
    redirectWithError('db_error', 'Insert exception: ' . $e->getMessage());
}

// SEND EMAIL WITH FULL DEBUG
$debug_info = [];
$debug_info[] = "Starting email send to: $email";
$debug_info[] = "Token: $token";
$debug_info[] = "Expires: $expires";

// Try to send email
try {
    $result = sendPasswordResetEmail($email, $user['name'], $token, true);
    
    if ($result['success']) {
        // Success - log and redirect
        if (function_exists('logAction')) {
            logAction($conn, $user['user_id'], 'PASSWORD_RESET_REQUEST', "Email sent to: $email");
        }
        
        // Store debug info in session
        $_SESSION['mail_success'] = [
            'email' => $email,
            'time' => date('Y-m-d H:i:s'),
            'debug' => $result['debug'] ?? 'No debug output'
        ];
        
        // In debug mode, show the reset link directly
        if (DEBUG_MODE) {
            $_SESSION['debug_link'] = "http://localhost/lhtm_system/auth/reset_password.php?token=$token&email=" . urlencode($email);
        }
        
        header('Location: forgot_password.php?success=1');
        exit;
        
    } else {
        // Email failed - capture all debug info
        $debug_info[] = "EMAIL FAILED: " . $result['message'];
        $debug_info[] = "DEBUG OUTPUT: " . ($result['debug'] ?? 'No debug output');
        
        // Try to get PHPMailer errors from the mail_config.php function
        redirectWithError('mail_failed', implode("\n", $debug_info));
    }
    
} catch (Exception $e) {
    $debug_info[] = "EXCEPTION: " . $e->getMessage();
    $debug_info[] = "Trace: " . $e->getTraceAsString();
    redirectWithError('mail_failed', implode("\n", $debug_info));
}
?>