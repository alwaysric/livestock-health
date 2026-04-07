<?php
session_start();
if (!isset($_SESSION['user_id'])) exit;
include '../config/db.php';

$today = date('Y-m-d');
$result = $conn->query("
    SELECT a.tag_number, h.next_vaccination, h.vaccination
    FROM health_records h
    JOIN animals a ON h.animal_id = a.animal_id
    WHERE h.next_vaccination <= '$today'
");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Alerts</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-4">
    <h3>⚠️ Vaccination Alerts</h3>
    <?php if($result->num_rows == 0): ?>
        <div class="alert alert-success">No due vaccinations.</div>
    <?php else: ?>
        <table class="table table-warning">
            <tr><th>Tag</th><th>Vaccine</th><th>Due Date</th></tr>
            <?php while($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= $row['tag_number'] ?></td>
                <td><?= $row['vaccination'] ?></td>
                <td><?= $row['next_vaccination'] ?></td>
            </tr>
            <?php endwhile; ?>
        </table>
    <?php endif; ?>
    <a href="../dashboard.php" class="btn btn-secondary">Back</a>
</body>
</html>