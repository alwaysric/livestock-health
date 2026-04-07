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

$page_title = 'Register';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - LiveStockHealth</title>
    
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
            background: linear-gradient(rgba(0, 0, 0, 0.7), rgba(0, 0, 0, 0.7)), 
                        url('https://images.unsplash.com/photo-1527153818091-1a9638521e2a?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
        }
        
        /* Animated overlay */
        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(45deg, 
                rgba(102, 126, 234, 0.2) 0%, 
                rgba(28, 200, 138, 0.2) 50%, 
                rgba(246, 194, 62, 0.2) 100%);
            animation: gradientShift 15s ease infinite;
            pointer-events: none;
        }
        
        @keyframes gradientShift {
            0% { opacity: 0.3; }
            50% { opacity: 0.6; }
            100% { opacity: 0.3; }
        }
        
        .register-container {
            width: 100%;
            max-width: 500px;
            padding: 20px;
            position: relative;
            z-index: 10;
            margin: 40px auto;
        }
        
        .register-card {
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
        
        .register-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 30px;
            text-align: center;
            color: white;
            position: relative;
            overflow: hidden;
        }
        
        .register-header::before {
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
        
        .register-header i {
            font-size: 48px;
            margin-bottom: 10px;
            animation: bounce 2s infinite;
        }
        
        @keyframes bounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }
        
        .register-header h3 {
            font-weight: 600;
            margin-bottom: 5px;
        }
        
        .register-header p {
            opacity: 0.9;
            margin-bottom: 0;
        }
        
        .register-body {
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
        
        /* Role Info Box - Replaces Dropdown */
        .role-info-box {
            background: linear-gradient(135deg, #667eea15 0%, #764ba215 100%);
            border: 2px solid #667eea;
            border-radius: 12px;
            padding: 15px 20px;
            margin-bottom: 25px;
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .role-icon {
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 24px;
        }
        
        .role-details {
            flex: 1;
        }
        
        .role-details h6 {
            margin: 0;
            font-weight: 600;
            color: #333;
        }
        
        .role-details p {
            margin: 5px 0 0;
            font-size: 13px;
            color: #666;
        }
        
        .role-badge {
            background: #ffd700;
            color: #333;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            display: inline-block;
        }
        
        .btn-register {
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
        
        .btn-register::before {
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
        
        .btn-register:hover::before {
            width: 300px;
            height: 300px;
        }
        
        .btn-register:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.4);
        }
        
        .btn-register i {
            margin-right: 8px;
        }
        
        .login-link {
            text-align: center;
            margin-top: 25px;
            padding-top: 20px;
            border-top: 2px solid #e1e5eb;
        }
        
        .login-link a {
            color: #667eea;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s;
        }
        
        .login-link a:hover {
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
        
        .back-to-home {
            text-align: center;
            margin-top: 20px;
        }
        
        .back-to-home a {
            color: white;
            text-decoration: none;
            font-size: 14px;
            transition: all 0.3s;
        }
        
        .back-to-home a:hover {
            color: #ffd700;
        }
        
        .back-to-home i {
            margin-right: 5px;
        }
        
        /* Floating animation for card */
        .register-card {
            animation: float 6s ease-in-out infinite;
        }
        
        @keyframes float {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
            100% { transform: translateY(0px); }
        }
        
        /* Info message */
        .info-message {
            background: #e8f4fd;
            border-radius: 10px;
            padding: 12px 15px;
            margin-bottom: 20px;
            font-size: 14px;
            color: #0369a1;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .info-message i {
            font-size: 18px;
        }
    </style>
</head>
<body>
    <div class="register-container">
        <div class="register-card" data-aos="fade-up">
            <div class="register-header">
                <i class="bi bi-person-plus-fill"></i>
                <h3>Create Account</h3>
                <p>Join our livestock management community</p>
            </div>
            
            <div class="register-body">
                <?php if(isset($_GET['error'])): ?>
                    <div class="alert alert-danger">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>
                        <?php 
                            if($_GET['error'] == 'email_exists') {
                                echo 'Email already exists. Please use another email.';
                            } elseif($_GET['error'] == 'invalid') {
                                echo 'Please fill in all fields correctly.';
                            } elseif($_GET['error'] == 'insert_failed') {
                                echo 'Registration failed. Please try again.';
                            } else {
                                echo 'Registration failed. Please try again.';
                            }
                        ?>
                    </div>
                <?php endif; ?>
                
                <?php if(isset($_GET['success'])): ?>
                    <div class="alert alert-success">
                        <i class="bi bi-check-circle-fill me-2"></i>
                        Registration successful! Redirecting to login...
                    </div>
                    <meta http-equiv="refresh" content="2;url=index.php">
                <?php endif; ?>
                
                <!-- Info Message -->
                <div class="info-message">
                    <i class="bi bi-info-circle-fill"></i>
                    <span>All new accounts are registered as <strong>Farm Workers</strong> by default.</span>
                </div>
                
                <form method="POST" action="save_user.php" id="registerForm">
                    <!-- Hidden role field - always worker -->
                    <input type="hidden" name="role" value="worker">
                    
                    <div class="form-group">
                        <label for="name">
                            <i class="bi bi-person"></i> Full Name
                        </label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="bi bi-person"></i>
                            </span>
                            <input type="text" class="form-control" id="name" name="name" 
                                   placeholder="Enter your full name" required 
                                   pattern="[A-Za-z\s]+" title="Name should only contain letters and spaces">
                        </div>
                    </div>
                    
                    <!-- Role Display (Information Only - Not Editable) -->
                    <div class="form-group">
                        <label for="role_display">
                            <i class="bi bi-briefcase"></i> Account Type
                        </label>
                        <div class="role-info-box">
                            <div class="role-icon">
                                <i class="bi bi-tools"></i>
                            </div>
                            <div class="role-details">
                                <h6>Farm Worker <span class="role-badge">Default</span></h6>
                                <p><i class="bi bi-check-circle-fill text-success me-1"></i> Basic access to manage animals and health records</p>
                            </div>
                        </div>
                    </div>
                    
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
                                   placeholder="••••••••" required minlength="6">
                            <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>
                        <div class="password-strength" id="passwordStrength"></div>
                        <small class="text-muted">
                            <i class="bi bi-shield-check"></i> Minimum 6 characters
                        </small>
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
                    
                    <div class="form-group">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="terms" required>
                            <label class="form-check-label" for="terms">
                                I agree to the <a href="#" class="text-decoration-none">Terms of Service</a> 
                                and <a href="#" class="text-decoration-none">Privacy Policy</a>
                            </label>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn-register" id="submitBtn">
                        <i class="bi bi-check-circle"></i> Register as Farm Worker
                    </button>
                </form>
                
                <div class="login-link">
                    <p class="mb-0">
                        Already have an account? 
                        <a href="index.php">
                            <i class="bi bi-box-arrow-in-right"></i> Login Here
                        </a>
                    </p>
                </div>
                
                <!-- Note about role change -->
                <div class="text-center mt-3">
                    <small class="text-muted">
                        <i class="bi bi-question-circle"></i> 
                        Need a different role? Contact the system administrator.
                    </small>
                </div>
            </div>
        </div>
        
        <div class="back-to-home">
            <a href="/lhtm_system/index.php">
                <i class="bi bi-arrow-left"></i> Back to Home
            </a>
        </div>
    </div>
    
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
        
        // Password strength checker
        document.getElementById('password').addEventListener('input', function() {
            const password = this.value;
            const strengthBar = document.getElementById('passwordStrength');
            
            // Remove all classes
            strengthBar.className = 'password-strength';
            
            if (password.length === 0) {
                strengthBar.style.width = '0';
                return;
            }
            
            // Check strength
            let strength = 0;
            
            // Length check
            if (password.length >= 8) strength++;
            if (password.length >= 12) strength++;
            
            // Character variety
            if (/[a-z]/.test(password)) strength++;
            if (/[A-Z]/.test(password)) strength++;
            if (/[0-9]/.test(password)) strength++;
            if (/[^a-zA-Z0-9]/.test(password)) strength++;
            
            // Apply class based on strength
            if (strength <= 2) {
                strengthBar.classList.add('weak');
            } else if (strength <= 4) {
                strengthBar.classList.add('medium');
            } else {
                strengthBar.classList.add('strong');
            }
        });
        
        // Password match checker
        document.getElementById('confirm_password').addEventListener('input', function() {
            const password = document.getElementById('password').value;
            const confirm = this.value;
            const matchDiv = document.getElementById('passwordMatch');
            
            if (confirm.length === 0) {
                matchDiv.innerHTML = '';
                return;
            }
            
            if (password === confirm) {
                matchDiv.innerHTML = '<span class="text-success"><i class="bi bi-check-circle"></i> Passwords match</span>';
                document.getElementById('submitBtn').disabled = false;
            } else {
                matchDiv.innerHTML = '<span class="text-danger"><i class="bi bi-exclamation-circle"></i> Passwords do not match</span>';
                document.getElementById('submitBtn').disabled = true;
            }
        });
        
        // Form validation
        document.getElementById('registerForm').addEventListener('submit', function(e) {
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
    </script>
</body>
</html>