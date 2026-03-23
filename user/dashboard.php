<?php 
include "../includes/conn.php";

// Protect page
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'user') {
    header("Location: ../auth/login.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>User Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body { font-family: Arial; background: #1d1d1d; color: white; padding: 10px; margin:0; }
        h2 { color: #e63946; text-align: center; }
        textarea { width: 95%; padding:10px; margin:10px 0; border-radius:8px; font-size:16px; resize:none; }
        .emergency-btn { background:red; color:white; font-size:18px; padding:15px; border:none; border-radius:12px; cursor:pointer; width:95%; max-width:300px; display:block; margin:0 auto; }
        .emergency-btn:hover { background:darkred; }
        #status { margin-top:15px; font-size:16px; color:lime; text-align:center; }
        table { width:100%; border-collapse:collapse; margin-top:20px; background:#fff; color:#000; font-size:14px; }
        th, td { padding:6px; border:1px solid #ddd; text-align:center; }
        th { background:#e63946; color:white; }
        a.logout { color:#e63946; text-decoration:none; display:block; margin:20px auto; font-weight:bold; text-align:center; }

        /* Mobile responsiveness */
        @media(max-width:600px){
            table, thead, tbody, th, td, tr { display:block; }
            tr { margin-bottom:15px; border-bottom:2px solid #e63946; }
            th { display:none; }
            td { text-align:right; padding-left:50%; position:relative; }
            td::before { content:attr(data-label); position:absolute; left:10px; font-weight:bold; color:#e63946; text-align:left; }
        }
    </style>
</head>
<body>

<h2>Welcome, <?php echo $_SESSION['username']; ?></h2>

<textarea id="message" placeholder="Describe your emergency..." rows="4"></textarea>
<button class="emergency-btn" onclick="sendEmergency()">🚨 SEND EMERGENCY</button>

<div id="status"></div>

<h3 style="text-align:center;margin-top:30px;">Your Previous Reports</h3>
<table>
    <tbody>
        <?php
        $user_id = $_SESSION['user_id'];
        $res = mysqli_query($conn, "SELECT * FROM reports WHERE user_id='$user_id' ORDER BY id DESC");
        while($row = mysqli_fetch_assoc($res)){
            echo "<tr>
                    <td data-label='ID'>{$row['id']}</td>
                    <td data-label='Message'>{$row['message']}</td>
                    <td data-label='Status'>{$row['status']}</td>
                    <td data-label='Response'>{$row['response']}</td>
                    <td data-label='Time'>{$row['created_at']}</td>
                  </tr>";
        }
        ?>
    </tbody>
</table>

<a href="../auth/logout.php" class="logout">Logout</a>

<script>
// Logout warning
window.addEventListener('beforeunload', function(e){
    e.preventDefault();
    e.returnValue = 'Are you sure you want to leave or logout? Your unsaved changes may be lost.';
});

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
                location.reload(); // reload history
            };
            xhr.send("latitude="+lat+"&longitude="+lng+"&message="+encodeURIComponent(message));
        }, function(){
            alert("Geolocation permission denied. Cannot send emergency.");
        });
    } else { alert("Geolocation not supported by this browser."); }
}
</script>

</body>
</html>