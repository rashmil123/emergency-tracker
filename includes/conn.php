<?php
$conn = mysqli_connect("localhost", "root", "", "emergency_tracker");

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

session_start();
?>