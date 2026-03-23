<?php
include "includes/conn.php";

$username = "admin";
$email = "admin@email.com";
$password = "123456";

$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

// Check if already exists
$check = $conn->prepare("SELECT id FROM users WHERE email=?");
$check->bind_param("s", $email);
$check->execute();
$check->store_result();

if ($check->num_rows > 0) {
    echo "Admin already exists!";
} else {

    $stmt = $conn->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, 'admin')");
    $stmt->bind_param("sss", $username, $email, $hashedPassword);

    if ($stmt->execute()) {
        echo "✅ Admin created successfully!";
    } else {
        echo "❌ Error: " . $stmt->error;
    }
}
?>