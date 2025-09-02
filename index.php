<?php
session_start();

// If already logged in → go to dashboard
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Prokash - Community Alerts Platform</title>
  <link rel="stylesheet" href="css/style.css">
  <style>
    body {
      margin: 0;
      padding: 0;
      height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
      background-image: url('images/bg.jpg');
      background-size: cover;
      background-position: center;
      font-family: Arial, sans-serif;
    }

    .welcome-box {
      background: rgba(255, 255, 255, 0.95);
      padding: 40px 50px;
      border-radius: 12px;
      text-align: center;
      max-width: 420px;
      width: 100%;
      box-shadow: 0 4px 12px rgba(0,0,0,0.25);
    }

    .welcome-box h1 {
      font-size: 28px;
      color: #222;
      margin-bottom: 10px;
    }

    .welcome-box p {
      font-size: 15px;
      color: #444;
      margin-bottom: 25px;
    }

    .welcome-box a {
      display: block;
      margin: 12px 0;
      padding: 12px;
      border-radius: 6px;
      text-decoration: none;
      font-size: 16px;
      font-weight: bold;
      transition: background 0.3s ease;
    }

    .btn-login {
      background: #3366cc;
      color: white;
    }

    .btn-login:hover {
      background: #264d99;
    }

    .btn-signup {
      background: #eaeaea;
      color: #333;
      border: 1px solid #ccc;
    }

    .btn-signup:hover {
      background: #ddd;
    }
  </style>
</head>
<body>
  <div class="welcome-box">
    <h1>Welcome to Prokash</h1>
    <p>A community-powered awareness and alert platform for Bangladesh.</p>
    <a href="login.php" class="btn-login">Login</a>
    <a href="signup.php" class="btn-signup">Sign Up</a>
  </div>
</body>
</html>


