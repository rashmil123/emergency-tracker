<?php
include "../includes/conn.php";

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../auth/login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<title>Pending Reports</title>
<style>
    body { font-family: Arial, sans-serif; background:#1d1d1d; color:white; padding:20px; margin:0; }
    h1 { color:#e63946; text-align:center; margin-bottom:20px; }
    table { width:100%; border-collapse:collapse; margin-top:20px; background:#fff; color:#000; font-size:14px; }
    th, td { padding:6px; border:1px solid #ddd; text-align:center; }
    th { background:#e63946; color:white; }
    button.notify-btn { background:#e63946; color:#fff; border:none; padding:5px 10px; border-radius:5px; cursor:pointer; }
    button.notify-btn:hover { background:#c5303f; }
    a { color:#e63946; text-decoration:none; display:inline-block; margin-top:20px; }
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

<h1>Pending Reports</h1>

<table id="pendingTable">
    <thead>
        <tr>
            <th>ID</th>
            <th>User</th>
            <th>Message</th>
            <th>Location</th>
            <th>Time</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $res = mysqli_query($conn, "SELECT reports.*, users.username 
                                    FROM reports 
                                    JOIN users ON reports.user_id = users.id 
                                    WHERE reports.status='Pending' 
                                    ORDER BY reports.created_at DESC");
        while ($row = mysqli_fetch_assoc($res)) {
            echo "<tr>
                    <td data-label='ID'>{$row['id']}</td>
                    <td data-label='User'>{$row['username']}</td>
                    <td data-label='Message'>{$row['message']}</td>
                    <td data-label='Location'><a href='https://www.google.com/maps?q={$row['latitude']},{$row['longitude']}' target='_blank'>View Map</a></td>
                    <td data-label='Time'>{$row['created_at']}</td>
                    <td data-label='Action'>
                        <button class='notify-btn' onclick='notifyReport({$row['id']})'>Notify</button>
                    </td>
                  </tr>";
        }
        ?>
    </tbody>
</table>

<a href="dashboard.php">Back to Dashboard</a>

<script>
function notifyReport(id){
    if(confirm('Notify this report?')){
        fetch('notify_report.php', {
            method: 'POST',
            headers: {'Content-Type':'application/x-www-form-urlencoded'},
            body: 'id='+id
        })
        .then(res => res.text())
        .then(data => {
            alert(data);
            location.reload(); // refresh the table after notifying
        });
    }
}
</script>

</body>
</html>