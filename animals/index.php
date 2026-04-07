<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/index.php');
    exit;
}
require_once '../config/db.php';

// Handle delete if requested
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM animals WHERE animal_id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    header('Location: index.php?msg=deleted');
    exit;
}

$result = $conn->query("SELECT * FROM animals ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Animals</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body class="bg-light">
    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="bi bi-github"></i> Manage Animals</h2>
            <div>
                <a href="add.php" class="btn btn-primary">
                    <i class="bi bi-plus-circle"></i> Add New Animal
                </a>
                <a href="../dashboard.php" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Back
                </a>
            </div>
        </div>
        
        <?php if(isset($_GET['msg'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?php 
                    if($_GET['msg'] == 'added') echo "Animal added successfully!";
                    if($_GET['msg'] == 'updated') echo "Animal updated successfully!";
                    if($_GET['msg'] == 'deleted') echo "Animal deleted successfully!";
                ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        
        <div class="card shadow">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-primary">
                            <tr>
                                <th>Tag Number</th>
                                <th>Species</th>
                                <th>Breed</th>
                                <th>Date of Birth</th>
                                <th>Age</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if($result->num_rows > 0): ?>
                                <?php while($row = $result->fetch_assoc()): ?>
                                    <?php 
                                        $age = '';
                                        if($row['date_of_birth']) {
                                            $dob = new DateTime($row['date_of_birth']);
                                            $now = new DateTime();
                                            $diff = $now->diff($dob);
                                            $age = $diff->y . ' yrs, ' . $diff->m . ' months';
                                        }
                                    ?>
                                    <tr>
                                        <td><strong><?= htmlspecialchars($row['tag_number']) ?></strong></td>
                                        <td><?= htmlspecialchars($row['species']) ?></td>
                                        <td><?= htmlspecialchars($row['breed']) ?></td>
                                        <td><?= $row['date_of_birth'] ? date('d/m/Y', strtotime($row['date_of_birth'])) : '-' ?></td>
                                        <td><?= $age ?: '-' ?></td>
                                        <td>
                                            <a href="edit.php?id=<?= $row['animal_id'] ?>" class="btn btn-sm btn-warning">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <a href="?delete=<?= $row['animal_id'] ?>" 
                                               class="btn btn-sm btn-danger" 
                                               onclick="return confirm('Are you sure you want to delete this animal?')">
                                                <i class="bi bi-trash"></i>
                                            </a>
                                            <a href="../health/view.php?animal_id=<?= $row['animal_id'] ?>" 
                                               class="btn btn-sm btn-info">
                                                <i class="bi bi-file-medical"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="text-center py-4">
                                        <i class="bi bi-inbox" style="font-size: 2rem;"></i>
                                        <p class="mt-2">No animals found. Click "Add New Animal" to get started.</p>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>