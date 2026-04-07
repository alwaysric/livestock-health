<?php
session_start();
include '../config/db.php';
$id = $_GET['id'];
$animal = $conn->query("SELECT * FROM animals WHERE animal_id = $id")->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tag = $_POST['tag'];
    $species = $_POST['species'];
    $breed = $_POST['breed'];
    $dob = $_POST['dob'];
    $stmt = $conn->prepare("UPDATE animals SET tag_number=?, species=?, breed=?, date_of_birth=? WHERE animal_id=?");
    $stmt->bind_param("ssssi", $tag, $species, $breed, $dob, $id);
    $stmt->execute();
    header('Location: index.php');
}
?>
<form method="POST">
    <input name="tag" value="<?= $animal['tag_number'] ?>">
    <input name="species" value="<?= $animal['species'] ?>">
    <input name="breed" value="<?= $animal['breed'] ?>">
    <input type="date" name="dob" value="<?= $animal['date_of_birth'] ?>">
    <button>Update</button>
</form>