<?php
session_start();
require_once '../config/db.php';
require_once '../config/mail_config.php';

$page_title = 'Mail Debug Test';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mail Debug Test</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body { background: #1a1e2b; color: #fff; font-family: monospace; padding: 20px; }
        .container { max-width: 1200px; margin: 0 auto; }
        .card { background: #2d3349; border: none; border-radius: 10px; padding: 20px; margin-bottom: 20px; }
        .success { color: #00ff00; }
        .error { color: #ff4444; }
        .warning { color: #ffaa00; }
        .info { color: #00ccff; }
        pre { background: #1e1e2f; padding: 15px; border-radius: 5px; color: #00ff00; overflow: auto; }
        .btn { padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; margin: 5px; }
        .btn-primary { background: #667eea; color: white; }
        .btn-success { background: #28a745; color: white; }
        .btn-danger { background: #dc3545; color: white; }
        .debug-box { border-left: 4px solid #ffd700; padding: 10px; margin: 10px 0; background: rgba(255,215,0,0.1); }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="mb-4">📧 PHPMailer Debug Test</h1>
        
        <div class="card">
            <h3>System Information</h3>
            <table class="table table-dark">
                <tr>
                    <td>PHP Version:</td>
                    <td><?= phpversion() ?></td>
                </tr>
                <tr>
                    <td>OpenSSL Extension:</td>
                    <td><?= extension_loaded('openssl') ? '✅ Loaded' : '❌ Not Loaded' ?></td>
                </tr>
                <tr>
                    <td>PHPMailer Files:</td>
                    <td><?= class_exists('PHPMailer\PHPMailer\PHPMailer') ? '✅ Loaded' : '❌ Not Found' ?></td>
                </tr>
                <tr>
                    <td>Database Connection:</td>
                    <td><?= isset($conn) && $conn ? '✅ Connected' : '❌ Not Connected' ?></td>
                </tr>
            </table>
        </div>
        
        <div class="card">
            <h3>Test Email Configuration</h3>
            <form method="POST" class="mb-3">
                <div class="mb-3">
                    <label for="test_email">Test Email Address:</label>
                    <input type="email" class="form-control" id="test_email" name="test_email" 
                           value="your-email@gmail.com" required>
                </div>
                <button type="submit" name="action" value="test" class="btn btn-primary">
                    <i class="bi bi-envelope"></i> Send Test Email
                </button>
                <button type="submit" name="action" value="debug" class="btn btn-warning">
                    <i class="bi bi-bug"></i> Run Full Debug
                </button>
            </form>
            
            <?php
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                echo "<div class='debug-box'>";
                
                if ($_POST['action'] === 'test') {
                    $test_email = $_POST['test_email'];
                    echo "<h4>📨 Sending test email to: $test_email</h4>";
                    
                    // Run test
                    $result = testMailConfiguration($test_email);
                    
                    echo "<pre class='mt-3'>$result</pre>";
                    
                } elseif ($_POST['action'] === 'debug') {
                    echo "<h4>🔍 Running Full Debug</h4>";
                    
                    // Test 1: Check PHPMailer files
                    echo "<h5>Test 1: PHPMailer Files</h5>";
                    $files = [
                        '../vendor/phpmailer/src/Exception.php',
                        '../vendor/phpmailer/src/PHPMailer.php',
                        '../vendor/phpmailer/src/SMTP.php'
                    ];
                    
                    foreach ($files as $file) {
                        if (file_exists($file)) {
                            echo "<span class='success'>✅ Found: $file</span><br>";
                        } else {
                            echo "<span class='error'>❌ Missing: $file</span><br>";
                        }
                    }
                    
                    // Test 2: SMTP Connection
                    echo "<h5 class='mt-3'>Test 2: SMTP Connection</h5>";
                    $test_mail = new PHPMailer\PHPMailer\PHPMailer(true);
                    try {
                        $test_mail->isSMTP();
                        $test_mail->Host = 'smtp.gmail.com';
                        $test_mail->SMTPAuth = true;
                        $test_mail->Username = 'your-email@gmail.com';
                        $test_mail->Password = 'your-app-password';
                        $test_mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
                        $test_mail->Port = 587;
                        $test_mail->SMTPDebug = 2;
                        
                        ob_start();
                        $test_mail->smtpConnect();
                        $debug_output = ob_get_clean();
                        
                        echo "<span class='success'>✅ SMTP Connection Successful</span><br>";
                        echo "<pre class='mt-2'>$debug_output</pre>";
                        
                    } catch (Exception $e) {
                        echo "<span class='error'>❌ SMTP Connection Failed: " . $e->getMessage() . "</span><br>";
                        if (isset($debug_output)) {
                            echo "<pre class='mt-2'>$debug_output</pre>";
                        }
                    }
                }
                
                echo "</div>";
            }
            ?>
        </div>
        
        <div class="card">
            <h3>Debug Instructions</h3>
            <ol class="text-light">
                <li>Make sure you have Gmail App Password configured in <code>config/mail_config.php</code></li>
                <li>Click "Send Test Email" to verify email delivery</li>
                <li>Click "Run Full Debug" for comprehensive diagnostics</li>
                <li>Check the debug panel on the forgot password page for detailed logs</li>
            </ol>
            
            <div class="alert alert-info">
                <i class="bi bi-info-circle"></i>
                <strong>Debug Mode is ENABLED.</strong> You can see detailed email logs on the forgot password page.
                Remember to disable debug mode in production!
            </div>
        </div>
        
        <div class="text-center mt-4">
            <a href="forgot_password.php" class="btn btn-primary">← Back to Forgot Password</a>
            <a href="index.php" class="btn btn-success">Go to Login</a>
        </div>
    </div>
</body>
</html>