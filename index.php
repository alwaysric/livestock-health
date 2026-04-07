<?php
session_start();
if (isset($_SESSION['user_id'])) {
    // If already logged in, redirect to appropriate dashboard
    if ($_SESSION['role'] === 'admin') {
        header('Location: admin_dashboard.php');
    } else {
        header('Location: dashboard.php');
    }
    exit;
}

$page_title = 'Home';
// Define BASE_URL for consistent linking
if (!defined('BASE_URL')) {
    define('BASE_URL', '/lhtm_system/');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LiveStockHealth - Smart Farming Solutions</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- AOS Animation -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/style.css">
    
    <style>
        /* Header Styles - Matching Main Dashboard */
        :root {
            --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --header-height: 70px;
        }
        
        .navbar-custom {
            background: var(--primary-gradient);
            padding: 0.8rem 0;
            box-shadow: 0 4px 20px rgba(0,0,0,0.15);
            position: sticky;
            top: 0;
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
            font-size: 1.8rem;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.2);
            letter-spacing: 1px;
            transition: all 0.3s ease;
        }
        
        .navbar-custom .navbar-brand:hover {
            transform: scale(1.05);
            text-shadow: 3px 3px 6px rgba(0,0,0,0.3);
        }
        
        .navbar-custom .navbar-brand i {
            color: #ffd700;
            margin-right: 8px;
            font-size: 2rem;
            animation: bounce 2s infinite;
        }
        
        @keyframes bounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-3px); }
        }
        
        .navbar-custom .navbar-brand span {
            color: #ffd700;
            font-weight: 800;
        }
        
        .navbar-custom .nav-link {
            color: rgba(255,255,255,0.9) !important;
            font-weight: 500;
            padding: 0.5rem 1rem !important;
            border-radius: 25px;
            transition: all 0.3s ease;
            margin: 0 2px;
            font-size: 1rem;
        }
        
        .navbar-custom .nav-link:hover {
            background: rgba(255,255,255,0.2);
            color: white !important;
            transform: translateY(-2px);
        }
        
        .navbar-custom .nav-link.active {
            background: rgba(255,255,255,0.25);
            color: white !important;
            font-weight: 600;
        }
        
        .navbar-custom .nav-link i {
            margin-right: 5px;
        }
        
        .navbar-custom .btn-outline-light {
            border-radius: 25px;
            padding: 0.5rem 1.5rem;
            font-weight: 600;
            transition: all 0.3s ease;
            border: 2px solid white;
        }
        
        .navbar-custom .btn-outline-light:hover {
            background: white;
            color: #667eea !important;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
        
        /* Hero Section with Background */
        .landing-wrapper {
            background: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)), 
                        url('https://images.unsplash.com/photo-1527153818091-1a9638521e2a?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            min-height: 100vh;
            position: relative;
        }
        
        .landing-wrapper::before {
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
        
        /* Glass Card */
        .glass-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border: none;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.2);
            transition: transform 0.3s ease;
        }
        
        .glass-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 25px 50px rgba(0,0,0,0.3);
        }
        
        /* Feature Cards */
        .feature-card {
            transition: all 0.3s ease;
            border: none;
            border-radius: 15px;
            overflow: hidden;
            background: white;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
        }
        
        .feature-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 30px rgba(102,126,234,0.2);
        }
        
        .feature-card .icon-wrapper {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto;
            transition: all 0.3s ease;
        }
        
        .feature-card:hover .icon-wrapper {
            transform: scale(1.1) rotate(5deg);
        }
        
        /* Stats Numbers */
        .stat-number {
            font-size: 2.5rem;
            font-weight: 700;
            color: #ffd700;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.2);
        }
        
        /* CTA Section */
        .cta-section {
            background: var(--primary-gradient);
            position: relative;
            overflow: hidden;
        }
        
        .cta-section::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 200%;
            height: 200%;
            background: rgba(255,255,255,0.1);
            transform: rotate(45deg);
            animation: shine 3s infinite;
        }
        
        @keyframes shine {
            0% { transform: rotate(45deg) translateY(-100%); }
            100% { transform: rotate(45deg) translateY(100%); }
        }
        
        /* Testimonial Cards */
        .testimonial-card {
            background: white;
            border: none;
            border-radius: 15px;
            transition: all 0.3s ease;
        }
        
        .testimonial-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .navbar-custom .navbar-brand {
                font-size: 1.5rem;
            }
            
            .navbar-custom .navbar-brand i {
                font-size: 1.7rem;
            }
            
            .stat-number {
                font-size: 2rem;
            }
        }
    </style>
</head>
<body>
    <!-- Loading Spinner (Hidden by default) -->
    <div class="spinner-wrapper" id="globalSpinner">
        <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
    </div>

    <!-- Header - Identical to Dashboard -->
    <nav class="navbar navbar-expand-lg navbar-dark navbar-custom">
        <div class="container">
            <!-- Brand Logo - LiveStockHealth with Icon -->
            <a class="navbar-brand" href="<?= BASE_URL ?>index.php">
                <i class="bi bi-github"></i> LiveStock<span>Health</span>
            </a>
            
            <!-- Mobile Toggle Button -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain" 
                    aria-controls="navbarMain" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <!-- Navigation Links -->
            <div class="collapse navbar-collapse" id="navbarMain">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="#features">
                            <i class="bi bi-star"></i> Features
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#about">
                            <i class="bi bi-info-circle"></i> About
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#testimonials">
                            <i class="bi bi-chat-quote"></i> Testimonials
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#contact">
                            <i class="bi bi-envelope"></i> Contact
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="btn btn-outline-light ms-3" href="<?= BASE_URL ?>auth/register.php">
                            <i class="bi bi-person-plus"></i> Register
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="btn btn-outline-light ms-2" href="<?= BASE_URL ?>auth/index.php">
                            <i class="bi bi-box-arrow-in-right"></i> Login
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section with Background -->
    <div class="landing-wrapper">
        <div class="container">
            <div class="row min-vh-100 align-items-center">
                <div class="col-lg-6 text-white" data-aos="fade-right">
                    <h1 class="display-3 fw-bold mb-4">
                        Livestock Health Monitoring And Tracking System
                    </h1>
                    <p class="lead mb-4">
                        Track animal health, manage vaccinations, and monitor livestock wellbeing with our comprehensive digital solution trusted by 500+ farmers.
                    </p>
                    <div class="d-flex gap-3">
                        <a href="<?= BASE_URL ?>auth/register.php" class="btn btn-light btn-lg px-4">
                            Get Started <i class="bi bi-arrow-right"></i>
                        </a>
                        <a href="#features" class="btn btn-outline-light btn-lg px-4">
                            Learn More
                        </a>
                    </div>
                    
                    <!-- Stats -->
                    <div class="row mt-5">
                        <div class="col-4">
                            <div class="stat-number">0+</div>
                            <p class="text-white-50">Active Farmers</p>
                        </div>
                        <div class="col-4">
                            <div class="stat-number">0+</div>
                            <p class="text-white-50">Animals Tracked</p>
                        </div>
                        <div class="col-4">
                            <div class="stat-number">99%</div>
                            <p class="text-white-50">Satisfaction</p>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-6" data-aos="fade-left">
                    <div class="glass-card p-5 text-center">
                        <i class="bi bi-github" style="font-size: 80px; color: #667eea;"></i>
                        <h3 class="mt-3">Ready to get started?</h3>
                        <p class="text-muted">Join thousands of farmers managing their livestock efficiently</p>
                        <div class="d-grid gap-3 mt-4">
                            <a href="<?= BASE_URL ?>auth/register.php" class="btn btn-primary btn-lg">
                                Create Free Account
                            </a>
                            <a href="<?= BASE_URL ?>auth/index.php" class="btn btn-outline-secondary btn-lg">
                                Already have an account? Login
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Features Section -->
    <section id="features" class="py-5 bg-light">
        <div class="container py-5">
            <div class="text-center mb-5" data-aos="fade-up">
                <h2 class="display-5 fw-bold">Why Choose Our System?</h2>
                <p class="lead text-muted">Comprehensive features designed for modern livestock management</p>
            </div>
            
            <div class="row g-4">
                <div class="col-md-4" data-aos="fade-up">
                    <div class="card feature-card h-100">
                        <div class="card-body text-center p-4">
                            <div class="icon-wrapper bg-primary bg-opacity-10 mb-3">
                                <i class="bi bi-github fs-1 text-primary"></i>
                            </div>
                            <h4>Animal Tracking</h4>
                            <p class="text-muted">Track individual animals with unique tag numbers, species, breed, and medical history.</p>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4" data-aos="fade-up" data-aos-delay="100">
                    <div class="card feature-card h-100">
                        <div class="card-body text-center p-4">
                            <div class="icon-wrapper bg-success bg-opacity-10 mb-3">
                                <i class="bi bi-file-medical fs-1 text-success"></i>
                            </div>
                            <h4>Health Records</h4>
                            <p class="text-muted">Complete medical history including diagnoses, treatments, and vaccination schedules.</p>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4" data-aos="fade-up" data-aos-delay="200">
                    <div class="card feature-card h-100">
                        <div class="card-body text-center p-4">
                            <div class="icon-wrapper bg-warning bg-opacity-10 mb-3">
                                <i class="bi bi-bell fs-1 text-warning"></i>
                            </div>
                            <h4>Smart Alerts</h4>
                            <p class="text-muted">Automatic notifications for upcoming vaccinations and health checkups.</p>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4" data-aos="fade-up">
                    <div class="card feature-card h-100">
                        <div class="card-body text-center p-4">
                            <div class="icon-wrapper bg-info bg-opacity-10 mb-3">
                                <i class="bi bi-bar-chart fs-1 text-info"></i>
                            </div>
                            <h4>Reports & Analytics</h4>
                            <p class="text-muted">Generate comprehensive health reports and analyze trends.</p>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4" data-aos="fade-up" data-aos-delay="100">
                    <div class="card feature-card h-100">
                        <div class="card-body text-center p-4">
                            <div class="icon-wrapper bg-danger bg-opacity-10 mb-3">
                                <i class="bi bi-shield-lock fs-1 text-danger"></i>
                            </div>
                            <h4>Role-Based Access</h4>
                            <p class="text-muted">Different access levels for farmers, workers, and veterinarians.</p>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4" data-aos="fade-up" data-aos-delay="200">
                    <div class="card feature-card h-100">
                        <div class="card-body text-center p-4">
                            <div class="icon-wrapper bg-secondary bg-opacity-10 mb-3">
                                <i class="bi bi-person-circle fs-1 text-secondary"></i>
                            </div>
                            <h4>Profile Management</h4>
                            <p class="text-muted">Personalized profiles with settings and password management.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

   <!-- About Section -->
<section id="about" class="py-5" style="background: linear-gradient(rgba(0, 0, 0, 0.8), rgba(0, 0, 0, 0.8)), url('https://images.unsplash.com/photo-1527153818091-1a9638521e2a?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80'); background-size: cover; background-attachment: fixed;">
    <div class="container py-5">
        <div class="row align-items-center">
            <div class="col-lg-6" data-aos="fade-right">
                <img src="https://images.unsplash.com/photo-1596733430284-f7437764b1a9?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" 
                     alt="Healthy cattle on modern farm" 
                     class="img-fluid rounded-3 shadow-lg border border-light">
                <div class="row mt-3">
                    <div class="col-6">
                        <img src="https://images.unsplash.com/photo-1527153818091-1a9638521e2a?ixlib=rb-4.0.3&auto=format&fit=crop&w=400&q=80" 
                             alt="Sheep grazing" 
                             class="img-fluid rounded-3 shadow-sm border border-light">
                    </div>
                    <div class="col-6">
                        <img src="https://images.unsplash.com/photo-1484557985045-edf25e08da73?ixlib=rb-4.0.3&auto=format&fit=crop&w=400&q=80" 
                             alt="Goat on farm" 
                             class="img-fluid rounded-3 shadow-sm border border-light">
                    </div>
                </div>
            </div>
            <div class="col-lg-6 text-white" data-aos="fade-left">
                <h2 class="display-6 fw-bold mb-4 text-white">Modern Solution for Traditional Farming</h2>
                <p class="lead mb-4 text-white-50">We combine technology with agriculture to help farmers make data-driven decisions for healthier livestock and better yields.</p>
                
                <div class="d-flex mb-3">
                    <i class="bi bi-check-circle-fill text-warning fs-4 me-3"></i>
                    <div>
                        <h5 class="text-white">Easy to Use</h5>
                        <p class="text-white-50">Simple interface designed specifically for farmers and farm workers.</p>
                    </div>
                </div>
                
                <div class="d-flex mb-3">
                    <i class="bi bi-check-circle-fill text-warning fs-4 me-3"></i>
                    <div>
                        <h5 class="text-white">Real-Time Updates</h5>
                        <p class="text-white-50">Instant access to animal health records and vaccination alerts.</p>
                    </div>
                </div>
                
                <div class="d-flex mb-4">
                    <i class="bi bi-check-circle-fill text-warning fs-4 me-3"></i>
                    <div>
                        <h5 class="text-white">Secure & Reliable</h5>
                        <p class="text-white-50">Your farm data is encrypted, backed up, and securely stored.</p>
                    </div>
                </div>
                
                <a href="<?= BASE_URL ?>auth/register.php" class="btn btn-warning btn-lg text-dark fw-bold">
                    Start Free Trial <i class="bi bi-arrow-right"></i>
                </a>
            </div>
        </div>
    </div>
</section>

    <!-- Livestock Gallery -->
    <section class="py-5 bg-light">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="display-5 fw-bold">Happy & Healthy Livestock</h2>
                <p class="lead text-muted">See the animals our system helps care for</p>
            </div>
            <div class="row g-4">
                <div class="col-md-3 col-6" data-aos="zoom-in">
                    <img src="https://images.unsplash.com/photo-1570042225831-d98fa7577f1e?ixlib=rb-4.0.3&auto=format&fit=crop&w=400&q=80" 
                         alt="Cow" 
                         class="img-fluid rounded-3 shadow">
                    <p class="text-center mt-2 fw-bold">Cattle</p>
                </div>
                <div class="col-md-3 col-6" data-aos="zoom-in" data-aos-delay="100">
                    <img src="https://images.unsplash.com/photo-1484557985045-edf25e08da73?ixlib=rb-4.0.3&auto=format&fit=crop&w=400&q=80" 
                         alt="Goat" 
                         class="img-fluid rounded-3 shadow">
                    <p class="text-center mt-2 fw-bold">Sheep</p>
                </div>
                <div class="col-md-3 col-6" data-aos="zoom-in" data-aos-delay="200">
                    <img src="https://images.unsplash.com/photo-1527153818091-1a9638521e2a?ixlib=rb-4.0.3&auto=format&fit=crop&w=400&q=80" 
                         alt="Sheep" 
                         class="img-fluid rounded-3 shadow">
                    <p class="text-center mt-2 fw-bold">Goats</p>
                </div>
                <div class="col-md-3 col-6" data-aos="zoom-in" data-aos-delay="300">
                    <img src="https://images.unsplash.com/photo-1516467508483-a7212febe31a?ixlib=rb-4.0.3&auto=format&fit=crop&w=400&q=80" 
                         alt="Pig" 
                         class="img-fluid rounded-3 shadow">
                    <p class="text-center mt-2 fw-bold">Pigs</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Testimonials Section -->
    <section id="testimonials" class="py-5">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="display-5 fw-bold">What Farmers Say</h2>
                <p class="lead text-muted">Trusted by livestock farmers worldwide</p>
            </div>
            <div class="row">
                <div class="col-md-4 mb-4" data-aos="fade-up">
                    <div class="card testimonial-card h-100 p-4">
                        <div class="text-warning mb-3">
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                        </div>
                        <p class="card-text">"This system has revolutionized how I manage my dairy farm. I never miss a vaccination now!"</p>
                        <div class="d-flex align-items-center mt-3">
                            <i class="bi bi-person-circle fs-1 me-3 text-primary"></i>
                            <div>
                                <h6 class="mb-0">John Mwangi</h6>
                                <small class="text-muted">Dairy Farmer, Kenya</small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-4" data-aos="fade-up" data-aos-delay="100">
                    <div class="card testimonial-card h-100 p-4">
                        <div class="text-warning mb-3">
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                        </div>
                        <p class="card-text">"The health records feature is amazing. I can track every treatment my sheep receive."</p>
                        <div class="d-flex align-items-center mt-3">
                            <i class="bi bi-person-circle fs-1 me-3 text-success"></i>
                            <div>
                                <h6 class="mb-0">Sarah Johnson</h6>
                                <small class="text-muted">Sheep Farmer, Australia</small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-4" data-aos="fade-up" data-aos-delay="200">
                    <div class="card testimonial-card h-100 p-4">
                        <div class="text-warning mb-3">
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                        </div>
                        <p class="card-text">"As a veterinarian, this tool helps me keep all my clients' records organized and accessible."</p>
                        <div class="d-flex align-items-center mt-3">
                            <i class="bi bi-person-circle fs-1 me-3 text-warning"></i>
                            <div>
                                <h6 class="mb-0">Dr. Carlos Ruiz</h6>
                                <small class="text-muted">Veterinarian, Spain</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Call to Action -->
    <section id="contact" class="py-5 cta-section text-white">
        <div class="container text-center py-4">
            <h2 class="display-5 fw-bold mb-4">Ready to Transform Your Farm Management?</h2>
            <p class="lead mb-4">Join thousands of farmers who are already using our system.</p>
            <a href="<?= BASE_URL ?>auth/register.php" class="btn btn-light btn-lg px-5 py-3 fw-bold">
                Get Started Today <i class="bi bi-arrow-right"></i>
            </a>
        </div>
    </section>

    <!-- Footer - Identical to Dashboard -->
    <footer class="footer bg-dark text-white py-5">
        <div class="container">
            <div class="row">
                <!-- Company Info -->
                <div class="col-lg-4 col-md-6 mb-4">
                    <h5 class="text-uppercase mb-4">
                        <i class="bi bi-github text-warning"></i> 
                        Livestock<span class="text-warning">Health</span>
                    </h5>
                    <p class="text-white-50">
                        Smart farming solution for modern agriculture. Track, monitor, and improve livestock health with our comprehensive digital platform.
                    </p>
                    <div class="social-links">
                        <a href="#" class="text-white-50 me-2" data-bs-toggle="tooltip" title="Facebook">
                            <i class="bi bi-facebook fs-5"></i>
                        </a>
                        <a href="#" class="text-white-50 me-2" data-bs-toggle="tooltip" title="Twitter">
                            <i class="bi bi-twitter fs-5"></i>
                        </a>
                        <a href="#" class="text-white-50 me-2" data-bs-toggle="tooltip" title="Instagram">
                            <i class="bi bi-instagram fs-5"></i>
                        </a>
                        <a href="#" class="text-white-50 me-2" data-bs-toggle="tooltip" title="LinkedIn">
                            <i class="bi bi-linkedin fs-5"></i>
                        </a>
                        <a href="#" class="text-white-50" data-bs-toggle="tooltip" title="YouTube">
                            <i class="bi bi-youtube fs-5"></i>
                        </a>
                    </div>
                </div>
                
                <!-- Quick Links -->
                <div class="col-lg-2 col-md-6 mb-4">
                    <h5 class="text-uppercase mb-4">Quick Links</h5>
                    <ul class="list-unstyled">
                        <li class="mb-2">
                            <a href="#features" class="text-white-50 text-decoration-none">
                                <i class="bi bi-chevron-right"></i> Features
                            </a>
                        </li>
                        <li class="mb-2">
                            <a href="#about" class="text-white-50 text-decoration-none">
                                <i class="bi bi-chevron-right"></i> About Us
                            </a>
                        </li>
                        <li class="mb-2">
                            <a href="#testimonials" class="text-white-50 text-decoration-none">
                                <i class="bi bi-chevron-right"></i> Testimonials
                            </a>
                        </li>
                        <li class="mb-2">
                            <a href="<?= BASE_URL ?>auth/register.php" class="text-white-50 text-decoration-none">
                                <i class="bi bi-chevron-right"></i> Register
                            </a>
                        </li>
                        <li class="mb-2">
                            <a href="<?= BASE_URL ?>auth/index.php" class="text-white-50 text-decoration-none">
                                <i class="bi bi-chevron-right"></i> Login
                            </a>
                        </li>
                    </ul>
                </div>
                
                <!-- Contact Info -->
                <div class="col-lg-3 col-md-6 mb-4">
                    <h5 class="text-uppercase mb-4">Contact Info</h5>
                    <ul class="list-unstyled text-white-50">
                        <li class="mb-3">
                            <i class="bi bi-geo-alt text-warning me-2"></i> 
                            123 Farm Street, Nakuru, Kenya
                        </li>
                        <li class="mb-3">
                            <i class="bi bi-envelope text-warning me-2"></i> 
                            <a href="mailto:support@livestockhealth.com" class="text-white-50 text-decoration-none">
                                support@livestockhealth.com
                            </a>
                        </li>
                        <li class="mb-3">
                            <i class="bi bi-telephone text-warning me-2"></i> 
                            <a href="tel:+254713505483" class="text-white-50 text-decoration-none">
                                +254 713 505 483
                            </a>
                        </li>
                        <li class="mb-3">
                            <i class="bi bi-clock text-warning me-2"></i> 
                            Mon - Fri: 8:00 AM - 6:00 PM
                        </li>
                    </ul>
                </div>
                
                <!-- Newsletter & Security -->
                <div class="col-lg-3 col-md-6 mb-4">
                    <h5 class="text-uppercase mb-4">Newsletter</h5>
                    <p class="text-white-50">Subscribe for updates and farming tips.</p>
                    <div class="input-group mb-3">
                        <input type="email" class="form-control bg-dark text-white border-secondary" 
                               placeholder="Your email" id="newsletterEmail">
                        <button class="btn btn-warning" type="button" onclick="alert('Newsletter feature coming soon!')">
                            <i class="bi bi-send"></i>
                        </button>
                    </div>
                    
                    <hr class="bg-secondary">
                    
                    <!-- Security Badge -->
                    <div class="d-flex align-items-center text-white-50 mb-2">
                        <i class="bi bi-shield-check text-warning fs-4 me-2"></i>
                        <span>Secure & Encrypted</span>
                    </div>
                    
                    <!-- System Status -->
                    <div class="d-flex align-items-center text-white-50">
                        <i class="bi bi-check-circle-fill text-success me-2"></i>
                        <span>System Status: <span class="text-success">Online</span></span>
                    </div>
                </div>
            </div>
            
            <!-- Footer Bottom -->
            <div class="footer-bottom mt-4 pt-4 border-top border-secondary">
                <div class="row">
                    <div class="col-md-6 text-md-start text-center mb-3 mb-md-0">
                        <p class="text-white-50 small mb-0">
                            &copy; <?= date('Y') ?> Livestock Health Monitoring And Tracking System. All rights reserved.
                        </p>
                    </div>
                    <div class="col-md-6 text-md-end text-center">
                        <a href="#" class="text-white-50 text-decoration-none small me-3">Privacy Policy</a>
                        <a href="#" class="text-white-50 text-decoration-none small me-3">Terms of Service</a>
                        <a href="#" class="text-white-50 text-decoration-none small">Cookie Policy</a>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script src="<?= BASE_URL ?>assets/js/script.js"></script>
    
    <script>
        // Initialize AOS
        AOS.init({
            duration: 1000,
            once: true
        });
        
        // Initialize tooltips
        document.addEventListener('DOMContentLoaded', function() {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        });
        
        // Smooth scroll for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });
        
        // Newsletter subscription
        document.getElementById('newsletterEmail')?.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                alert('Newsletter feature coming soon!');
            }
        });
    </script>
</body>
</html>