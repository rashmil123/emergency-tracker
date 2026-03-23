<?php
include "../includes/conn.php";

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

// Get count of pending reports
$res = mysqli_query($conn, "SELECT COUNT(*) AS pending_count FROM reports WHERE status='Pending'");
$row = mysqli_fetch_assoc($res);
$pendingCount = $row['pending_count'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Dashboard</title>

<style>
body {
    font-family: 'Segoe UI', sans-serif;
    background: #f5f7fb;
    color: #333;
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 40px 20px;
    margin: 0;
}

/* Title */
h1 {
    color: #e63946;
    margin-bottom: 40px;
}

/* Buttons */
.btn {
    background: #e63946;
    color: white;
    border: none;
    padding: 20px 40px;
    margin: 15px;
    font-size: 20px;
    border-radius: 12px;
    cursor: pointer;
    position: relative;
    min-width: 240px;
    text-align: center;
    text-decoration: none;
    display: inline-block;
    transition: all 0.3s;
}

.btn:hover {
    background: #c5303f;
    transform: translateY(-3px);
}

/* Notification badge */
.notification-badge {
    position: absolute;
    top: 10px;
    right: 15px;
    background: red;
    color: white;
    border-radius: 50%;
    padding: 5px 10px;
    font-weight: bold;
    font-size: 14px;
}

/* Logout style */
.logout {
    background: #555;
}

.logout:hover {
    background: #333;
}
</style>

</head>
<body>

<h1>Welcome Admin: <?php echo htmlspecialchars($_SESSION['username']); ?></h1>

<!-- Pending Reports -->
<a href="pending_reports.php" class="btn">
    Pending Reports
    <?php if ($pendingCount > 0): ?>
        <span class="notification-badge"><?php echo $pendingCount; ?></span>
    <?php endif; ?>
</a>

<!-- Report Summary -->
<a href="report.php" class="btn">
    Report Summary
</a>

<!-- Users -->
<a href="user.php" class="btn">
    View Users
</a>

<!-- Logout -->
<a href="../auth/logout.php" class="btn logout"
   onclick="return confirm('Are you sure you want to logout?');">
   Logout
</a>

</body>
</html>