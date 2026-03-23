<?php include "../includes/conn.php"; ?>

<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <style>
        body {
            font-family: Arial;
            background: #1d1d1d;
            color: white;
        }

        .card {
            width: 350px;
            margin: 80px auto;
            padding: 25px;
            background: #fff;
            color: #000;
            border-radius: 10px;
            text-align: center;
        }

        input {
            width: 90%;
            padding: 10px;
            margin: 8px 0;
        }

        button {
            background: #e63946;
            color: white;
            padding: 10px;
            border: none;
            width: 95%;
            border-radius: 6px;
            cursor: pointer;
        }

        button:hover {
            background: #c92f3c;
        }

        a {
            text-decoration: none;
            color: #e63946;
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
</div>

</body>
</html>

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