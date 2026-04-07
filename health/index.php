<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: /lhtm_system/auth/index.php');
    exit;
}

$page_title = 'Health Records';
$base_path = '..';
require_once '../config/db.php';

// Handle delete
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM health_records WHERE record_id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        logAction($conn, $_SESSION['user_id'], 'DELETE_RECORD', "Deleted health record ID: $id");
        header('Location: index.php?msg=deleted');
        exit;
    }
}

// Get all health records with animal info
$query = "
    SELECT 
        h.*,
        a.tag_number,
        a.species,
        a.breed,
        u.name as recorded_by_name
    FROM health_records h
    JOIN animals a ON h.animal_id = a.animal_id
    LEFT JOIN users u ON h.recorded_by = u.user_id
    ORDER BY h.record_date DESC, h.created_at DESC
";

$result = $conn->query($query);

include '../includes/header.php';
include '../includes/navbar.php';
?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>
            <i class="bi bi-file-medical text-success"></i> 
            Health Records
        </h2>
        <div>
            <a href="add.php" class="btn btn-success">
                <i class="bi bi-plus-circle"></i> Add Health Record
            </a>
            <a href="index.php" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-repeat"></i> Refresh
            </a>
        </div>
    </div>
    
    <?php if(isset($_GET['msg'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill"></i>
            <?php 
                if($_GET['msg'] == 'added') echo "Health record added successfully!";
                if($_GET['msg'] == 'updated') echo "Health record updated successfully!";
                if($_GET['msg'] == 'deleted') echo "Health record deleted successfully!";
            ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    
    <?php if(isset($_GET['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="bi bi-exclamation-triangle-fill"></i>
            <?php 
                if($_GET['error'] == 'animal_not_found') echo "Selected animal not found!";
                if($_GET['error'] == 'insert_failed') echo "Failed to add record. Please try again.";
            ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    
    <div class="card shadow">
        <div class="card-body">
            <!-- Search Box -->
            <div class="row mb-3">
                <div class="col-md-4">
                    <input type="text" id="tableSearch" class="form-control" 
                           placeholder="🔍 Search records...">
                </div>
                <div class="col-md-8 text-end">
                    <span class="text-muted">
                        Total Records: <strong><?= $result ? $result->num_rows : 0 ?></strong>
                    </span>
                </div>
            </div>
            
            <?php if($result && $result->num_rows > 0): ?>
                <div class="table-responsive">
                    <table class="table table-hover table-bordered" id="recordsTable">
                        <thead class="table-success">
                            <tr>
                                <th>ID</th>
                                <th>Animal Tag</th>
                                <th>Species</th>
                                <th>Diagnosis</th>
                                <th>Treatment</th>
                                <th>Vaccination</th>
                                <th>Next Vaccination</th>
                                <th>Record Date</th>
                                <th>Recorded By</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td>#<?= $row['record_id'] ?></td>
                                    <td>
                                        <strong><?= htmlspecialchars($row['tag_number']) ?></strong>
                                        <br>
                                        <small class="text-muted"><?= htmlspecialchars($row['breed'] ?? '') ?></small>
                                    </td>
                                    <td><?= htmlspecialchars($row['species']) ?></td>
                                    <td>
                                        <?= htmlspecialchars(substr($row['diagnosis'] ?? '', 0, 50)) ?>
                                        <?= strlen($row['diagnosis'] ?? '') > 50 ? '...' : '' ?>
                                    </td>
                                    <td>
                                        <?= htmlspecialchars(substr($row['treatment'] ?? '', 0, 50)) ?>
                                        <?= strlen($row['treatment'] ?? '') > 50 ? '...' : '' ?>
                                    </td>
                                    <td>
                                        <?php if($row['vaccination']): ?>
                                            <span class="badge bg-info text-dark">
                                                <?= htmlspecialchars($row['vaccination']) ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">None</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if($row['next_vaccination']): ?>
                                            <?php 
                                                $next_date = strtotime($row['next_vaccination']);
                                                $today = strtotime(date('Y-m-d'));
                                                $days_diff = floor(($next_date - $today) / (60 * 60 * 24));
                                                
                                                if($days_diff < 0) {
                                                    echo '<span class="badge bg-danger">Overdue by ' . abs($days_diff) . ' days</span>';
                                                } elseif($days_diff <= 7) {
                                                    echo '<span class="badge bg-warning text-dark">Due in ' . $days_diff . ' days</span>';
                                                } else {
                                                    echo date('M d, Y', strtotime($row['next_vaccination']));
                                                }
                                            ?>
                                        <?php else: ?>
                                            <span class="text-muted">Not scheduled</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?= date('M d, Y', strtotime($row['record_date'])) ?>
                                    </td>
                                    <td>
                                        <small><?= htmlspecialchars($row['recorded_by_name'] ?? 'System') ?></small>
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="edit.php?id=<?= $row['record_id'] ?>" 
                                               class="btn btn-sm btn-warning" 
                                               title="Edit Record">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <a href="?delete=<?= $row['record_id'] ?>" 
                                               class="btn btn-sm btn-danger" 
                                               onclick="return confirm('Are you sure you want to delete this health record?')"
                                               title="Delete Record">
                                                <i class="bi bi-trash"></i>
                                            </a>
                                            <a href="view.php?animal_id=<?= $row['animal_id'] ?>" 
                                               class="btn btn-sm btn-info"
                                               title="View All Records for this Animal">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="text-center py-5">
                    <i class="bi bi-file-medical" style="font-size: 5rem; color: #ccc;"></i>
                    <h4 class="mt-3 text-muted">No Health Records Found</h4>
                    <p class="text-muted">Get started by adding your first health record.</p>
                    
                    <div class="mt-4">
                        <a href="add.php" class="btn btn-success btn-lg">
                            <i class="bi bi-plus-circle"></i> Add First Health Record
                        </a>
                        <a href="../animals/add.php" class="btn btn-primary btn-lg">
                            <i class="bi bi-github"></i> Add Animal First
                        </a>
                    </div>
                    
                    <!-- Debug Info -->
                    <div class="mt-4 text-start bg-light p-3 rounded">
                        <h6>Debug Information:</h6>
                        <?php
                        // Check if there are animals
                        $animal_check = $conn->query("SELECT COUNT(*) as total FROM animals");
                        $animal_count = $animal_check->fetch_assoc()['total'];
                        
                        $record_check = $conn->query("SELECT COUNT(*) as total FROM health_records");
                        $record_count = $record_check->fetch_assoc()['total'];
                        ?>
                        <ul class="list-unstyled">
                            <li>📊 Animals in database: <strong><?= $animal_count ?></strong></li>
                            <li>📊 Health records in database: <strong><?= $record_count ?></strong></li>
                            <?php if($animal_count == 0): ?>
                                <li class="text-warning">⚠️ You need to add animals before adding health records.</li>
                            <?php endif; ?>
                        </ul>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Search Script -->
<script>
document.getElementById('tableSearch')?.addEventListener('keyup', function() {
    const searchTerm = this.value.toLowerCase();
    const tableRows = document.querySelectorAll('#recordsTable tbody tr');
    
    tableRows.forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(searchTerm) ? '' : 'none';
    });
});
</script>

<?php
include '../includes/footer.php';
?>