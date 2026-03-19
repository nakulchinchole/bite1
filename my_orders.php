<?php
include 'db.php';
if (!isset($_SESSION['customer_id'])) { header("Location: login.php"); exit(); }
$customer_id = $_SESSION['customer_id'];
$orders = mysqli_query($conn, "SELECT o.*, r.name as rest_name FROM orders o JOIN restaurants r ON o.restaurant_id=r.restaurant_id WHERE o.customer_id=$customer_id ORDER BY o.created_at DESC");
$status_labels = ['placed'=>'Order Placed','preparing'=>'Preparing','out_for_delivery'=>'Out for Delivery','delivered'=>'Delivered'];
$status_colors = ['placed'=>'#f4a261','preparing'=>'#E63946','out_for_delivery'=>'#2196f3','delivered'=>'#4caf50'];
$status_icons = ['placed'=>'📋','preparing'=>'👨‍🍳','out_for_delivery'=>'🚴','delivered'=>'✅'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>BITE — My Orders</title>
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

.page-wrap{max-width:800px;margin:0 auto;padding:40px 24px;flex:1;width:100%;}
.page-header{display:flex;align-items:center;justify-content:space-between;margin-bottom:32px;}
.page-title{font-size:30px;font-weight:900;color:#fff;margin-bottom:4px;}
.page-sub{font-size:14px;color:#555;}
.btn-order-now{background:#E63946;color:#fff;text-decoration:none;border-radius:10px;padding:11px 22px;font-size:13px;font-weight:700;transition:all 0.2s;white-space:nowrap;}
.btn-order-now:hover{background:#c1121f;color:#fff;box-shadow:0 6px 20px rgba(230,57,70,0.35);transform:translateY(-1px);}

.section-title{font-size:11px;font-weight:700;color:#E63946;text-transform:uppercase;letter-spacing:0.15em;margin-bottom:20px;}

/* ORDER CARD */
.order-card{background:#111;border:1px solid #1a1a1a;border-radius:20px;overflow:hidden;margin-bottom:16px;transition:all 0.25s;}
.order-card:hover{border-color:#2a2a2a;transform:translateY(-2px);box-shadow:0 12px 30px rgba(0,0,0,0.4);}
.order-top{padding:20px 24px;border-bottom:1px solid #1a1a1a;display:flex;align-items:center;justify-content:space-between;}
.order-top-left{display:flex;align-items:center;gap:14px;}
.order-icon{width:44px;height:44px;border-radius:12px;background:#1a1a1a;border:1px solid #222;display:flex;align-items:center;justify-content:center;font-size:20px;flex-shrink:0;}
.order-id{font-size:12px;color:#555;font-weight:600;text-transform:uppercase;letter-spacing:0.1em;margin-bottom:3px;}
.order-rest{font-size:16px;font-weight:800;color:#fff;}
.status-badge{display:inline-flex;align-items:center;gap:6px;font-size:12px;font-weight:700;padding:6px 14px;border-radius:999px;}
.order-mid{padding:16px 24px;border-bottom:1px solid #1a1a1a;}
.order-items-preview{font-size:13px;color:#555;font-weight:500;margin-bottom:8px;}
.order-meta{display:flex;align-items:center;gap:16px;flex-wrap:wrap;}
.meta-item{display:flex;align-items:center;gap:6px;font-size:12px;color:#444;}
.order-bottom{padding:16px 24px;display:flex;align-items:center;justify-content:space-between;}
.order-total{font-size:20px;font-weight:900;color:#fff;}
.order-total span{font-size:13px;color:#444;font-weight:400;margin-left:4px;}
.btn-track{display:inline-flex;align-items:center;gap:6px;background:#E63946;color:#fff;text-decoration:none;border-radius:10px;padding:10px 20px;font-size:13px;font-weight:700;transition:all 0.2s;}
.btn-track:hover{background:#c1121f;color:#fff;box-shadow:0 4px 15px rgba(230,57,70,0.3);}

/* EMPTY STATE */
.empty-state{text-align:center;padding:80px 20px;}
.empty-icon{font-size:64px;margin-bottom:20px;display:block;animation:float 3s ease-in-out infinite;}
@keyframes float{0%,100%{transform:translateY(0);}50%{transform:translateY(-10px);}}
.empty-title{font-size:22px;font-weight:800;color:#333;margin-bottom:8px;}
.empty-sub{font-size:14px;color:#444;margin-bottom:28px;}
.btn-start{display:inline-block;background:#E63946;color:#fff;text-decoration:none;border-radius:12px;padding:14px 32px;font-size:15px;font-weight:700;transition:all 0.3s;}
.btn-start:hover{background:#c1121f;color:#fff;box-shadow:0 8px 25px rgba(230,57,70,0.4);transform:translateY(-2px);}

.footer{background:#050505;border-top:1px solid #111;text-align:center;padding:20px;font-size:12px;color:#222;}
</style>
</head>
<body>
<nav class="navbar">
  <a href="index.php" class="logo">BITE</a>
  <div class="nav-right">
    <a href="index.php" class="nav-link-custom">Home</a>
    <a href="logout.php" class="btn-logout">Logout</a>
  </div>
</nav>

<div class="page-wrap">
  <div class="page-header">
    <div>
      <div class="page-title">My Orders 🧾</div>
      <div class="page-sub">Track and manage all your orders</div>
    </div>
    <a href="index.php" class="btn-order-now">+ Order Again</a>
  </div>

  <?php if(mysqli_num_rows($orders) == 0): ?>
  <div class="empty-state">
    <span class="empty-icon">🍽️</span>
    <div class="empty-title">No orders yet!</div>
    <div class="empty-sub">Looks like you haven't ordered anything yet. Let's fix that!</div>
    <a href="index.php" class="btn-start">Browse Restaurants →</a>
  </div>

  <?php else: ?>
  <div class="section-title">Your Order History</div>
  <?php
  $rest_emojis = ['🍛','🍕','🍔','🍜','🌮','🍣'];
  $ei = 0;
  while($o = mysqli_fetch_assoc($orders)):
    $color = $status_colors[$o['status']] ?? '#aaa';
    $label = $status_labels[$o['status']] ?? $o['status'];
    $icon = $status_icons[$o['status']] ?? '📦';
    $items_q = mysqli_query($conn, "SELECT m.item_name, oi.quantity FROM order_items oi JOIN menu_items m ON oi.item_id=m.item_id WHERE oi.order_id={$o['order_id']}");
    $item_names = [];
    while($it = mysqli_fetch_assoc($items_q)) $item_names[] = $it['item_name'].' x'.$it['quantity'];
    $remoji = $rest_emojis[$ei % count($rest_emojis)]; $ei++;
  ?>
  <div class="order-card">
    <div class="order-top">
      <div class="order-top-left">
        <div class="order-icon"><?= $remoji ?></div>
        <div>
          <div class="order-id">Order #<?= $o['order_id'] ?></div>
          <div class="order-rest"><?= htmlspecialchars($o['rest_name']) ?></div>
        </div>
      </div>
      <span class="status-badge" style="background:<?= $color ?>18;color:<?= $color ?>;border:1px solid <?= $color ?>33;">
        <?= $icon ?> <?= $label ?>
      </span>
    </div>
    <div class="order-mid">
      <div class="order-items-preview"><?= implode(' · ', $item_names) ?></div>
      <div class="order-meta">
        <span class="meta-item">🕐 <?= date('d M Y, h:i A', strtotime($o['created_at'])) ?></span>
        <span class="meta-item">📍 <?= htmlspecialchars($o['address']) ?></span>
      </div>
    </div>
    <div class="order-bottom">
      <div class="order-total">₹<?= number_format($o['total_amount'],2) ?><span>total paid</span></div>
      <a href="track_order.php?id=<?= $o['order_id'] ?>" class="btn-track">Track Order →</a>
    </div>
  </div>
  <?php endwhile; ?>
  <?php endif; ?>
</div>

<div class="footer">&copy; 2026 BITE — Best Instant Takeaway Experience</div>
</body>
</html>