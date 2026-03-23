<?php
include "../includes/conn.php";

if(!isset($_SESSION['user_id']) || $_SESSION['role']!='admin'){
    echo json_encode([]); exit();
}

$res = mysqli_query($conn,"SELECT reports.*, users.username FROM reports JOIN users ON reports.user_id=users.id ORDER BY reports.id DESC");

$reports=[];
while($row=mysqli_fetch_assoc($res)){
    $reports[]=$row;
}

echo json_encode($reports);
?>