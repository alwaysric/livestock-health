<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/index.php');
    exit;
}

$page_title = 'Animal Details';
require_once '../config/db.php';
require_once '../includes/header.php';
require_once '../includes/navbar.php';

$animal_id = $_GET['id'];

// Get animal details
$animal_stmt = $conn->prepare("
    SELECT a.*, u.name as registered_by_name 
    FROM animals a 
    LEFT JOIN users u ON a.registered_by = u.user_id 
    WHERE a.animal_id = ?
");
$animal_stmt->bind_param("i", $animal_id);
$animal_stmt->execute();
$animal = $animal_stmt->get_result()->fetch_assoc();

if (!$animal) {
    header('Location: index.php');
    exit;
}

// Get health records
$health_records = $conn->prepare("
    SELECT h.*, u.name as recorded_by_name 
    FROM health_records h 
    LEFT JOIN users u ON h.recorded_by = u.user_id 
    WHERE h.animal_id = ? 
    ORDER BY h.record_date DESC
");
$health_records->bind_param("i", $animal_id);
$health_records->execute();
$records_result = $health_records->get_result();

// Get vaccination schedule
$vaccinations = $conn->prepare("
    SELECT * FROM vaccination_schedule 
    WHERE animal_id = ? 
    ORDER BY scheduled_date DESC
");
$vaccinations->bind_param("i", $animal_id);
$vaccinations->execute();
$vax_result = $vaccinations->get_result();

// Calculate age
$age = '';
if ($animal['date_of_birth']) {
    $dob = new DateTime($animal['date_of_birth']);
    $now = new DateTime();
    $diff = $now->diff($dob);
    $age = $diff->y . ' years, ' . $diff->m . ' months, ' . $diff->d . ' days';
}
?>

<div class="container-fluid py-4">
    <div class="row mb-3">
        <div class="col-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="../dashboard.php">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="index.php">Animals</a></li>
                    <li class="breadcrumb-item active"><?= $animal['tag_number'] ?></li>
                </ol>
            </nav>
        </div>
    </div>
    
    <div class="row">
        <!-- Animal Profile Card -->
        <div class="col-md-4 mb-4">
            <div class="glass-card p-4">
                <div class="text-center mb-3">
                    <div class="display-1">
                        <?php
                        $icon = 'fa-cow';
                        if($animal['species'] == 'Sheep') $icon = 'fa-sheep';
                        elseif($animal['species'] == 'Goat') $icon = 'fa-goat';
                        elseif($animal['species'] == 'Pig') $icon = 'fa-pig';
                        elseif($animal['species'] == 'Horse') $icon = 'fa-horse';
                        ?>
                        <i class="fas <?= $icon ?>"></i>
                    </div>
                    <h3><?= $animal['name'] ?? $animal['tag_number'] ?></h3>
                    <span class="badge bg-<?= $animal['health_status'] == 'healthy' ? 'success' : ($animal['health_status'] == 'critical' ? 'danger' : 'warning') ?>">
                        <?= ucfirst($animal['health_status']) ?>
                    </span>
                </div>
                
                <hr>
                
                <div class="row g-2">
                    <div class="col-6">
                        <small class="text-muted">Tag Number</small>
                        <p class="fw-bold"><?= $animal['tag_number'] ?></p>
                    </div>
                    <div class="col-6">
                        <small class="text-muted">RFID Tag</small>
                        <p class="fw-bold"><?= $animal['rfid_tag'] ?? 'Not assigned' ?></p>
                    </div>
                    <div class="col-6">
                        <small class="text-muted">Species</small>
                        <p><?= $animal['species'] ?></p>
                    </div>
                    <div class="col-6">
                        <small class="text-muted">Breed</small>
                        <p><?= $animal['breed'] ?? 'Unknown' ?></p>
                    </div>
                    <div class="col-6">
                        <small class="text-muted">Gender</small>
                        <p><?= ucfirst($animal['gender'] ?? 'Unknown') ?></p>
                    </div>
                    <div class="col-6">
                        <small class="text-muted">Age</small>
                        <p><?= $age ?: 'Unknown' ?></p>
                    </div>
                    <div class="col-6">
                        <small class="text-muted">Weight</small>
                        <p><?= $animal['weight'] ? $animal['weight'] . ' kg' : 'Unknown' ?></p>
                    </div>
                    <div class="col-6">
                        <small class="text-muted">Location</small>
                        <p><?= $animal['location'] ?? 'Unknown' ?></p>
                    </div>
                    <div class="col-12">
                        <small class="text-muted">Date Acquired</small>
                        <p><?= $animal['date_acquired'] ? date('d/m/Y', strtotime($animal['date_acquired'])) : 'Unknown' ?></p>
                    </div>
                    <div class="col-12">
                        <small class="text-muted">Markings</small>
                        <p><?= $animal['markings'] ?? 'None recorded' ?></p>
                    </div>
                    <div class="col-12">
                        <small class="text-muted">Notes</small>
                        <p><?= $animal['notes'] ?? 'No notes' ?></p>
                    </div>
                </div>
                
                <hr>
                
                <div class="d-grid gap-2">
                    <a href="edit.php?id=<?= $animal['animal_id'] ?>" class="btn btn-warning">
                        <i class="fas fa-edit"></i> Edit Animal
                    </a>
                    <a href="../health/add.php?animal_id=<?= $animal['animal_id'] ?>" class="btn btn-success">
                        <i class="fas fa-plus-circle"></i> Add Health Record
                    </a>
                    <a href="delete.php?id=<?= $animal['animal_id'] ?>" 
                       class="btn btn-danger delete-confirm">
                        <i class="fas fa-trash"></i> Delete Animal
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Health Records -->
        <div class="col-md-8 mb-4">
            <div class="glass-card p-4 mb-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0"><i class="fas fa-notes-medical me-2"></i>Health Records</h5>
                    <span class="badge bg-primary"><?= $records_result->num_rows ?> records</span>
                </div>
                
                <?php if($records_result->num_rows > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Date</th>
                                    <th>Type</th>
                                    <th>Diagnosis</th>
                                    <th>Treatment</th>
                                    <th>Vet</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while($record = $records_result->fetch_assoc()): ?>
                                    <tr>
                                        <td><?= date('d/m/Y', strtotime($record['record_date'])) ?></td>
                                        <td>
                                            <span class="badge bg-<?= $record['record_type'] == 'vaccination' ? 'info' : 'secondary' ?>">
                                                <?= ucfirst($record['record_type']) ?>
                                            </span>
                                        </td>
                                        <td><?= substr($record['diagnosis'] ?? 'N/A', 0, 30) ?>...</td>
                                        <td><?= substr($record['treatment'] ?? 'N/A', 0, 30) ?>...</td>
                                        <td><?= $record['recorded_by_name'] ?? 'System' ?></td>
                                        <td>
                                            <a href="../health/edit.php?id=<?= $record['record_id'] ?>" 
                                               class="btn btn-sm btn-warning">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="../health/delete.php?id=<?= $record['record_id'] ?>" 
                                               class="btn btn-sm btn-danger delete-confirm">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p class="text-muted text-center py-3">No health records found</p>
                <?php endif; ?>
            </div>
            
            <!-- Vaccination Schedule -->
            <div class="glass-card p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0"><i class="fas fa-syringe me-2"></i>Vaccination Schedule</h5>
                    <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addVaccinationModal">
                        <i class="fas fa-plus"></i> Schedule
                    </button>
                </div>
                
                <?php if($vax_result->num_rows > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Vaccine</th>
                                    <th>Scheduled</th>
                                    <th>Administered</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while($vax = $vax_result->fetch_assoc()): ?>
                                    <tr>
                                        <td><?= $vax['vaccine_name'] ?></td>
                                        <td><?= date('d/m/Y', strtotime($vax['scheduled_date'])) ?></td>
                                        <td><?= $vax['administered_date'] ? date('d/m/Y', strtotime($vax['administered_date'])) : '-' ?></td>
                                        <td>
                                            <span class="badge bg-<?= 
                                                $vax['status'] == 'completed' ? 'success' : 
                                                ($vax['status'] == 'pending' ? 'warning' : 'danger') 
                                            ?>">
                                                <?= ucfirst($vax['status']) ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p class="text-muted text-center py-3">No vaccinations scheduled</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Add Vaccination Modal -->
<div class="modal fade" id="addVaccinationModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="fas fa-syringe"></i> Schedule Vaccination</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="../health/schedule_vaccination.php" method="POST">
                <input type="hidden" name="animal_id" value="<?= $animal_id ?>">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Vaccine Name</label>
                        <input type="text" name="vaccine_name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Scheduled Date</label>
                        <input type="date" name="scheduled_date" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Notes</label>
                        <textarea name="notes" class="form-control" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Schedule</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>