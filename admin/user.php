<?php
include "../includes/conn.php";

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

$message = "";

// HANDLE ADD USER
if(isset($_POST['add_user'])){
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $role = trim($_POST['role']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    mysqli_query($conn, "INSERT INTO users (username, email, password, role, created_at) 
        VALUES ('$username', '$email', '$password', '$role', NOW())");
    $message = "User added successfully!";
}

// HANDLE EDIT USER
if(isset($_POST['edit_user'])){
    $id = $_POST['id'];
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $role = trim($_POST['role']);

    $query = "UPDATE users SET username='$username', email='$email', role='$role'";
    if(!empty($_POST['password'])){
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $query .= ", password='$password'";
    }
    $query .= " WHERE id='$id'";
    mysqli_query($conn, $query);
    $message = "User updated successfully!";
}

// HANDLE DELETE USER
if(isset($_POST['delete_user'])){
    $id = $_POST['id'];
    mysqli_query($conn, "DELETE FROM users WHERE id='$id'");
    $message = "User deleted successfully!";
}

// FETCH USERS
$res = mysqli_query($conn, "SELECT id, username, email, role, created_at FROM users ORDER BY id ASC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>User Management</title>
<style>
body { font-family: Arial; background: #f5f7fb; padding: 20px; }
h1 { color: #e63946; }
table { width: 100%; background: white; border-collapse: collapse; border-radius: 10px; overflow: hidden; margin-top: 15px; }
th { background: #e63946; color: white; }
th, td { padding: 10px; border-bottom: 1px solid #eee; text-align: left; }
button, input[type=submit] { padding: 6px 12px; border: none; border-radius: 6px; background: #e63946; color: white; cursor: pointer; }
button:hover, input[type=submit]:hover { background: #c5303f; }
.back { display: inline-block; margin-bottom: 15px; text-decoration: none; color: #333; }
.message { margin-bottom: 15px; font-weight: bold; color: green; }

/* MODAL STYLES */
.modal {
    display: none; 
    position: fixed; 
    z-index: 1000; 
    padding-top: 60px; 
    left: 0;
    top: 0;
    width: 100%; 
    height: 100%; 
    overflow: auto; 
    background-color: rgba(0,0,0,0.5); 
}

.modal-content {
    background-color: #fff;
    margin: auto;
    padding: 20px;
    border-radius: 10px;
    width: 400px;
    position: relative;
}

.close {
    color: #aaa;
    position: absolute;
    top: 10px;
    right: 20px;
    font-size: 28px;
    font-weight: bold;
    cursor: pointer;
}

.close:hover { color: #000; }

label { display:block; margin-top:10px; font-weight:bold; }
input[type=text], input[type=email], input[type=password], select { width: 100%; padding:8px; margin-top:5px; border-radius:6px; border:1px solid #ccc; }
</style>
</head>
<body>

<a href="dashboard.php" class="back">← Back to Dashboard</a>
<h1>User Management</h1>

<?php if($message) echo "<div class='message'>$message</div>"; ?>

<!-- Add User Button -->
<button id="addUserBtn">+ Add User</button>

<!-- ADD USER MODAL -->
<div id="addUserModal" class="modal">
    <div class="modal-content">
        <span class="close" id="closeAddModal">&times;</span>
        <form method="POST">
            <h3>Add New User</h3>
            <label>Username</label>
            <input type="text" name="username" required>

            <label>Email</label>
            <input type="email" name="email" required>

            <label>Password</label>
            <input type="password" name="password" required>

            <label>Role</label>
            <select name="role" required>
                <option value="user">User</option>
                <option value="admin">Admin</option>
            </select>

            <input type="submit" name="add_user" value="Add User">
        </form>
    </div>
</div>

<!-- EDIT USER MODAL -->
<div id="editUserModal" class="modal">
    <div class="modal-content">
        <span class="close" id="closeEditModal">&times;</span>
        <form method="POST" id="editUserForm">
            <h3>Edit User</h3>
            <input type="hidden" name="id" id="edit_id">

            <label>Username</label>
            <input type="text" name="username" id="edit_username" required>

            <label>Email</label>
            <input type="email" name="email" id="edit_email" required>

            <label>Password (leave blank to keep current)</label>
            <input type="password" name="password">

            <label>Role</label>
            <select name="role" id="edit_role" required>
                <option value="user">User</option>
                <option value="admin">Admin</option>
            </select>

            <input type="submit" name="edit_user" value="Update User">
        </form>
    </div>
</div>

<!-- USERS TABLE -->
<table>
<thead>
<tr>
<th>ID</th>
<th>Username</th>
<th>Email</th>
<th>Role</th>
<th>Created At</th>
<th>Action</th>
</tr>
</thead>
<tbody>
<?php while($row = mysqli_fetch_assoc($res)): ?>
<tr>
<td><?= $row['id'] ?></td>
<td><?= htmlspecialchars($row['username']) ?></td>
<td><?= htmlspecialchars($row['email']) ?></td>
<td><?= $row['role'] ?></td>
<td><?= $row['created_at'] ?></td>
<td>
    <button class="editBtn" 
        data-id="<?= $row['id'] ?>" 
        data-username="<?= htmlspecialchars($row['username']) ?>" 
        data-email="<?= htmlspecialchars($row['email']) ?>"
        data-role="<?= $row['role'] ?>">Edit</button>

    <form method="POST" style="display:inline-block;">
        <input type="hidden" name="id" value="<?= $row['id'] ?>">
        <input type="submit" name="delete_user" value="Delete" onclick="return confirm('Delete this user?')">
    </form>
</td>
</tr>
<?php endwhile; ?>
</tbody>
</table>

<script>
// ADD USER MODAL
var addModal = document.getElementById("addUserModal");
var addBtn = document.getElementById("addUserBtn");
var closeAdd = document.getElementById("closeAddModal");

addBtn.onclick = function() { addModal.style.display = "block"; }
closeAdd.onclick = function() { addModal.style.display = "none"; }
window.onclick = function(event) { if(event.target == addModal) addModal.style.display = "none"; }

// EDIT USER MODAL
var editModal = document.getElementById("editUserModal");
var closeEdit = document.getElementById("closeEditModal");

closeEdit.onclick = function() { editModal.style.display = "none"; }
window.onclick = function(event) { 
    if(event.target == editModal) editModal.style.display = "none"; 
}

// Open edit modal and populate fields
var editButtons = document.querySelectorAll(".editBtn");
editButtons.forEach(function(btn){
    btn.onclick = function(){
        document.getElementById("edit_id").value = this.dataset.id;
        document.getElementById("edit_username").value = this.dataset.username;
        document.getElementById("edit_email").value = this.dataset.email;
        document.getElementById("edit_role").value = this.dataset.role;
        editModal.style.display = "block";
    }
});
</script>

</body>
</html>