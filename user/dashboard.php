<?php
include "../includes/conn.php";
if (session_status() === PHP_SESSION_NONE) session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'user') {
    header("Location: ../auth/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// HANDLE AJAX REQUEST (INSERT REPORT)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['message'])) {
    $message = trim($_POST['message']);
    $lat = $_POST['latitude'] ?? null;
    $lng = $_POST['longitude'] ?? null;

    if ($message == "") {
        echo "Please describe your emergency.";
        exit;
    }

    $stmt = $conn->prepare("INSERT INTO reports (user_id, message, latitude, longitude) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("isss", $user_id, $message, $lat, $lng);

    if ($stmt->execute()) {
        echo "✅ Emergency sent successfully!";
    } else {
        echo "❌ Error: " . $conn->error;
    }
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>User Dashboard</title>
<link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">

<style>
/* ===== BODY ===== */
body {
    margin: 0;
    font-family: 'Roboto', sans-serif;
    background: #f5f7fb;
    display: flex;
    min-height: 100vh;
}

/* ===== SIDEBAR ===== */
.sidebar {
    width: 220px;
    background: #e63946;
    min-height: 100vh;
    color: white;
    display: flex;
    flex-direction: column;
    position: sticky;
    top: 0;
}
.sidebar h2 {
    text-align: center;
    padding: 20px;
    margin: 0;
    font-size: 18px;
}
.sidebar a {
    color: white;
    text-decoration: none;
    padding: 15px 20px;
    display: flex;
    align-items: center;
    gap: 10px;
    font-weight: 500;
    transition: 0.3s;
}
.sidebar a:hover {
    background: #c5303f;
}

/* ===== MAIN AREA ===== */
.main {
    flex: 1;
    padding: 20px;
}

/* ===== HEADER ===== */
.header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: white;
    padding: 12px 20px;
    border-radius: 12px;
    margin-bottom: 20px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.05);
}

/* ===== PROFILE DROPDOWN ===== */
.profile { position: relative; cursor: pointer; font-weight: 500; }
.dropdown {
    position: absolute;
    right: 0;
    top: 40px;
    background: white;
    display: none;
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    z-index: 10;
}
.dropdown a {
    display: block;
    padding: 10px 20px;
    color: #e63946;
    text-decoration: none;
}
.dropdown a:hover { background: #f5f5f5; }

/* ===== CARD ===== */
.card {
    background: white;
    padding: 30px 25px;
    border-radius: 16px;
    box-shadow: 0 6px 20px rgba(0,0,0,0.1);
    max-width: 600px;
    margin: auto;
    transition: transform 0.3s, box-shadow 0.3s;
}
.card:hover {
    transform: translateY(-3px);
    box-shadow: 0 12px 30px rgba(0,0,0,0.15);
}

/* CARD TITLE */
.card h3 {
    color: #e63946;
    font-size: 26px;
    margin-bottom: 20px;
    text-align: center;
    letter-spacing: 0.5px;
}

/* FORM GROUP */
.form-group {
    margin-bottom: 20px;
    display: flex;
    flex-direction: column;
}
.form-group label {
    font-weight: 500;
    margin-bottom: 8px;
    color: #333;
}
textarea {
    width: 100%;
    padding: 14px;
    border-radius: 12px;
    border: 1px solid #ccc;
    font-size: 15px;
    resize: none;
    transition: border 0.3s, box-shadow 0.3s;
}
textarea:focus {
    border-color: #e63946;
    box-shadow: 0 0 12px rgba(230,57,70,0.2);
    outline: none;
}

/* BUTTONS */
.button-group {
    display: flex;
    justify-content: space-between;
    gap: 10px;
}
button {
    flex: 1;
    padding: 14px;
    border-radius: 12px;
    border: none;
    font-size: 16px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.3s;
}
#sendBtn {
    background: #e63946;
    color: white;
    box-shadow: 0 4px 12px rgba(230,57,70,0.3);
}
#sendBtn:hover {
    background: #c5303f;
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(230,57,70,0.4);
}
button.secondary {
    background: #f5f5f5;
    color: #333;
    border: 1px solid #ccc;
}
button.secondary:hover {
    background: #eaeaea;
}

/* STATUS TEXT */
#status {
    margin-top: 15px;
    text-align: center;
    font-weight: 500;
    color: green;
}

/* VIEW REPORTS LINK */
.view-reports {
    display: block;
    margin-top: 20px;
    text-align: center;
    color: #e63946;
    text-decoration: none;
    font-weight: 500;
    transition: 0.3s;
}
.view-reports:hover {
    text-decoration: underline;
}

/* RESPONSIVE */
@media(max-width: 768px){
    .sidebar { width: 60px; }
    .sidebar h2 { display: none; }
    .sidebar a { justify-content: center; }
    .card { width: 100%; padding: 25px 20px; }
    .button-group { flex-direction: column; }
}
</style>
</head>
<body>

<!-- SIDEBAR -->
<div class="sidebar">
    <h2>EMERGENCY 24/7</h2>
    <a href="#">🏠 Dashboard</a>
    <a href="reports.php">📄 Reports</a>
    <a href="profile.php">👤 Profile</a>
    <a href="../auth/logout.php">🚪 Logout</a>
</div>

<!-- MAIN CONTENT -->
<div class="main">
    <div class="header">
        <div><strong>Dashboard</strong></div>
        <div class="profile" onclick="toggleDropdown()">
            👤 <?php echo htmlspecialchars($_SESSION['username']); ?>
            <div class="dropdown" id="dropdown">
                <a href="profile.php">Profile</a>
                <a href="../auth/logout.php">Logout</a>
            </div>
        </div>
    </div>

    <!-- EMERGENCY CARD -->
    <div class="card">
        <h3>🚨 Emergency Report</h3>

        <div class="form-group">
            <label for="message">Describe your emergency:</label>
            <textarea id="message" placeholder="E.g., Fire at 3rd floor, need immediate help..." rows="4"></textarea>
        </div>

        <div class="button-group">
            <button id="sendBtn" onclick="sendEmergency()">Send Emergency</button>
            <button id="clearBtn" onclick="clearMessage()" class="secondary">Clear</button>
        </div>

        <div id="status"></div>
        <a href="reports.php" class="view-reports">📄 View Past Reports</a>
    </div>
</div>

<script>
function toggleDropdown() {
    let d = document.getElementById('dropdown');
    d.style.display = d.style.display === 'block' ? 'none' : 'block';
}
window.onclick = function(e){
    if(!e.target.closest('.profile')){
        document.getElementById('dropdown').style.display = 'none';
    }
};

function clearMessage() {
    document.getElementById('message').value = '';
    document.getElementById('status').innerText = '';
}

function sendEmergency() {
    let message = document.getElementById('message').value.trim();
    if(message === ""){
        alert("Please describe your emergency.");
        return;
    }

    if(navigator.geolocation){
        navigator.geolocation.getCurrentPosition(function(pos){
            let xhr = new XMLHttpRequest();
            xhr.open("POST","",true);
            xhr.setRequestHeader("Content-type","application/x-www-form-urlencoded");
            xhr.onload = function(){
                document.getElementById("status").innerText = this.responseText;
                document.getElementById("message").value = "";
            };
            xhr.send(
                "message=" + encodeURIComponent(message) +
                "&latitude=" + pos.coords.latitude +
                "&longitude=" + pos.coords.longitude
            );
        }, function(){
            alert("Location access denied.");
        });
    } else {
        alert("Geolocation not supported.");
    }
}
</script>

</body>
</html>