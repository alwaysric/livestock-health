<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../config/db.php';
require_once '../config/mail_config.php';

// Force debug mode
define('DEBUG_MODE', true);

echo "<!DOCTYPE html>
<html>
<head>
    <title>Email Debug Test</title>
    <style>
        body { background: #1e1e2f; color: #fff; font-family: monospace; padding: 20px; }
        .success { color: #00ff00; }
        .error { color: #ff4444; }
        .warning { color: #ffaa00; }
        .info { color: #00ccff; }
        .box { background: #2d3349; padding: 15px; margin: 10px 0; border-radius: 5px; }
        pre { background: #000; padding: 10px; color: #00ff00; overflow: auto; }
    </style>
</head>
<body>
    <h1>🔍 Email Debug Test</h1>";

// Test 1: Check if config files exist
echo "<div class='box'>";
echo "<h2>Test 1: File Checks</h2>";

$files_to_check = [
    '../config/db.php' => 'Database Config',
    '../config/mail_config.php' => 'Mail Config',
    '../vendor/phpmailer/src/PHPMailer.php' => 'PHPMailer',
    '../vendor/phpmailer/src/SMTP.php' => 'SMTP Class',
    '../vendor/phpmailer/src/Exception.php' => 'Exception Class'
];

foreach ($files_to_check as $file => $name) {
    if (file_exists($file)) {
        echo "<span class='success'>✅ $name: Found</span><br>";
    } else {
        echo "<span class='error'>❌ $name: NOT FOUND at $file</span><br>";
    }
}
echo "</div>";

// Test 2: Check database connection
echo "<div class='box'>";
echo "<h2>Test 2: Database Connection</h2>";
if (isset($conn) && $conn) {
    echo "<span class='success'>✅ Database connected</span><br>";
    
    // Check if users table exists
    $result = $conn->query("SHOW TABLES LIKE 'users'");
    if ($result->num_rows > 0) {
        echo "<span class='success'>✅ Users table exists</span><br>";
    } else {
        echo "<span class='error'>❌ Users table NOT found</span><br>";
    }
} else {
    echo "<span class='error'>❌ Database connection failed</span><br>";
}
echo "</div>";

// Test 3: Check PHP extensions
echo "<div class='box'>";
echo "<h2>Test 3: PHP Extensions</h2>";
$extensions = ['openssl', 'mysqli', 'json', 'session'];
foreach ($extensions as $ext) {
    if (extension_loaded($ext)) {
        echo "<span class='success'>✅ $ext: Loaded</span><br>";
    } else {
        echo "<span class='error'>❌ $ext: NOT loaded</span><br>";
    }
}
echo "</div>";

// Test 4: Test email with detailed debug
echo "<div class='box'>";
echo "<h2>Test 4: Send Test Email</h2>";

if (isset($_POST['test_email'])) {
    $test_email = $_POST['test_email'];
    echo "<h3>Sending to: $test_email</h3>";
    
    // Create a test token
    $test_token = bin2hex(random_bytes(16));
    
    // Send email with full debug
    $result = sendPasswordResetEmail($test_email, 'Test User', $test_token, true);
    
    if ($result['success']) {
        echo "<span class='success'>✅ EMAIL SENT SUCCESSFULLY!</span><br>";
    } else {
        echo "<span class='error'>❌ EMAIL FAILED: " . $result['message'] . "</span><br>";
    }
    
    if (!empty($result['debug'])) {
        echo "<h4>Debug Output:</h4>";
        echo "<pre>" . htmlspecialchars($result['debug']) . "</pre>";
    }
    
    // Show the reset link that would have been sent
    $reset_link = "http://localhost/lhtm_system/auth/reset_password.php?token=$test_token&email=" . urlencode($test_email);
    echo "<h4>Reset Link (for testing):</h4>";
    echo "<pre><a href='$reset_link' target='_blank'>$reset_link</a></pre>";
    
} else {
    echo "<form method='POST'>";
    echo "<label>Enter your email:</label><br>";
    echo "<input type='email' name='test_email' required style='width:300px; padding:5px;'> ";
    echo "<button type='submit'>Send Test Email</button>";
    echo "</form>";
}
echo "</div>";

// Test 5: Check mail_config.php settings
echo "<div class='box'>";
echo "<h2>Test 5: Mail Configuration</h2>";

// Try to read the mail_config.php file
$config_content = file_get_contents('../config/mail_config.php');
if ($config_content) {
    // Check for placeholder values
    if (strpos($config_content, 'your-email@gmail.com') !== false) {
        echo "<span class='warning'>⚠️ WARNING: Using placeholder email (your-email@gmail.com)</span><br>";
    } else {
        echo "<span class='success'>✅ Custom email configured</span><br>";
    }
    
    if (strpos($config_content, 'your-app-password') !== false) {
        echo "<span class='warning'>⚠️ WARNING: Using placeholder password (your-app-password)</span><br>";
    } else {
        echo "<span class='success'>✅ Custom password configured</span><br>";
    }
}
echo "</div>";

// Test 6: Check if we can write to session
echo "<div class='box'>";
echo "<h2>Test 6: Session</h2>";
$_SESSION['debug_test'] = 'working';
if (isset($_SESSION['debug_test'])) {
    echo "<span class='success'>✅ Session is working</span><br>";
} else {
    echo "<span class='error'>❌ Session NOT working</span><br>";
}
echo "</div>";

// Test 7: Manual SMTP Test
echo "<div class='box'>";
echo "<h2>Test 7: Manual SMTP Connection Test</h2>";

if (class_exists('PHPMailer\PHPMailer\PHPMailer')) {
    $mail = new PHPMailer\PHPMailer\PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'your-email@gmail.com'; // REPLACE WITH YOUR EMAIL
        $mail->Password = 'your-app-password';    // REPLACE WITH YOUR APP PASSWORD
        $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;
        $mail->Timeout = 30;
        
        ob_start();
        if ($mail->smtpConnect()) {
            $debug_out = ob_get_clean();
            echo "<span class='success'>✅ SMTP Connection Successful!</span><br>";
        } else {
            $debug_out = ob_get_clean();
            echo "<span class='error'>❌ SMTP Connection Failed</span><br>";
        }
        echo "<pre>$debug_out</pre>";
        
    } catch (Exception $e) {
        $debug_out = ob_get_clean();
        echo "<span class='error'>❌ Exception: " . $e->getMessage() . "</span><br>";
        echo "<pre>$debug_out</pre>";
    }
} else {
    echo "<span class='error'>❌ PHPMailer class not found</span><br>";
}
echo "</div>";

echo "<hr>";
echo "<p><a href='forgot_password.php'>← Back to Forgot Password</a> | ";
echo "<a href='index.php'>Go to Login</a></p>";

echo "</body></html>";
?>