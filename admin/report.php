<?php
include "../includes/conn.php";

if (session_status() === PHP_SESSION_NONE) {
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
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Reports Management</title>

<style>
body {
    margin: 0;
    font-family: 'Segoe UI', sans-serif;
    background: #f5f7fb;
}

/* HEADER */
.header {
    background: white;
    padding: 15px 20px;
    border-bottom: 1px solid #ddd;
}

.header h1 {
    margin: 0;
    color: #e63946;
}

/* CONTAINER */
.container {
    padding: 20px;
}

/* CONTROLS */
.controls {
    display: flex;
    gap: 10px;
    margin-bottom: 15px;
    flex-wrap: wrap;
}

input, select {
    padding: 8px;
    border-radius: 6px;
    border: 1px solid #ccc;
}

/* CARD */
.card {
    background: white;
    padding: 15px;
    border-radius: 12px;
    box-shadow: 0 2px 6px rgba(0,0,0,0.1);
}

/* TABLE */
table {
    width: 100%;
    border-collapse: collapse;
}

th {
    background: #e63946;
    color: white;
}

th, td {
    padding: 10px;
    border-bottom: 1px solid #eee;
    text-align: center;
}

/* STATUS BADGE */
.status {
    padding: 5px 10px;
    border-radius: 20px;
    font-size: 12px;
    color: white;
}

.pending { background: orange; }
.notified { background: green; }

/* BUTTONS */
button {
    padding: 6px 10px;
    border: none;
    border-radius: 6px;
    background: #e63946;
    color: white;
    cursor: pointer;
}

button:hover {
    background: #c5303f;
}

/* RESPONSE INPUT */
input.response {
    width: 120px;
}

/* BACK */
.back {
    display: inline-block;
    margin-top: 15px;
    color: #e63946;
    text-decoration: none;
}

/* MOBILE */
@media(max-width: 768px){
    table, thead, tbody, th, td, tr {
        display: block;
    }
    th { display: none; }
    td {
        text-align: right;
        padding-left: 50%;
        position: relative;
    }
    td::before {
        content: attr(data-label);
        position: absolute;
        left: 10px;
        font-weight: bold;
        color: #e63946;
    }
}
</style>
</head>

<body>

<div class="header">
    <h1>Reports Management</h1>
</div>

<div class="container">

<h3>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?></h3>

<!-- CONTROLS -->
<div class="controls">
    <input type="text" id="search" placeholder="Search message/user...">
    
    <select id="filter">
        <option value="all">All</option>
        <option value="Pending">Pending</option>
        <option value="Notified">Notified</option>
    </select>
</div>

<div class="card">

<table id="reportTable">
<thead>
<tr>
<th>ID</th>
<th>User</th>
<th>Message</th>
<th>Location</th>
<th>Status</th>
<th>Response</th>
<th>Time</th>
<th>Action</th>
</tr>
</thead>
<tbody></tbody>
</table>

</div>

<a href="dashboard.php" class="back">← Back to Dashboard</a>

</div>

<script>

let reportsData = [];

// FETCH REPORTS
function fetchReports() {
    fetch('fetch_reports.php')
    .then(res => res.json())
    .then(data => {
        reportsData = data;
        renderTable();
    });
}

// RENDER TABLE
function renderTable() {
    let tbody = document.querySelector("#reportTable tbody");
    tbody.innerHTML = '';

    let search = document.getElementById('search').value.toLowerCase();
    let filter = document.getElementById('filter').value;

    reportsData.forEach(r => {

        if (filter !== 'all' && r.status !== filter) return;

        if (
            !r.username.toLowerCase().includes(search) &&
            !r.message.toLowerCase().includes(search)
        ) return;

        let statusClass = r.status === 'Pending' ? 'pending' : 'notified';

        tbody.innerHTML += `
        <tr>
            <td data-label="ID">${r.id}</td>
            <td data-label="User">${r.username}</td>
            <td data-label="Message">${r.message}</td>
            <td data-label="Location">
                <a href="https://www.google.com/maps?q=${r.latitude},${r.longitude}" target="_blank">Map</a>
            </td>
            <td data-label="Status">
                <span class="status ${statusClass}">${r.status}</span>
            </td>
            <td data-label="Response">
                <input class="response" id="res-${r.id}" value="${r.response ?? ''}">
            </td>
            <td data-label="Time">${r.created_at}</td>
            <td data-label="Action">
                <button onclick="updateResponse(${r.id})">Save</button>
                ${r.status === 'Pending' ? `<button onclick="notifyReport(${r.id})">Notify</button>` : ''}
            </td>
        </tr>`;
    });
}

// SAVE RESPONSE
function updateResponse(id) {
    let response = document.getElementById(`res-${id}`).value;

    fetch('update_response.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: `id=${id}&response=${response}`
    })
    .then(res => res.text())
    .then(alert)
    .then(fetchReports);
}

// NOTIFY
function notifyReport(id) {
    if(confirm("Notify report?")) {
        fetch('notify_report.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: 'id=' + id
        })
        .then(res => res.text())
        .then(alert)
        .then(fetchReports);
    }
}

// EVENTS
document.getElementById('search').addEventListener('input', renderTable);
document.getElementById('filter').addEventListener('change', renderTable);

// INIT
fetchReports();
setInterval(fetchReports, 10000);

</script>

</body>
</html>