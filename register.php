<?php
include 'db.php';
$error = "";
$success = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $check = mysqli_query($conn, "SELECT * FROM customers WHERE email='$email'");
    if (mysqli_num_rows($check) > 0) {
        $error = "Email already registered! Please login.";
    } else {
        $sql = "INSERT INTO customers (name, email, password, phone, address) VALUES ('$name', '$email', '$password', '$phone', '$address')";
        if (mysqli_query($conn, $sql)) {
            $success = "Account created successfully!";
        } else {
            $error = "Something went wrong. Try again!";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>BITE — Sign Up</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
<style>
*{margin:0;padding:0;box-sizing:border-box;}
body{background:#0a0a0a;color:#fff;font-family:'Inter',sans-serif;min-height:100vh;display:flex;flex-direction:column;}
.page{display:grid;grid-template-columns:1fr 1fr;min-height:100vh;}
.right{background:#0f0f0f;border-right:1px solid #111;display:flex;flex-direction:column;justify-content:center;align-items:center;padding:60px;position:relative;overflow:hidden;}
.right-glow{position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);width:400px;height:400px;background:radial-gradient(circle,rgba(230,57,70,0.12),transparent 70%);pointer-events:none;animation:breathe 4s ease-in-out infinite;}
@keyframes breathe{0%,100%{transform:translate(-50%,-50%) scale(1);}50%{transform:translate(-50%,-50%) scale(1.1);}}
.right-logo{font-size:72px;font-weight:900;color:#E63946;letter-spacing:6px;margin-bottom:12px;position:relative;z-index:1;text-shadow:0 0 60px rgba(230,57,70,0.4);}
.right-tagline{font-size:14px;color:#444;margin-bottom:48px;position:relative;z-index:1;text-align:center;}
.steps{display:flex;flex-direction:column;gap:12px;width:100%;max-width:280px;position:relative;z-index:1;}
.step{display:flex;align-items:center;gap:14px;}
.step-num{width:32px;height:32px;border-radius:50%;background:#E63946;color:#fff;font-size:13px;font-weight:700;display:flex;align-items:center;justify-content:center;flex-shrink:0;}
.step-text{font-size:13px;color:#666;font-weight:500;}
.step-line{width:2px;height:16px;background:#1a1a1a;margin-left:15px;}
.food-float{position:absolute;font-size:24px;opacity:0.04;animation:floatAround linear infinite;}
@keyframes floatAround{0%{transform:translateY(100%) rotate(0deg);}100%{transform:translateY(-100px) rotate(360deg);}}
.left{background:#0a0a0a;display:flex;flex-direction:column;justify-content:center;padding:60px;}
.left-logo{font-size:28px;font-weight:900;color:#E63946;letter-spacing:4px;margin-bottom:40px;display:inline-block;}
.left-title{font-size:32px;font-weight:800;color:#fff;line-height:1.2;margin-bottom:8px;}
.left-sub{font-size:15px;color:#555;margin-bottom:36px;}
.form-label{color:#666;font-size:13px;font-weight:500;margin-bottom:8px;display:block;}
.form-control{background:#111;border:1px solid #222;color:#fff;border-radius:12px;padding:13px 18px;font-size:14px;width:100%;font-family:'Inter',sans-serif;transition:all 0.3s;margin-bottom:16px;}
.form-control:focus{outline:none;border-color:#E63946;background:#111;color:#fff;box-shadow:0 0 0 3px rgba(230,57,70,0.1);}
.form-control::placeholder{color:#333;}
.row-grid{display:grid;grid-template-columns:1fr 1fr;gap:14px;}
.btn-register{width:100%;background:#E63946;color:#fff;border:none;border-radius:12px;padding:15px;font-size:15px;font-weight:700;cursor:pointer;font-family:'Inter',sans-serif;transition:all 0.3s;position:relative;overflow:hidden;}
.btn-register::before{content:'';position:absolute;inset:0;background:linear-gradient(135deg,rgba(255,255,255,0.1),transparent);pointer-events:none;}
.btn-register:hover{background:#c1121f;box-shadow:0 8px 30px rgba(230,57,70,0.4);transform:translateY(-1px);}
.login-link{text-align:center;margin-top:20px;font-size:14px;color:#555;}
.login-link a{color:#E63946;text-decoration:none;font-weight:600;}
.error-msg{background:rgba(230,57,70,0.1);border:1px solid rgba(230,57,70,0.3);color:#E63946;padding:12px 16px;border-radius:10px;font-size:13px;margin-bottom:16px;}
.success-msg{background:rgba(76,175,80,0.1);border:1px solid rgba(76,175,80,0.3);color:#4caf50;padding:12px 16px;border-radius:10px;font-size:13px;margin-bottom:16px;}
</style>
</head>
<body>
<div class="page">
  <div class="right">
    <div class="right-glow"></div>
    <span class="food-float" style="left:10%;animation-duration:12s;">🍛</span>
    <span class="food-float" style="left:50%;animation-duration:9s;animation-delay:3s;">🍕</span>
    <span class="food-float" style="left:80%;animation-duration:14s;animation-delay:1s;">🍔</span>
    <div class="right-logo">BITE</div>
    <div class="right-tagline">Best Instant Takeaway Experience</div>
    <div class="steps">
      <div class="step"><div class="step-num">1</div><div class="step-text">Create your free account</div></div>
      <div class="step-line"></div>
      <div class="step"><div class="step-num">2</div><div class="step-text">Browse restaurants & menu</div></div>
      <div class="step-line"></div>
      <div class="step"><div class="step-num">3</div><div class="step-text">Place your order in seconds</div></div>
      <div class="step-line"></div>
      <div class="step"><div class="step-num">4</div><div class="step-text">Track delivery in real-time</div></div>
    </div>
  </div>

  <div class="left">
    <span class="left-logo">BITE</span>
    <div class="left-title">Create your account 🚀</div>
    <div class="left-sub">Join thousands of happy customers in Nagpur</div>

    <?php if($error): ?><div class="error-msg">⚠️ <?= $error ?></div><?php endif; ?>
    <?php if($success): ?><div class="success-msg">✅ <?= $success ?> <a href="login.php" style="color:#4caf50;font-weight:700;">Login now →</a></div><?php endif; ?>

    <form method="POST">
      <div class="row-grid">
        <div>
          <label class="form-label">Full name</label>
          <input type="text" name="name" class="form-control" placeholder="Your name" required>
        </div>
        <div>
          <label class="form-label">Phone</label>
          <input type="text" name="phone" class="form-control" placeholder="9876543210" required>
        </div>
      </div>
      <label class="form-label">Email address</label>
      <input type="email" name="email" class="form-control" placeholder="you@example.com" required>
      <label class="form-label">Password</label>
      <input type="password" name="password" class="form-control" placeholder="Create a strong password" required>
      <label class="form-label">Delivery address</label>
      <textarea name="address" class="form-control" placeholder="Your full delivery address" rows="2" required></textarea>
      <button type="submit" class="btn-register">Create my BITE account →</button>
    </form>

    <div class="login-link">Already have an account? <a href="login.php">Login here →</a></div>
  </div>
</div>
</body>
</html>