<?php
include '../db.php';
if (!isset($_SESSION['admin'])) { header("Location: login.php"); exit(); }
if (isset($_POST['add_item'])) {
    $name = $_POST['item_name'];
    $price = $_POST['price'];
    $cat = $_POST['category'];
    $rid = $_POST['restaurant_id'];
    mysqli_query($conn, "INSERT INTO menu_items (restaurant_id, item_name, price, category) VALUES ($rid, '$name', $price, '$cat')");
    header("Location: menu.php?msg=added"); exit();
}
if (isset($_GET['toggle'])) {
    $id = $_GET['toggle'];
    $item = mysqli_fetch_assoc(mysqli_query($conn, "SELECT is_available FROM menu_items WHERE item_id=$id"));
    $new = $item['is_available'] ? 0 : 1;
    mysqli_query($conn, "UPDATE menu_items SET is_available=$new WHERE item_id=$id");
    header("Location: menu.php?msg=updated"); exit();
}
$items = mysqli_query($conn,"SELECT m.*,r.name as rname FROM menu_items m JOIN restaurants r ON m.restaurant_id=r.restaurant_id ORDER BY m.restaurant_id,m.item_id");
$restaurants = mysqli_query($conn,"SELECT * FROM restaurants");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>BITE Admin — Menu</title>
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
.topbar{background:#0d0d0d;border-bottom:1px solid #161616;padding:20px 36px;}
.topbar-title{font-size:20px;font-weight:800;color:#fff;}
.topbar-sub{font-size:13px;color:#444;margin-top:2px;}
.content{padding:32px 36px;}
.success-msg{background:rgba(76,175,80,0.08);border:1px solid rgba(76,175,80,0.2);color:#4caf50;padding:14px 18px;border-radius:12px;font-size:13px;font-weight:600;margin-bottom:24px;display:flex;align-items:center;gap:8px;}
.page-grid{display:grid;grid-template-columns:1fr 320px;gap:24px;align-items:start;}
.section-title{font-size:11px;font-weight:700;color:#E63946;text-transform:uppercase;letter-spacing:0.15em;margin-bottom:18px;}
.table-wrap{background:#0d0d0d;border:1px solid #161616;border-radius:18px;overflow:hidden;}
table{width:100%;border-collapse:collapse;}
thead tr{background:#0a0a0a;border-bottom:1px solid #161616;}
th{padding:14px 20px;text-align:left;font-size:11px;color:#333;text-transform:uppercase;letter-spacing:0.1em;font-weight:700;}
td{padding:14px 20px;font-size:14px;color:#777;border-bottom:1px solid #111;vertical-align:middle;}
tbody tr:last-child td{border-bottom:none;}
tbody tr:hover td{background:#0f0f0f;}
.td-name{color:#fff;font-weight:600;}
.td-price{color:#E63946;font-weight:700;}
.avail-badge{display:inline-flex;align-items:center;font-size:11px;font-weight:700;padding:4px 12px;border-radius:999px;}
.btn-toggle{text-decoration:none;border-radius:8px;padding:6px 14px;font-size:12px;font-weight:700;transition:all 0.2s;display:inline-block;}
.add-card{background:#0d0d0d;border:1px solid #161616;border-radius:18px;padding:24px;position:sticky;top:20px;}
.add-title{font-size:16px;font-weight:800;color:#fff;margin-bottom:4px;}
.add-sub{font-size:13px;color:#444;margin-bottom:22px;}
.form-label{color:#555;font-size:13px;font-weight:500;margin-bottom:8px;display:block;}
.form-control,.form-select{background:#111;border:1px solid #1a1a1a;color:#fff;border-radius:10px;padding:11px 14px;font-size:14px;width:100%;font-family:'Inter',sans-serif;margin-bottom:14px;transition:border-color 0.2s;}
.form-control:focus,.form-select:focus{outline:none;border-color:#E63946;box-shadow:0 0 0 3px rgba(230,57,70,0.08);}
.form-control::placeholder{color:#333;}
.form-select option{background:#111;}
.btn-add{width:100%;background:#E63946;color:#fff;border:none;border-radius:10px;padding:13px;font-size:14px;font-weight:700;cursor:pointer;font-family:'Inter',sans-serif;transition:all 0.3s;}
.btn-add:hover{background:#c1121f;box-shadow:0 6px 20px rgba(230,57,70,0.35);}
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
  <a href="orders.php" class="nav-item"><span class="nav-icon">🧾</span> Orders</a>
  <a href="menu.php" class="nav-item active"><span class="nav-icon">🍽️</span> Menu Items</a>
  <div class="sidebar-footer">
    <a href="logout.php" class="logout-btn"><span>🚪</span> Logout</a>
  </div>
</div>

<div class="main">
  <div class="topbar">
    <div class="topbar-title">Menu Items 🍽️</div>
    <div class="topbar-sub">Add new items or toggle availability</div>
  </div>
  <div class="content">
    <?php if(isset($_GET['msg'])): ?>
    <div class="success-msg">✅ Changes saved successfully!</div>
    <?php endif; ?>
    <div class="page-grid">
      <div>
        <div class="section-title">All Menu Items</div>
        <div class="table-wrap">
          <table>
            <thead><tr><th>Item Name</th><th>Restaurant</th><th>Category</th><th>Price</th><th>Status</th><th>Action</th></tr></thead>
            <tbody>
            <?php while($item = mysqli_fetch_assoc($items)): $avail = $item['is_available']; ?>
            <tr>
              <td class="td-name"><?= htmlspecialchars($item['item_name']) ?></td>
              <td><?= htmlspecialchars($item['rname']) ?></td>
              <td><?= htmlspecialchars($item['category']) ?></td>
              <td class="td-price">₹<?= number_format($item['price'],2) ?></td>
              <td>
                <?php if($avail): ?>
                <span class="avail-badge" style="background:rgba(76,175,80,0.1);color:#4caf50;border:1px solid rgba(76,175,80,0.2);">● Available</span>
                <?php else: ?>
                <span class="avail-badge" style="background:rgba(230,57,70,0.1);color:#E63946;border:1px solid rgba(230,57,70,0.2);">● Unavailable</span>
                <?php endif; ?>
              </td>
              <td>
                <a href="menu.php?toggle=<?= $item['item_id'] ?>" class="btn-toggle"
                  style="background:<?= $avail?'rgba(230,57,70,0.1)':'rgba(76,175,80,0.1)' ?>;color:<?= $avail?'#E63946':'#4caf50' ?>;border:1px solid <?= $avail?'rgba(230,57,70,0.2)':'rgba(76,175,80,0.2)' ?>;">
                  <?= $avail ? 'Disable' : 'Enable' ?>
                </a>
              </td>
            </tr>
            <?php endwhile; ?>
            </tbody>
          </table>
        </div>
      </div>

      <div>
        <div class="add-card">
          <div class="add-title">Add New Item ✨</div>
          <div class="add-sub">Add a new dish to any restaurant</div>
          <form method="POST">
            <input type="hidden" name="add_item" value="1">
            <label class="form-label">Restaurant</label>
            <select name="restaurant_id" class="form-select">
              <?php mysqli_data_seek($restaurants,0); while($r=mysqli_fetch_assoc($restaurants)): ?>
              <option value="<?= $r['restaurant_id'] ?>"><?= htmlspecialchars($r['name']) ?></option>
              <?php endwhile; ?>
            </select>
            <label class="form-label">Item Name</label>
            <input type="text" name="item_name" class="form-control" placeholder="e.g. Paneer Butter Masala" required>
            <label class="form-label">Category</label>
            <input type="text" name="category" class="form-control" placeholder="e.g. Main Course" required>
            <label class="form-label">Price (₹)</label>
            <input type="number" name="price" class="form-control" placeholder="e.g. 250" step="0.01" required>
            <button type="submit" class="btn-add">+ Add Menu Item</button>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
</body>
</html>
