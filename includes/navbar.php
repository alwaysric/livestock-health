<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Define BASE_URL if not already defined
if (!defined('BASE_URL')) {
    define('BASE_URL', '/lhtm_system/');
}

// Get the current file and directory for active link detection
$current_file = basename($_SERVER['PHP_SELF']);
$current_dir = basename(dirname($_SERVER['PHP_SELF']));

// Function to check if a link is active
function isActive($link, $current_file, $current_dir) {
    if (strpos($link, $current_file) !== false) {
        return 'active';
    }
    if (strpos($link, $current_dir) !== false && $current_dir != 'lhtm_system' && $current_dir != 'includes') {
        return 'active';
    }
    return '';
}
?>

<!-- Main Navigation Header - Identical on ALL Pages -->
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
            <?php if(isset($_SESSION['user_id'])): ?>
                <!-- Navigation for Logged-in Users -->
                <ul class="navbar-nav me-auto">
                    <?php if($_SESSION['role'] === 'admin'): ?>
                        <!-- Admin Specific Links -->
                        <li class="nav-item">
                            <a class="nav-link <?= isActive('admin_dashboard.php', $current_file, $current_dir) ?>" 
                               href="<?= BASE_URL ?>admin_dashboard.php">
                                <i class="bi bi-shield-shaded"></i> Admin
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= isActive('users.php', $current_file, $current_dir) ?>" 
                               href="<?= BASE_URL ?>admin/users.php">
                                <i class="bi bi-people"></i> Users
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= isActive('system_stats.php', $current_file, $current_dir) ?>" 
                               href="<?= BASE_URL ?>admin/system_stats.php">
                                <i class="bi bi-bar-chart"></i> Stats
                            </a>
                        </li>
                    <?php endif; ?>
                    
                    <!-- Common Links for All Users -->
                    <li class="nav-item">
                        <a class="nav-link <?= isActive('dashboard.php', $current_file, $current_dir) ?>" 
                           href="<?= BASE_URL ?>dashboard.php">
                            <i class="bi bi-speedometer2"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= isActive('animals', $current_file, $current_dir) ?>" 
                           href="<?= BASE_URL ?>animals/index.php">
                            <i class="bi bi-github"></i> Animals
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= isActive('health', $current_file, $current_dir) ?>" 
                           href="<?= BASE_URL ?>health/index.php">
                            <i class="bi bi-file-medical"></i> Health
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= isActive('alerts', $current_file, $current_dir) ?>" 
                           href="<?= BASE_URL ?>alerts/index.php">
                            <i class="bi bi-bell"></i> Alerts
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= isActive('reports', $current_file, $current_dir) ?>" 
                           href="<?= BASE_URL ?>reports/index.php">
                            <i class="bi bi-file-text"></i> Reports
                        </a>
                    </li>
                </ul>
                
                <!-- User Menu Dropdown -->
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" 
                           data-bs-toggle="dropdown" aria-expanded="false">
                            <span class="user-avatar">
                                <i class="bi bi-person-circle"></i>
                            </span>
                            <?= htmlspecialchars($_SESSION['name'] ?? 'User') ?>
                            <span class="badge bg-<?= $_SESSION['role'] === 'admin' ? 'danger' : 'warning' ?>">
                                <?= ucfirst($_SESSION['role']) ?>
                            </span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                            <li>
                                <a class="dropdown-item" href="<?= BASE_URL ?>profile/index.php">
                                    <i class="bi bi-person"></i> My Profile
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="<?= BASE_URL ?>settings/index.php">
                                    <i class="bi bi-gear"></i> Settings
                                </a>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <a class="dropdown-item text-danger" href="<?= BASE_URL ?>auth/logout.php">
                                    <i class="bi bi-box-arrow-right"></i> Logout
                                </a>
                            </li>
                        </ul>
                    </li>
                </ul>
            <?php else: ?>
                <!-- Navigation for Guests (Not Logged In) -->
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link <?= isActive('index.php', $current_file, $current_dir) ?>" 
                           href="<?= BASE_URL ?>index.php#features">
                            <i class="bi bi-star"></i> Features
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= isActive('index.php', $current_file, $current_dir) ?>" 
                           href="<?= BASE_URL ?>index.php#about">
                            <i class="bi bi-info-circle"></i> About
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= isActive('register.php', $current_file, $current_dir) ?>" 
                           href="<?= BASE_URL ?>auth/register.php">
                            <i class="bi bi-person-plus"></i> Register
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="btn btn-outline-light ms-3" href="<?= BASE_URL ?>auth/index.php">
                            <i class="bi bi-box-arrow-in-right"></i> Login
                        </a>
                    </li>
                </ul>
            <?php endif; ?>
        </div>
    </div>
</nav>

<!-- Breadcrumb Navigation (for logged-in users, except dashboard) -->
<?php if(isset($_SESSION['user_id']) && $current_file != 'dashboard.php' && $current_file != 'admin_dashboard.php' && $current_file != 'index.php'): ?>
<div class="container mt-3">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb breadcrumb-custom">
            <li class="breadcrumb-item">
                <a href="<?= BASE_URL . ($_SESSION['role'] === 'admin' ? 'admin_dashboard.php' : 'dashboard.php') ?>">
                    <i class="bi bi-house-door"></i> Dashboard
                </a>
            </li>
            <?php
            // Add folder as breadcrumb if not root
            if($current_dir != 'lhtm_system' && $current_dir != '' && $current_dir != 'admin' && $current_dir != 'includes') {
                echo '<li class="breadcrumb-item">';
                echo '<a href="' . BASE_URL . $current_dir . '/index.php">';
                echo '<i class="bi bi-folder"></i> ' . ucfirst($current_dir);
                echo '</a>';
                echo '</li>';
            }
            
            // Add current page if not index
            if($current_file != 'index.php') {
                $page_name = str_replace(['.php', '_'], ['', ' '], $current_file);
                echo '<li class="breadcrumb-item active">' . ucfirst($page_name) . '</li>';
            }
            ?>
        </ol>
    </nav>
</div>
<?php endif; ?>

<main>