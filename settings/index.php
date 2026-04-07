<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/index.php');
    exit;
}

$page_title = 'Settings';
$base_path = '..';
require_once '../config/db.php';

$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT * FROM users WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

include '../includes/header.php';
include '../includes/navbar.php';
?>

<div class="container mt-4">
    <h2 class="mb-4">
        <i class="bi bi-gear"></i> System Settings
    </h2>
    
    <?php if(isset($_GET['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <i class="bi bi-check-circle"></i> Settings updated successfully!
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    
    <div class="row">
        <!-- Settings Navigation -->
        <div class="col-md-3">
            <div class="list-group shadow-sm mb-4">
                <button class="list-group-item list-group-item-action active" id="profile-tab" data-bs-toggle="list" href="#profile">
                    <i class="bi bi-person"></i> Profile Settings
                </button>
                <button class="list-group-item list-group-item-action" id="security-tab" data-bs-toggle="list" href="#security">
                    <i class="bi bi-shield-lock"></i> Security
                </button>
                <button class="list-group-item list-group-item-action" id="notifications-tab" data-bs-toggle="list" href="#notifications">
                    <i class="bi bi-bell"></i> Notifications
                </button>
                <button class="list-group-item list-group-item-action" id="preferences-tab" data-bs-toggle="list" href="#preferences">
                    <i class="bi bi-sliders2"></i> Preferences
                </button>
                <button class="list-group-item list-group-item-action" id="backup-tab" data-bs-toggle="list" href="#backup">
                    <i class="bi bi-database"></i> Backup & Data
                </button>
            </div>
            
            <div class="card shadow-sm">
                <div class="card-body">
                    <h6 class="card-title">Account Status</h6>
                    <hr>
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <i class="bi bi-shield-check text-success fs-3"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <p class="mb-0 fw-bold">Active Account</p>
                            <small class="text-muted">Member since <?= date('M d, Y', strtotime($user['created_at'])) ?></small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Settings Content -->
        <div class="col-md-9">
            <div class="tab-content">
                <!-- Profile Settings -->
                <div class="tab-pane fade show active" id="profile">
                    <div class="card shadow-sm">
                        <div class="card-header bg-white">
                            <h5 class="mb-0">
                                <i class="bi bi-person"></i> Profile Settings
                            </h5>
                        </div>
                        <div class="card-body">
                            <form action="update.php" method="POST">
                                <input type="hidden" name="action" value="profile">
                                
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Full Name</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-person"></i></span>
                                        <input type="text" name="name" class="form-control form-control-lg" 
                                               value="<?= htmlspecialchars($user['name']) ?>" required>
                                    </div>
                                    <div class="form-text">Your full name as displayed in the system.</div>
                                </div>
                                
                                <div class="mb-4">
                                    <label class="form-label fw-bold">Email Address</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                                        <input type="email" name="email" class="form-control form-control-lg" 
                                               value="<?= htmlspecialchars($user['email']) ?>" required>
                                    </div>
                                    <div class="form-text">We'll never share your email with anyone else.</div>
                                </div>
                                
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-primary btn-lg">
                                        <i class="bi bi-check-circle"></i> Save Changes
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                
                <!-- Security Settings -->
                <div class="tab-pane fade" id="security">
                    <div class="card shadow-sm">
                        <div class="card-header bg-white">
                            <h5 class="mb-0">
                                <i class="bi bi-shield-lock"></i> Security Settings
                            </h5>
                        </div>
                        <div class="card-body">
                            <h6 class="fw-bold">Change Password</h6>
                            <p class="text-muted small">Update your password to keep your account secure.</p>
                            
                            <form action="../profile/change_password.php" method="POST" class="mt-3">
                                <div class="mb-3">
                                    <label class="form-label">Current Password</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-lock"></i></span>
                                        <input type="password" name="current_password" class="form-control" required>
                                        <button class="btn btn-outline-secondary toggle-password" type="button" data-target="#current-password">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">New Password</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-key"></i></span>
                                        <input type="password" name="new_password" class="form-control" required>
                                        <button class="btn btn-outline-secondary toggle-password" type="button" data-target="#new-password">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                    </div>
                                    <div class="form-text">Minimum 8 characters, include uppercase and numbers.</div>
                                </div>
                                
                                <div class="mb-4">
                                    <label class="form-label">Confirm New Password</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-key"></i></span>
                                        <input type="password" name="confirm_password" class="form-control" required>
                                    </div>
                                </div>
                                
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-warning btn-lg">
                                        <i class="bi bi-check-circle"></i> Update Password
                                    </button>
                                </div>
                            </form>
                            
                            <hr class="my-4">
                            
                            <h6 class="fw-bold">Two-Factor Authentication (2FA)</h6>
                            <p class="text-muted">Add an extra layer of security to your account.</p>
                            
                            <button class="btn btn-outline-primary" disabled>
                                <i class="bi bi-shield"></i> Enable 2FA
                            </button>
                            <span class="badge bg-secondary ms-2">Coming Soon</span>
                        </div>
                    </div>
                </div>
                
                <!-- Notification Settings -->
                <div class="tab-pane fade" id="notifications">
                    <div class="card shadow-sm">
                        <div class="card-header bg-white">
                            <h5 class="mb-0">
                                <i class="bi bi-bell"></i> Notification Preferences
                            </h5>
                        </div>
                        <div class="card-body">
                            <form action="update.php" method="POST">
                                <input type="hidden" name="action" value="notifications">
                                
                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox" id="emailAlerts" name="email_alerts" checked>
                                    <label class="form-check-label fw-bold" for="emailAlerts">
                                        Email Alerts
                                    </label>
                                    <div class="form-text">Receive vaccination reminders and important updates via email.</div>
                                </div>
                                
                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox" id="dashboardAlerts" name="dashboard_alerts" checked>
                                    <label class="form-check-label fw-bold" for="dashboardAlerts">
                                        Dashboard Notifications
                                    </label>
                                    <div class="form-text">Show notifications on your dashboard.</div>
                                </div>
                                
                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox" id="weeklyReports" name="weekly_reports">
                                    <label class="form-check-label fw-bold" for="weeklyReports">
                                        Weekly Reports
                                    </label>
                                    <div class="form-text">Receive weekly summary reports of your farm's health status.</div>
                                </div>
                                
                                <div class="form-check form-switch mb-4">
                                    <input class="form-check-input" type="checkbox" id="browserAlerts" name="browser_alerts" checked>
                                    <label class="form-check-label fw-bold" for="browserAlerts">
                                        Browser Notifications
                                    </label>
                                    <div class="form-text">Receive real-time notifications in your browser.</div>
                                </div>
                                
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-primary btn-lg">
                                        <i class="bi bi-check-circle"></i> Save Preferences
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                
                <!-- Preferences -->
                <div class="tab-pane fade" id="preferences">
                    <div class="card shadow-sm">
                        <div class="card-header bg-white">
                            <h5 class="mb-0">
                                <i class="bi bi-sliders2"></i> System Preferences
                            </h5>
                        </div>
                        <div class="card-body">
                            <form action="update.php" method="POST">
                                <input type="hidden" name="action" value="preferences">
                                
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Date Format</label>
                                    <select name="date_format" class="form-select form-select-lg">
                                        <option value="Y-m-d">2025-12-31 (ISO 8601)</option>
                                        <option value="d/m/Y">31/12/2025 (UK/Australia)</option>
                                        <option value="m/d/Y">12/31/2025 (US)</option>
                                        <option value="M d, Y">Dec 31, 2025 (Short month)</option>
                                        <option value="F d, Y">December 31, 2025 (Full month)</option>
                                    </select>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Time Format</label>
                                    <select name="time_format" class="form-select form-select-lg">
                                        <option value="H:i">14:30 (24-hour)</option>
                                        <option value="h:i A">02:30 PM (12-hour)</option>
                                    </select>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Items Per Page</label>
                                    <select name="items_per_page" class="form-select form-select-lg">
                                        <option value="10">10 items</option>
                                        <option value="25" selected>25 items</option>
                                        <option value="50">50 items</option>
                                        <option value="100">100 items</option>
                                    </select>
                                </div>
                                
                                <div class="mb-4">
                                    <label class="form-label fw-bold">Default Dashboard View</label>
                                    <select name="default_view" class="form-select form-select-lg">
                                        <option value="overview">Overview</option>
                                        <option value="animals">Animals</option>
                                        <option value="health">Health Records</option>
                                        <option value="alerts">Alerts</option>
                                    </select>
                                </div>
                                
                                <div class="alert alert-warning">
                                    <i class="bi bi-exclamation-triangle"></i> 
                                    Some preferences are not yet implemented and will be available in future updates.
                                </div>
                                
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-primary btn-lg" disabled>
                                        <i class="bi bi-check-circle"></i> Save Preferences
                                    </button>
                                    <span class="text-muted text-center mt-2">Coming Soon</span>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                
                <!-- Backup & Data -->
                <div class="tab-pane fade" id="backup">
                    <div class="card shadow-sm">
                        <div class="card-header bg-white">
                            <h5 class="mb-0">
                                <i class="bi bi-database"></i> Backup & Data Management
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-4">
                                    <div class="border rounded p-4 text-center h-100">
                                        <i class="bi bi-download fs-1 text-primary mb-3"></i>
                                        <h6>Export Data</h6>
                                        <p class="text-muted small">Download your farm data as CSV or Excel.</p>
                                        <button class="btn btn-outline-primary" disabled>
                                            <i class="bi bi-file-earmark-spreadsheet"></i> Export
                                        </button>
                                        <span class="badge bg-secondary ms-2">Soon</span>
                                    </div>
                                </div>
                                
                                <div class="col-md-6 mb-4">
                                    <div class="border rounded p-4 text-center h-100">
                                        <i class="bi bi-upload fs-1 text-success mb-3"></i>
                                        <h6>Import Data</h6>
                                        <p class="text-muted small">Import animals and health records.</p>
                                        <button class="btn btn-outline-success" disabled>
                                            <i class="bi bi-upload"></i> Import
                                        </button>
                                        <span class="badge bg-secondary ms-2">Soon</span>
                                    </div>
                                </div>
                                
                                <div class="col-md-6 mb-4">
                                    <div class="border rounded p-4 text-center h-100">
                                        <i class="bi bi-archive fs-1 text-warning mb-3"></i>
                                        <h6>Backup</h6>
                                        <p class="text-muted small">Create manual backup of all your data.</p>
                                        <button class="btn btn-outline-warning" disabled>
                                            <i class="bi bi-database"></i> Backup Now
                                        </button>
                                        <span class="badge bg-secondary ms-2">Soon</span>
                                    </div>
                                </div>
                                
                                <div class="col-md-6 mb-4">
                                    <div class="border rounded p-4 text-center h-100">
                                        <i class="bi bi-arrow-counterclockwise fs-1 text-info mb-3"></i>
                                        <h6>Restore</h6>
                                        <p class="text-muted small">Restore from previous backup.</p>
                                        <button class="btn btn-outline-info" disabled>
                                            <i class="bi bi-arrow-repeat"></i> Restore
                                        </button>
                                        <span class="badge bg-secondary ms-2">Soon</span>
                                    </div>
                                </div>
                            </div>
                            
                            <hr>
                            
                            <div class="alert alert-danger">
                                <i class="bi bi-exclamation-triangle-fill"></i>
                                <strong>Danger Zone:</strong> Account deletion is irreversible.
                                <div class="mt-2">
                                    <button class="btn btn-outline-danger" disabled>
                                        <i class="bi bi-trash"></i> Delete Account
                                    </button>
                                    <span class="badge bg-secondary ms-2">Coming Soon</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$additional_scripts = '
<script>
    // Activate tab based on URL hash
    document.addEventListener("DOMContentLoaded", function() {
        var hash = window.location.hash;
        if (hash) {
            var tab = document.querySelector(\'[href="\' + hash + \'"]\');
            if (tab) {
                var trigger = new bootstrap.Tab(tab);
                trigger.show();
            }
        }
    });
</script>
';

include '../includes/footer.php';
?>