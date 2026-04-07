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

$page_title = 'Login';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Livestock Health System</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- AOS Animation -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    
    <style>
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
        
        /* Header Styles */
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
        
        /* Main Content - with margin-top for fixed header */
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
        
        /* Animated overlay */
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
        
        .login-container {
            width: 100%;
            max-width: 450px;
            padding: 20px;
            position: relative;
            z-index: 10;
        }
        
        .login-card {
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
        
        .login-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 30px;
            text-align: center;
            color: white;
            position: relative;
            overflow: hidden;
        }
        
        .login-header::before {
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
        
        .login-header i {
            font-size: 48px;
            margin-bottom: 10px;
            animation: bounce 2s infinite;
        }
        
        @keyframes bounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }
        
        .login-header h3 {
            font-weight: 600;
            margin-bottom: 5px;
        }
        
        .login-header p {
            opacity: 0.9;
            margin-bottom: 0;
        }
        
        .login-body {
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
        
        .btn-login {
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
        
        .btn-login::before {
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
        
        .btn-login:hover::before {
            width: 300px;
            height: 300px;
        }
        
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.4);
        }
        
        .btn-login i {
            margin-right: 8px;
        }
        
        .register-link {
            text-align: center;
            margin-top: 25px;
            padding-top: 20px;
            border-top: 2px solid #e1e5eb;
        }
        
        .register-link a {
            color: #667eea;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s;
        }
        
        .register-link a:hover {
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
        
        .forgot-password {
            text-align: right;
            margin-top: 10px;
        }
        
        .forgot-password a {
            color: #6c757d;
            text-decoration: none;
            font-size: 14px;
            transition: all 0.3s;
        }
        
        .forgot-password a:hover {
            color: #667eea;
        }
        
        /* Footer Styles */
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
        
        .footer .social-links i {
            margin: 0;
            color: white;
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
        
        /* Floating animation for card */
        .login-card {
            animation: float 6s ease-in-out infinite;
        }
        
        @keyframes float {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
            100% { transform: translateY(0px); }
        }
        
        /* Responsive adjustments */
        @media (max-width: 768px) {
            .main-content {
                margin-top: 70px;
            }
            
            .login-container {
                padding: 10px;
            }
            
            .login-body {
                padding: 20px;
            }
            
            .footer {
                padding: 2rem 0 0.5rem;
            }
        }
        
        /* Loading spinner */
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
    <!-- Loading Spinner -->
    <div class="spinner-wrapper" id="spinner">
        <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
    </div>

    <!-- Header / Navigation -->
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

    <!-- Main Content with Background -->
    <div class="main-content">
        <div class="login-container" data-aos="fade-up">
            <div class="login-card">
                <div class="login-header">
                    <i class="bi bi-box-arrow-in-right"></i>
                    <h3>Welcome Back!</h3>
                    <p>Please login to your account</p>
                </div>
                
                <div class="login-body">
                    <?php if(isset($_GET['error'])): ?>
                        <div class="alert alert-danger">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i>
                            Invalid email or password! Please try again.
                        </div>
                    <?php endif; ?>
                    
                    <?php if(isset($_GET['success'])): ?>
                        <div class="alert alert-success">
                            <i class="bi bi-check-circle-fill me-2"></i>
                            Registration successful! Please login.
                        </div>
                    <?php endif; ?>
                    
                    <?php if(isset($_GET['loggedout'])): ?>
                        <div class="alert alert-success">
                            <i class="bi bi-check-circle-fill me-2"></i>
                            You have been successfully logged out.
                        </div>
                    <?php endif; ?>
                    
                    <!-- Password Reset Success Message -->
                    <?php if(isset($_GET['reset']) && $_GET['reset'] == 'success'): ?>
                        <div class="alert alert-success">
                            <i class="bi bi-check-circle-fill me-2"></i>
                            Password reset successful! Please login with your new password.
                        </div>
                    <?php endif; ?>
                    
                    <form method="POST" action="login.php" id="loginForm">
                        <div class="form-group">
                            <label for="email">
                                <i class="bi bi-envelope"></i> Email Address
                            </label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="bi bi-envelope"></i>
                                </span>
                                <input type="email" class="form-control" id="email" name="email" 
                                       placeholder="your@email.com" required>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="password">
                                <i class="bi bi-lock"></i> Password
                            </label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="bi bi-lock"></i>
                                </span>
                                <input type="password" class="form-control" id="password" name="password" 
                                       placeholder="••••••••" required>
                                <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                            <div class="forgot-password">
                                <a href="forgot_password.php">
                                    <i class="bi bi-question-circle"></i> Forgot password?
                                </a>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="remember">
                                <label class="form-check-label" for="remember">
                                    Remember me on this device
                                </label>
                            </div>
                        </div>
                        
                        <button type="submit" class="btn-login" id="loginBtn">
                            <i class="bi bi-box-arrow-in-right"></i> Login
                        </button>
                    </form>
                    
                    <div class="register-link">
                        <p class="mb-0">
                            Don't have an account? 
                            <a href="register.php">
                                <i class="bi bi-person-plus"></i> Create Account
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

    <!-- Presentable Footer -->
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
                        <a href="#" data-bs-toggle="tooltip" title="Facebook">
                            <i class="bi bi-facebook"></i>
                        </a>
                        <a href="#" data-bs-toggle="tooltip" title="Twitter">
                            <i class="bi bi-twitter"></i>
                        </a>
                        <a href="#" data-bs-toggle="tooltip" title="Instagram">
                            <i class="bi bi-instagram"></i>
                        </a>
                        <a href="#" data-bs-toggle="tooltip" title="LinkedIn">
                            <i class="bi bi-linkedin"></i>
                        </a>
                        <a href="#" data-bs-toggle="tooltip" title="YouTube">
                            <i class="bi bi-youtube"></i>
                        </a>
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
                        <button class="btn btn-warning" type="button">
                            <i class="bi bi-send"></i>
                        </button>
                    </div>
                    <hr class="text-white-50 mt-3">
                    <p class="mb-0 text-white-50">
                        <i class="bi bi-shield-check"></i> 
                        Secure & Encrypted
                    </p>
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

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- AOS Animation -->
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    
    <script>
        // Initialize AOS
        AOS.init({
            duration: 1000,
            once: true
        });
        
        // Toggle password visibility
        document.getElementById('togglePassword').addEventListener('click', function() {
            const passwordInput = document.getElementById('password');
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            this.querySelector('i').classList.toggle('bi-eye');
            this.querySelector('i').classList.toggle('bi-eye-slash');
        });
        
        // Show loading spinner on form submit
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            document.getElementById('spinner').classList.add('show');
        });
        
        // Auto-hide alerts after 5 seconds
        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(function(alert) {
                alert.style.transition = 'opacity 0.5s';
                alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 500);
            });
        }, 5000);
        
        // Initialize tooltips
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function(tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
        
        // Remember me functionality (optional)
        if (localStorage.getItem('rememberedEmail')) {
            document.getElementById('email').value = localStorage.getItem('rememberedEmail');
            document.getElementById('remember').checked = true;
        }
        
        document.getElementById('remember').addEventListener('change', function() {
            if (this.checked) {
                const email = document.getElementById('email').value;
                if (email) {
                    localStorage.setItem('rememberedEmail', email);
                }
            } else {
                localStorage.removeItem('rememberedEmail');
            }
        });
    </script>
</body>
</html>