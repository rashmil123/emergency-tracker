<?php
include "../includes/conn.php";

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

// AJAX HANDLER
if (isset($_GET['stats'])) {
    $pending = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS c FROM reports WHERE status='Pending'"))['c'];
    $total = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS c FROM reports"))['c'];

    echo json_encode([
        "pending" => $pending,
        "total" => $total
    ]);
    exit;
}

// INITIAL LOAD
$pendingCount = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS c FROM reports WHERE status='Pending'"))['c'];
$totalReports = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS c FROM reports"))['c'];
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
    margin: 0;
}

/* HEADER */
.header {
    background: #e63946;
    color: white;
    padding: 20px;
    text-align: center;
}

/* CONTAINER */
.container {
    padding: 20px;
}

/* STATS */
.stats {
    display: flex;
    gap: 20px;
    flex-wrap: wrap;
    margin-bottom: 30px;
}

.stat-card {
    flex: 1;
    min-width: 200px;
    background: white;
    padding: 20px;
    border-radius: 12px;
    text-align: center;
    box-shadow: 0 2px 6px rgba(0,0,0,0.1);
}

.stat-card h2 {
    color: #e63946;
}

/* BUTTON GRID */
.grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
}

.btn {
    background: #e63946;
    color: white;
    padding: 25px;
    text-align: center;
    border-radius: 12px;
    text-decoration: none;
    font-size: 18px;
    position: relative;
    transition: 0.3s;
}

.btn:hover {
    background: #c5303f;
    transform: translateY(-3px);
}

/* BADGE */
.badge {
    position: absolute;
    top: 10px;
    right: 15px;
    background: red;
    border-radius: 50%;
    padding: 5px 10px;
}

/* LOGOUT */
.logout {
    background: #555;
}
.logout:hover {
    background: #333;
}
</style>

</head>
<body>

<div class="header">
    <h1>Admin Dashboard</h1>
    <p>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?></p>
</div>

<div class="container">

    <!-- STATS -->
    <div class="stats">
        <div class="stat-card">
            <h3>Total Reports</h3>
            <h2 id="total"><?php echo $totalReports; ?></h2>
        </div>

        <div class="stat-card">
            <h3>Pending Reports</h3>
            <h2 id="pending"><?php echo $pendingCount; ?></h2>
        </div>
    </div>

    <!-- BUTTONS -->
    <div class="grid">
        <a href="pending_reports.php" class="btn">
            📄 Pending Reports
            <span id="badge" class="badge" 
                style="<?php echo ($pendingCount > 0) ? '' : 'display:none;'; ?>">
                <?php echo $pendingCount; ?>
            </span>
        </a>

        <a href="report.php" class="btn">📊 Report Summary</a>

        <a href="user.php" class="btn">👥 Manage Users</a>

        <a href="../auth/logout.php" id="logoutBtn" class="btn logout">🚪 Logout</a>
    </div>

</div>

<!-- SOUND -->
<audio id="alertSound">
    <source src="alert.mp3" type="audio/mpeg">
</audio>

<script>
let lastPending = <?php echo $pendingCount; ?>;

// FETCH STATS
function fetchStats() {
    let xhr = new XMLHttpRequest();
    xhr.open("GET", "?stats=1", true);

    xhr.onload = function () {
        let data = JSON.parse(this.responseText);

        // Update UI
        document.getElementById("pending").innerText = data.pending;
        document.getElementById("total").innerText = data.total;

        let badge = document.getElementById("badge");

        if (data.pending > 0) {
            badge.style.display = "inline-block";
            badge.innerText = data.pending;
        } else {
            badge.style.display = "none";
        }

        // 🔔 PLAY SOUND IF NEW REPORT
        if (data.pending > lastPending) {
            document.getElementById("alertSound").play();
        }

        lastPending = data.pending;
    };

    xhr.send();
}

// AUTO REFRESH EVERY 5 SEC
setInterval(fetchStats, 5000);

// LOGOUT CONFIRM
document.getElementById("logoutBtn").addEventListener("click", function(e){
    if (!confirm("Logout?")) e.preventDefault();
});
</script>

</body>
</html>