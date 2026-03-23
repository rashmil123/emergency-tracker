<?php
include "../includes/conn.php";

// Protect page
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
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Admin Reports</title>
<style>
    body { font-family: Arial, sans-serif; background: #1d1d1d; color: white; padding: 10px; margin: 0; }
    h1, h2 { color: #e63946; text-align: center; }
    table { width: 100%; border-collapse: collapse; margin-top: 20px; background: #fff; color: #000; font-size: 14px; }
    th, td { padding: 6px; border: 1px solid #ddd; text-align: center; }
    th { background: #e63946; color: white; }
    a { color: #e63946; text-decoration: none; }
    a:hover { text-decoration: underline; }
    .logout { color: #e63946; text-align: center; display: block; margin: 20px auto; font-weight: bold; }
    button.notify-btn {
        background: #e63946; color: #fff; border: none; padding: 5px 10px; border-radius: 5px; cursor: pointer;
    }
    button.notify-btn:hover { background: #c5303f; }
    .notification-badge {
        background: red; color: white; border-radius: 50%; padding: 4px 8px; font-weight: bold;
        position: relative; top: -10px; left: 5px; font-size: 14px;
    }
    @media(max-width: 600px) {
        table, thead, tbody, th, td, tr { display: block; }
        tr { margin-bottom: 15px; border-bottom: 2px solid #e63946; }
        th { display: none; }
        td { text-align: right; padding-left: 50%; position: relative; }
        td::before {
            content: attr(data-label);
            position: absolute;
            left: 10px;
            font-weight: bold;
            color: #e63946;
            text-align: left;
        }
    }
</style>
</head>
<body>

<h1>Admin Dashboard - Reports</h1>
<h2>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?></h2>

<!-- Incoming Reports section -->
<h2><span id="notificationCount" class="notification-badge" style="display:none;">0</span></h2>
<table id="incomingReportsTable">

    <tbody></tbody>
</table>

<!-- Report Summary section -->
<h2>Report Summary</h2>
<table id="summaryReportsTable">
    <thead>
        <tr>
            <th>ID</th>
            <th>User</th>
            <th>Message</th>
            <th>Location</th>
            <th>Status</th>
            <th>Response</th>
            <th>Time</th>
        </tr>
    </thead>
    <tbody></tbody>
</table>
<a href="dashboard.php">Back to Dashboard</a>
<script>

    // Fetch all reports from server
    function fetchReports() {
        fetch('fetch_reports.php')
            .then(res => res.json())
            .then(data => {
                const incomingTbody = document.querySelector("#incomingReportsTable tbody");
                const summaryTbody = document.querySelector("#summaryReportsTable tbody");
                incomingTbody.innerHTML = '';
                summaryTbody.innerHTML = '';

                let pendingCount = 0;

                data.forEach(report => {
                    if (report.status === 'Pending') {
                        pendingCount++;
                        incomingTbody.innerHTML += `
                            <tr>
                                <td data-label="ID">${report.id}</td>
                                <td data-label="User">${report.username}</td>
                                <td data-label="Message">${report.message}</td>
                                <td data-label="Location"><a href="https://www.google.com/maps?q=${report.latitude},${report.longitude}" target="_blank">View Map</a></td>
                                <td data-label="Time">${report.created_at}</td>
                                <td data-label="Action"><button class="notify-btn" onclick="notifyReport(${report.id})">Notify</button></td>
                            </tr>
                        `;
                    } else if (report.status === 'Notified') {
                        summaryTbody.innerHTML += `
                            <tr>
                                <td data-label="ID">${report.id}</td>
                                <td data-label="User">${report.username}</td>
                                <td data-label="Message">${report.message}</td>
                                <td data-label="Location"><a href="https://www.google.com/maps?q=${report.latitude},${report.longitude}" target="_blank">View Map</a></td>
                                <td data-label="Status">${report.status}</td>
                                <td data-label="Response">${report.response ? report.response : 'No response'}</td>
                                <td data-label="Time">${report.created_at}</td>
                            </tr>
                        `;
                    }
                });

                // Show or hide notification badge
                const notificationBadge = document.getElementById('notificationCount');
                if (pendingCount > 0) {
                    notificationBadge.style.display = 'inline-block';
                    notificationBadge.textContent = pendingCount;
                } else {
                    notificationBadge.style.display = 'none';
                }
            })
            .catch(console.error);
    }

    // Notify a report
    function notifyReport(id) {
        if (confirm('Notify this report?')) {
            fetch('notify_report.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: 'id=' + id
            })
            .then(res => res.text())
            .then(data => {
                alert(data);
                fetchReports();
            });
        }
    }

    // Initial load & refresh every 10 seconds
    fetchReports();
    setInterval(fetchReports, 10000);
</script>

</body>
</html>