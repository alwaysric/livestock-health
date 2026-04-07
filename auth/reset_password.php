<?php
session_start();
if (isset($_SESSION['user_id'])) {
    // If already logged in, redirect to appropriate dashboard
    if ($_SESSION['role'] === 'admin') {
        header('Location: /lhtm_system/admin_dashboard.php');
    } else {
        header('Location: /lhtm_system/dashboard.php');
    }
    exit;
}

require_once '../config/db.php';

$page_title = 'Reset Password';
$token = $_GET['token'] ?? '';
$email = $_GET['email'] ?? '';

// Verify token exists and is valid
if (!empty($token) && !empty($email)) {
    $stmt = $conn->prepare("SELECT * FROM password_resets WHERE email = ? AND token = ? AND used = 0 AND expires > NOW()");
    $stmt->bind_param("ss", $email, $token);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        // Token invalid or expired
        header('Location: forgot_password.php?error=invalid_token');
        exit;
    }
    $reset_data = $result->fetch_assoc();
} else {
    header('Location: forgot_password.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - Livestock Health System</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- AOS Animation -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    
    <style>
        /* Same styles as forgot_password.php */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }
        
        body {
            min-height: 100vh;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            flex-direction: column;
        }
        
        .navbar-custom {
            background: linear-gradient(135deg, #2c3e50 0%, #3498db 100%);
            padding: 1rem 0;
            box-shadow: 0 4px 20px rgba(0,0,0,0.2);
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
            animation: slideDown 0.5s ease-out;
        }
        
        @keyframes slideDown {
            from {
                transform: translateY(-100%);
            }
            to {
                transform: translateY(0);
            }
        }
        
        .navbar-custom .navbar-brand {
            color: white;
            font-weight: 700;
            font-size: 1.5rem;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.2);
        }
        
        .navbar-custom .navbar-brand i {
            color: #ffd700;
            margin-right: 8px;
        }
        
        .navbar-custom .nav-link {
            color: rgba(255,255,255,0.9) !important;
            font-weight: 500;
            padding: 0.5rem 1rem !important;
            border-radius: 25px;
            transition: all 0.3s;
        }
        
        .navbar-custom .nav-link:hover {
            background: rgba(255,255,255,0.2);
            color: white !important;
            transform: translateY(-2px);
        }
        
        .navbar-custom .btn-outline-light {
            border-radius: 25px;
            padding: 0.5rem 1.5rem;
            font-weight: 600;
            transition: all 0.3s;
        }
        
        .navbar-custom .btn-outline-light:hover {
            background: white;
            color: #2c3e50 !important;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
        
        .main-content {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: calc(100vh - 200px);
            margin-top: 80px;
            margin-bottom: 40px;
            padding: 20px;
            background: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), 
                        url('https://images.unsplash.com/photo-1527153818091-1a9638521e2a?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            position: relative;
        }
        
        .main-content::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(45deg, 
                rgba(102, 126, 234, 0.3) 0%, 
                rgba(28, 200, 138, 0.3) 50%, 
                rgba(246, 194, 62, 0.3) 100%);
            animation: gradientShift 15s ease infinite;
            pointer-events: none;
        }
        
        @keyframes gradientShift {
            0% { opacity: 0.3; }
            50% { opacity: 0.6; }
            100% { opacity: 0.3; }
        }
        
        .reset-container {
            width: 100%;
            max-width: 500px;
            padding: 20px;
            position: relative;
            z-index: 10;
        }
        
        .reset-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border: none;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            overflow: hidden;
            animation: slideUp 0.6s ease-out;
        }
        
        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .reset-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 30px;
            text-align: center;
            color: white;
            position: relative;
            overflow: hidden;
        }
        
        .reset-header::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 200%;
            height: 200%;
            background: rgba(255, 255, 255, 0.1);
            transform: rotate(45deg);
            animation: shine 3s infinite;
        }
        
        @keyframes shine {
            0% { transform: rotate(45deg) translateY(-100%); }
            100% { transform: rotate(45deg) translateY(100%); }
        }
        
        .reset-header i {
            font-size: 48px;
            margin-bottom: 10px;
            animation: bounce 2s infinite;
        }
        
        @keyframes bounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }
        
        .reset-header h3 {
            font-weight: 600;
            margin-bottom: 5px;
        }
        
        .reset-header p {
            opacity: 0.9;
            margin-bottom: 0;
        }
        
        .reset-body {
            padding: 40px;
        }
        
        .form-group {
            margin-bottom: 25px;
            position: relative;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: #333;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .input-group {
            border: 2px solid #e1e5eb;
            border-radius: 12px;
            overflow: hidden;
            transition: all 0.3s;
            background: white;
        }
        
        .input-group:focus-within {
            border-color: #667eea;
            box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
        }
        
        .input-group-text {
            background: #f8f9fc;
            border: none;
            color: #667eea;
            padding: 0 15px;
        }
        
        .form-control {
            border: none;
            height: 50px;
            padding: 0 15px;
            font-size: 15px;
            background: white;
        }
        
        .form-control:focus {
            outline: none;
            box-shadow: none;
        }
        
        .btn-update {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            height: 55px;
            border-radius: 12px;
            font-weight: 600;
            font-size: 16px;
            text-transform: uppercase;
            letter-spacing: 1px;
            cursor: pointer;
            transition: all 0.3s;
            width: 100%;
            position: relative;
            overflow: hidden;
        }
        
        .btn-update::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.3);
            transform: translate(-50%, -50%);
            transition: width 0.6s, height 0.6s;
        }
        
        .btn-update:hover::before {
            width: 300px;
            height: 300px;
        }
        
        .btn-update:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.4);
        }
        
        .btn-update i {
            margin-right: 8px;
        }
        
        .back-to-login {
            text-align: center;
            margin-top: 25px;
            padding-top: 20px;
            border-top: 2px solid #e1e5eb;
        }
        
        .back-to-login a {
            color: #667eea;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s;
        }
        
        .back-to-login a:hover {
            color: #764ba2;
            text-decoration: underline;
        }
        
        .alert {
            border-radius: 12px;
            padding: 15px 20px;
            margin-bottom: 25px;
            border: none;
            animation: slideIn 0.5s;
        }
        
        .alert-danger {
            background: #fee;
            color: #c33;
        }
        
        .alert-success {
            background: #e8f5e9;
            color: #2e7d32;
        }
        
        .password-strength {
            height: 5px;
            border-radius: 3px;
            margin-top: 8px;
            transition: all 0.3s;
        }
        
        .password-strength.weak { background: #f44336; width: 33%; }
        .password-strength.medium { background: #ff9800; width: 66%; }
        .password-strength.strong { background: #4caf50; width: 100%; }
        
        .email-display {
            background: #e8f4fd;
            border-radius: 10px;
            padding: 12px 15px;
            margin-bottom: 20px;
            font-size: 14px;
            color: #0369a1;
            display: flex;
            align-items: center;
            gap: 10px;
            word-break: break-all;
        }
        
        .email-display i {
            font-size: 18px;
        }
        
        .footer {
            background: linear-gradient(135deg, #2c3e50 0%, #1e2a36 100%);
            color: white;
            padding: 3rem 0 1rem;
            position: relative;
            z-index: 100;
            border-top: 3px solid #ffd700;
        }
        
        .footer h5 {
            color: #ffd700;
            font-weight: 600;
            margin-bottom: 1.5rem;
            position: relative;
            padding-bottom: 10px;
        }
        
        .footer h5::after {
            content: '';
            position: absolute;
            left: 0;
            bottom: 0;
            width: 50px;
            height: 2px;
            background: #ffd700;
        }
        
        .footer a {
            color: rgba(255,255,255,0.8);
            text-decoration: none;
            transition: all 0.3s;
            display: inline-block;
            margin-bottom: 8px;
        }
        
        .footer a:hover {
            color: #ffd700;
            transform: translateX(5px);
        }
        
        .footer i {
            margin-right: 8px;
            color: #ffd700;
        }
        
        .footer .social-links a {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 36px;
            height: 36px;
            background: rgba(255,255,255,0.1);
            border-radius: 50%;
            margin-right: 8px;
            transition: all 0.3s;
        }
        
        .footer .social-links a:hover {
            background: #ffd700;
            color: #2c3e50;
            transform: translateY(-3px);
        }
        
        .footer .social-links a:hover i {
            color: #2c3e50;
        }
        
        .footer-bottom {
            border-top: 1px solid rgba(255,255,255,0.1);
            margin-top: 2rem;
            padding-top: 1rem;
            text-align: center;
            color: rgba(255,255,255,0.6);
        }
        
        .footer-bottom a {
            color: rgba(255,255,255,0.6);
            margin: 0 10px;
        }
        
        .footer-bottom a:hover {
            color: #ffd700;
            transform: none;
        }
        
        .reset-card {
            animation: float 6s ease-in-out infinite;
        }
        
        @keyframes float {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
            100% { transform: translateY(0px); }
        }
        
        @media (max-width: 768px) {
            .main-content {
                margin-top: 70px;
            }
            
            .reset-container {
                padding: 10px;
            }
            
            .reset-body {
                padding: 20px;
            }
        }
        
        .spinner-wrapper {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255,255,255,0.8);
            z-index: 9999;
            justify-content: center;
            align-items: center;
        }
        
        .spinner-wrapper.show {
            display: flex;
        }
    </style>
</head>
<body>
    <div class="spinner-wrapper" id="spinner">
        <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
    </div>

    <nav class="navbar navbar-expand-lg navbar-dark navbar-custom">
        <div class="container">
            <a class="navbar-brand" href="/lhtm_system/index.php">
                <i class="bi bi-github"></i> LiveStock<span style="color: #ffd700;">Health</span>
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="/lhtm_system/index.php#features">
                            <i class="bi bi-star"></i> Features
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/lhtm_system/index.php#about">
                            <i class="bi bi-info-circle"></i> About
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="register.php">
                            <i class="bi bi-person-plus"></i> Register
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="btn btn-outline-light ms-3" href="index.php">
                            <i class="bi bi-box-arrow-in-right"></i> Login
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="main-content">
        <div class="reset-container" data-aos="fade-up">
            <div class="reset-card">
                <div class="reset-header">
                    <i class="bi bi-key"></i>
                    <h3>Reset Password</h3>
                    <p>Create a new password</p>
                </div>
                
                <div class="reset-body">
                    <?php if(isset($_GET['error'])): ?>
                        <div class="alert alert-danger">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i>
                            <?php 
                                if($_GET['error'] == 'password_mismatch') {
                                    echo 'Passwords do not match.';
                                } elseif($_GET['error'] == 'weak_password') {
                                    echo 'Password must be at least 6 characters.';
                                } elseif($_GET['error'] == 'update_failed') {
                                    echo 'Failed to update password. Please try again.';
                                } else {
                                    echo 'An error occurred. Please try again.';
                                }
                            ?>
                        </div>
                    <?php endif; ?>
                    
                    <div class="email-display">
                        <i class="bi bi-envelope-fill"></i>
                        <span>Resetting password for: <strong><?= htmlspecialchars($email) ?></strong></span>
                    </div>
                    
                    <form method="POST" action="update_password.php" id="resetForm">
                        <input type="hidden" name="email" value="<?= htmlspecialchars($email) ?>">
                        <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">
                        
                        <div class="form-group">
                            <label for="password">
                                <i class="bi bi-lock"></i> New Password
                            </label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="bi bi-lock"></i>
                                </span>
                                <input type="password" class="form-control" id="password" name="password" 
                                       placeholder="••••••••" required minlength="6">
                                <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                            <div class="password-strength" id="passwordStrength"></div>
                            <small class="text-muted">Minimum 6 characters</small>
                        </div>
                        
                        <div class="form-group">
                            <label for="confirm_password">
                                <i class="bi bi-lock-fill"></i> Confirm Password
                            </label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="bi bi-lock-fill"></i>
                                </span>
                                <input type="password" class="form-control" id="confirm_password" 
                                       placeholder="••••••••" required>
                            </div>
                            <div id="passwordMatch" class="small mt-1"></div>
                        </div>
                        
                        <button type="submit" class="btn-update" id="updateBtn">
                            <i class="bi bi-check-circle"></i> Update Password
                        </button>
                    </form>
                    
                    <div class="back-to-login">
                        <p class="mb-0">
                            <a href="index.php">
                                <i class="bi bi-box-arrow-in-right"></i> Back to Login
                            </a>
                        </p>
                    </div>
                </div>
            </div>
            
            <div class="text-center mt-3">
                <a href="/lhtm_system/index.php" class="text-white text-decoration-none">
                    <i class="bi bi-arrow-left"></i> Back to Home
                </a>
            </div>
        </div>
    </div>

    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 col-md-6 mb-4">
                    <h5>
                        <i class="bi bi-github"></i> LivestockHealth
                    </h5>
                    <p class="text-white-50">
                        Smart farming solution for modern agriculture. Track, monitor, and improve livestock health with our comprehensive digital platform.
                    </p>
                    <div class="social-links">
                        <a href="#" data-bs-toggle="tooltip" title="Facebook"><i class="bi bi-facebook"></i></a>
                        <a href="#" data-bs-toggle="tooltip" title="Twitter"><i class="bi bi-twitter"></i></a>
                        <a href="#" data-bs-toggle="tooltip" title="Instagram"><i class="bi bi-instagram"></i></a>
                        <a href="#" data-bs-toggle="tooltip" title="LinkedIn"><i class="bi bi-linkedin"></i></a>
                        <a href="#" data-bs-toggle="tooltip" title="YouTube"><i class="bi bi-youtube"></i></a>
                    </div>
                </div>
                
                <div class="col-lg-2 col-md-6 mb-4">
                    <h5>Quick Links</h5>
                    <ul class="list-unstyled">
                        <li><a href="/lhtm_system/index.php#features"><i class="bi bi-chevron-right"></i> Features</a></li>
                        <li><a href="/lhtm_system/index.php#about"><i class="bi bi-chevron-right"></i> About Us</a></li>
                        <li><a href="register.php"><i class="bi bi-chevron-right"></i> Register</a></li>
                        <li><a href="index.php"><i class="bi bi-chevron-right"></i> Login</a></li>
                    </ul>
                </div>
                
                <div class="col-lg-3 col-md-6 mb-4">
                    <h5>Contact Info</h5>
                    <ul class="list-unstyled">
                        <li><i class="bi bi-geo-alt"></i> 123 Farm Street, Nairobi, Kenya</li>
                        <li><i class="bi bi-envelope"></i> support@livestockhealth.com</li>
                        <li><i class="bi bi-telephone"></i> +254 712 345 678</li>
                        <li><i class="bi bi-clock"></i> Mon - Fri: 8:00 AM - 6:00 PM</li>
                    </ul>
                </div>
                
                <div class="col-lg-3 col-md-6 mb-4">
                    <h5>Newsletter</h5>
                    <p class="text-white-50">Subscribe for updates and farming tips.</p>
                    <div class="input-group">
                        <input type="email" class="form-control" placeholder="Your email">
                        <button class="btn btn-warning" type="button"><i class="bi bi-send"></i></button>
                    </div>
                    <hr class="text-white-50 mt-3">
                    <p class="mb-0 text-white-50"><i class="bi bi-shield-check"></i> Secure & Encrypted</p>
                </div>
            </div>
            
            <div class="footer-bottom">
                <div class="row">
                    <div class="col-md-6 text-md-start">
                        &copy; <?= date('Y') ?> Livestock Health System. All rights reserved.
                    </div>
                    <div class="col-md-6 text-md-end">
                        <a href="#">Privacy Policy</a>
                        <a href="#">Terms of Service</a>
                        <a href="#">Cookie Policy</a>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    
    <script>
        AOS.init({ duration: 1000, once: true });
        
        document.getElementById('togglePassword')?.addEventListener('click', function() {
            const passwordInput = document.getElementById('password');
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            this.querySelector('i').classList.toggle('bi-eye');
            this.querySelector('i').classList.toggle('bi-eye-slash');
        });
        
        document.getElementById('password')?.addEventListener('input', function() {
            const password = this.value;
            const strengthBar = document.getElementById('passwordStrength');
            strengthBar.className = 'password-strength';
            
            if (password.length === 0) {
                strengthBar.style.width = '0';
                return;
            }
            
            let strength = 0;
            if (password.length >= 8) strength++;
            if (password.length >= 12) strength++;
            if (/[a-z]/.test(password)) strength++;
            if (/[A-Z]/.test(password)) strength++;
            if (/[0-9]/.test(password)) strength++;
            if (/[^a-zA-Z0-9]/.test(password)) strength++;
            
            if (strength <= 2) strengthBar.classList.add('weak');
            else if (strength <= 4) strengthBar.classList.add('medium');
            else strengthBar.classList.add('strong');
        });
        
        document.getElementById('confirm_password')?.addEventListener('input', function() {
            const password = document.getElementById('password').value;
            const confirm = this.value;
            const matchDiv = document.getElementById('passwordMatch');
            
            if (confirm.length === 0) {
                matchDiv.innerHTML = '';
                return;
            }
            
            if (password === confirm) {
                matchDiv.innerHTML = '<span class="text-success"><i class="bi bi-check-circle"></i> Passwords match</span>';
                document.getElementById('updateBtn').disabled = false;
            } else {
                matchDiv.innerHTML = '<span class="text-danger"><i class="bi bi-exclamation-circle"></i> Passwords do not match</span>';
                document.getElementById('updateBtn').disabled = true;
            }
        });
        
        document.getElementById('resetForm')?.addEventListener('submit', function(e) {
            const password = document.getElementById('password').value;
            const confirm = document.getElementById('confirm_password').value;
            
            if (password !== confirm) {
                e.preventDefault();
                alert('Passwords do not match!');
                return false;
            }
            
            if (password.length < 6) {
                e.preventDefault();
                alert('Password must be at least 6 characters long!');
                return false;
            }
            
            document.getElementById('spinner').classList.add('show');
        });
        
        setTimeout(function() {
            document.querySelectorAll('.alert').forEach(function(alert) {
                alert.style.transition = 'opacity 0.5s';
                alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 500);
            });
        }, 5000);
        
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function(tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    </script>
</body>
</html>