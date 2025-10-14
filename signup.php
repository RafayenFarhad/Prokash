<!DOCTYPE html>
<html>
<head>
  <title>Sign Up - Prokash</title>
  <link rel="stylesheet" href="css/style.css">
  <style>
    body {
      margin: 0; padding: 0; height: 100vh;
      display: flex; justify-content: center; align-items: center;
      background: url('images/bg.jpg') no-repeat center center/cover;
      font-family: Arial, sans-serif;
    }
    .signup-box {
      background: rgba(255, 255, 255, 0.95);
      padding: 30px 40px;
      border-radius: 10px;
      max-width: 420px; width: 100%;
      box-shadow: 0 0 15px rgba(0,0,0,0.2);
      text-align: center;
    }
    .signup-box input {
      width: 100%; padding: 10px; margin: 10px 0;
      border: 1px solid #ccc; border-radius: 5px; font-size: 16px;
    }
    .signup-box button {
      width: 100%; padding: 10px;
      background-color: #3366cc; color: white;
      border: none; border-radius: 5px;
      font-size: 16px; cursor: pointer;
    }
    .signup-box a { color: #3366cc; text-decoration: none; }
    .signup-box a:hover { text-decoration: underline; }
  </style>
</head>
<body>
  <div class="signup-box">
    <h2>Create your Prokash account</h2>
    <form action="signup_process.php" method="POST">
      <input type="text" name="name" placeholder="Full Name" required>
      <input type="email" name="email" placeholder="Email Address" required>
      <input type="tel" name="phone" placeholder="Phone Number (11 digits)" pattern="\d{11}" required>
      <input type="password" name="password" placeholder="Password (8–12 chars, 1 number, 1 special char)" 
             pattern="(?=.*\d)(?=.*[\W_]).{8,12}" required>
      <input type="text" name="location" placeholder="City / Area" required>
      <button type="submit">Sign Up</button>
    </form>
    <p>Already registered? <a href="login.php">Login here</a></p>
  </div>
</body>
</html>
