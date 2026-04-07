<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/index.php');
    exit;
}
require_once '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tag = $_POST['tag'];
    $species = $_POST['species'];
    $breed = $_POST['breed'];
    $dob = $_POST['dob'] ?: null;
    $registered_by = $_SESSION['user_id'];

    $stmt = $conn->prepare("INSERT INTO animals (tag_number, species, breed, date_of_birth, registered_by) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssi", $tag, $species, $breed, $dob, $registered_by);
    
    if ($stmt->execute()) {
        header('Location: index.php?msg=added');
    } else {
        $error = "Failed to add animal. Tag number might be duplicate.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Animal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0"><i class="bi bi-plus-circle"></i> Add New Animal</h4>
                    </div>
                    <div class="card-body">
                        <?php if(isset($error)): ?>
                            <div class="alert alert-danger"><?= $error ?></div>
                        <?php endif; ?>
                        
                        <form method="POST">
                            <div class="mb-3">
                                <label class="form-label">Tag Number <span class="text-danger">*</span></label>
                                <input type="text" name="tag" class="form-control" required 
                                       placeholder="e.g., COW-001, SHEEP-023">
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Species <span class="text-danger">*</span></label>
                                <select name="species" class="form-select" required>
                                    <option value="">Select Species</option>
                                    <option value="Cattle">🐄 Cattle</option>
                                    <option value="Sheep">🐑 Sheep</option>
                                    <option value="Goat">🐐 Goat</option>
                                    <option value="Pig">🐖 Pig</option>
                                    <option value="Horse">🐎 Horse</option>
                                </select>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Breed</label>
                                <input type="text" name="breed" class="form-control" 
                                       placeholder="e.g., Friesian, Merino">
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Date of Birth</label>
                                <input type="date" name="dob" class="form-control">
                            </div>
                            
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-save"></i> Save Animal
                                </button>
                                <a href="index.php" class="btn btn-outline-secondary">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>