<?php
require_once './includes/config_session.inc.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Login | FlexiStay</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="./assets/css/login.css">
</head>
<body>

<div class="container">
  <!-- Tab Navigation -->
  <div class="tabs">
    <button id="login-tab" class="active">Login</button>
    <button id="signup-tab">Sign Up</button>
  </div>

  <!-- Login Form -->
  <div id="login-form" class="form-wrapper active">
    <form action="./auth/login.inc.php" method="POST">
      <label for="login_email">Email</label>
      <input type="email" name="email" id="login_email" required />

      <label for="login_password">Password</label>
      <input type="password" name="password" id="login_password" required />

      <button type="submit" name="login">Login</button>
    </form>
  </div>

  <!-- Signup Form -->
  <div id="signup-form" class="form-wrapper">
    <form action="./auth/user_signup.inc.php" method="POST">
      <label for="signup_name">Full Name</label>
      <input type="text" name="name" id="signup_name" required />

      <label for="signup_email">Email</label>
      <input type="email" name="email" id="signup_email" required />

      <label for="signup_mobile">Mobile Number</label>
      <input type="text" name="mobile" id="signup_mobile" required />

      <label for="signup_dob">Date of Birth</label>
      <input type="date" name="dob" id="signup_dob" required />

      <label for="signup_password">Password</label>
      <input type="password" name="password" id="signup_password" required />

      <label for="signup_confirm">Confirm Password</label>
      <input type="password" name="confirm_password" id="signup_confirm" required />

      <button type="submit" name="signup">Sign Up</button>
    </form>
  </div>
</div>

<!-- Toggle Script -->
<script>
  const loginTab = document.getElementById("login-tab");
  const signupTab = document.getElementById("signup-tab");
  const loginForm = document.getElementById("login-form");
  const signupForm = document.getElementById("signup-form");

  loginTab.onclick = () => {
    loginTab.classList.add("active");
    signupTab.classList.remove("active");
    loginForm.classList.add("active");
    signupForm.classList.remove("active");
  };

  signupTab.onclick = () => {
    signupTab.classList.add("active");
    loginTab.classList.remove("active");
    signupForm.classList.add("active");
    loginForm.classList.remove("active");
  };
</script>

</body>
</html>
