<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: /lhtm_system/auth/index.php');
    exit;
}

$page_title = 'Reports';
$base_path = '..';
require_once '../config/db.php';

// Get filter parameters
$filter_type = $_GET['type'] ?? 'all';
$filter_date = $_GET['date'] ?? date('Y-m');
$filter_animal = $_GET['animal_id'] ?? '';

// Get all animals for dropdown
$animals = $conn->query("SELECT animal_id, tag_number, species FROM animals ORDER BY tag_number");

// 1. HEALTH RECORDS REPORT
$health_report = $conn->query("
    SELECT 
        h.*,
        a.tag_number,
        a.species,
        a.breed,
        u.name as recorded_by_name
    FROM health_records h
    JOIN animals a ON h.animal_id = a.animal_id
    LEFT JOIN users u ON h.recorded_by = u.user_id
    ORDER BY h.record_date DESC
    LIMIT 100
");

// 2. VACCINATION REPORT - Using health_records table
$vaccination_report = $conn->query("
    SELECT 
        h.*,
        a.tag_number,
        a.species,
        a.breed,
        DATEDIFF(h.next_vaccination, CURDATE()) as days_until_due
    FROM health_records h
    JOIN animals a ON h.animal_id = a.animal_id
    WHERE h.vaccination IS NOT NULL 
    AND h.vaccination != ''
    ORDER BY 
        CASE 
            WHEN h.next_vaccination < CURDATE() THEN 0
            WHEN h.next_vaccination <= DATE_ADD(CURDATE(), INTERVAL 7 DAY) THEN 1
            ELSE 2
        END,
        h.next_vaccination ASC
");

// 3. ANIMAL SUMMARY REPORT
$animal_summary = $conn->query("
    SELECT 
        a.*,
        COUNT(DISTINCT h.record_id) as total_health_records,
        COUNT(DISTINCT CASE WHEN h.vaccination IS NOT NULL AND h.vaccination != '' THEN h.record_id END) as total_vaccinations,
        MAX(h.record_date) as last_visit,
        u.name as registered_by_name
    FROM animals a
    LEFT JOIN health_records h ON a.animal_id = h.animal_id
    LEFT JOIN users u ON a.registered_by = u.user_id
    GROUP BY a.animal_id
    ORDER BY a.created_at DESC
");

// 4. OVERDUE VACCINATIONS - Using health_records
$overdue_vaccinations = $conn->query("
    SELECT 
        h.*,
        a.tag_number,
        a.species,
        a.breed,
        DATEDIFF(CURDATE(), h.next_vaccination) as days_overdue
    FROM health_records h
    JOIN animals a ON h.animal_id = a.animal_id
    WHERE h.next_vaccination < CURDATE()
    AND h.vaccination IS NOT NULL 
    AND h.vaccination != ''
    ORDER BY days_overdue DESC
");

// 5. MONTHLY STATISTICS
$monthly_stats = $conn->query("
    SELECT 
        DATE_FORMAT(record_date, '%Y-%m') as month,
        COUNT(*) as total_records,
        COUNT(DISTINCT animal_id) as animals_treated,
        SUM(CASE WHEN vaccination IS NOT NULL AND vaccination != '' THEN 1 ELSE 0 END) as vaccinations
    FROM health_records
    GROUP BY DATE_FORMAT(record_date, '%Y-%m')
    ORDER BY month DESC
    LIMIT 12
");

// 6. SPECIES DISTRIBUTION
$species_distribution = $conn->query("
    SELECT 
        species,
        COUNT(*) as total,
        SUM(CASE WHEN status = 'active' THEN 1 ELSE 0 END) as active
    FROM animals
    GROUP BY species
    ORDER BY total DESC
");

include '../includes/header.php';
include '../includes/navbar.php';
?>

<div class="container mt-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>
            <i class="bi bi-file-text text-primary"></i> 
            Livestock Health Reports
        </h2>
        <div>
            <button onclick="window.print()" class="btn btn-outline-secondary">
                <i class="bi bi-printer"></i> Print Report
            </button>
            <a href="?export=csv" class="btn btn-success">
                <i class="bi bi-download"></i> Export CSV
            </a>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Report Type</label>
                    <select name="type" class="form-select">
                        <option value="all" <?= $filter_type == 'all' ? 'selected' : '' ?>>All Reports</option>
                        <option value="health" <?= $filter_type == 'health' ? 'selected' : '' ?>>Health Records</option>
                        <option value="vaccination" <?= $filter_type == 'vaccination' ? 'selected' : '' ?>>Vaccination Schedule</option>
                        <option value="animals" <?= $filter_type == 'animals' ? 'selected' : '' ?>>Animal Summary</option>
                        <option value="overdue" <?= $filter_type == 'overdue' ? 'selected' : '' ?>>Overdue Vaccinations</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Month</label>
                    <input type="month" name="date" class="form-control" value="<?= $filter_date ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Animal</label>
                    <select name="animal_id" class="form-select">
                        <option value="">All Animals</option>
                        <?php while($animal = $animals->fetch_assoc()): ?>
                            <option value="<?= $animal['animal_id'] ?>" <?= $filter_animal == $animal['animal_id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($animal['tag_number']) ?> (<?= $animal['species'] ?>)
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-filter"></i> Apply Filters
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h6 class="text-white-50">Total Health Records</h6>
                    <h3 class="text-white"><?= $health_report->num_rows ?></h3>
                    <small>Last 100 records</small>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h6 class="text-white-50">Vaccinations</h6>
                    <h3 class="text-white"><?= $vaccination_report->num_rows ?></h3>
                    <small>Scheduled & Given</small>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card bg-warning text-dark">
                <div class="card-body">
                    <h6 class="text-dark-50">Overdue Vaccinations</h6>
                    <h3 class="text-dark"><?= $overdue_vaccinations->num_rows ?></h3>
                    <small>Need immediate attention</small>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <h6 class="text-white-50">Total Animals</h6>
                    <h3 class="text-white"><?= $animal_summary->num_rows ?></h3>
                    <small>In the system</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Reports Section -->
    <div class="row">
        <!-- Health Records Report -->
        <?php if($filter_type == 'all' || $filter_type == 'health'): ?>
        <div class="col-12 mb-4">
            <div class="card shadow">
                <div class="card-header bg-white">
                    <h5 class="mb-0">
                        <i class="bi bi-file-medical text-primary"></i> 
                        Recent Health Records
                    </h5>
                </div>
                <div class="card-body">
                    <?php if($health_report && $health_report->num_rows > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-hover table-sm">
                                <thead class="table-light">
                                    <tr>
                                        <th>Date</th>
                                        <th>Animal</th>
                                        <th>Species</th>
                                        <th>Diagnosis</th>
                                        <th>Treatment</th>
                                        <th>Vaccination</th>
                                        <th>Next Due</th>
                                        <th>Recorded By</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while($row = $health_report->fetch_assoc()): ?>
                                        <tr>
                                            <td><?= date('M d, Y', strtotime($row['record_date'])) ?></td>
                                            <td>
                                                <strong><?= htmlspecialchars($row['tag_number'] ?? '') ?></strong>
                                            </td>
                                            <td><?= htmlspecialchars($row['species'] ?? '') ?></td>
                                            <td><?= htmlspecialchars(substr($row['diagnosis'] ?? '', 0, 30)) ?>...</td>
                                            <td><?= htmlspecialchars(substr($row['treatment'] ?? '', 0, 30)) ?>...</td>
                                            <td>
                                                <?php if($row['vaccination']): ?>
                                                    <span class="badge bg-info"><?= htmlspecialchars($row['vaccination']) ?></span>
                                                <?php else: ?>
                                                    <span class="badge bg-secondary">None</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if($row['next_vaccination']): ?>
                                                    <?= date('M d, Y', strtotime($row['next_vaccination'])) ?>
                                                <?php else: ?>
                                                    -
                                                <?php endif; ?>
                                            </td>
                                            <td><?= htmlspecialchars($row['recorded_by_name'] ?? 'System') ?></td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <p class="text-muted text-center py-3 mb-0">No health records found</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Vaccination Schedule Report -->
        <?php if($filter_type == 'all' || $filter_type == 'vaccination'): ?>
        <div class="col-12 mb-4">
            <div class="card shadow">
                <div class="card-header bg-white">
                    <h5 class="mb-0">
                        <i class="bi bi-calendar-check text-success"></i> 
                        Vaccination Schedule
                    </h5>
                </div>
                <div class="card-body">
                    <?php if($vaccination_report && $vaccination_report->num_rows > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-hover table-sm">
                                <thead class="table-light">
                                    <tr>
                                        <th>Animal</th>
                                        <th>Species</th>
                                        <th>Vaccination</th>
                                        <th>Next Due Date</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while($row = $vaccination_report->fetch_assoc()): ?>
                                        <tr class="<?= 
                                            ($row['days_until_due'] < 0) ? 'table-danger' : 
                                            (($row['days_until_due'] <= 7) ? 'table-warning' : '') 
                                        ?>">
                                            <td>
                                                <strong><?= htmlspecialchars($row['tag_number'] ?? '') ?></strong>
                                            </td>
                                            <td><?= htmlspecialchars($row['species'] ?? '') ?></td>
                                            <td><?= htmlspecialchars($row['vaccination'] ?? '') ?></td>
                                            <td>
                                                <?php if($row['next_vaccination']): ?>
                                                    <?= date('M d, Y', strtotime($row['next_vaccination'])) ?>
                                                <?php else: ?>
                                                    Not scheduled
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php
                                                $days = $row['days_until_due'] ?? 0;
                                                if($days < 0) {
                                                    echo '<span class="badge bg-danger">Overdue by ' . abs($days) . ' days</span>';
                                                } elseif($days == 0) {
                                                    echo '<span class="badge bg-warning text-dark">Due Today</span>';
                                                } elseif($days <= 7) {
                                                    echo '<span class="badge bg-warning text-dark">Due in ' . $days . ' days</span>';
                                                } else {
                                                    echo '<span class="badge bg-success">On Schedule</span>';
                                                }
                                                ?>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <p class="text-muted text-center py-3 mb-0">No vaccination records found</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Overdue Vaccinations -->
        <?php if($filter_type == 'all' || $filter_type == 'overdue'): ?>
        <div class="col-12 mb-4">
            <div class="card shadow border-danger">
                <div class="card-header bg-danger text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-exclamation-triangle"></i> 
                        Overdue Vaccinations - Immediate Action Required
                    </h5>
                </div>
                <div class="card-body">
                    <?php if($overdue_vaccinations && $overdue_vaccinations->num_rows > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Animal</th>
                                        <th>Species</th>
                                        <th>Vaccination</th>
                                        <th>Due Date</th>
                                        <th>Days Overdue</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while($row = $overdue_vaccinations->fetch_assoc()): ?>
                                        <tr>
                                            <td>
                                                <strong><?= htmlspecialchars($row['tag_number'] ?? '') ?></strong>
                                            </td>
                                            <td><?= htmlspecialchars($row['species'] ?? '') ?></td>
                                            <td><?= htmlspecialchars($row['vaccination'] ?? '') ?></td>
                                            <td><?= date('M d, Y', strtotime($row['next_vaccination'])) ?></td>
                                            <td>
                                                <span class="badge bg-danger"><?= $row['days_overdue'] ?> days</span>
                                            </td>
                                            <td>
                                                <a href="../health/add.php?animal_id=<?= $row['animal_id'] ?>" 
                                                   class="btn btn-sm btn-warning">
                                                    <i class="bi bi-plus-circle"></i> Add Record
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-success mb-0">
                            <i class="bi bi-check-circle"></i> 
                            No overdue vaccinations! All vaccinations are up to date.
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Animal Summary Report -->
        <?php if($filter_type == 'all' || $filter_type == 'animals'): ?>
        <div class="col-md-6 mb-4">
            <div class="card shadow h-100">
                <div class="card-header bg-white">
                    <h5 class="mb-0">
                        <i class="bi bi-github text-primary"></i> 
                        Animal Summary
                    </h5>
                </div>
                <div class="card-body">
                    <?php if($animal_summary && $animal_summary->num_rows > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Tag</th>
                                        <th>Species</th>
                                        <th>Health Records</th>
                                        <th>Last Visit</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while($row = $animal_summary->fetch_assoc()): ?>
                                        <tr>
                                            <td>
                                                <strong><?= htmlspecialchars($row['tag_number'] ?? '') ?></strong>
                                            </td>
                                            <td><?= htmlspecialchars($row['species'] ?? '') ?></td>
                                            <td class="text-center"><?= $row['total_health_records'] ?? 0 ?></td>
                                            <td>
                                                <?= $row['last_visit'] ? date('M d, Y', strtotime($row['last_visit'])) : 'Never' ?>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <p class="text-muted text-center py-3 mb-0">No animals found</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Species Distribution -->
        <div class="col-md-6 mb-4">
            <div class="card shadow h-100">
                <div class="card-header bg-white">
                    <h5 class="mb-0">
                        <i class="bi bi-pie-chart text-success"></i> 
                        Species Distribution
                    </h5>
                </div>
                <div class="card-body">
                    <?php if($species_distribution && $species_distribution->num_rows > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Species</th>
                                        <th>Total</th>
                                        <th>Active</th>
                                        <th>Percentage</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    $total_animals = $animal_summary->num_rows;
                                    while($row = $species_distribution->fetch_assoc()): 
                                        $percentage = $total_animals > 0 ? round(($row['total'] / $total_animals) * 100) : 0;
                                    ?>
                                        <tr>
                                            <td><?= htmlspecialchars($row['species'] ?? 'Unknown') ?></td>
                                            <td class="text-center"><?= $row['total'] ?></td>
                                            <td class="text-center"><?= $row['active'] ?></td>
                                            <td>
                                                <div class="progress">
                                                    <div class="progress-bar bg-success" style="width: <?= $percentage ?>%">
                                                        <?= $percentage ?>%
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <p class="text-muted text-center py-3 mb-0">No species data available</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Monthly Statistics -->
        <?php if($filter_type == 'all'): ?>
        <div class="col-12 mb-4">
            <div class="card shadow">
                <div class="card-header bg-white">
                    <h5 class="mb-0">
                        <i class="bi bi-bar-chart text-info"></i> 
                        Monthly Activity (Last 12 Months)
                    </h5>
                </div>
                <div class="card-body">
                    <?php if($monthly_stats && $monthly_stats->num_rows > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Month</th>
                                        <th class="text-center">Health Records</th>
                                        <th class="text-center">Animals Treated</th>
                                        <th class="text-center">Vaccinations</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while($row = $monthly_stats->fetch_assoc()): ?>
                                        <tr>
                                            <td><?= date('F Y', strtotime($row['month'] . '-01')) ?></td>
                                            <td class="text-center"><?= $row['total_records'] ?></td>
                                            <td class="text-center"><?= $row['animals_treated'] ?></td>
                                            <td class="text-center"><?= $row['vaccinations'] ?></td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <p class="text-muted text-center py-3 mb-0">No monthly data available</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <!-- Export Note -->
    <div class="alert alert-info mt-3">
        <i class="bi bi-info-circle"></i> 
        <strong>Note:</strong> CSV export functionality will be available in the next update.
    </div>
</div>

<?php
include '../includes/footer.php';
?>