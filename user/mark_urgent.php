<?php
include "../includes/conn.php";

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Protect page: only logged-in users
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'user') {
    echo "Unauthorized access.";
    exit();
}

// Check POST parameter
if (!isset($_POST['id']) || !is_numeric($_POST['id'])) {
    echo "Invalid report ID.";
    exit();
}

$report_id = intval($_POST['id']);
$user_id = $_SESSION['user_id'];

// Verify that the report belongs to this user and is pending
$res = mysqli_query($conn, "SELECT * FROM reports WHERE id='$report_id' AND user_id='$user_id' AND status='Pending'");

if (mysqli_num_rows($res) === 0) {
    echo "Report not found or cannot be marked urgent.";
    exit();
}

// Update status to 'Urgent'
$update = mysqli_query($conn, "UPDATE reports SET status='Urgent' WHERE id='$report_id'");

if ($update) {
    echo "Report marked as urgent successfully!";
} else {
    echo "Failed to mark report as urgent. Please try again.";
}
?>