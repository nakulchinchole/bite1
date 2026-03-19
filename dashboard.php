<?php
include '../db.php';
if (!isset($_SESSION['admin'])) { header("Location: login.php"); exit(); }
$total_orders = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) as c FROM orders"))['c'];
$total_customers = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) as c FROM customers"))['c'];
$total_revenue = mysqli_fetch_assoc(mysqli_query($conn,"SELECT SUM(total_amount) as s FROM orders"))['s'] ?? 0;
$pending_orders = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) as c FROM orders WHERE status!='delivered'"))['c'];
$recent_orders = mysqli_query($conn,"SELECT o.*,c.name as cname,r.name as rname FROM orders o JOIN customers c ON o.customer_id=c.customer_id JOIN restaurants r ON o.restaurant_id=r.restaurant_id ORDER BY o.created_at DESC LIMIT 8");
$status_colors = ['placed'=>'#f4a261','preparing'=>'#E63946','out_for_delivery'=>'#2196f3','delivered'=>'#4caf50'];
$status_labels = ['placed'=>'Placed','preparing'=>'Preparing','out_for_delivery'=>'Out for Delivery','delivered'=>'Delivered'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>BITE Admin — Dashboard</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
<style>
*{margin:0;padding:0;box-sizing:border-box;}
body{background:#080808;color:#fff;font-family:'Inter',sans-serif;min-height:100vh;display:flex;}

/* SIDEBAR */
.sidebar{width:240px;background:#0d0d0d;border-right:1px solid #161616;min-height:100vh;padding:0;flex-shrink:0;position:fixed;top:0;left:0;height:100%;display:flex;flex-direction:column;}
.sidebar-brand{padding:28px 24px;border-bottom:1px solid #161616;}
.sidebar-logo{font-size:24px;font-weight:900;color:#E63946;letter-spacing:4px;}
.sidebar-tag{font-size:10px;color:#333;text-transform:uppercase;letter-spacing:0.15em;display:block;margin-top:3px;font-weight:600;}
.nav-section{font-size:10px;color:#2a2a2a;text-transform:uppercase;letter-spacing:0.15em;padding:24px 24px 8px;font-weight:700;}
.nav-item{display:flex;align-items:center;gap:12px;padding:12px 24px;color:#444;text-decoration:none;font-size:14px;font-weight:500;transition:all 0.2s;border-left:3px solid transparent;margin:1px 0;}
.nav-item:hover{background:#141414;color:#888;border-left-color:#222;}
.nav-item.active{background:#1a0a0a;color:#fff;border-left-color:#E63946;}
.nav-icon{font-size:16px;width:20px;text-align:center;}
.sidebar-footer{margin-top:auto;padding:20px 24px;border-top:1px solid #161616;}
.logout-btn{display:flex;align-items:center;gap:10px;color:#333;text-decoration:none;font-size:13px;font-weight:500;transition:color 0.2s;}
.logout-btn:hover{color:#E63946;}

/* MAIN */
.main{margin-left:240px;flex:1;padding:0;}
.topbar{background:#0d0d0d;border-bottom:1px solid #161616;padding:20px 36px;display:flex;align-items:center;justify-content:space-between;}
.topbar-title{font-size:20px;font-weight:800;color:#fff;}
.topbar-sub{font-size:13px;color:#444;margin-top:2px;}
.admin-badge{display:flex;align-items:center;gap:10px;background:#141414;border:1px solid #1a1a1a;border-radius:10px;padding:8px 16px;}
.admin-dot{width:8px;height:8px;background:#4caf50;border-radius:50%;flex-shrink:0;}
.admin-name{font-size:13px;color:#888;font-weight:500;}

.content{padding:32px 36px;}

/* STATS */
.stats-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:16px;margin-bottom:32px;}
.stat-card{background:#0d0d0d;border:1px solid #161616;border-radius:18px;padding:22px;position:relative;overflow:hidden;transition:all 0.25s;}
.stat-card:hover{border-color:#222;transform:translateY(-2px);}
.stat-card::after{content:'';position:absolute;bottom:0;left:0;right:0;height:2px;background:linear-gradient(90deg,#E63946,transparent);}
.stat-top{display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;}
.stat-icon-wrap{width:42px;height:42px;border-radius:12px;background:#1a1a1a;display:flex;align-items:center;justify-content:center;font-size:18px;}
.stat-trend{font-size:11px;color:#4caf50;font-weight:600;background:rgba(76,175,80,0.1);padding:3px 8px;border-radius:999px;}
.stat-num{font-size:34px;font-weight:900;color:#fff;margin-bottom:4px;line-height:1;}
.stat-label{font-size:12px;color:#444;font-weight:500;text-transform:uppercase;letter-spacing:0.08em;}

/* TABLE */
.section-header{display:flex;align-items:center;justify-content:space-between;margin-bottom:18px;}
.section-title{font-size:11px;font-weight:700;color:#E63946;text-transform:uppercase;letter-spacing:0.15em;}
.view-all{font-size:12px;color:#444;text-decoration:none;font-weight:600;transition:color 0.2s;}
.view-all:hover{color:#E63946;}
.table-wrap{background:#0d0d0d;border:1px solid #161616;border-radius:18px;overflow:hidden;}
table{width:100%;border-collapse:collapse;}
thead tr{background:#0a0a0a;border-bottom:1px solid #161616;}
th{padding:14px 20px;text-align:left;font-size:11px;color:#333;text-transform:uppercase;letter-spacing:0.1em;font-weight:700;}
td{padding:16px 20px;font-size:14px;color:#888;border-bottom:1px solid #111;}
tbody tr:last-child td{border-bottom:none;}
tbody tr{transition:background 0.15s;}
tbody tr:hover td{background:#111;color:#ccc;}
.status-badge{display:inline-flex;align-items:center;gap:5px;font-size:11px;font-weight:700;padding:4px 12px;border-radius:999px;}
.td-name{color:#fff;font-weight:600;}
.td-amount{color:#E63946;font-weight:700;}
.btn-manage{display:inline-flex;align-items:center;gap:4px;background:transparent;color:#444;text-decoration:none;border:1px solid #1a1a1a;border-radius:8px;padding:5px 12px;font-size:12px;font-weight:600;transition:all 0.2s;}
.btn-manage:hover{border-color:#E63946;color:#E63946;}
</style>
</head>
<body>
<div class="sidebar">
  <div class="sidebar-brand">
    <div class="sidebar-logo">BITE</div>
    <span class="sidebar-tag">Admin Panel</span>
  </div>
  <div class="nav-section">Main Menu</div>
  <a href="dashboard.php" class="nav-item active"><span class="nav-icon">📊</span> Dashboard</a>
  <a href="orders.php" class="nav-item"><span class="nav-icon">🧾</span> Orders</a>
  <a href="menu.php" class="nav-item"><span class="nav-icon">🍽️</span> Menu Items</a>
  <div class="sidebar-footer">
    <a href="logout.php" class="logout-btn"><span>🚪</span> Logout</a>
  </div>
</div>

<div class="main">
  <div class="topbar">
    <div>
      <div class="topbar-title">Dashboard</div>
      <div class="topbar-sub">Welcome back, <?= $_SESSION['admin'] ?> 👋</div>
    </div>
    <div class="admin-badge">
      <div class="admin-dot"></div>
      <div class="admin-name">Admin · Online</div>
    </div>
  </div>

  <div class="content">
    <div class="stats-grid">
      <div class="stat-card">
        <div class="stat-top"><div class="stat-icon-wrap">🧾</div><span class="stat-trend">Live</span></div>
        <div class="stat-num"><?= $total_orders ?></div>
        <div class="stat-label">Total Orders</div>
      </div>
      <div class="stat-card">
        <div class="stat-top"><div class="stat-icon-wrap">👥</div><span class="stat-trend">Active</span></div>
        <div class="stat-num"><?= $total_customers ?></div>
        <div class="stat-label">Customers</div>
      </div>
      <div class="stat-card">
        <div class="stat-top"><div class="stat-icon-wrap">💰</div><span class="stat-trend">Total</span></div>
        <div class="stat-num">₹<?= number_format($total_revenue,0) ?></div>
        <div class="stat-label">Revenue</div>
      </div>
      <div class="stat-card">
        <div class="stat-top"><div class="stat-icon-wrap">⏳</div><span class="stat-trend" style="color:#f4a261;background:rgba(244,162,97,0.1);">Pending</span></div>
        <div class="stat-num"><?= $pending_orders ?></div>
        <div class="stat-label">Pending Orders</div>
      </div>
    </div>

    <div class="section-header">
      <div class="section-title">Recent Orders</div>
      <a href="orders.php" class="view-all">View all →</a>
    </div>
    <div class="table-wrap">
      <table>
        <thead><tr><th>Order ID</th><th>Customer</th><th>Restaurant</th><th>Amount</th><th>Status</th><th>Action</th></tr></thead>
        <tbody>
        <?php while($o = mysqli_fetch_assoc($recent_orders)):
          $color = $status_colors[$o['status']] ?? '#aaa';
          $label = $status_labels[$o['status']] ?? $o['status'];
        ?>
        <tr>
          <td><span style="color:#555;font-weight:700;">#<?= $o['order_id'] ?></span></td>
          <td class="td-name"><?= htmlspecialchars($o['cname']) ?></td>
          <td><?= htmlspecialchars($o['rname']) ?></td>
          <td class="td-amount">₹<?= number_format($o['total_amount'],2) ?></td>
          <td><span class="status-badge" style="background:<?= $color ?>18;color:<?= $color ?>;border:1px solid <?= $color ?>33;"><?= $label ?></span></td>
          <td><a href="orders.php" class="btn-manage">Manage →</a></td>
        </tr>
        <?php endwhile; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
</body>
</html>