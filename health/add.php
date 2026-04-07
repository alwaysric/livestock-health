<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: /lhtm_system/auth/index.php');
    exit;
}

$page_title = 'Add Health Record';
$base_path = '..';
require_once '../config/db.php';

// Get all active animals for dropdown
$animals = $conn->query("SELECT animal_id, tag_number, species, breed FROM animals WHERE status = 'active' ORDER BY tag_number");

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $animal_id = $_POST['animal_id'] ?? 0;
    $diagnosis = $_POST['diagnosis'] ?? '';
    $treatment = $_POST['treatment'] ?? '';
    $vaccination = $_POST['vaccination'] ?? '';
    $next_vaccination = $_POST['next_vaccination'] ?: null;
    $record_date = $_POST['record_date'] ?? date('Y-m-d');
    $recorded_by = $_SESSION['user_id'];
    
    // Validate
    if (empty($animal_id) || empty($diagnosis)) {
        header('Location: add.php?error=required');
        exit;
    }
    
    $stmt = $conn->prepare("
        INSERT INTO health_records 
        (animal_id, diagnosis, treatment, vaccination, next_vaccination, record_date, recorded_by) 
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ");
    
    $stmt->bind_param("isssssi", $animal_id, $diagnosis, $treatment, $vaccination, $next_vaccination, $record_date, $recorded_by);
    
    if ($stmt->execute()) {
        logAction($conn, $_SESSION['user_id'], 'ADD_RECORD', "Added health record for animal ID: $animal_id");
        header('Location: index.php?msg=added');
    } else {
        header('Location: add.php?error=insert_failed');
    }
    exit;
}

include '../includes/header.php';
include '../includes/navbar.php';
?>

<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-success text-white">
                    <h4 class="mb-0">
                        <i class="bi bi-plus-circle"></i> Add New Health Record
                    </h4>
                </div>
                <div class="card-body">
                    
                    <?php if(isset($_GET['error'])): ?>
                        <div class="alert alert-danger">
                            <i class="bi bi-exclamation-triangle"></i>
                            <?php 
                                if($_GET['error'] == 'required') echo "Please fill in all required fields.";
                                if($_GET['error'] == 'insert_failed') echo "Failed to add record. Please try again.";
                            ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if($animals->num_rows == 0): ?>
                        <div class="alert alert-warning">
                            <i class="bi bi-exclamation-triangle"></i>
                            No animals found. Please <a href="../animals/add.php">add an animal first</a>.
                        </div>
                    <?php endif; ?>
                    
                    <form method="POST" id="healthForm">
                        <!-- Animal Selection -->
                        <div class="mb-3">
                            <label class="form-label fw-bold">
                                Select Animal <span class="text-danger">*</span>
                            </label>
                            <select name="animal_id" class="form-select form-select-lg" required <?= $animals->num_rows == 0 ? 'disabled' : '' ?>>
                                <option value="">-- Choose Animal --</option>
                                <?php while($animal = $animals->fetch_assoc()): ?>
                                    <option value="<?= $animal['animal_id'] ?>">
                                        <?= htmlspecialchars($animal['tag_number']) ?> - 
                                        <?= htmlspecialchars($animal['species']) ?> 
                                        (<?= htmlspecialchars($animal['breed'] ?? 'No breed') ?>)
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        
                        <!-- Record Date -->
                        <div class="mb-3">
                            <label class="form-label fw-bold">Record Date</label>
                            <input type="date" name="record_date" class="form-control" 
                                   value="<?= date('Y-m-d') ?>" max="<?= date('Y-m-d') ?>">
                        </div>
                        
                        <!-- Diagnosis -->
                        <div class="mb-3">
                            <label class="form-label fw-bold">
                                Diagnosis / Condition <span class="text-danger">*</span>
                            </label>
                            <textarea name="diagnosis" class="form-control" rows="3" 
                                      placeholder="Describe the diagnosis or condition..." required></textarea>
                        </div>
                        
                        <!-- Treatment -->
                        <div class="mb-3">
                            <label class="form-label fw-bold">Treatment Given</label>
                            <textarea name="treatment" class="form-control" rows="3" 
                                      placeholder="Describe the treatment administered..."></textarea>
                        </div>
                        
                        <!-- Vaccination -->
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Vaccination</label>
                                <input type="text" name="vaccination" class="form-control" 
                                       placeholder="e.g., FMD, Brucellosis, etc.">
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Next Vaccination Date</label>
                                <input type="date" name="next_vaccination" class="form-control" 
                                       min="<?= date('Y-m-d') ?>">
                                <div class="form-text">Leave blank if not applicable</div>
                            </div>
                        </div>
                        
                        <hr>
                        
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-success btn-lg" 
                                    <?= $animals->num_rows == 0 ? 'disabled' : '' ?>>
                                <i class="bi bi-save"></i> Save Health Record
                            </button>
                            <a href="index.php" class="btn btn-outline-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- Quick Tips -->
            <div class="card mt-3">
                <div class="card-body bg-light">
                    <h6><i class="bi bi-lightbulb"></i> Quick Tips:</h6>
                    <ul class="mb-0 small">
                        <li>Fill in as much detail as possible for accurate records</li>
                        <li>For vaccinations, always set the next vaccination date</li>
                        <li>You can edit records later if needed</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
include '../includes/footer.php';
?>