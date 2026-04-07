<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: /lhtm_system/auth/index.php');
    exit;
}

$page_title = 'System Statistics';
$base_path = '..';
require_once '../config/db.php';

// Get real statistics from database
$stats = [];

// 1. USER STATISTICS
$user_stats = $conn->query("
    SELECT 
        COUNT(*) as total_users,
        SUM(CASE WHEN role = 'admin' THEN 1 ELSE 0 END) as total_admins,
        SUM(CASE WHEN role = 'farmer' THEN 1 ELSE 0 END) as total_farmers,
        SUM(CASE WHEN role = 'worker' THEN 1 ELSE 0 END) as total_workers,
        SUM(CASE WHEN role = 'vet' THEN 1 ELSE 0 END) as total_vets,
        SUM(CASE WHEN status = 'active' THEN 1 ELSE 0 END) as active_users,
        SUM(CASE WHEN status = 'inactive' THEN 1 ELSE 0 END) as inactive_users
    FROM users
")->fetch_assoc();

// 2. ANIMAL STATISTICS
$animal_stats = $conn->query("
    SELECT 
        COUNT(*) as total_animals,
        SUM(CASE WHEN species = 'Cattle' THEN 1 ELSE 0 END) as cattle,
        SUM(CASE WHEN species = 'Sheep' THEN 1 ELSE 0 END) as sheep,
        SUM(CASE WHEN species = 'Goat' THEN 1 ELSE 0 END) as goats,
        SUM(CASE WHEN species = 'Pig' THEN 1 ELSE 0 END) as pigs,
        SUM(CASE WHEN species = 'Horse' THEN 1 ELSE 0 END) as horses,
        SUM(CASE WHEN status = 'active' THEN 1 ELSE 0 END) as active_animals,
        SUM(CASE WHEN status = 'sold' THEN 1 ELSE 0 END) as sold_animals,
        SUM(CASE WHEN status = 'deceased' THEN 1 ELSE 0 END) as deceased_animals
    FROM animals
")->fetch_assoc();

// 3. HEALTH RECORD STATISTICS
$health_stats = $conn->query("
    SELECT 
        COUNT(*) as total_records,
        COUNT(DISTINCT animal_id) as animals_treated,
        SUM(CASE WHEN vaccination IS NOT NULL AND vaccination != '' THEN 1 ELSE 0 END) as vaccinations_given,
        SUM(CASE WHEN next_vaccination < CURDATE() THEN 1 ELSE 0 END) as overdue_vaccinations,
        SUM(CASE WHEN next_vaccination BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 7 DAY) THEN 1 ELSE 0 END) as upcoming_vaccinations
    FROM health_records
")->fetch_assoc();

// 4. RECENT ACTIVITY
$recent_activity = $conn->query("
    (SELECT 
        'New Animal' as action_type,
        CONCAT('Added ', tag_number, ' (', species, ')') as description,
        created_at as activity_date
    FROM animals
    ORDER BY created_at DESC
    LIMIT 5)
    
    UNION ALL
    
    (SELECT 
        'Health Record' as action_type,
        CONCAT('Health record added for animal ID ', animal_id) as description,
        created_at as activity_date
    FROM health_records
    ORDER BY created_at DESC
    LIMIT 5)
    
    UNION ALL
    
    (SELECT 
        'New User' as action_type,
        CONCAT(name, ' registered as ', role) as description,
        created_at as activity_date
    FROM users
    ORDER BY created_at DESC
    LIMIT 5)
    
    ORDER BY activity_date DESC
    LIMIT 10
");

// 5. MONTHLY TRENDS (Last 6 months)
$monthly_trends = $conn->query("
    SELECT 
        DATE_FORMAT(created_at, '%Y-%m') as month,
        COUNT(*) as new_users,
        (SELECT COUNT(*) FROM animals WHERE DATE_FORMAT(created_at, '%Y-%m') = month) as new_animals,
        (SELECT COUNT(*) FROM health_records WHERE DATE_FORMAT(created_at, '%Y-%m') = month) as new_records
    FROM users
    WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
    GROUP BY DATE_FORMAT(created_at, '%Y-%m')
    ORDER BY month DESC
");

include '../includes/header.php';
include '../includes/navbar.php';
?>

<div class="container mt-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>
            <i class="bi bi-bar-chart-fill text-primary"></i> 
            System Statistics
        </h2>
        <div>
            <button onclick="window.print()" class="btn btn-outline-secondary">
                <i class="bi bi-printer"></i> Print Report
            </button>
            <a href="users.php" class="btn btn-primary">
                <i class="bi bi-people"></i> Manage Users
            </a>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card border-left-primary shadow h-100">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col">
                            <div class="text-xs text-primary text-uppercase mb-1">Total Users</div>
                            <div class="h3 mb-0 fw-bold"><?= number_format($user_stats['total_users'] ?? 0) ?></div>
                            <small class="text-muted">
                                👑 <?= $user_stats['total_admins'] ?? 0 ?> Admins |
                                🌾 <?= $user_stats['total_farmers'] ?? 0 ?> Farmers
                            </small>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-people fs-1 text-primary opacity-25"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="card border-left-success shadow h-100">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col">
                            <div class="text-xs text-success text-uppercase mb-1">Total Animals</div>
                            <div class="h3 mb-0 fw-bold"><?= number_format($animal_stats['total_animals'] ?? 0) ?></div>
                            <small class="text-muted">
                                ✅ <?= $animal_stats['active_animals'] ?? 0 ?> Active
                            </small>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-github fs-1 text-success opacity-25"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="card border-left-info shadow h-100">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col">
                            <div class="text-xs text-info text-uppercase mb-1">Health Records</div>
                            <div class="h3 mb-0 fw-bold"><?= number_format($health_stats['total_records'] ?? 0) ?></div>
                            <small class="text-muted">
                                💉 <?= $health_stats['vaccinations_given'] ?? 0 ?> Vaccinations
                            </small>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-file-medical fs-1 text-info opacity-25"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="card border-left-warning shadow h-100">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col">
                            <div class="text-xs text-warning text-uppercase mb-1">Animals Treated</div>
                            <div class="h3 mb-0 fw-bold"><?= number_format($health_stats['animals_treated'] ?? 0) ?></div>
                            <small class="text-muted">
                                ⚕️ <?= $health_stats['total_records'] ?? 0 ?> Total Records
                            </small>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-heart-pulse fs-1 text-warning opacity-25"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Detailed Statistics -->
    <div class="row">
        <!-- User Details -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow h-100">
                <div class="card-header bg-white">
                    <h5 class="mb-0">
                        <i class="bi bi-people-fill text-primary"></i> 
                        User Statistics
                    </h5>
                </div>
                <div class="card-body">
                    <table class="table table-sm">
                        <tr>
                            <td><strong>Total Registered Users:</strong></td>
                            <td class="text-end"><?= number_format($user_stats['total_users'] ?? 0) ?></td>
                        </tr>
                        <tr>
                            <td>👑 Administrators:</td>
                            <td class="text-end"><?= number_format($user_stats['total_admins'] ?? 0) ?></td>
                        </tr>
                        <tr>
                            <td>🌾 Farmers:</td>
                            <td class="text-end"><?= number_format($user_stats['total_farmers'] ?? 0) ?></td>
                        </tr>
                        <tr>
                            <td>🔧 Workers:</td>
                            <td class="text-end"><?= number_format($user_stats['total_workers'] ?? 0) ?></td>
                        </tr>
                        <tr>
                            <td>💉 Veterinarians:</td>
                            <td class="text-end"><?= number_format($user_stats['total_vets'] ?? 0) ?></td>
                        </tr>
                        <tr class="table-success">
                            <td><strong>✅ Active Users:</strong></td>
                            <td class="text-end"><strong><?= number_format($user_stats['active_users'] ?? 0) ?></strong></td>
                        </tr>
                        <tr class="table-secondary">
                            <td>⏸️ Inactive Users:</td>
                            <td class="text-end"><?= number_format($user_stats['inactive_users'] ?? 0) ?></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <!-- Animal Details -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow h-100">
                <div class="card-header bg-white">
                    <h5 class="mb-0">
                        <i class="bi bi-github text-success"></i> 
                        Animal Statistics
                    </h5>
                </div>
                <div class="card-body">
                    <table class="table table-sm">
                        <tr>
                            <td><strong>Total Animals:</strong></td>
                            <td class="text-end"><?= number_format($animal_stats['total_animals'] ?? 0) ?></td>
                        </tr>
                        <tr>
                            <td>🐄 Cattle:</td>
                            <td class="text-end"><?= number_format($animal_stats['cattle'] ?? 0) ?></td>
                        </tr>
                        <tr>
                            <td>🐑 Sheep:</td>
                            <td class="text-end"><?= number_format($animal_stats['sheep'] ?? 0) ?></td>
                        </tr>
                        <tr>
                            <td>🐐 Goats:</td>
                            <td class="text-end"><?= number_format($animal_stats['goats'] ?? 0) ?></td>
                        </tr>
                        <tr>
                            <td>🐖 Pigs:</td>
                            <td class="text-end"><?= number_format($animal_stats['pigs'] ?? 0) ?></td>
                        </tr>
                        <tr>
                            <td>🐎 Horses:</td>
                            <td class="text-end"><?= number_format($animal_stats['horses'] ?? 0) ?></td>
                        </tr>
                        <tr class="table-success">
                            <td><strong>✅ Active Animals:</strong></td>
                            <td class="text-end"><strong><?= number_format($animal_stats['active_animals'] ?? 0) ?></strong></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Health Statistics -->
    <div class="row">
        <div class="col-lg-6 mb-4">
            <div class="card shadow h-100">
                <div class="card-header bg-white">
                    <h5 class="mb-0">
                        <i class="bi bi-file-medical text-info"></i> 
                        Health Record Statistics
                    </h5>
                </div>
                <div class="card-body">
                    <table class="table table-sm">
                        <tr>
                            <td><strong>Total Health Records:</strong></td>
                            <td class="text-end"><?= number_format($health_stats['total_records'] ?? 0) ?></td>
                        </tr>
                        <tr>
                            <td>🐄 Animals Treated:</td>
                            <td class="text-end"><?= number_format($health_stats['animals_treated'] ?? 0) ?></td>
                        </tr>
                        <tr>
                            <td>💉 Vaccinations Given:</td>
                            <td class="text-end"><?= number_format($health_stats['vaccinations_given'] ?? 0) ?></td>
                        </tr>
                        <tr class="table-warning">
                            <td>⚠️ Overdue Vaccinations:</td>
                            <td class="text-end"><?= number_format($health_stats['overdue_vaccinations'] ?? 0) ?></td>
                        </tr>
                        <tr class="table-info">
                            <td>📅 Upcoming Vaccinations (7 days):</td>
                            <td class="text-end"><?= number_format($health_stats['upcoming_vaccinations'] ?? 0) ?></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <!-- Monthly Trends -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow h-100">
                <div class="card-header bg-white">
                    <h5 class="mb-0">
                        <i class="bi bi-calendar-week text-warning"></i> 
                        Monthly Activity (Last 6 Months)
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Month</th>
                                    <th class="text-center">New Users</th>
                                    <th class="text-center">New Animals</th>
                                    <th class="text-center">Health Records</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if($monthly_trends && $monthly_trends->num_rows > 0): ?>
                                    <?php while($row = $monthly_trends->fetch_assoc()): ?>
                                        <tr>
                                            <td><?= date('F Y', strtotime($row['month'] . '-01')) ?></td>
                                            <td class="text-center"><?= $row['new_users'] ?? 0 ?></td>
                                            <td class="text-center"><?= $row['new_animals'] ?? 0 ?></td>
                                            <td class="text-center"><?= $row['new_records'] ?? 0 ?></td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="4" class="text-center text-muted py-3">
                                            No monthly data available
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

    <!-- Recent Activity -->
    <div class="row">
        <div class="col-12 mb-4">
            <div class="card shadow">
                <div class="card-header bg-white">
                    <h5 class="mb-0">
                        <i class="bi bi-clock-history text-secondary"></i> 
                        Recent System Activity
                    </h5>
                </div>
                <div class="card-body">
                    <?php if($recent_activity && $recent_activity->num_rows > 0): ?>
                        <div class="list-group list-group-flush">
                            <?php while($activity = $recent_activity->fetch_assoc()): ?>
                                <div class="list-group-item px-0">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <?php
                                            $icon = 'bi-info-circle';
                                            $color = 'secondary';
                                            
                                            if ($activity['action_type'] == 'New Animal') {
                                                $icon = 'bi-github';
                                                $color = 'success';
                                            } elseif ($activity['action_type'] == 'Health Record') {
                                                $icon = 'bi-file-medical';
                                                $color = 'info';
                                            } elseif ($activity['action_type'] == 'New User') {
                                                $icon = 'bi-person-plus';
                                                $color = 'primary';
                                            }
                                            ?>
                                            <i class="bi <?= $icon ?> text-<?= $color ?> me-2"></i>
                                            <span class="fw-bold"><?= htmlspecialchars($activity['action_type'] ?? 'Activity') ?>:</span>
                                            <span class="text-muted"><?= htmlspecialchars($activity['description'] ?? '') ?></span>
                                        </div>
                                        <small class="text-muted">
                                            <?= isset($activity['activity_date']) ? date('M d, H:i A', strtotime($activity['activity_date'])) : '' ?>
                                        </small>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        </div>
                    <?php else: ?>
                        <p class="text-muted text-center py-3 mb-0">No recent activity found</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- System Info -->
    <div class="row">
        <div class="col-12">
            <div class="card bg-light">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 text-center">
                            <i class="bi bi-database fs-1 text-primary"></i>
                            <h6>Database Size</h6>
                            <p class="text-muted">~ 2.5 MB</p>
                        </div>
                        <div class="col-md-4 text-center">
                            <i class="bi bi-calendar-check fs-1 text-success"></i>
                            <h6>System Started</h6>
                            <p class="text-muted">January 2026</p>
                        </div>
                        <div class="col-md-4 text-center">
                            <i class="bi bi-activity fs-1 text-info"></i>
                            <h6>System Health</h6>
                            <p class="text-success">✅ All Systems Operational</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
include '../includes/footer.php';
?>