<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: auth/index.php');
    exit;
}

$page_title = 'Dashboard';
$base_path = '';
require_once 'config/db.php';

// Get user statistics
$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'];

// Total animals in system
$animalCount = $conn->query("SELECT COUNT(*) AS total FROM animals")->fetch_assoc()['total'];

// Total health records
$recordCount = $conn->query("SELECT COUNT(*) AS total FROM health_records")->fetch_assoc()['total'];

// Due vaccinations
$dueCount = $conn->query("
    SELECT COUNT(*) AS total 
    FROM health_records 
    WHERE next_vaccination <= CURDATE()
")->fetch_assoc()['total'];

// Animals registered by current user
$myAnimals = $conn->query("
    SELECT COUNT(*) AS total 
    FROM animals 
    WHERE registered_by = $user_id
")->fetch_assoc()['total'];

// Recent health records
$recentHealth = $conn->query("
    SELECT h.*, a.tag_number 
    FROM health_records h
    JOIN animals a ON h.animal_id = a.animal_id
    ORDER BY h.record_date DESC
    LIMIT 5
");

// Upcoming vaccinations (next 7 days)
$upcomingVaccinations = $conn->query("
    SELECT a.tag_number, h.vaccination, h.next_vaccination
    FROM health_records h
    JOIN animals a ON h.animal_id = a.animal_id
    WHERE h.next_vaccination BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 7 DAY)
    ORDER BY h.next_vaccination ASC
");

// Recent animals added
$recentAnimals = $conn->query("
    SELECT * FROM animals 
    ORDER BY created_at DESC 
    LIMIT 5
");

include 'includes/header.php';
include 'includes/navbar.php';
?>

<div class="container mt-4">
    <!-- Welcome Banner -->
    <div class="alert alert-primary alert-animated d-flex justify-content-between align-items-center">
        <div>
            <h4 class="alert-heading">
                Welcome back, <?= htmlspecialchars($_SESSION['name']) ?>! 👋
            </h4>
            <p class="mb-0">
                You're logged in as <strong class="badge bg-warning text-dark"><?= ucfirst($role) ?></strong>. 
                Here's your farm summary.
            </p>
        </div>
        <span class="badge bg-light text-dark p-3">
            <i class="bi bi-calendar3"></i> 
            <?= date('l, F d, Y') ?>
        </span>
    </div>
    
    <!-- Statistics Cards -->
    <div class="row mt-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stat-card" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <div class="card-body">
                    <h6 class="text-white-50">Total Animals</h6>
                    <h2 class="text-white display-6"><?= $animalCount ?></h2>
                    <a href="animals/index.php" class="text-white">View all →</a>
                    <i class="bi bi-github stat-icon"></i>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stat-card" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);">
                <div class="card-body">
                    <h6 class="text-white-50">Health Records</h6>
                    <h2 class="text-white display-6"><?= $recordCount ?></h2>
                    <a href="health/index.php" class="text-white">View all →</a>
                    <i class="bi bi-file-medical stat-icon"></i>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stat-card" style="background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);">
                <div class="card-body">
                    <h6 class="text-white-50">Due Vaccinations</h6>
                    <h2 class="text-white display-6"><?= $dueCount ?></h2>
                    <a href="alerts/index.php" class="text-white">Check now →</a>
                    <i class="bi bi-exclamation-triangle stat-icon"></i>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stat-card" style="background: linear-gradient(135deg, #3a1c71 0%, #d76d77 50%, #ffaf7b 100%);">
                <div class="card-body">
                    <h6 class="text-white-50">My Registered Animals</h6>
                    <h2 class="text-white display-6"><?= $myAnimals ?></h2>
                    <a href="animals/index.php?my=1" class="text-white">View mine →</a>
                    <i class="bi bi-person-badge stat-icon"></i>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Quick Actions -->
    <div class="row mt-2">
        <div class="col-lg-8">
            <div class="card shadow h-100">
                <div class="card-header bg-white py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="bi bi-lightning-charge-fill text-warning"></i> Quick Actions
                        </h5>
                        <span class="badge bg-primary">Today</span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-sm-6 col-md-4">
                            <a href="animals/add.php" class="btn btn-outline-primary w-100 p-3">
                                <i class="bi bi-plus-circle fs-2 d-block mb-2"></i>
                                Add Animal
                            </a>
                        </div>
                        <div class="col-sm-6 col-md-4">
                            <a href="health/add.php" class="btn btn-outline-success w-100 p-3">
                                <i class="bi bi-file-plus fs-2 d-block mb-2"></i>
                                Health Record
                            </a>
                        </div>
                        <div class="col-sm-6 col-md-4">
                            <a href="alerts/index.php" class="btn btn-outline-warning w-100 p-3">
                                <i class="bi bi-bell fs-2 d-block mb-2"></i>
                                Check Alerts
                            </a>
                        </div>
                        <div class="col-sm-6 col-md-4">
                            <a href="reports/index.php" class="btn btn-outline-info w-100 p-3">
                                <i class="bi bi-file-text fs-2 d-block mb-2"></i>
                                Generate Report
                            </a>
                        </div>
                        <div class="col-sm-6 col-md-4">
                            <a href="profile/index.php" class="btn btn-outline-secondary w-100 p-3">
                                <i class="bi bi-person fs-2 d-block mb-2"></i>
                                My Profile
                            </a>
                        </div>
                        <div class="col-sm-6 col-md-4">
                            <a href="settings/index.php" class="btn btn-outline-dark w-100 p-3">
                                <i class="bi bi-gear fs-2 d-block mb-2"></i>
                                Settings
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <div class="card shadow h-100">
                <div class="card-header bg-white py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="bi bi-calendar-check text-success"></i> Upcoming Vaccinations
                        </h5>
                        <span class="badge bg-success">Next 7 days</span>
                    </div>
                </div>
                <div class="card-body">
                    <?php if($upcomingVaccinations && $upcomingVaccinations->num_rows > 0): ?>
                        <div class="list-group list-group-flush">
                            <?php while($vaccine = $upcomingVaccinations->fetch_assoc()): ?>
                                <div class="list-group-item px-0">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <strong class="text-primary"><?= htmlspecialchars($vaccine['tag_number']) ?></strong>
                                            <br>
                                            <small class="text-muted"><?= htmlspecialchars($vaccine['vaccination']) ?></small>
                                        </div>
                                        <div class="text-end">
                                            <span class="badge bg-warning text-dark">
                                                <?= date('M d', strtotime($vaccine['next_vaccination'])) ?>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-4">
                            <i class="bi bi-check-circle text-success fs-1"></i>
                            <p class="mt-2 mb-0">No upcoming vaccinations</p>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="card-footer bg-white">
                    <a href="alerts/index.php" class="text-decoration-none">View all alerts →</a>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Recent Activity -->
    <div class="row mt-4">
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-header bg-white py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="bi bi-clock-history text-info"></i> Recently Added Animals
                        </h5>
                        <a href="animals/index.php" class="btn btn-sm btn-outline-primary">View All</a>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Tag</th>
                                    <th>Species</th>
                                    <th>Breed</th>
                                    <th>Added</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if($recentAnimals && $recentAnimals->num_rows > 0): ?>
                                    <?php while($animal = $recentAnimals->fetch_assoc()): ?>
                                        <tr>
                                            <td><strong><?= htmlspecialchars($animal['tag_number']) ?></strong></td>
                                            <td><?= htmlspecialchars($animal['species']) ?></td>
                                            <td><?= htmlspecialchars($animal['breed']) ?></td>
                                            <td>
                                                <small class="text-muted">
                                                    <?= date('M d, Y', strtotime($animal['created_at'])) ?>
                                                </small>
                                            </td>
                                            <td>
                                                <a href="animals/view_single.php?id=<?= $animal['animal_id'] ?>" 
                                                   class="btn btn-sm btn-info" data-bs-toggle="tooltip" title="View Details">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="5" class="text-center py-4">
                                            <i class="bi bi-inbox fs-1 text-muted d-block mb-2"></i>
                                            <p class="mb-0">No animals added yet</p>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-header bg-white py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="bi bi-file-medical text-success"></i> Recent Health Records
                        </h5>
                        <a href="health/index.php" class="btn btn-sm btn-outline-success">View All</a>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Animal</th>
                                    <th>Diagnosis</th>
                                    <th>Date</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if($recentHealth && $recentHealth->num_rows > 0): ?>
                                    <?php while($record = $recentHealth->fetch_assoc()): ?>
                                        <tr>
                                            <td><strong><?= htmlspecialchars($record['tag_number']) ?></strong></td>
                                            <td><?= htmlspecialchars(substr($record['diagnosis'], 0, 30)) ?>...</td>
                                            <td>
                                                <small class="text-muted">
                                                    <?= date('M d, Y', strtotime($record['record_date'])) ?>
                                                </small>
                                            </td>
                                            <td>
                                                <a href="health/view.php?animal_id=<?= $record['animal_id'] ?>" 
                                                   class="btn btn-sm btn-success" data-bs-toggle="tooltip" title="View Records">
                                                    <i class="bi bi-file-text"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="4" class="text-center py-4">
                                            <i class="bi bi-file-medical fs-1 text-muted d-block mb-2"></i>
                                            <p class="mb-0">No health records yet</p>
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
$additional_scripts = '
<script>
    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll(\'[data-bs-toggle="tooltip"]\'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    })
</script>
';

include 'includes/footer.php';
?>