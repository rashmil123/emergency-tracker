<?php
include "../includes/conn.php";

$id = $_POST['id'];
$response = $_POST['response'];

$stmt = $conn->prepare("UPDATE reports SET response=? WHERE id=?");
$stmt->bind_param("si", $response, $id);
$stmt->execute();

echo "Response updated!";
?>