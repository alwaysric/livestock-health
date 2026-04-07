<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../auth/index.php');
    exit;
}

$page_title = 'Manage Users';
$base_path = '..';
require_once '../config/db.php';

// Handle status update
if (isset($_GET['action']) && isset($_GET['id'])) {
    $user_id = $_GET['id'];
    $action = $_GET['action'];
    
    if ($action == 'activate') {
        $status = 'active';
        $log_action = 'USER_ACTIVATE';
    } elseif ($action == 'deactivate') {
        $status = 'inactive';
        $log_action = 'USER_DEACTIVATE';
    } elseif ($action == 'suspend') {
        $status = 'suspended';
        $log_action = 'USER_SUSPEND';
    }
    
    if (isset($status)) {
        $stmt = $conn->prepare("UPDATE users SET status = ? WHERE user_id = ?");
        $stmt->bind_param("si", $status, $user_id);
        $stmt->execute();
        logAction($conn, $_SESSION['user_id'], $log_action, "User ID $user_id status changed to $status");
        header('Location: users.php?msg=updated');
        exit;
    }
}

// Get all users
$users = $conn->query("SELECT * FROM users ORDER BY created_at DESC");

include '../includes/header.php';
include '../includes/navbar.php';
?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>
            <i class="bi bi-people"></i> Manage Users
        </h2>
        <div>
            <a href="system_stats.php" class="btn btn-info">
                <i class="bi bi-bar-chart"></i> System Stats
            </a>
            <a href="../auth/register.php" class="btn btn-success">
                <i class="bi bi-person-plus"></i> Add New User
            </a>
        </div>
    </div>
    
    <?php if(isset($_GET['msg'])): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <i class="bi bi-check-circle"></i> User status updated successfully!
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    
    <div class="card shadow">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th>Joined</th>
                            <th>Last Login</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($user = $users->fetch_assoc()): ?>
                            <tr>
                                <td>#<?= $user['user_id'] ?></td>
                                <td>
                                    <strong><?= htmlspecialchars($user['name']) ?></strong>
                                    <?php if($user['user_id'] == $_SESSION['user_id']): ?>
                                        <span class="badge bg-info">You</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= htmlspecialchars($user['email']) ?></td>
                                <td>
                                    <span class="badge bg-<?= 
                                        $user['role'] == 'admin' ? 'danger' : 
                                        ($user['role'] == 'vet' ? 'warning text-dark' : 
                                        ($user['role'] == 'farmer' ? 'success' : 'info')) 
                                    ?>">
                                        <?= ucfirst($user['role']) ?>
                                    </span>
                                </td>
                                <td>
                                    <?php
                                    $status_color = [
                                        'active' => 'success',
                                        'inactive' => 'secondary',
                                        'suspended' => 'danger'
                                    ];
                                    ?>
                                    <span class="badge bg-<?= $status_color[$user['status']] ?>">
                                        <?= ucfirst($user['status']) ?>
                                    </span>
                                </td>
                                <td>
                                    <small><?= date('M d, Y', strtotime($user['created_at'])) ?></small>
                                </td>
                                <td>
                                    <?php if($user['last_login']): ?>
                                        <small><?= date('M d, H:i', strtotime($user['last_login'])) ?></small>
                                    <?php else: ?>
                                        <small class="text-muted">Never</small>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="btn-group">
                                        <a href="edit_user.php?id=<?= $user['user_id'] ?>" 
                                           class="btn btn-sm btn-outline-primary" 
                                           data-bs-toggle="tooltip" 
                                           title="Edit User">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        
                                        <?php if($user['user_id'] != $_SESSION['user_id']): ?>
                                            <?php if($user['status'] == 'active'): ?>
                                                <a href="?action=deactivate&id=<?= $user['user_id'] ?>" 
                                                   class="btn btn-sm btn-outline-warning"
                                                   onclick="return confirm('Deactivate this user?')"
                                                   data-bs-toggle="tooltip" 
                                                   title="Deactivate">
                                                    <i class="bi bi-pause-circle"></i>
                                                </a>
                                            <?php elseif($user['status'] == 'inactive'): ?>
                                                <a href="?action=activate&id=<?= $user['user_id'] ?>" 
                                                   class="btn btn-sm btn-outline-success"
                                                   onclick="return confirm('Activate this user?')"
                                                   data-bs-toggle="tooltip" 
                                                   title="Activate">
                                                    <i class="bi bi-play-circle"></i>
                                                </a>
                                            <?php endif; ?>
                                            
                                            <a href="delete_user.php?id=<?= $user['user_id'] ?>" 
                                               class="btn btn-sm btn-outline-danger"
                                               onclick="return confirm('Are you sure you want to delete this user? This action cannot be undone.')"
                                               data-bs-toggle="tooltip" 
                                               title="Delete User">
                                                <i class="bi bi-trash"></i>
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php
include '../includes/footer.php';
?>