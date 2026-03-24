<?php include "../includes/conn.php"; 
if (session_status() == PHP_SESSION_NONE) session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login - Emergency System</title>

<style>
/* ===== BODY ===== */
body {
    font-family: 'Segoe UI', sans-serif;
    background: linear-gradient(135deg, #e63946, #f5f7fb);
    margin: 0;
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
}

/* ===== CARD ===== */
.card {
    background: white;
    padding: 50px 40px;
    border-radius: 16px;
    box-shadow: 0 12px 24px rgba(0,0,0,0.2);
    width: 360px;
    max-width: 90%;
    text-align: center;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}
.card:hover {
    transform: translateY(-5px);
    box-shadow: 0 16px 32px rgba(0,0,0,0.25);
}

/* TITLE */
.card h2 {
    color: #e63946;
    font-size: 36px;
    margin-bottom: 25px;
    text-shadow: 1px 1px 2px rgba(0,0,0,0.2);
}

/* INPUT FIELDS */
input {
    width: 100%;
    padding: 14px;
    margin: 12px 0;
    border: 1px solid #ccc;
    border-radius: 10px;
    font-size: 15px;
    box-sizing: border-box;
    transition: 0.3s;
}
input:focus {
    border-color: #e63946;
    box-shadow: 0 0 8px rgba(230, 57, 70, 0.3);
    outline: none;
}

/* BUTTON */
button {
    width: 100%;
    padding: 14px;
    background: #e63946;
    color: white;
    border: none;
    border-radius: 10px;
    cursor: pointer;
    font-size: 16px;
    font-weight: 500;
    margin-top: 10px;
    transition: 0.3s ease;
}
button:hover {
    background: #c5303f;
    transform: translateY(-2px);
    box-shadow: 0 6px 12px rgba(0,0,0,0.15);
}

/* BACK TO HOME BUTTON */
.back-home {
    display: inline-block;
    margin-top: 15px;
    padding: 12px 20px;
    background: #555;
    color: white;
    border-radius: 10px;
    text-decoration: none;
    transition: 0.3s;
}
.back-home:hover {
    background: #333;
}

/* REGISTER LINK */
p {
    margin-top: 15px;
    font-size: 14px;
}
p a {
    color: #e63946;
    text-decoration: none;
    font-weight: 500;
}
p a:hover {
    text-decoration: underline;
}

/* RESPONSIVE */
@media (max-width: 400px){
    .card {
        padding: 40px 25px;
    }
    .card h2 {
        font-size: 28px;
    }
}
</style>
</head>
<body>

<div class="card">
    <h2>Login</h2>

    <form method="POST">
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit" name="login">Login</button>
    </form>

    <p>No account? <a href="register.php">Register</a></p>
    <a href="../index.php" class="back-home">🏠 Back to Home</a>
</div>

<?php
if (isset($_POST['login'])) {

    $email = $_POST['email'];
    $password = $_POST['password'];

    // Find user
    $stmt = $conn->prepare("SELECT id, username, password, role FROM users WHERE email=?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {

        // Verify password
        if (password_verify($password, $row['password'])) {

            // ✅ Create session
            if (session_status() == PHP_SESSION_NONE) session_start();
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['username'] = $row['username'];
            $_SESSION['role'] = $row['role'];

            // 🔀 Redirect based on role
            if ($row['role'] == 'admin') {
                header("Location: ../admin/dashboard.php");
            } else {
                header("Location: ../user/dashboard.php");
            }
            exit();

        } else {
            echo "<script>alert('Wrong password');</script>";
        }

    } else {
        echo "<script>alert('User not found');</script>";
    }
}
?>
</body>
</html>