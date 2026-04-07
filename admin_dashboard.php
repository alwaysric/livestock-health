<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: auth/index.php');
    exit;
}

$page_title = 'Admin Dashboard';
$base_path = '';
require_once 'config/db.php';

$user_id = $_SESSION['user_id'];

// Get system statistics
$total_users = $conn->query("SELECT COUNT(*) as total FROM users")->fetch_assoc()['total'];
$total_admins = $conn->query("SELECT COUNT(*) as total FROM users WHERE role = 'admin'")->fetch_assoc()['total'];
$total_farmers = $conn->query("SELECT COUNT(*) as total FROM users WHERE role = 'farmer'")->fetch_assoc()['total'];
$total_workers = $conn->query("SELECT COUNT(*) as total FROM users WHERE role = 'worker'")->fetch_assoc()['total'];
$total_vets = $conn->query("SELECT COUNT(*) as total FROM users WHERE role = 'vet'")->fetch_assoc()['total'];

$total_animals = $conn->query("SELECT COUNT(*) as total FROM animals")->fetch_assoc()['total'];
$total_records = $conn->query("SELECT COUNT(*) as total FROM health_records")->fetch_assoc()['total'];
$active_animals = $conn->query("SELECT COUNT(*) as total FROM animals WHERE status = 'active'")->fetch_assoc()['total'];

// Get recent users
$recent_users = $conn->query("SELECT * FROM users ORDER BY created_at DESC LIMIT 5");

// Get recent logs
$recent_logs = $conn->query("
    SELECT l.*, u.name 
    FROM system_logs l 
    LEFT JOIN users u ON l.user_id = u.user_id 
    ORDER BY l.created_at DESC 
    LIMIT 10
");

include 'includes/header.php';
include 'includes/navbar.php';
?>

<div class="container mt-4">
    <!-- Welcome Banner -->
    <div class="alert alert-warning alert-animated d-flex justify-content-between align-items-center">
        <div>
            <h4 class="alert-heading">
                <i class="bi bi-shield-shaded"></i> Admin Dashboard
            </h4>
            <p class="mb-0">
                Welcome back, <strong><?= htmlspecialchars($_SESSION['name']) ?></strong>! 
                You have full system access.
            </p>
        </div>
        <span class="badge bg-dark p-3">
            <i class="bi bi-calendar3"></i> <?= date('l, F d, Y') ?>
        </span>
    </div>
    
    <!-- System Overview Cards -->
    <div class="row mt-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stat-card" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <div class="card-body">
                    <h6 class="text-white-50">Total Users</h6>
                    <h2 class="text-white display-6"><?= $total_users ?></h2>
                    <small class="text-white">
                        <i class="bi bi-people"></i> 
                        A: <?= $total_admins ?> | F: <?= $total_farmers ?> | W: <?= $total_workers ?> | V: <?= $total_vets ?>
                    </small>
                    <i class="bi bi-people stat-icon"></i>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stat-card" style="background: linear-gradient(135deg, #1cc88a 0%, #169b6b 100%);">
                <div class="card-body">
                    <h6 class="text-white-50">Total Animals</h6>
                    <h2 class="text-white display-6"><?= $total_animals ?></h2>
                    <small class="text-white">
                        <i class="bi bi-check-circle"></i> Active: <?= $active_animals ?>
                    </small>
                    <i class="bi bi-github stat-icon"></i>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stat-card" style="background: linear-gradient(135deg, #36b9cc 0%, #258391 100%);">
                <div class="card-body">
                    <h6 class="text-white-50">Health Records</h6>
                    <h2 class="text-white display-6"><?= $total_records ?></h2>
                    <small class="text-white">Medical & Vaccination records</small>
                    <i class="bi bi-file-medical stat-icon"></i>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stat-card" style="background: linear-gradient(135deg, #f6c23e 0%, #dda20a 100%);">
                <div class="card-body">
                    <h6 class="text-white-50">System Health</h6>
                    <h2 class="text-white display-6">98%</h2>
                    <small class="text-white">Uptime: 30 days</small>
                    <i class="bi bi-activity stat-icon"></i>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Quick Actions -->
    <div class="row mt-2">
        <div class="col-md-8">
            <div class="card shadow h-100">
                <div class="card-header bg-white">
                    <h5 class="mb-0">
                        <i class="bi bi-lightning-charge text-warning"></i> Admin Actions
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-sm-6 col-md-4">
                            <a href="admin/users.php" class="btn btn-outline-primary w-100 p-3">
                                <i class="bi bi-people fs-2 d-block mb-2"></i>
                                Manage Users
                            </a>
                        </div>
                        <div class="col-sm-6 col-md-4">
                            <a href="admin/system_stats.php" class="btn btn-outline-success w-100 p-3">
                                <i class="bi bi-bar-chart fs-2 d-block mb-2"></i>
                                System Stats
                            </a>
                        </div>
                        <div class="col-sm-6 col-md-4">
                            <a href="animals/index.php" class="btn btn-outline-info w-100 p-3">
                                <i class="bi bi-github fs-2 d-block mb-2"></i>
                                All Animals
                            </a>
                        </div>
                        <div class="col-sm-6 col-md-4">
                            <a href="health/index.php" class="btn btn-outline-warning w-100 p-3">
                                <i class="bi bi-file-medical fs-2 d-block mb-2"></i>
                                All Records
                            </a>
                        </div>
                        <div class="col-sm-6 col-md-4">
                            <a href="reports/index.php" class="btn btn-outline-secondary w-100 p-3">
                                <i class="bi bi-file-text fs-2 d-block mb-2"></i>
                                System Report
                            </a>
                        </div>
                        <div class="col-sm-6 col-md-4">
                            <a href="settings/index.php" class="btn btn-outline-dark w-100 p-3">
                                <i class="bi bi-gear fs-2 d-block mb-2"></i>
                                System Settings
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card shadow h-100">
                <div class="card-header bg-white">
                    <h5 class="mb-0">
                        <i class="bi bi-clock-history"></i> Recent Users
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        <?php while($user = $recent_users->fetch_assoc()): ?>
                            <div class="list-group-item">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong><?= htmlspecialchars($user['name']) ?></strong>
                                        <br>
                                        <small class="text-muted">
                                            <span class="badge bg-<?= 
                                                $user['role'] == 'admin' ? 'danger' : 
                                                ($user['role'] == 'vet' ? 'warning' : 
                                                ($user['role'] == 'farmer' ? 'success' : 'info')) 
                                            ?>">
                                                <?= ucfirst($user['role']) ?>
                                            </span>
                                        </small>
                                    </div>
                                    <small class="text-muted">
                                        <?= date('M d', strtotime($user['created_at'])) ?>
                                    </small>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    </div>
                </div>
                <div class="card-footer bg-white">
                    <a href="admin/users.php" class="text-decoration-none">View all users →</a>
                </div>
            </div>
        </div>
    </div>
    
    <!-- System Logs -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="bi bi-journal-text"></i> Recent System Activity
                    </h5>
                    <span class="badge bg-secondary">Last 10 actions</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Time</th>
                                    <th>User</th>
                                    <th>Action</th>
                                    <th>Description</th>
                                    <th>IP Address</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if($recent_logs && $recent_logs->num_rows > 0): ?>
                                    <?php while($log = $recent_logs->fetch_assoc()): ?>
                                        <tr>
                                            <td>
                                                <small><?= date('M d, H:i', strtotime($log['created_at'])) ?></small>
                                            </td>
                                            <td><?= htmlspecialchars($log['name'] ?? 'System') ?></td>
                                            <td>
                                                <span class="badge bg-primary"><?= $log['action'] ?></span>
                                            </td>
                                            <td><?= htmlspecialchars($log['description']) ?></td>
                                            <td><small><?= $log['ip_address'] ?></small></td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="5" class="text-center py-4">
                                            <i class="bi bi-journal-text fs-1 text-muted d-block mb-2"></i>
                                            <p class="mb-0">No system logs found</p>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
include 'includes/footer.php';
?>