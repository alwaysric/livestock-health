<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../auth/index.php');
    exit;
}

$page_title = 'Edit User';
$base_path = '..';
require_once '../config/db.php';

$user_id = $_GET['id'] ?? 0;

// Get user data
$stmt = $conn->prepare("SELECT * FROM users WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

if (!$user) {
    header('Location: users.php');
    exit;
}

// Update user
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $role = $_POST['role'];
    $status = $_POST['status'];
    
    $update = $conn->prepare("UPDATE users SET name = ?, email = ?, role = ?, status = ? WHERE user_id = ?");
    $update->bind_param("ssssi", $name, $email, $role, $status, $user_id);
    
    if ($update->execute()) {
        logAction($conn, $_SESSION['user_id'], 'USER_EDIT', "Edited user ID $user_id: $name");
        header('Location: users.php?msg=updated');
        exit;
    }
}

include '../includes/header.php';
include '../includes/navbar.php';
?>

<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="bi bi-pencil-square"></i> Edit User</h5>
                </div>
                <div class="card-body">
                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Full Name</label>
                            <input type="text" name="name" class="form-control" 
                                   value="<?= htmlspecialchars($user['name']) ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold">Email Address</label>
                            <input type="email" name="email" class="form-control" 
                                   value="<?= htmlspecialchars($user['email']) ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold">Role</label>
                            <select name="role" class="form-select" required>
                                <option value="admin" <?= $user['role'] == 'admin' ? 'selected' : '' ?>>👑 Administrator</option>
                                <option value="farmer" <?= $user['role'] == 'farmer' ? 'selected' : '' ?>>🌾 Farmer</option>
                                <option value="worker" <?= $user['role'] == 'worker' ? 'selected' : '' ?>>🔧 Worker</option>
                                <option value="vet" <?= $user['role'] == 'vet' ? 'selected' : '' ?>>💉 Veterinarian</option>
                            </select>
                        </div>
                        
                        <div class="mb-4">
                            <label class="form-label fw-bold">Account Status</label>
                            <select name="status" class="form-select" required>
                                <option value="active" <?= $user['status'] == 'active' ? 'selected' : '' ?>>✅ Active</option>
                                <option value="inactive" <?= $user['status'] == 'inactive' ? 'selected' : '' ?>>⏸️ Inactive</option>
                                <option value="suspended" <?= $user['status'] == 'suspended' ? 'selected' : '' ?>>⛔ Suspended</option>
                            </select>
                        </div>
                        
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save"></i> Update User
                            </button>
                            <a href="users.php" class="btn btn-outline-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
include '../includes/footer.php';
?>