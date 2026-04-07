<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/index.php');
    exit;
}
require_once '../config/db.php';

$user_id = $_SESSION['user_id'];
$action = $_POST['action'];

if ($action === 'profile') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    
    $stmt = $conn->prepare("UPDATE users SET name = ?, email = ? WHERE user_id = ?");
    $stmt->bind_param("ssi", $name, $email, $user_id);
    
    if ($stmt->execute()) {
        $_SESSION['name'] = $name;
        header('Location: index.php?tab=profile&success=1');
    } else {
        header('Location: index.php?tab=profile&error=1');
    }
    exit;
}

// Default redirect
header('Location: index.php');
exit;
?>