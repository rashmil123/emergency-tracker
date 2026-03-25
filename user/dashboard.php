<?php
include "../includes/conn.php";

if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'user') {
    header("Location: ../auth/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>User Dashboard</title>
<link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
<style>
/* ===== Body & Font ===== */
body {
    margin: 0;
    font-family: 'Roboto', sans-serif;
    background: #f5f7fb;
    display: flex;
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
    font-size: 20px;
    text-align: center;
    padding: 20px 10px;
    border-bottom: 1px solid rgba(255,255,255,0.3);
    margin: 0;
}

.sidebar a {
    color: white;
    text-decoration: none;
    padding: 15px 20px;
    display: flex;
    align-items: center;
    gap: 10px;
    transition: background 0.3s;
}

.sidebar a:hover {
    background: #c5303f;
}

/* ===== MAIN CONTENT ===== */
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
    padding: 10px 20px;
    border-radius: 12px;
    box-shadow: 0 2px 6px rgba(0,0,0,0.1);
    margin-bottom: 20px;
}

.header .logo {
    font-weight: 700;
    color: #e63946;
    font-size: 20px;
}

.profile {
    position: relative;
    cursor: pointer;
}

.profile-icon {
    font-size: 28px;
}

/* ===== Dropdown Menu ===== */
.dropdown {
    position: absolute;
    right: 0;
    top: 50px;
    background: white;
    border-radius: 8px;
    display: none;
    min-width: 140px;
    box-shadow: 0 2px 6px rgba(0,0,0,0.15);
}

.dropdown a {
    display: block;
    padding: 10px;
    text-decoration: none;
    color: #e63946;
    font-weight: 500;
}

.dropdown a:hover {
    background: #f5f5f5;
}

/* ===== CARD ===== */
.card {
    background: white;
    padding: 20px;
    border-radius: 12px;
    box-shadow: 0 2px 6px rgba(0,0,0,0.1);
    margin-bottom: 20px;
}

/* ===== Textarea ===== */
textarea {
    width: 100%;
    padding: 12px;
    border-radius: 8px;
    border: 1px solid #ccc;
    font-size: 15px;
    resize: none;
}

/* ===== Buttons ===== */
button.emergency-btn {
    background: #e63946;
    color: white;
    padding: 12px;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    font-size: 16px;
    margin-top: 10px;
    width: 100%;
    max-width: 300px;
    display: block;
    transition: background 0.3s;
}
button.emergency-btn:hover {
    background: #c5303f;
}

a.reports-btn {
    background: #e63946;
    color: white;
    padding: 10px 15px;
    text-decoration: none;
    border-radius: 8px;
    display: inline-block;
    margin-top: 10px;
    transition: background 0.3s;
}
a.reports-btn:hover {
    background: #c5303f;
}

/* ===== Status Text ===== */
#status {
    margin-top: 10px;
    font-size: 16px;
    color: green;
    text-align: center;
}

/* ===== MOBILE ===== */
@media(max-width:768px){
    body {
        flex-direction: column;
    }
    .sidebar {
        width: 100%;
        flex-direction: row;
        overflow-x: auto;
        min-height: auto;
    }
    .sidebar h2 {
        display: none;
    }
    .sidebar a {
        flex: 1;
        justify-content: center;
        padding: 12px;
    }
}
</style>
</head>
<body>

<!-- ===== SIDEBAR ===== -->
<div class="sidebar">
    <h2>EMERGENCY TRACKER 24/7</h2>
    <a href="#"><span>🏠</span> Dashboard</a>
    <a href="reports.php"><span>📄</span> Reports</a>
    <a href="profile.php"><span>👤</span> Profile</a>
    <a href="../auth/logout.php"><span>🚪</span> Logout</a>
</div>

<!-- ===== MAIN CONTENT ===== -->
<div class="main">
    <div class="header">
        <div class="logo">Dashboard</div>
        <div class="profile" onclick="toggleDropdown()">
            <span class="profile-icon">👤</span>
            <div class="dropdown" id="profileDropdown">
                <a href="profile.php">Profile</a>
                <a href="../auth/logout.php">Logout</a>
            </div>
        </div>
    </div>

    <!-- EMERGENCY SUBMISSION CARD -->
    <div class="card">
        <h3>🚨 Submit Emergency Report</h3>
        <textarea id="message" rows="4" placeholder="Describe your emergency..."></textarea>
        <button class="emergency-btn" onclick="sendEmergency()">Send Emergency</button>
        <div id="status"></div>
        <a href="reports.php" class="reports-btn">View All Reports</a>
    </div>
</div>

<!-- ===== JAVASCRIPT ===== -->
<script>
function toggleDropdown() {
    let dropdown = document.getElementById('profileDropdown');
    dropdown.style.display = dropdown.style.display === 'block' ? 'none' : 'block';
}

// Close dropdown when clicking outside
window.addEventListener('click', function(e){
    if(!document.querySelector('.profile').contains(e.target)){
        document.getElementById('profileDropdown').style.display = 'none';
    }
});

// SEND EMERGENCY FUNCTION
function sendEmergency() {
    let message = document.getElementById('message').value.trim();
    if(message === "") { alert("Please describe your emergency."); return; }

    if(navigator.geolocation){
        navigator.geolocation.getCurrentPosition(function(position){
            let lat = position.coords.latitude;
            let lng = position.coords.longitude;

            let xhr = new XMLHttpRequest();
            xhr.open("POST","report.php",true);
            xhr.setRequestHeader("Content-type","application/x-www-form-urlencoded");
            xhr.onload = function(){
                document.getElementById("status").innerText = this.responseText;
                document.getElementById("message").value="";
            };
            xhr.send("latitude="+lat+"&longitude="+lng+"&message="+encodeURIComponent(message));
        }, function(){
            alert("Geolocation permission denied. Cannot send emergency.");
        });
    } else { 
        alert("Geolocation not supported by this browser."); 
    }
}
</script>

</body>
</html>