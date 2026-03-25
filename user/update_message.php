    <?php
include "../includes/conn.php";

if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'user') {
    exit("Unauthorized access");
}

$user_id = $_SESSION['user_id'];

if (isset($_POST['id']) && isset($_POST['message'])) {
    $id = intval($_POST['id']);
    $message = mysqli_real_escape_string($conn, $_POST['message']);

    // Update only user's own report
    $query = "UPDATE reports SET message='$message' WHERE id='$id' AND user_id='$user_id'";
    if (mysqli_query($conn, $query)) {
        echo "Message updated successfully!";
    } else {
        echo "Failed to update message.";
    }
} else {
    echo "Invalid input.";
}
?>