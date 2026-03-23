<?php
include "../includes/conn.php";

if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin'){
    echo "Access denied";
    exit();
}

$id = $_POST['id'];
$response_message = "Emergency received. Help is on the way!";

$stmt = $conn->prepare("UPDATE reports SET status='Notified', response=? WHERE id=?");
$stmt->bind_param("si", $response_message, $id);

if($stmt->execute()){
    echo "Report notified successfully!";
}else{
    echo "Failed to notify report";
}
?>