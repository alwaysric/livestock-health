<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: /lhtm_system/auth/index.php');
    exit;
}

$page_title = 'My Profile';
$base_path = '..';
require_once '../config/db.php';

$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT * FROM users WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

// If user not found, logout
if (!$user) {
    header('Location: /lhtm_system/auth/logout.php');
    exit;
}

include '../includes/header.php';
include '../includes/navbar.php';
?>

<div class="container mt-4">
    <?php if(isset($_GET['updated'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle"></i> Profile updated successfully!
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    
    <div class="row">
        <!-- Profile Sidebar -->
        <div class="col-lg-4">
            <div class="card shadow">
                <div class="card-body text-center p-4">
                    <div class="profile-avatar mb-3">
                        <?= strtoupper(substr($user['name'] ?? 'U', 0, 1)) ?>
                    </div>
                    
                    <h4 class="fw-bold"><?= htmlspecialchars($user['name'] ?? 'Unknown User') ?></h4>
                    
                    <div class="mb-3">
                        <?php
                        // Define role badges safely
                        $role_badges = [
                            'admin' => 'bg-danger',
                            'farmer' => 'bg-success',
                            'worker' => 'bg-info',
                            'vet' => 'bg-warning text-dark'
                        ];
                        
                        // Get current role with default
                        $current_role = $user['role'] ?? 'farmer';
                        $badge_color = $role_badges[$current_role] ?? 'bg-secondary';
                        ?>
                        <span class="badge <?= $badge_color ?> fs-6">
                            <i class="bi bi-person-badge"></i> <?= ucfirst($current_role) ?>
                        </span>
                        
                        <?php
                        // Status badge
                        $status = $user['status'] ?? 'active';
                        $status_color = $status == 'active' ? 'bg-success' : ($status == 'inactive' ? 'bg-secondary' : 'bg-danger');
                        ?>
                        <span class="badge <?= $status_color ?> fs-6">
                            <i class="bi bi-check-circle"></i> <?= ucfirst($status) ?>
                        </span>
                    </div>
                    
                    <div class="d-grid gap-2 mt-3">
                        <a href="edit.php" class="btn btn-outline-primary">
                            <i class="bi bi-pencil-square"></i> Edit Profile
                        </a>
                        <a href="change_password.php" class="btn btn-outline-warning">
                            <i class="bi bi-key"></i> Change Password
                        </a>
                        <a href="../settings/index.php" class="btn btn-outline-secondary">
                            <i class="bi bi-gear"></i> Settings
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Account Statistics -->
            <div class="card shadow mt-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0">
                        <i class="bi bi-bar-chart"></i> Account Statistics
                    </h5>
                </div>
                <div class="card-body">
                    <?php
                    $member_since = isset($user['created_at']) ? new DateTime($user['created_at']) : new DateTime();
                    $now = new DateTime();
                    $membership_days = $now->diff($member_since)->days;
                    
                    // Get counts safely
                    $animalCount = 0;
                    $recordCount = 0;
                    $vaccineCount = 0;
                    
                    if ($user_id) {
                        $animal_result = $conn->query("SELECT COUNT(*) as count FROM animals WHERE registered_by = $user_id");
                        if ($animal_result) {
                            $animalCount = $animal_result->fetch_assoc()['count'] ?? 0;
                        }
                        
                        $record_result = $conn->query("SELECT COUNT(*) as count FROM health_records WHERE recorded_by = $user_id");
                        if ($record_result) {
                            $recordCount = $record_result->fetch_assoc()['count'] ?? 0;
                        }
                        
                        $vaccine_result = $conn->query("
                            SELECT COUNT(*) as count 
                            FROM health_records 
                            WHERE recorded_by = $user_id 
                            AND vaccination IS NOT NULL 
                            AND vaccination != ''
                        ");
                        if ($vaccine_result) {
                            $vaccineCount = $vaccine_result->fetch_assoc()['count'] ?? 0;
                        }
                    }
                    ?>
                    
                    <div class="d-flex justify-content-between mb-3">
                        <span class="text-muted">Member since:</span>
                        <strong><?= isset($user['created_at']) ? date('M d, Y', strtotime($user['created_at'])) : 'N/A' ?></strong>
                    </div>
                    
                    <div class="d-flex justify-content-between mb-3">
                        <span class="text-muted">Membership days:</span>
                        <strong><?= $membership_days ?> days</strong>
                    </div>
                    
                    <hr>
                    
                    <div class="d-flex justify-content-between mb-2">
                        <span><i class="bi bi-github text-primary"></i> Animals registered:</span>
                        <strong class="text-primary"><?= $animalCount ?></strong>
                    </div>
                    
                    <div class="d-flex justify-content-between mb-2">
                        <span><i class="bi bi-file-medical text-success"></i> Health records:</span>
                        <strong class="text-success"><?= $recordCount ?></strong>
                    </div>
                    
                    <div class="d-flex justify-content-between">
                        <span><i class="bi bi-shield-check text-warning"></i> Vaccinations given:</span>
                        <strong class="text-warning"><?= $vaccineCount ?></strong>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Profile Details -->
        <div class="col-lg-8">
            <div class="card shadow">
                <div class="card-header bg-white">
                    <h5 class="mb-0">
                        <i class="bi bi-info-circle"></i> Profile Information
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <label class="text-muted text-uppercase small">Full Name</label>
                            <p class="fs-5 fw-bold mb-0"><?= htmlspecialchars($user['name'] ?? 'Not set') ?></p>
                        </div>
                        
                        <div class="col-md-6 mb-4">
                            <label class="text-muted text-uppercase small">Email Address</label>
                            <p class="fs-5 mb-0">
                                <i class="bi bi-envelope"></i> 
                                <?= htmlspecialchars($user['email'] ?? 'Not set') ?>
                            </p>
                        </div>
                        
                        <div class="col-md-6 mb-4">
                            <label class="text-muted text-uppercase small">Role</label>
                            <p class="fs-5 mb-0">
                                <?php
                                $role_icons = [
                                    'admin' => '👑',
                                    'farmer' => '🌾',
                                    'worker' => '🔧',
                                    'vet' => '💉'
                                ];
                                $current_role = $user['role'] ?? 'farmer';
                                $icon = $role_icons[$current_role] ?? '👤';
                                echo $icon . ' ' . ucfirst($current_role);
                                ?>
                            </p>
                        </div>
                        
                        <div class="col-md-6 mb-4">
                            <label class="text-muted text-uppercase small">Last Login</label>
                            <p class="fs-5 mb-0">
                                <i class="bi bi-clock"></i> 
                                <?= isset($user['last_login']) && $user['last_login'] ? date('M d, Y H:i A', strtotime($user['last_login'])) : 'First login' ?>
                            </p>
                        </div>
                        
                        <div class="col-md-6 mb-4">
                            <label class="text-muted text-uppercase small">Account Created</label>
                            <p class="fs-5 mb-0">
                                <i class="bi bi-calendar"></i> 
                                <?= isset($user['created_at']) ? date('M d, Y', strtotime($user['created_at'])) : 'N/A' ?>
                            </p>
                        </div>
                        
                        <div class="col-md-6 mb-4">
                            <label class="text-muted text-uppercase small">Account Status</label>
                            <p class="fs-5 mb-0">
                                <?php
                                $status = $user['status'] ?? 'active';
                                $status_badge = $status == 'active' ? 'bg-success' : ($status == 'inactive' ? 'bg-secondary' : 'bg-danger');
                                ?>
                                <span class="badge <?= $status_badge ?>">
                                    <?= ucfirst($status) ?>
                                </span>
                            </p>
                        </div>
                    </div>
                    
                    <hr>
                    
                    <div class="alert alert-info">
                        <div class="d-flex">
                            <i class="bi bi-shield-lock fs-4 me-3"></i>
                            <div>
                                <h6 class="alert-heading">Privacy Protected</h6>
                                <p class="mb-0 small">
                                    Your profile information is secure and only visible to you and system administrators.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Recent Activity -->
            <div class="card shadow mt-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0">
                        <i class="bi bi-activity"></i> Recent Activity
                    </h5>
                </div>
                <div class="card-body">
                    <?php
                    // Get recent activity safely
                    $recent_activity = false;
                    if ($user_id) {
                        $recent_activity = $conn->query("
                            (SELECT 
                                'animal' as type,
                                animal_id as reference_id,
                                tag_number as reference,
                                created_at as activity_date,
                                'Added new animal' as action
                            FROM animals 
                            WHERE registered_by = $user_id
                            ORDER BY created_at DESC
                            LIMIT 3)
                            
                            UNION ALL
                            
                            (SELECT 
                                'health' as type,
                                record_id as reference_id,
                                (SELECT tag_number FROM animals WHERE animal_id = h.animal_id) as reference,
                                created_at as activity_date,
                                CONCAT('Added health record') as action
                            FROM health_records h
                            WHERE recorded_by = $user_id
                            ORDER BY created_at DESC
                            LIMIT 3)
                            
                            ORDER BY activity_date DESC
                            LIMIT 5
                        ");
                    }
                    ?>
                    
                    <?php if($recent_activity && $recent_activity->num_rows > 0): ?>
                        <div class="timeline">
                            <?php while($activity = $recent_activity->fetch_assoc()): ?>
                                <div class="d-flex mb-4">
                                    <div class="flex-shrink-0">
                                        <?php if(($activity['type'] ?? '') == 'animal'): ?>
                                            <div class="bg-primary bg-opacity-10 p-2 rounded-circle">
                                                <i class="bi bi-github text-primary"></i>
                                            </div>
                                        <?php else: ?>
                                            <div class="bg-success bg-opacity-10 p-2 rounded-circle">
                                                <i class="bi bi-file-medical text-success"></i>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h6 class="mb-1"><?= htmlspecialchars($activity['action'] ?? 'Activity') ?></h6>
                                        <small class="text-muted">
                                            <i class="bi bi-clock"></i> 
                                            <?= isset($activity['activity_date']) ? date('M d, Y H:i A', strtotime($activity['activity_date'])) : 'Unknown date' ?>
                                            <?php if(!empty($activity['reference'])): ?>
                                                - Animal: <?= htmlspecialchars($activity['reference']) ?>
                                            <?php endif; ?>
                                        </small>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-4">
                            <i class="bi bi-clock-history fs-1 text-muted"></i>
                            <p class="mt-2 mb-0">No recent activity found</p>
                            <a href="../animals/add.php" class="btn btn-primary mt-3">
                                <i class="bi bi-plus-circle"></i> Add Your First Animal
                            </a>
                        </div>
                    <?php endif; ?>
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

include '../includes/footer.php';
?>