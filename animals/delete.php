<?php
include '../config/db.php';
$id = $_GET['id'];
$conn->query("DELETE FROM animals WHERE animal_id = $id");
header('Location: index.php');
?>