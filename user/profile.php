<?php
include "../includes/conn.php";

if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'user') {
    header("Location: ../auth/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$message = "";
$alert = "";

// Fetch user info
$res = mysqli_query($conn, "SELECT username, email FROM users WHERE id='$user_id'");
$user = mysqli_fetch_assoc($res);

// Handle form submission
if(isset($_POST['update_profile'])){
    $email = trim($_POST['email']);
    $current = trim($_POST['current_password']);
    $new = trim($_POST['new_password']);
    $confirm = trim($_POST['confirm_password']);

    // Update email first
    if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
        $message = "Invalid email address.";
    } else {
        mysqli_query($conn, "UPDATE users SET email='".mysqli_real_escape_string($conn,$email)."' WHERE id='$user_id'");
        $message = "Email updated successfully!";
        $alert = "Email updated successfully!";
    }

    // Change password if all fields filled
    if($current || $new || $confirm){
        $res_pass = mysqli_query($conn, "SELECT password FROM users WHERE id='$user_id'");
        $row = mysqli_fetch_assoc($res_pass);

        if(empty($current) || empty($new) || empty($confirm)){
            $message .= "<br>Please fill all password fields to change password.";
        } elseif(!password_verify($current, $row['password'])){
            $message .= "<br>Current password is incorrect.";
        } elseif($new !== $confirm){
            $message .= "<br>New passwords do not match.";
        } elseif(strlen($new) < 6){
            $message .= "<br>Password must be at least 6 characters.";
        } else {
            $hash = password_hash($new, PASSWORD_DEFAULT);
            mysqli_query($conn, "UPDATE users SET password='$hash' WHERE id='$user_id'");
            $message .= "<br>Password updated successfully!";
            $alert .= ($alert ? "\\n" : "")."Password updated successfully!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>User Profile</title>
<style>
body { margin:0; font-family:'Segoe UI',sans-serif; background:#f5f7fb; }
.header { display:flex; justify-content:space-between; align-items:center; padding:15px 20px; background:white; border-bottom:1px solid #ddd; }
.header h2 { color:#e63946; margin:0; }
.container { padding:20px; max-width:500px; margin:20px auto; background:white; border-radius:12px; box-shadow:0 2px 6px rgba(0,0,0,0.1); }
label { display:block; margin-top:15px; font-weight:bold; }
input[type=text], input[type=email], input[type=password] { width:100%; padding:10px; margin-top:5px; border-radius:8px; border:1px solid #ccc; }
button { margin-top:15px; background:#e63946; color:white; padding:10px 15px; border:none; border-radius:8px; cursor:pointer; width:100%; font-size:16px; }
button:hover { background:#c5303f; }
.message { margin-top:15px; font-weight:bold; color:green; }
a.back { display:block; margin-top:15px; color:#e63946; text-decoration:none; font-weight:bold; }
</style>
</head>
<body>

<div class="header">
    <h2>Your Profile</h2>
    <a href="dashboard.php" class="back">← Back to Dashboard</a>
</div>

<div class="container">
    <form method="POST" id="profileForm">
        <label>Username</label>
        <input type="text" value="<?php echo htmlspecialchars($user['username']); ?>" disabled>

        <label>Email</label>
        <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>

        <h3>Change Password</h3>
        <label>Current Password</label>
        <input type="password" name="current_password">

        <label>New Password</label>
        <input type="password" name="new_password">

        <label>Confirm New Password</label>
        <input type="password" name="confirm_password">

        <button type="submit" name="update_profile">Update Profile</button>
    </form>

    <?php if($message) echo "<div class='message'>".$message."</div>"; ?>
</div>

<script>
// Show alert if profile/password updated successfully
<?php if($alert): ?>
alert("<?php echo $alert; ?>");
<?php endif; ?>

// Optional: client-side check for password match before submitting
document.getElementById('profileForm').addEventListener('submit', function(e){
    let newPass = this.new_password.value.trim();
    let confirmPass = this.confirm_password.value.trim();
    if(newPass || confirmPass){ // only check if user filled password fields
        if(newPass !== confirmPass){
            e.preventDefault();
            alert("New passwords do not match.");
            return false;
        }
        if(newPass.length < 6){
            e.preventDefault();
            alert("Password must be at least 6 characters.");
            return false;
        }
    }
});
</script>

</body>
</html>