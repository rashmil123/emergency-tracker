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
<title>Your Reports</title>
<style>
body {
    margin: 0;
    font-family: 'Segoe UI', sans-serif;
    background: #f5f7fb;
}

.header {
    background: white;
    padding: 15px 20px;
    border-bottom: 1px solid #ddd;
}

.header h2 {
    margin: 0;
    color: #e63946;
}

a.back {
    color: #e63946;
    text-decoration: none;
    font-weight: bold;
}

/* ===== CONTAINER ===== */
.container {
    padding: 20px;
}

/* ===== CONTROLS ===== */
.controls {
    display: flex;
    gap: 10px;
    margin-bottom: 15px;
    flex-wrap: wrap;
}

.controls input {
    padding: 8px;
    border-radius: 6px;
    border: 1px solid #ccc;
}

/* ===== CARD ===== */
.card {
    background: white;
    padding: 15px;
    border-radius: 12px;
    box-shadow: 0 2px 6px rgba(0,0,0,0.1);
}

/* ===== TABLE ===== */
table {
    width: 100%;
    border-collapse: collapse;
}

th {
    background: #e63946;
    color: white;
    font-weight: 500;
}

th, td {
    padding: 10px;
    border-bottom: 1px solid #eee;
    text-align: center;
    font-size: 14px;
}

/* ===== STATUS BADGES ===== */
.status-badge {
    padding: 5px 10px;
    border-radius: 20px;
    font-size: 12px;
    color: white;
}

.pending { background: orange; }
.notified { background: green; }
.resolved { background: blue; }
.urgent { background: red; }

/* ===== BUTTONS ===== */
button {
    padding: 6px 10px;
    border: none;
    border-radius: 6px;
    background: #e63946;
    color: white;
    cursor: pointer;
    margin: 2px;
}

button:hover {
    background: #c5303f;
}

/* ===== RESPONSE DISPLAY ===== */
span.response-display {
    display: inline-block;
    padding: 5px 10px;
    border-radius: 6px;
    background: #f0f0f0;
    color: #333;
    font-size: 14px;
    min-width: 120px;
}

/* ===== MODAL ===== */
.modal {
    display: none;
    position: fixed;
    z-index: 999;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    overflow: auto;
    background-color: rgba(0,0,0,0.5);
}

.modal-content {
    background: white;
    margin: 10% auto;
    padding: 20px;
    border-radius: 12px;
    width: 90%;
    max-width: 500px;
}

.modal-header {
    font-weight: bold;
    color: #e63946;
    margin-bottom: 10px;
}

.modal textarea {
    width: 100%;
    height: 100px;
    padding: 10px;
    border-radius: 6px;
    border: 1px solid #ccc;
    font-size: 14px;
}

/* MODAL BUTTONS */
.modal button {
    margin-top: 10px;
}

/* MOBILE RESPONSIVE */
@media(max-width: 768px) {
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
        text-align: left;
    }
}
</style>
</head>
<body>

<div class="header">
    <h2>Your Reports</h2>
    <a href="dashboard.php" class="back">← Back to Dashboard</a>
</div>

<div class="container">

    <div class="controls">
        <input type="text" id="search" placeholder="Search message/status...">
    </div>

    <div class="card">
    <table id="reportsTable">
        <thead>
            <tr>
                <th>ID</th>
                <th>Message</th>
                <th>Status</th>
                <th>Response</th>
                <th>Time</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $res = mysqli_query($conn, "SELECT * FROM reports WHERE user_id='$user_id' ORDER BY id DESC");
            while($row = mysqli_fetch_assoc($res)){
                $statusClass = strtolower($row['status']);
                $responseText = htmlspecialchars($row['response']);
                echo "<tr>
                        <td data-label='ID'>{$row['id']}</td>
                        <td data-label='Message'>".htmlspecialchars($row['message'])."</td>
                        <td data-label='Status'><span class='status-badge {$statusClass}'>".htmlspecialchars($row['status'])."</span></td>
                        <td data-label='Response'><span class='response-display'>{$responseText}</span></td>
                        <td data-label='Time'>{$row['created_at']}</td>
                        <td data-label='Action'>
                            <button onclick='openEditModal({$row['id']}, \"".addslashes(htmlspecialchars($row['message']))."\")'>Edit</button>
                            <button onclick='deleteReport({$row['id']})'>Delete</button>
                        </td>
                      </tr>";
            }
            ?>
        </tbody>
    </table>
    </div>

</div>

<!-- EDIT MODAL -->
<div id="editModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">Edit Report</div>
        <textarea id="editMessage"></textarea>
        <input type="hidden" id="editReportId">
        <button onclick="saveEdit()">Save</button>
        <button onclick="closeModal()">Cancel</button>
    </div>
</div>

<script>
// OPEN MODAL
function openEditModal(id, message){
    document.getElementById('editReportId').value = id;
    document.getElementById('editMessage').value = message;
    document.getElementById('editModal').style.display = 'block';
}

// CLOSE MODAL
function closeModal(){
    document.getElementById('editModal').style.display = 'none';
}

// SAVE EDIT
function saveEdit(){
    let id = document.getElementById('editReportId').value;
    let message = document.getElementById('editMessage').value.trim();

    if(message === ""){
        alert("Message cannot be empty!");
        return;
    }

    fetch('update_message.php', {
        method: 'POST',
        headers: {'Content-Type':'application/x-www-form-urlencoded'},
        body: 'id='+id+'&message='+encodeURIComponent(message)
    })
    .then(res => res.text())
    .then(alert)
    .then(() => {
        closeModal();
        location.reload();
    });
}

// DELETE REPORT
function deleteReport(id){
    if(confirm("Are you sure you want to delete this report?")){
        fetch('delete_report.php', {
            method: 'POST',
            headers: {'Content-Type':'application/x-www-form-urlencoded'},
            body: 'id='+id
        })
        .then(res => res.text())
        .then(alert)
        .then(()=>location.reload());
    }
}

// SEARCH/FILTER TABLE
document.getElementById('search').addEventListener('input', function(){
    let filter = this.value.toLowerCase();
    let rows = document.querySelectorAll('#reportsTable tbody tr');
    rows.forEach(row => {
        let msg = row.children[1].innerText.toLowerCase();
        let status = row.children[2].innerText.toLowerCase();
        row.style.display = (msg.includes(filter) || status.includes(filter)) ? '' : 'none';
    });
});

// CLOSE MODAL ON CLICK OUTSIDE
window.onclick = function(event){
    let modal = document.getElementById('editModal');
    if(event.target == modal){
        closeModal();
    }
}
</script>

</body>
</html>