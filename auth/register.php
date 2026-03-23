<?php include "../includes/conn.php"; ?>

<!DOCTYPE html>
<html>
<head>
    <title>Register</title>
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
        small {
            color: gray;
            display: block;
            margin-bottom: 10px;
            font-size: 12px;
        }
    </style>
</head>
<body>

<div class="card">
    <h2>Create Account</h2>

    <form method="POST">
        <input type="text" name="username" placeholder="Username" required>
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Password" required>
        <small>Password should be at least 6 characters</small>

        <button type="submit" name="register">Register</button>
    </form>

    <p>Already have an account? <a href="login.php">Login</a></p>
</div>

</body>
</html>

<?php
if (isset($_POST['register'])) {

    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    if (strlen($password) < 6) {
    echo "<script>alert('Password must be at least 6 characters');</script>";
    return;
    }

    // 🔐 Hash password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // ❗ Check if email exists
    $check = $conn->prepare("SELECT id FROM users WHERE email=?");
    $check->bind_param("s", $email);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        echo "<script>alert('Email already exists');</script>";
    } else {

        // ✅ Insert user
        $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $username, $email, $hashedPassword);

        if ($stmt->execute()) {
            echo "<script>alert('Registered successfully'); window.location='login.php';</script>";
        } else {
            echo "<script>alert('Error occurred');</script>";
        }
    }
}
?>