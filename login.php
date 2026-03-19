<?php
include 'db.php';
$error = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $sql = "SELECT * FROM customers WHERE email='$email' AND password='$password'";
    $result = mysqli_query($conn, $sql);
    if (mysqli_num_rows($result) == 1) {
        $row = mysqli_fetch_assoc($result);
        $_SESSION['customer_id'] = $row['customer_id'];
        $_SESSION['name'] = $row['name'];
        header("Location: index.php");
        exit();
    } else {
        $error = "Invalid email or password!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>BITE — Login</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
*{margin:0;padding:0;box-sizing:border-box;}
body{background:#0f0f0f;color:#fff;font-family:'Segoe UI',sans-serif;min-height:100vh;display:flex;flex-direction:column;}
.navbar{background:#0f0f0f;border-bottom:1px solid #1f1f1f;padding:14px 32px;display:flex;align-items:center;justify-content:space-between;}
.logo{font-size:24px;font-weight:800;color:#E63946;letter-spacing:3px;text-decoration:none;}
.nav-link-custom{color:#aaa;text-decoration:none;font-size:14px;margin-left:20px;}
.nav-link-custom:hover{color:#fff;}
.main{flex:1;display:flex;align-items:center;justify-content:center;padding:40px 20px;}
.login-box{background:#1a1a1a;border:1px solid #2a2a2a;border-radius:16px;display:grid;grid-template-columns:1fr 1fr;max-width:800px;width:100%;overflow:hidden;}
.login-left{padding:40px;}
.login-right{background:#E63946;padding:40px;display:flex;flex-direction:column;justify-content:center;align-items:center;text-align:center;}
.login-title{font-size:26px;font-weight:700;color:#fff;margin-bottom:6px;}
.login-sub{font-size:13px;color:#888;margin-bottom:28px;}
.form-label{color:#aaa;font-size:13px;margin-bottom:6px;display:block;}
.form-control{background:#0f0f0f;border:1px solid #2a2a2a;color:#fff;border-radius:8px;padding:12px 16px;font-size:14px;width:100%;margin-bottom:16px;}
.form-control:focus{outline:none;border-color:#E63946;background:#0f0f0f;color:#fff;box-shadow:none;}
.form-control::placeholder{color:#444;}
.btn-login{width:100%;background:#E63946;color:#fff;border:none;border-radius:8px;padding:13px;font-size:15px;font-weight:600;cursor:pointer;margin-top:4px;transition:background 0.2s;}
.btn-login:hover{background:#c1121f;}
.signup-link{text-align:center;margin-top:20px;font-size:13px;color:#666;}
.signup-link a{color:#E63946;text-decoration:none;font-weight:600;}
.right-logo{font-size:42px;font-weight:800;color:#fff;letter-spacing:4px;margin-bottom:8px;}
.right-tagline{font-size:13px;color:rgba(255,255,255,0.7);margin-bottom:32px;}
.feature{display:flex;align-items:center;gap:10px;margin-bottom:14px;text-align:left;width:100%;}
.feature-icon{width:28px;height:28px;background:rgba(255,255,255,0.2);border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:13px;flex-shrink:0;}
.feature-text{font-size:13px;color:rgba(255,255,255,0.85);}
.error-msg{background:#2a0a0a;border:1px solid #E63946;color:#E63946;padding:10px 14px;border-radius:8px;font-size:13px;margin-bottom:16px;}
.footer{background:#0a0a0a;border-top:1px solid #1f1f1f;text-align:center;padding:16px;font-size:12px;color:#444;}
</style>
</head>
<body>
<nav class="navbar">
  <a href="index.php" class="logo">BITE</a>
  <div>
    <a href="index.php" class="nav-link-custom">Home</a>
    <a href="register.php" class="nav-link-custom">Sign Up</a>
  </div>
</nav>
<div class="main">
  <div class="login-box">
    <div class="login-left">
      <div class="login-title">Welcome back 👋</div>
      <div class="login-sub">Login to order your favourite food</div>
      <?php if($error): ?>
      <div class="error-msg"><?= $error ?></div>
      <?php endif; ?>
      <form method="POST">
        <label class="form-label">Email address</label>
        <input type="email" name="email" class="form-control" placeholder="you@example.com" required>
        <label class="form-label">Password</label>
        <input type="password" name="password" class="form-control" placeholder="Enter your password" required>
        <button type="submit" class="btn-login">Login to BITE</button>
      </form>
      <div class="signup-link">
        Don't have an account? <a href="register.php">Sign up free</a>
      </div>
    </div>
    <div class="login-right">
      <div class="right-logo">BITE</div>
      <div class="right-tagline">Best Instant Takeaway Experience</div>
      <div class="feature">
        <div class="feature-icon">🍕</div>
        <div class="feature-text">Order from top restaurants</div>
      </div>
      <div class="feature">
        <div class="feature-icon">🚴</div>
        <div class="feature-text">Live order tracking</div>
      </div>
      <div class="feature">
        <div class="feature-icon">⚡</div>
        <div class="feature-text">Fast delivery, every time</div>
      </div>
      <div class="feature">
        <div class="feature-icon">🔒</div>
        <div class="feature-text">100% secure and safe</div>
      </div>
    </div>
  </div>
</div>
<div class="footer">
  &copy; 2026 BITE — Best Instant Takeaway Experience. Made with ❤️ in Nagpur.
</div>
</body>
</html>
