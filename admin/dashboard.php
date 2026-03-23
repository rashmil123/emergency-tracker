<?php
include "../includes/conn.php";
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

// Get count of pending (unnotified) reports
$res = mysqli_query($conn, "SELECT COUNT(*) AS pending_count FROM reports WHERE status='Pending'");
$row = mysqli_fetch_assoc($res);
$pendingCount = $row['pending_count'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Admin Dashboard</title>
<style>
    body {
        font-family: Arial, sans-serif;
        background: #1d1d1d;
        color: white;
        display: flex;
        flex-direction: column;
        align-items: center;
        padding: 50px 20px;
        height: 100vh;
        margin: 0;
    }
    h1 {
        color: #e63946;
        margin-bottom: 40px;
    }
    .btn {
        background: #e63946;
        color: white;
        border: none;
        padding: 20px 40px;
        margin: 20px;
        font-size: 24px;
        border-radius: 12px;
        cursor: pointer;
        position: relative;
        min-width: 220px;
        text-align: center;
        text-decoration: none;
        display: inline-block;
        transition: background 0.3s;
    }
    .btn:hover {
        background: #c5303f;
    }
    .notification-badge {
        position: absolute;
        top: 10px;
        right: 15px;
        background: red;
        color: white;
        border-radius: 50%;
        padding: 5px 12px;
        font-weight: bold;
        font-size: 16px;
    }
</style>
</head>
<body>

<h1>Welcome Admin: <?php echo htmlspecialchars($_SESSION['username']); ?></h1>

<a href="pending_reports.php" class="btn">
    Pending Reports
    <?php if ($pendingCount > 0): ?>
        <span class="notification-badge"><?php echo $pendingCount; ?></span>
    <?php endif; ?>
</a>

<a href="report.php" class="btn">Report Summary</a>

<a href="../auth/logout.php" class="btn" style="background:#555;" onclick="return confirm('Are you sure you want to logout?');">Logout</a>



</body>
</html>