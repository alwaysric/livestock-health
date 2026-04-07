<?php
// Mail Configuration for PHPMailer with Enhanced Error Handling

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// Load PHPMailer files with error checking
$phpmailer_files = [
    'Exception' => __DIR__ . '/../vendor/phpmailer/src/Exception.php',
    'PHPMailer' => __DIR__ . '/../vendor/phpmailer/src/PHPMailer.php',
    'SMTP' => __DIR__ . '/../vendor/phpmailer/src/SMTP.php'
];

foreach ($phpmailer_files as $name => $file) {
    if (!file_exists($file)) {
        die("FATAL ERROR: PHPMailer $name file not found at: $file");
    }
    require_once $file;
}

/**
 * Send password reset email with detailed debug
 */
function sendPasswordResetEmail($to, $name, $token, $debug = false) {
    $response = [
        'success' => false,
        'message' => '',
        'debug' => ''
    ];
    
    $debug_output = [];
    
    // CONFIGURATION - REPLACE WITH YOUR ACTUAL CREDENTIALS
    $smtp_host = 'smtp.gmail.com';
    $smtp_username = 'ndunguerik254@gmail.com';  // <-- CHANGE THIS
    $smtp_password = 'bupn rofu ptku nixb';     // <-- CHANGE THIS (16-char app password)
    $smtp_port = 587;
    $smtp_secure = 'tls';
    
    $debug_output[] = "=== PHPMailer Configuration ===";
    $debug_output[] = "To: $to";
    $debug_output[] = "Name: $name";
    $debug_output[] = "Host: $smtp_host";
    $debug_output[] = "Username: $smtp_username";
    $debug_output[] = "Port: $smtp_port";
    $debug_output[] = "Secure: $smtp_secure";
    
    // Check if credentials are still placeholders
    if ($smtp_username === 'your-email@gmail.com') {
        $response['message'] = 'Please configure your email in mail_config.php';
        $response['debug'] = implode("\n", $debug_output);
        return $response;
    }
    
    if ($smtp_password === 'your-app-password') {
        $response['message'] = 'Please configure your app password in mail_config.php';
        $response['debug'] = implode("\n", $debug_output);
        return $response;
    }
    
    $mail = new PHPMailer(true);
    
    try {
        // Server settings with debug
        if ($debug) {
            $mail->SMTPDebug = SMTP::DEBUG_SERVER;
            $mail->Debugoutput = function($str, $level) use (&$debug_output) {
                $debug_output[] = "SMTP [$level]: " . trim($str);
            };
        } else {
            $mail->SMTPDebug = 0;
        }
        
        $mail->isSMTP();
        $mail->Host       = $smtp_host;
        $mail->SMTPAuth   = true;
        $mail->Username   = $smtp_username;
        $mail->Password   = $smtp_password;
        $mail->SMTPSecure = $smtp_secure === 'tls' ? PHPMailer::ENCRYPTION_STARTTLS : PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port       = $smtp_port;
        $mail->Timeout    = 30; // Increase timeout
        
        $debug_output[] = "=== SMTP Configuration Set ===";
        
        // Recipients
        $mail->setFrom($smtp_username, 'Livestock Health System');
        $mail->addAddress($to, $name);
        $mail->addReplyTo($smtp_username, 'Support');
        
        $debug_output[] = "=== Recipients Set ===";
        
        // Content
        $mail->isHTML(true);
        $mail->Subject = 'Password Reset Request - Livestock Health System';
        
        $reset_link = "http://localhost/lhtm_system/auth/reset_password.php?token=$token&email=" . urlencode($to);
        $debug_output[] = "Reset Link: $reset_link";
        
        // HTML Email Body
        $mail->Body = "
        <!DOCTYPE html>
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 20px; text-align: center; }
                .content { padding: 30px; background: #f9f9f9; }
                .button { display: inline-block; padding: 12px 30px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; text-decoration: none; border-radius: 5px; margin: 20px 0; }
                .footer { text-align: center; padding: 20px; color: #666; font-size: 12px; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h2>Password Reset Request</h2>
                </div>
                <div class='content'>
                    <p>Hello <strong>$name</strong>,</p>
                    <p>We received a request to reset your password for your Livestock Health System account.</p>
                    <p>Click the button below to reset your password:</p>
                    <p style='text-align: center;'>
                        <a href='$reset_link' class='button'>Reset Password</a>
                    </p>
                    <p>Or copy and paste this link into your browser:</p>
                    <p style='word-break: break-all;'>$reset_link</p>
                    <p><strong>Note:</strong> This link will expire in 1 hour.</p>
                    <p>If you didn't request this, please ignore this email.</p>
                </div>
                <div class='footer'>
                    <p>&copy; " . date('Y') . " Livestock Health System. All rights reserved.</p>
                    <p>This is an automated message, please do not reply.</p>
                </div>
            </div>
        </body>
        </html>
        ";
        
        $mail->AltBody = "Hello $name,\n\nWe received a request to reset your password.\n\nClick this link to reset: $reset_link\n\nThis link expires in 1 hour.\n\nIf you didn't request this, please ignore this email.";
        
        $debug_output[] = "=== Email Content Prepared ===";
        $debug_output[] = "Attempting to send...";
        
        // Send email
        $mail->send();
        
        $debug_output[] = "=== EMAIL SENT SUCCESSFULLY ===";
        
        $response['success'] = true;
        $response['message'] = 'Email sent successfully';
        $response['debug'] = implode("\n", $debug_output);
        
    } catch (Exception $e) {
        $debug_output[] = "=== EMAIL FAILED ===";
        $debug_output[] = "Exception: " . $e->getMessage();
        $debug_output[] = "Mailer Error: " . $mail->ErrorInfo;
        $debug_output[] = "Trace: " . $e->getTraceAsString();
        
        $response['success'] = false;
        $response['message'] = "Mailer Error: " . $mail->ErrorInfo;
        $response['debug'] = implode("\n", $debug_output);
    }
    
    return $response;
}

/**
 * Test email configuration
 */
function testMailConfiguration($to = null) {
    if (!$to) {
        $to = 'your-email@gmail.com'; // Replace with your email for testing
    }
    
    $test_token = bin2hex(random_bytes(16));
    $result = sendPasswordResetEmail($to, 'Test User', $test_token, true);
    
    $output = [];
    $output[] = "=== TEST RESULTS ===";
    $output[] = "Time: " . date('Y-m-d H:i:s');
    $output[] = "Test Email: $to";
    $output[] = "Success: " . ($result['success'] ? 'YES' : 'NO');
    $output[] = "Message: " . $result['message'];
    $output[] = "";
    $output[] = "=== DEBUG OUTPUT ===";
    $output[] = $result['debug'];
    
    return implode("\n", $output);
}
?>