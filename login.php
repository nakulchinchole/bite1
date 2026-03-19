<?php
include '../db.php';
$error = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $result = mysqli_query($conn, "SELECT * FROM admins WHERE username='$username' AND password='$password'");
    if (mysqli_num_rows($result) == 1) {
        $_SESSION['admin'] = $username;
        header("Location: dashboard.php");
        exit();
    } else {
        $error = "Invalid username or password!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>BITE Admin — Login</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
<style>
*{margin:0;padding:0;box-sizing:border-box;}
body{background:#080808;color:#fff;font-family:'Inter',sans-serif;min-height:100vh;display:flex;overflow:hidden;}

/* ANIMATED BG */
.bg-effects{position:fixed;inset:0;pointer-events:none;z-index:0;}
.bg-glow-1{position:absolute;top:-200px;left:-200px;width:600px;height:600px;background:radial-gradient(circle,rgba(230,57,70,0.08),transparent 70%);animation:moveGlow1 8s ease-in-out infinite;}
.bg-glow-2{position:absolute;bottom:-200px;right:-200px;width:500px;height:500px;background:radial-gradient(circle,rgba(230,57,70,0.06),transparent 70%);animation:moveGlow2 10s ease-in-out infinite;}
.bg-glow-3{position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);width:800px;height:800px;background:radial-gradient(circle,rgba(230,57,70,0.03),transparent 60%);}
@keyframes moveGlow1{0%,100%{transform:translate(0,0);}50%{transform:translate(60px,40px);}}
@keyframes moveGlow2{0%,100%{transform:translate(0,0);}50%{transform:translate(-40px,-60px);}}

/* GRID LINES */
.grid-lines{position:fixed;inset:0;pointer-events:none;z-index:0;opacity:0.03;background-image:linear-gradient(#E63946 1px,transparent 1px),linear-gradient(90deg,#E63946 1px,transparent 1px);background-size:60px 60px;}

/* LEFT PANEL */
.left{flex:1;display:flex;flex-direction:column;justify-content:center;padding:60px 80px;position:relative;z-index:1;}
.left-top{margin-bottom:60px;}
.left-logo{font-size:32px;font-weight:900;color:#E63946;letter-spacing:5px;display:inline-block;position:relative;}
.left-logo::after{content:'';position:absolute;bottom:-4px;left:0;width:100%;height:2px;background:linear-gradient(90deg,#E63946,transparent);border-radius:2px;}
.left-admin-tag{display:inline-flex;align-items:center;gap:8px;background:rgba(230,57,70,0.08);border:1px solid rgba(230,57,70,0.2);color:#E63946;font-size:11px;font-weight:700;padding:5px 14px;border-radius:999px;margin-top:12px;text-transform:uppercase;letter-spacing:0.12em;}
.left-title{font-size:48px;font-weight:900;color:#fff;line-height:1.1;margin-bottom:16px;}
.left-title span{color:#E63946;}
.left-sub{font-size:15px;color:#444;line-height:1.6;max-width:400px;}
.features{display:flex;flex-direction:column;gap:14px;margin-top:48px;}
.feature{display:flex;align-items:center;gap:16px;padding:16px 20px;background:rgba(255,255,255,0.02);border:1px solid #161616;border-radius:14px;transition:all 0.3s;}
.feature:hover{background:rgba(230,57,70,0.04);border-color:rgba(230,57,70,0.15);transform:translateX(6px);}
.feature-icon{width:40px;height:40px;border-radius:10px;background:#111;border:1px solid #1a1a1a;display:flex;align-items:center;justify-content:center;font-size:18px;flex-shrink:0;}
.feature-title{font-size:14px;font-weight:700;color:#fff;margin-bottom:2px;}
.feature-sub{font-size:12px;color:#444;}

/* RIGHT PANEL — LOGIN FORM */
.right{width:480px;flex-shrink:0;background:#0d0d0d;border-left:1px solid #161616;display:flex;flex-direction:column;justify-content:center;padding:60px 50px;position:relative;z-index:1;}
.right::before{content:'';position:absolute;top:0;left:0;right:0;height:3px;background:linear-gradient(90deg,#E63946,#ff6b6b,transparent);}
.form-header{margin-bottom:36px;}
.form-title{font-size:26px;font-weight:900;color:#fff;margin-bottom:6px;}
.form-sub{font-size:14px;color:#444;}
.form-label{color:#555;font-size:12px;font-weight:600;margin-bottom:8px;display:block;text-transform:uppercase;letter-spacing:0.08em;}
.input-wrap{position:relative;margin-bottom:20px;}
.input-icon{position:absolute;left:16px;top:50%;transform:translateY(-50%);font-size:16px;pointer-events:none;}
.form-control{background:#111;border:1px solid #1a1a1a;color:#fff;border-radius:12px;padding:14px 16px 14px 46px;font-size:14px;width:100%;font-family:'Inter',sans-serif;transition:all 0.3s;}
.form-control:focus{outline:none;border-color:#E63946;background:#141414;box-shadow:0 0 0 3px rgba(230,57,70,0.08);}
.form-control::placeholder{color:#2a2a2a;}
.btn-login{width:100%;background:linear-gradient(135deg,#E63946,#c1121f);color:#fff;border:none;border-radius:12px;padding:16px;font-size:15px;font-weight:800;cursor:pointer;font-family:'Inter',sans-serif;transition:all 0.3s;position:relative;overflow:hidden;letter-spacing:0.02em;}
.btn-login::before{content:'';position:absolute;top:0;left:-100%;width:100%;height:100%;background:linear-gradient(90deg,transparent,rgba(255,255,255,0.1),transparent);transition:left 0.5s;}
.btn-login:hover::before{left:100%;}
.btn-login:hover{box-shadow:0 12px 35px rgba(230,57,70,0.45);transform:translateY(-2px);}
.btn-login:active{transform:translateY(0);}
.error-msg{background:rgba(230,57,70,0.08);border:1px solid rgba(230,57,70,0.25);color:#E63946;padding:13px 16px;border-radius:10px;font-size:13px;font-weight:500;margin-bottom:20px;display:flex;align-items:center;gap:10px;}
.hint-box{margin-top:24px;background:#111;border:1px solid #1a1a1a;border-radius:10px;padding:14px 16px;display:flex;align-items:center;gap:10px;}
.hint-icon{font-size:16px;}
.hint-text{font-size:12px;color:#444;}
.hint-text strong{color:#666;}

/* FLOATING DOTS */
.dot{position:absolute;border-radius:50%;pointer-events:none;animation:floatDot linear infinite;}
@keyframes floatDot{0%{transform:translateY(0) rotate(0deg);opacity:0;}10%{opacity:1;}90%{opacity:1;}100%{transform:translateY(-100vh) rotate(720deg);opacity:0;}}
</style>
</head>
<body>
<div class="bg-effects">
  <div class="bg-glow-1"></div>
  <div class="bg-glow-2"></div>
  <div class="bg-glow-3"></div>
</div>
<div class="grid-lines"></div>

<div class="left">
  <div class="left-top">
    <div class="left-logo">BITE</div>
    <div style="margin-top:12px;"><span class="left-admin-tag">🔐 Admin Panel</span></div>
  </div>
  <div class="left-title">Manage your<br>restaurant <span>empire.</span></div>
  <div class="left-sub">Full control over orders, menus, and customers — all in one powerful dashboard.</div>
  <div class="features">
    <div class="feature">
      <div class="feature-icon">🧾</div>
      <div>
        <div class="feature-title">Real-time Order Management</div>
        <div class="feature-sub">Update order status — customers see it instantly</div>
      </div>
    </div>
    <div class="feature">
      <div class="feature-icon">🍽️</div>
      <div>
        <div class="feature-title">Menu Control</div>
        <div class="feature-sub">Add items, set prices, toggle availability</div>
      </div>
    </div>
    <div class="feature">
      <div class="feature-icon">📊</div>
      <div>
        <div class="feature-title">Live Dashboard</div>
        <div class="feature-sub">Revenue, orders and customer stats at a glance</div>
      </div>
    </div>
  </div>
</div>

<div class="right">
  <div class="form-header">
    <div class="form-title">Admin Login 👋</div>
    <div class="form-sub">Enter your credentials to access the panel</div>
  </div>

  <?php if($error): ?>
  <div class="error-msg">⚠️ <?= $error ?></div>
  <?php endif; ?>

  <form method="POST">
    <label class="form-label">Username</label>
    <div class="input-wrap">
      <span class="input-icon">👤</span>
      <input type="text" name="username" class="form-control" placeholder="Enter username" required>
    </div>
    <label class="form-label">Password</label>
    <div class="input-wrap">
      <span class="input-icon">🔑</span>
      <input type="password" name="password" class="form-control" placeholder="Enter password" required>
    </div>
    <button type="submit" class="btn-login">Login to Admin Panel →</button>
  </form>

  <div class="hint-box">
    <span class="hint-icon">💡</span>
    <div class="hint-text">Default credentials — Username: <strong>admin</strong> · Password: <strong>1234</strong></div>
  </div>
</div>
</body>
</html>