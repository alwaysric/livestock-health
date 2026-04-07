<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../auth/index.php');
    exit;
}

require_once '../config/db.php';

$user_id = $_GET['id'] ?? 0;

// Don't allow admin to delete themselves
if ($user_id == $_SESSION['user_id']) {
    header('Location: users.php?error=self_delete');
    exit;
}

// Get user info before deleting
$stmt = $conn->prepare("SELECT name, email FROM users WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

if ($user) {
    // Log the deletion
    logAction($conn, $_SESSION['user_id'], 'USER_DELETE', "Deleted user: {$user['name']} ({$user['email']})");
    
    // Delete the user
    $delete = $conn->prepare("DELETE FROM users WHERE user_id = ?");
    $delete->bind_param("i", $user_id);
    $delete->execute();
}

header('Location: users.php?msg=deleted');
exit;
?>