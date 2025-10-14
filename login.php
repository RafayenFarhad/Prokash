<?php
session_start();
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
  <title>Login - Prokash</title>
  <link rel="stylesheet" href="css/style.css">
  <style>
    body {
      margin: 0; padding: 0; height: 100vh;
      display: flex; justify-content: center; align-items: center;
      background: url('images/bg.jpg') no-repeat center center/cover;
      font-family: Arial, sans-serif;
    }
    .login-box {
      background: rgba(255, 255, 255, 0.95);
      padding: 30px 40px;
      border-radius: 10px;
      max-width: 400px; width: 100%;
      box-shadow: 0 0 15px rgba(0,0,0,0.2);
      text-align: center;
    }
    .login-box input {
      width: 100%; padding: 10px; margin: 10px 0;
      border: 1px solid #ccc; border-radius: 5px; font-size: 16px;
    }
    .login-box button {
      width: 100%; padding: 10px;
      background-color: #3366cc; color: white;
      border: none; border-radius: 5px;
      font-size: 16px; cursor: pointer;
    }
    .login-box a { color: #3366cc; text-decoration: none; }
    .login-box a:hover { text-decoration: underline; }
  </style>
</head>
<body>
  <div class="login-box">
    <h2>Login to Prokash</h2>
    <form action="login_process.php" method="POST">
      <input type="email" name="email" placeholder="Email address" required>
      <input type="password" name="password" placeholder="Password" required>
      <button type="submit">Login</button>
    </form>
    <p>Don’t have an account? <a href="signup.php">Sign Up</a></p>
  </div>
</body>
</html>
