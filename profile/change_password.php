<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/index.php');
    exit;
}
require_once '../config/db.php';

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $current = $_POST['current_password'];
    $new = $_POST['new_password'];
    $confirm = $_POST['confirm_password'];
    
    $user_id = $_SESSION['user_id'];
    $stmt = $conn->prepare("SELECT password FROM users WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();
    
    if (password_verify($current, $user['password'])) {
        if ($new === $confirm) {
            $hashed = password_hash($new, PASSWORD_DEFAULT);
            $update = $conn->prepare("UPDATE users SET password = ? WHERE user_id = ?");
            $update->bind_param("si", $hashed, $user_id);
            
            if ($update->execute()) {
                $message = "Password changed successfully!";
            } else {
                $error = "Failed to update password.";
            }
        } else {
            $error = "New passwords do not match.";
        }
    } else {
        $error = "Current password is incorrect.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Password</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow">
                    <div class="card-header bg-warning">
                        <h4 class="mb-0"><i class="bi bi-key"></i> Change Password</h4>
                    </div>
                    <div class="card-body">
                        <?php if($message): ?>
                            <div class="alert alert-success"><?= $message ?></div>
                        <?php endif; ?>
                        
                        <?php if($error): ?>
                            <div class="alert alert-danger"><?= $error ?></div>
                        <?php endif; ?>
                        
                        <form method="POST">
                            <div class="mb-3">
                                <label class="form-label">Current Password</label>
                                <input type="password" name="current_password" class="form-control form-control-lg" required>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">New Password</label>
                                <input type="password" name="new_password" class="form-control form-control-lg" required>
                                <div class="form-text">Minimum 8 characters</div>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Confirm New Password</label>
                                <input type="password" name="confirm_password" class="form-control form-control-lg" required>
                            </div>
                            
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-warning btn-lg">
                                    <i class="bi bi-check-circle"></i> Update Password
                                </button>
                                <a href="index.php" class="btn btn-outline-secondary btn-lg">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>