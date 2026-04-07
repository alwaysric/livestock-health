<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Define base URL for consistent linking
if (!defined('BASE_URL')) {
    define('BASE_URL', '/lhtm_system/');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($page_title) ? $page_title . ' - ' : '' ?>LiveStockHealth</title>
    
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
        
        .navbar-custom .dropdown-menu {
            border: none;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            padding: 10px 0;
            margin-top: 10px;
            animation: fadeIn 0.3s ease-out;
        }
        
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .navbar-custom .dropdown-item {
            padding: 8px 20px;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        
        .navbar-custom .dropdown-item:hover {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white !important;
            transform: translateX(5px);
        }
        
        .navbar-custom .dropdown-item i {
            margin-right: 8px;
            width: 20px;
        }
        
        .navbar-custom .badge {
            font-size: 0.7rem;
            padding: 0.35rem 0.65rem;
            border-radius: 20px;
            margin-left: 5px;
        }
        
        .navbar-custom .user-avatar {
            width: 35px;
            height: 35px;
            border-radius: 50%;
            background: rgba(255,255,255,0.2);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-right: 8px;
        }
        
        .navbar-custom .user-avatar i {
            font-size: 1.2rem;
            margin: 0;
            color: white;
        }
        
        /* Breadcrumb Styles */
        .breadcrumb-custom {
            background: white;
            padding: 0.75rem 1.5rem;
            border-radius: 50px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            margin: 1rem 0;
        }
        
        .breadcrumb-custom .breadcrumb-item a {
            color: #667eea;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        
        .breadcrumb-custom .breadcrumb-item a:hover {
            color: #764ba2;
            text-decoration: underline;
        }
        
        .breadcrumb-custom .breadcrumb-item.active {
            color: #6c757d;
            font-weight: 500;
        }
        
        .breadcrumb-custom .breadcrumb-item + .breadcrumb-item::before {
            content: "→";
            color: #667eea;
        }
        
        /* Responsive adjustments */
        @media (max-width: 991px) {
            .navbar-custom .navbar-brand {
                font-size: 1.5rem;
            }
            
            .navbar-custom .navbar-brand i {
                font-size: 1.7rem;
            }
            
            .navbar-custom .btn-outline-light {
                margin-left: 0 !important;
                margin-top: 10px;
            }
            
            .breadcrumb-custom {
                border-radius: 15px;
                padding: 0.75rem 1rem;
            }
        }
        
        @media (max-width: 768px) {
            .navbar-custom .navbar-brand {
                font-size: 1.3rem;
            }
            
            .navbar-custom .navbar-brand i {
                font-size: 1.5rem;
            }
        }
        
        /* Loading animation for navigation */
        .nav-link {
            position: relative;
            overflow: hidden;
        }
        
        .nav-link::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            width: 0;
            height: 2px;
            background: #ffd700;
            transition: all 0.3s ease;
            transform: translateX(-50%);
        }
        
        .nav-link:hover::after {
            width: 80%;
        }
        
        .nav-link.active::after {
            width: 80%;
            background: #ffd700;
        }
    </style>
</head>
<body>