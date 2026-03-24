<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Emergency System</title>

<style>
/* ===== BODY ===== */
body {
    font-family: 'Segoe UI', sans-serif;
    background: linear-gradient(135deg, #e63946, #f5f7fb);
    color: #333;
    margin: 0;
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
}

/* ===== CONTAINER CARD ===== */
.container {
    background: white;
    padding: 50px 40px;
    border-radius: 16px;
    box-shadow: 0 12px 24px rgba(0,0,0,0.2);
    text-align: center;
    max-width: 450px;
    width: 90%;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}
.container:hover {
    transform: translateY(-5px);
    box-shadow: 0 16px 32px rgba(0,0,0,0.25);
}

/* TITLE */
h1 {
    color: #e63946;
    font-size: 44px;
    margin-bottom: 20px;
    text-shadow: 1px 1px 2px rgba(0,0,0,0.2);
}

/* SUBTITLE */
p {
    font-size: 16px;
    color: #555;
    margin-bottom: 30px;
}

/* BUTTONS */
.btn {
    display: inline-block;
    padding: 14px 30px;
    margin: 10px;
    background: #e63946;
    color: white;
    text-decoration: none;
    border-radius: 10px;
    font-size: 16px;
    font-weight: 600;
    transition: 0.3s ease;
    box-shadow: 0 6px 12px rgba(0,0,0,0.15);
}
.btn:hover {
    background: #c5303f;
    transform: translateY(-2px);
    box-shadow: 0 10px 20px rgba(0,0,0,0.2);
}

/* RESPONSIVE */
@media (max-width: 500px) {
    h1 {
        font-size: 36px;
    }
    .btn {
        width: 80%;
        margin: 10px 0;
    }
}
</style>
</head>
<body>

<div class="container">
    <h1>🚨 Emergency Tracker</h1>
    <p>Send your location instantly in case of an emergency.</p>

    <a href="auth/login.php" class="btn">Login</a>
    <a href="auth/register.php" class="btn">Register</a>
</div>

</body>
</html>