<?php
include "../includes/conn.php";

if(!isset($_SESSION['user_id'])){
    echo "Not logged in";
    exit();
}

$user_id = $_SESSION['user_id'];
$lat = $_POST['latitude'];
$lng = $_POST['longitude'];
$message = isset($_POST['message']) ? $_POST['message'] : "Emergency sent via app";

// Insert report with Pending status
$stmt = $conn->prepare("INSERT INTO reports (user_id, message, latitude, longitude, status) VALUES (?, ?, ?, ?, 'Pending')");
$stmt->bind_param("issd", $user_id, $message, $lat, $lng);

if($stmt->execute()){
    echo "✅ Emergency sent! Waiting for admin confirmation.";
}else{
    echo "❌ Failed to send emergency";
}
?>