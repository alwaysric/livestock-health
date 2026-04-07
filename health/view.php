<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: /lhtm_system/auth/index.php');
    exit;
}

$page_title = 'Animal Health Records';
$base_path = '..';
require_once '../config/db.php';

$animal_id = $_GET['animal_id'] ?? 0;

// Get animal info
$animal_info = $conn->query("
    SELECT a.*, u.name as registered_by_name 
    FROM animals a
    LEFT JOIN users u ON a.registered_by = u.user_id
    WHERE a.animal_id = $animal_id
");

if (!$animal_info || $animal_info->num_rows == 0) {
    header('Location: ../animals/index.php');
    exit;
}

$animal = $animal_info->fetch_assoc();

// Get health records for this animal
$records = $conn->query("
    SELECT h.*, u.name as recorded_by_name
    FROM health_records h
    LEFT JOIN users u ON h.recorded_by = u.user_id
    WHERE h.animal_id = $animal_id
    ORDER BY h.record_date DESC, h.created_at DESC
");

include '../includes/header.php';
include '../includes/navbar.php';
?>

<div class="container mt-4">
    <!-- Animal Summary Card -->
    <div class="card bg-light mb-4">
        <div class="card-body">
            <div class="row">
                <div class="col-md-8">
                    <h4>
                        <i class="bi bi-github"></i> 
                        <?= htmlspecialchars($animal['tag_number']) ?>
                        <small class="text-muted">(<?= htmlspecialchars($animal['species']) ?>)</small>
                    </h4>
                    <p>
                        <strong>Breed:</strong> <?= htmlspecialchars($animal['breed'] ?? 'Not specified') ?><br>
                        <strong>Date of Birth:</strong> <?= $animal['date_of_birth'] ? date('F d, Y', strtotime($animal['date_of_birth'])) : 'Not recorded' ?><br>
                        <strong>Status:</strong> 
                        <span class="badge bg-<?= $animal['status'] == 'active' ? 'success' : 'secondary' ?>">
                            <?= ucfirst($animal['status']) ?>
                        </span>
                    </p>
                </div>
                <div class="col-md-4 text-end">
                    <a href="../animals/edit.php?id=<?= $animal_id ?>" class="btn btn-warning">
                        <i class="bi bi-pencil"></i> Edit Animal
                    </a>
                    <a href="add.php?animal_id=<?= $animal_id ?>" class="btn btn-success">
                        <i class="bi bi-plus-circle"></i> Add Record
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Health Records -->
    <div class="card shadow">
        <div class="card-header bg-white">
            <h5 class="mb-0">
                <i class="bi bi-file-medical"></i> Health History
            </h5>
        </div>
        <div class="card-body">
            <?php if($records && $records->num_rows > 0): ?>
                <div class="timeline">
                    <?php while($record = $records->fetch_assoc()): ?>
                        <div class="card mb-3 border-<?= 
                            $record['next_vaccination'] && $record['next_vaccination'] < date('Y-m-d') ? 'danger' : 
                            ($record['vaccination'] ? 'info' : 'secondary') 
                        ?>">
                            <div class="card-header bg-<?= 
                                $record['next_vaccination'] && $record['next_vaccination'] < date('Y-m-d') ? 'danger text-white' : 
                                ($record['vaccination'] ? 'info' : 'light') 
                            ?>">
                                <div class="d-flex justify-content-between">
                                    <span>
                                        <i class="bi bi-calendar"></i> 
                                        <?= date('F d, Y', strtotime($record['record_date'])) ?>
                                    </span>
                                    <span>
                                        Recorded by: <strong><?= htmlspecialchars($record['recorded_by_name'] ?? 'System') ?></strong>
                                    </span>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <h6 class="text-danger">Diagnosis:</h6>
                                        <p><?= nl2br(htmlspecialchars($record['diagnosis'] ?? 'None')) ?></p>
                                    </div>
                                    <div class="col-md-6">
                                        <h6 class="text-success">Treatment:</h6>
                                        <p><?= nl2br(htmlspecialchars($record['treatment'] ?? 'None')) ?></p>
                                    </div>
                                </div>
                                
                                <?php if($record['vaccination']): ?>
                                    <div class="alert alert-info mt-2 mb-0">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <strong>Vaccination:</strong> <?= htmlspecialchars($record['vaccination']) ?>
                                            </div>
                                            <?php if($record['next_vaccination']): ?>
                                                <div class="col-md-6">
                                                    <strong>Next Due:</strong> 
                                                    <?= date('F d, Y', strtotime($record['next_vaccination'])) ?>
                                                    <?php
                                                    $next = strtotime($record['next_vaccination']);
                                                    $today = strtotime(date('Y-m-d'));
                                                    $days = floor(($next - $today) / (60 * 60 * 24));
                                                    
                                                    if($days < 0) {
                                                        echo '<span class="badge bg-danger ms-2">Overdue by ' . abs($days) . ' days</span>';
                                                    } elseif($days <= 7) {
                                                        echo '<span class="badge bg-warning ms-2">Due in ' . $days . ' days</span>';
                                                    }
                                                    ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="card-footer text-end">
                                <a href="edit.php?id=<?= $record['record_id'] ?>" class="btn btn-sm btn-warning">
                                    <i class="bi bi-pencil"></i> Edit
                                </a>
                                <a href="index.php?delete=<?= $record['record_id'] ?>" 
                                   class="btn btn-sm btn-danger"
                                   onclick="return confirm('Delete this record?')">
                                    <i class="bi bi-trash"></i> Delete
                                </a>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <div class="text-center py-5">
                    <i class="bi bi-file-medical" style="font-size: 4rem; color: #ccc;"></i>
                    <h5 class="mt-3 text-muted">No health records for this animal</h5>
                    <a href="add.php?animal_id=<?= $animal_id ?>" class="btn btn-success mt-3">
                        <i class="bi bi-plus-circle"></i> Add First Health Record
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php
include '../includes/footer.php';
?>