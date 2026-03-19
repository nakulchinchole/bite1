<?php
include '../db.php';
if (!isset($_SESSION['admin'])) { header("Location: login.php"); exit(); }
if (isset($_POST['update_status'])) {
    $oid = $_POST['order_id'];
    $status = $_POST['status'];
    mysqli_query($conn, "UPDATE orders SET status='$status' WHERE order_id=$oid");
    header("Location: orders.php?msg=updated"); exit();
}
$orders = mysqli_query($conn,"SELECT o.*,c.name as cname,r.name as rname FROM orders o JOIN customers c ON o.customer_id=c.customer_id JOIN restaurants r ON o.restaurant_id=r.restaurant_id ORDER BY o.created_at DESC");
$status_colors = ['placed'=>'#f4a261','preparing'=>'#E63946','out_for_delivery'=>'#2196f3','delivered'=>'#4caf50'];
$status_labels = ['placed'=>'Placed','preparing'=>'Preparing','out_for_delivery'=>'Out for Delivery','delivered'=>'Delivered'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>BITE Admin — Orders</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
<style>
*{margin:0;padding:0;box-sizing:border-box;}
body{background:#080808;color:#fff;font-family:'Inter',sans-serif;min-height:100vh;display:flex;}
.sidebar{width:240px;background:#0d0d0d;border-right:1px solid #161616;min-height:100vh;flex-shrink:0;position:fixed;top:0;left:0;height:100%;display:flex;flex-direction:column;}
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
.main{margin-left:240px;flex:1;}
.topbar{background:#0d0d0d;border-bottom:1px solid #161616;padding:20px 36px;display:flex;align-items:center;justify-content:space-between;}
.topbar-title{font-size:20px;font-weight:800;color:#fff;}
.topbar-sub{font-size:13px;color:#444;margin-top:2px;}
.content{padding:32px 36px;}
.success-msg{background:rgba(76,175,80,0.08);border:1px solid rgba(76,175,80,0.2);color:#4caf50;padding:14px 18px;border-radius:12px;font-size:13px;font-weight:600;margin-bottom:24px;display:flex;align-items:center;gap:8px;}
.section-title{font-size:11px;font-weight:700;color:#E63946;text-transform:uppercase;letter-spacing:0.15em;margin-bottom:18px;}
.table-wrap{background:#0d0d0d;border:1px solid #161616;border-radius:18px;overflow:hidden;}
table{width:100%;border-collapse:collapse;}
thead tr{background:#0a0a0a;border-bottom:1px solid #161616;}
th{padding:14px 20px;text-align:left;font-size:11px;color:#333;text-transform:uppercase;letter-spacing:0.1em;font-weight:700;}
td{padding:15px 20px;font-size:14px;color:#777;border-bottom:1px solid #111;vertical-align:middle;}
tbody tr:last-child td{border-bottom:none;}
tbody tr:hover td{background:#0f0f0f;}
.td-name{color:#fff;font-weight:600;}
.td-amount{color:#E63946;font-weight:700;}
.td-id{color:#444;font-weight:700;}
.status-badge{display:inline-flex;align-items:center;font-size:11px;font-weight:700;padding:4px 12px;border-radius:999px;}
.status-select{background:#111;border:1px solid #1a1a1a;color:#fff;border-radius:10px;padding:8px 12px;font-size:13px;cursor:pointer;font-family:'Inter',sans-serif;transition:border-color 0.2s;}
.status-select:focus{outline:none;border-color:#E63946;}
.status-select option{background:#111;}
.btn-update{background:#E63946;color:#fff;border:none;border-radius:10px;padding:8px 16px;font-size:12px;font-weight:700;cursor:pointer;font-family:'Inter',sans-serif;margin-left:8px;transition:all 0.2s;}
.btn-update:hover{background:#c1121f;box-shadow:0 4px 12px rgba(230,57,70,0.3);}
.update-form{display:flex;align-items:center;}
</style>
</head>
<body>
<div class="sidebar">
  <div class="sidebar-brand">
    <div class="sidebar-logo">BITE</div>
    <span class="sidebar-tag">Admin Panel</span>
  </div>
  <div class="nav-section">Main Menu</div>
  <a href="dashboard.php" class="nav-item"><span class="nav-icon">📊</span> Dashboard</a>
  <a href="orders.php" class="nav-item active"><span class="nav-icon">🧾</span> Orders</a>
  <a href="menu.php" class="nav-item"><span class="nav-icon">🍽️</span> Menu Items</a>
  <div class="sidebar-footer">
    <a href="logout.php" class="logout-btn"><span>🚪</span> Logout</a>
  </div>
</div>

<div class="main">
  <div class="topbar">
    <div>
      <div class="topbar-title">Manage Orders 🧾</div>
      <div class="topbar-sub">Update status — customer tracking updates instantly</div>
    </div>
  </div>
  <div class="content">
    <?php if(isset($_GET['msg']) && $_GET['msg']=='updated'): ?>
    <div class="success-msg">✅ Order status updated! Customer can see the change live.</div>
    <?php endif; ?>
    <div class="section-title">All Orders</div>
    <div class="table-wrap">
      <table>
        <thead><tr><th>Order ID</th><th>Customer</th><th>Restaurant</th><th>Amount</th><th>Date</th><th>Current Status</th><th>Update</th></tr></thead>
        <tbody>
        <?php while($o = mysqli_fetch_assoc($orders)):
          $color = $status_colors[$o['status']] ?? '#aaa';
          $label = $status_labels[$o['status']] ?? $o['status'];
        ?>
        <tr>
          <td class="td-id">#<?= $o['order_id'] ?></td>
          <td class="td-name"><?= htmlspecialchars($o['cname']) ?></td>
          <td><?= htmlspecialchars($o['rname']) ?></td>
          <td class="td-amount">₹<?= number_format($o['total_amount'],2) ?></td>
          <td><?= date('d M, h:i A', strtotime($o['created_at'])) ?></td>
          <td><span class="status-badge" style="background:<?= $color ?>18;color:<?= $color ?>;border:1px solid <?= $color ?>33;"><?= $label ?></span></td>
          <td>
            <form method="POST" class="update-form">
              <input type="hidden" name="order_id" value="<?= $o['order_id'] ?>">
              <input type="hidden" name="update_status" value="1">
              <select name="status" class="status-select">
                <option value="placed" <?= $o['status']=='placed'?'selected':'' ?>>Placed</option>
                <option value="preparing" <?= $o['status']=='preparing'?'selected':'' ?>>Preparing</option>
                <option value="out_for_delivery" <?= $o['status']=='out_for_delivery'?'selected':'' ?>>Out for Delivery</option>
                <option value="delivered" <?= $o['status']=='delivered'?'selected':'' ?>>Delivered</option>
              </select>
              <button type="submit" class="btn-update">Update</button>
            </form>
          </td>
        </tr>
        <?php endwhile; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
</body>
</html>