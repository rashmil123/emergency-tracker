<?php
include "../includes/conn.php";

if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'user') {
    exit("Unauthorized access");
}

$user_id = $_SESSION['user_id'];

// Validate POST data
if (isset($_POST['id'])) {
    $id = intval($_POST['id']);

    // Delete only the report that belongs to the current user
    $query = "DELETE FROM reports WHERE id='$id' AND user_id='$user_id'";
    if (mysqli_query($conn, $query)) {
        echo "Report deleted successfully!";
    } else {
        echo "Failed to delete report.";
    }
} else {
    echo "Invalid input.";
}
?>