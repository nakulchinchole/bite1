<?php
include 'db.php';
if (!isset($_SESSION['customer_id'])) { header("Location: login.php"); exit(); }
$order_id = $_GET['id'];
$customer_id = $_SESSION['customer_id'];
$order = mysqli_fetch_assoc(mysqli_query($conn, "SELECT o.*, r.name as rest_name FROM orders o JOIN restaurants r ON o.restaurant_id=r.restaurant_id WHERE o.order_id=$order_id AND o.customer_id=$customer_id"));
if (!$order) { header("Location: my_orders.php"); exit(); }
$order_items = mysqli_query($conn, "SELECT oi.*, m.item_name FROM order_items oi JOIN menu_items m ON oi.item_id=m.item_id WHERE oi.order_id=$order_id");
$statuses = ['placed','preparing','out_for_delivery','delivered'];
$current = array_search($order['status'], $statuses);
$status_labels = ['Order Placed','Preparing','Out for Delivery','Delivered'];
$status_icons = ['📋','👨‍🍳','🚴','✅'];
$status_desc = ['Your order has been received by the restaurant!','The restaurant is preparing your food fresh!','Your order is on the way — hold tight!','Your food has arrived! Enjoy your meal! 🎉'];
$status_colors = ['#f4a261','#E63946','#2196f3','#4caf50'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>BITE — Track Order #<?= $order_id ?></title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
<style>
*{margin:0;padding:0;box-sizing:border-box;}
body{background:#0a0a0a;color:#fff;font-family:'Inter',sans-serif;min-height:100vh;display:flex;flex-direction:column;}
.navbar{background:rgba(10,10,10,0.95);backdrop-filter:blur(20px);border-bottom:1px solid #1a1a1a;padding:16px 40px;display:flex;align-items:center;justify-content:space-between;position:sticky;top:0;z-index:100;}
.logo{font-size:24px;font-weight:900;color:#E63946;letter-spacing:4px;text-decoration:none;}
.nav-right{display:flex;align-items:center;gap:20px;}
.nav-link-custom{color:#666;text-decoration:none;font-size:14px;font-weight:500;transition:color 0.2s;}
.nav-link-custom:hover{color:#fff;}
.btn-logout{background:transparent;border:1px solid #E63946;color:#E63946;padding:7px 18px;border-radius:8px;font-size:13px;font-weight:600;text-decoration:none;transition:all 0.2s;}
.btn-logout:hover{background:#E63946;color:#fff;}
.page-wrap{max-width:720px;margin:0 auto;padding:40px 24px;flex:1;}

/* ORDER HEADER */
.order-header{background:#111;border:1px solid #1a1a1a;border-radius:20px;padding:28px;margin-bottom:20px;position:relative;overflow:hidden;}
.order-header::before{content:'';position:absolute;top:0;left:0;right:0;height:3px;background:linear-gradient(90deg,#E63946,#ff6b6b);}
.order-id-tag{font-size:12px;color:#E63946;font-weight:700;text-transform:uppercase;letter-spacing:0.12em;margin-bottom:8px;}
.order-rest-name{font-size:24px;font-weight:800;color:#fff;margin-bottom:6px;}
.order-meta{font-size:13px;color:#555;display:flex;align-items:center;gap:8px;flex-wrap:wrap;}
.meta-dot{width:4px;height:4px;background:#333;border-radius:50%;}

/* TRACKING */
.track-card{background:#111;border:1px solid #1a1a1a;border-radius:20px;padding:28px;margin-bottom:20px;}
.section-title{font-size:11px;font-weight:700;color:#E63946;text-transform:uppercase;letter-spacing:0.15em;margin-bottom:28px;}
.steps-wrap{display:flex;align-items:flex-start;justify-content:space-between;position:relative;margin-bottom:28px;}
.progress-track{position:absolute;top:22px;left:10%;right:10%;height:2px;background:#1a1a1a;z-index:0;}
.progress-fill{position:absolute;top:0;left:0;height:100%;background:linear-gradient(90deg,#E63946,#ff6b6b);border-radius:2px;transition:width 1s ease;}
.step{display:flex;flex-direction:column;align-items:center;flex:1;position:relative;z-index:1;}
.step-circle{width:46px;height:46px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:18px;margin-bottom:10px;border:2px solid #1a1a1a;background:#0a0a0a;transition:all 0.5s;}
.step-circle.done{background:#E63946;border-color:#E63946;box-shadow:0 4px 15px rgba(230,57,70,0.3);}
.step-circle.active{background:#E63946;border-color:#E63946;box-shadow:0 0 0 6px rgba(230,57,70,0.15),0 4px 20px rgba(230,57,70,0.4);animation:activePulse 2s ease-in-out infinite;}
@keyframes activePulse{0%,100%{box-shadow:0 0 0 6px rgba(230,57,70,0.15),0 4px 20px rgba(230,57,70,0.4);}50%{box-shadow:0 0 0 10px rgba(230,57,70,0.08),0 4px 25px rgba(230,57,70,0.5);}}
.step-circle.pending{background:#111;border-color:#1a1a1a;}
.step-label{font-size:11px;font-weight:600;text-align:center;color:#333;transition:color 0.3s;}
.step-label.done,.step-label.active{color:#fff;}

/* STATUS BOX */
.status-box{border-radius:14px;padding:24px;text-align:center;border:1px solid;transition:all 0.5s;}
.status-emoji{font-size:40px;margin-bottom:10px;display:block;animation:bounce 1s ease-in-out infinite alternate;}
@keyframes bounce{from{transform:translateY(0);}to{transform:translateY(-6px);}}
.status-title{font-size:20px;font-weight:800;color:#fff;margin-bottom:6px;}
.status-desc{font-size:13px;color:#888;}

/* ORDER ITEMS */
.items-card{background:#111;border:1px solid #1a1a1a;border-radius:20px;padding:28px;margin-bottom:20px;}
.order-item{display:flex;align-items:center;justify-content:space-between;padding:14px 0;border-bottom:1px solid #1a1a1a;}
.order-item:last-child{border-bottom:none;}
.item-left{display:flex;align-items:center;gap:12px;}
.item-dot{width:8px;height:8px;background:#E63946;border-radius:50%;flex-shrink:0;}
.item-name{font-size:14px;color:#fff;font-weight:500;}
.item-qty{font-size:12px;color:#444;margin-top:2px;}
.item-price{font-size:14px;color:#E63946;font-weight:700;}
.total-wrap{background:#0f0f0f;border-radius:12px;padding:18px;margin-top:6px;}
.total-row{display:flex;justify-content:space-between;font-size:14px;color:#555;margin-bottom:10px;}
.total-row.final{color:#fff;font-weight:800;font-size:17px;padding-top:12px;border-top:1px solid #1a1a1a;margin-top:4px;}

/* BUTTONS */
.btn-wrap{display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-top:4px;}
.btn-primary{display:block;text-align:center;background:#E63946;color:#fff;text-decoration:none;border-radius:12px;padding:14px;font-size:14px;font-weight:700;transition:all 0.3s;}
.btn-primary:hover{background:#c1121f;color:#fff;box-shadow:0 8px 25px rgba(230,57,70,0.35);transform:translateY(-1px);}
.btn-secondary{display:block;text-align:center;background:transparent;color:#888;text-decoration:none;border:1px solid #1a1a1a;border-radius:12px;padding:14px;font-size:14px;font-weight:600;transition:all 0.2s;}
.btn-secondary:hover{border-color:#E63946;color:#E63946;}
.footer{background:#050505;border-top:1px solid #111;text-align:center;padding:20px;font-size:12px;color:#222;}
</style>
</head>
<body>
<nav class="navbar">
  <a href="index.php" class="logo">BITE</a>
  <div class="nav-right">
    <a href="my_orders.php" class="nav-link-custom">My Orders</a>
    <a href="index.php" class="nav-link-custom">Home</a>
    <a href="logout.php" class="btn-logout">Logout</a>
  </div>
</nav>

<div class="page-wrap">
  <div class="order-header">
    <div class="order-id-tag">Order #<?= $order_id ?></div>
    <div class="order-rest-name"><?= htmlspecialchars($order['rest_name']) ?></div>
    <div class="order-meta">
      <span><?= date('d M Y, h:i A', strtotime($order['created_at'])) ?></span>
      <span class="meta-dot"></span>
      <span>📍 <?= htmlspecialchars($order['address']) ?></span>
    </div>
  </div>

  <div class="track-card">
    <div class="section-title">Live Tracking</div>
    <div class="steps-wrap">
      <div class="progress-track">
        <div class="progress-fill" style="width:<?= $current == 0 ? '0%' : ($current == 1 ? '33%' : ($current == 2 ? '66%' : '100%')) ?>"></div>
      </div>
      <?php for($i=0;$i<4;$i++): $cls=$i<$current?'done':($i==$current?'active':'pending'); ?>
      <div class="step">
        <div class="step-circle <?= $cls ?>"><?= $status_icons[$i] ?></div>
        <div class="step-label <?= $cls ?>"><?= $status_labels[$i] ?></div>
      </div>
      <?php endfor; ?>
    </div>
    <?php $col = $status_colors[$current]; ?>
    <div class="status-box" style="background:<?= $col ?>11;border-color:<?= $col ?>33;">
      <span class="status-emoji"><?= $status_icons[$current] ?></span>
      <div class="status-title" style="color:<?= $col ?>"><?= $status_labels[$current] ?></div>
      <div class="status-desc"><?= $status_desc[$current] ?></div>
    </div>
  </div>

  <div class="items-card">
    <div class="section-title">Order Details</div>
    <?php $subtotal=0; while($item=mysqli_fetch_assoc($order_items)): $subtotal+=$item['subtotal']; ?>
    <div class="order-item">
      <div class="item-left">
        <div class="item-dot"></div>
        <div>
          <div class="item-name"><?= htmlspecialchars($item['item_name']) ?></div>
          <div class="item-qty">Qty: <?= $item['quantity'] ?></div>
        </div>
      </div>
      <div class="item-price">₹<?= number_format($item['subtotal'],2) ?></div>
    </div>
    <?php endwhile; ?>
    <div class="total-wrap">
      <div class="total-row"><span>Subtotal</span><span>₹<?= number_format($subtotal,2) ?></span></div>
      <div class="total-row"><span>Delivery fee</span><span>₹30.00</span></div>
      <div class="total-row final"><span>Total Paid</span><span>₹<?= number_format($subtotal+30,2) ?></span></div>
    </div>
    <div class="btn-wrap" style="margin-top:20px;">
      <a href="index.php" class="btn-primary">Order More Food →</a>
      <a href="my_orders.php" class="btn-secondary">All Orders</a>
    </div>
  </div>
</div>

<div class="footer">&copy; 2026 BITE — Best Instant Takeaway Experience</div>
</body>
</html>