<?php
include 'db.php';
if (!isset($_SESSION['customer_id'])) { header("Location: login.php"); exit(); }
$restaurant_id = $_GET['id'];
$restaurant = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM restaurants WHERE restaurant_id=$restaurant_id"));
$menu_items = mysqli_query($conn, "SELECT * FROM menu_items WHERE restaurant_id=$restaurant_id AND is_available=1");
if (!$restaurant) { header("Location: index.php"); exit(); }
$emojis_map = ['Main Course'=>'🍛','Pizza'=>'🍕','Burger'=>'🍔','Pasta'=>'🍝','Bread'=>'🫓','Snacks'=>'🍟','Dessert'=>'🍰','Drinks'=>'🥤'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>BITE — <?= htmlspecialchars($restaurant['name']) ?></title>
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

/* RESTAURANT HERO */
.rest-hero{background:linear-gradient(135deg,#111,#0f0f0f);border-bottom:1px solid #1a1a1a;padding:36px 40px;position:relative;overflow:hidden;}
.rest-hero::before{content:'';position:absolute;top:0;left:0;right:0;bottom:0;background:radial-gradient(ellipse at 0% 50%,rgba(230,57,70,0.08),transparent 60%);pointer-events:none;}
.back-btn{display:inline-flex;align-items:center;gap:6px;color:#555;text-decoration:none;font-size:13px;font-weight:500;margin-bottom:20px;transition:color 0.2s;}
.back-btn:hover{color:#E63946;}
.hero-content{display:flex;align-items:center;gap:24px;}
.rest-avatar{width:80px;height:80px;background:#1a1a1a;border:1px solid #222;border-radius:18px;display:flex;align-items:center;justify-content:center;font-size:40px;flex-shrink:0;}
.rest-info h1{font-size:28px;font-weight:900;color:#fff;margin-bottom:6px;}
.rest-meta{display:flex;align-items:center;gap:12px;flex-wrap:wrap;margin-bottom:12px;}
.meta-tag{display:inline-flex;align-items:center;gap:6px;background:#1a1a1a;border:1px solid #222;color:#888;font-size:12px;font-weight:500;padding:5px 12px;border-radius:999px;}
.rating-tag{background:rgba(230,57,70,0.1);border-color:rgba(230,57,70,0.2);color:#E63946;}

/* LAYOUT */
.content{display:grid;grid-template-columns:1fr 360px;gap:24px;padding:28px 40px;flex:1;align-items:start;}

/* MENU */
.section-title{font-size:11px;font-weight:700;color:#E63946;text-transform:uppercase;letter-spacing:0.15em;margin-bottom:20px;}
.menu-list{display:flex;flex-direction:column;gap:12px;}
.menu-card{background:#111;border:1px solid #1a1a1a;border-radius:16px;padding:20px;display:flex;align-items:center;justify-content:space-between;gap:16px;transition:all 0.25s;position:relative;overflow:hidden;}
.menu-card:hover{border-color:#2a2a2a;background:#141414;transform:translateX(3px);}
.menu-card::before{content:'';position:absolute;left:0;top:0;bottom:0;width:3px;background:#E63946;border-radius:0 3px 3px 0;opacity:0;transition:opacity 0.25s;}
.menu-card:hover::before{opacity:1;}
.menu-emoji{width:52px;height:52px;background:#1a1a1a;border:1px solid #222;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:24px;flex-shrink:0;}
.menu-left{display:flex;align-items:center;gap:14px;flex:1;}
.menu-info{}
.menu-cat{font-size:10px;color:#E63946;font-weight:700;text-transform:uppercase;letter-spacing:0.1em;margin-bottom:4px;}
.menu-name{font-size:15px;font-weight:700;color:#fff;margin-bottom:6px;}
.menu-price{font-size:20px;font-weight:900;color:#fff;}
.menu-price-sub{font-size:12px;color:#444;font-weight:400;}
.btn-add{background:#E63946;color:#fff;border:none;border-radius:10px;padding:10px 22px;font-size:13px;font-weight:700;cursor:pointer;white-space:nowrap;transition:all 0.2s;font-family:'Inter',sans-serif;}
.btn-add:hover{background:#c1121f;box-shadow:0 4px 15px rgba(230,57,70,0.3);transform:translateY(-1px);}
.btn-added{background:#1a1a1a;color:#E63946;border:1px solid #E63946;border-radius:10px;padding:10px 22px;font-size:13px;font-weight:700;cursor:pointer;white-space:nowrap;font-family:'Inter',sans-serif;}
.empty-state{text-align:center;padding:60px 20px;color:#333;}
.empty-state div{font-size:48px;margin-bottom:12px;}

/* CART */
.cart-sticky{position:sticky;top:88px;}
.cart-box{background:#111;border:1px solid #1a1a1a;border-radius:20px;overflow:hidden;}
.cart-header{padding:20px 22px;border-bottom:1px solid #1a1a1a;display:flex;align-items:center;justify-content:space-between;}
.cart-title{font-size:16px;font-weight:800;color:#fff;}
.cart-count{background:#E63946;color:#fff;font-size:11px;font-weight:700;padding:2px 8px;border-radius:999px;min-width:20px;text-align:center;}
.cart-body{padding:20px 22px;}
.cart-empty{text-align:center;padding:32px 0;color:#333;}
.cart-empty-icon{font-size:36px;margin-bottom:8px;}
.cart-empty-text{font-size:13px;color:#333;}
.cart-item{display:flex;align-items:center;justify-content:space-between;padding:12px 0;border-bottom:1px solid #1a1a1a;}
.cart-item:last-child{border-bottom:none;}
.cart-item-name{font-size:13px;color:#ccc;font-weight:500;flex:1;}
.cart-item-qty{display:flex;align-items:center;gap:8px;margin:0 12px;}
.qty-btn{width:26px;height:26px;border-radius:8px;background:#1a1a1a;border:1px solid #222;color:#fff;font-size:15px;cursor:pointer;display:flex;align-items:center;justify-content:center;transition:all 0.2s;font-family:'Inter',sans-serif;}
.qty-btn:hover{border-color:#E63946;color:#E63946;background:#1f0a0a;}
.qty-num{font-size:14px;color:#fff;font-weight:600;min-width:18px;text-align:center;}
.cart-item-price{font-size:13px;color:#E63946;font-weight:700;}
.cart-footer{padding:0 22px 22px;}
.cart-totals{background:#0f0f0f;border-radius:12px;padding:16px;margin-bottom:16px;}
.total-row{display:flex;justify-content:space-between;font-size:13px;color:#555;margin-bottom:8px;}
.total-row:last-child{margin-bottom:0;color:#fff;font-weight:800;font-size:15px;padding-top:10px;border-top:1px solid #1a1a1a;margin-top:4px;}
.btn-checkout{width:100%;background:#E63946;color:#fff;border:none;border-radius:12px;padding:15px;font-size:15px;font-weight:700;cursor:pointer;font-family:'Inter',sans-serif;transition:all 0.3s;position:relative;overflow:hidden;}
.btn-checkout::before{content:'';position:absolute;inset:0;background:linear-gradient(135deg,rgba(255,255,255,0.1),transparent);pointer-events:none;}
.btn-checkout:hover{background:#c1121f;box-shadow:0 8px 25px rgba(230,57,70,0.4);transform:translateY(-1px);}
.btn-checkout:disabled{background:#1a1a1a;color:#333;cursor:not-allowed;transform:none;box-shadow:none;}
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

<div class="rest-hero">
  <a href="index.php" class="back-btn">← Back to restaurants</a>
  <div class="hero-content">
    <div class="rest-avatar">🍽️</div>
    <div class="rest-info">
      <h1><?= htmlspecialchars($restaurant['name']) ?></h1>
      <div class="rest-meta">
        <span class="meta-tag">🍴 <?= htmlspecialchars($restaurant['cuisine_type']) ?></span>
        <span class="meta-tag">📍 Nagpur</span>
        <span class="meta-tag">🕐 25–35 min</span>
        <span class="meta-tag rating-tag">★ <?= $restaurant['rating'] ?> Rating</span>
      </div>
    </div>
  </div>
</div>

<div class="content">
  <div>
    <div class="section-title">Menu Items</div>
    <div class="menu-list">
      <?php
      $items = [];
      while ($item = mysqli_fetch_assoc($menu_items)):
        $items[] = $item;
        $emoji = $emojis_map[$item['category']] ?? '🍽️';
      ?>
      <div class="menu-card" id="card-<?= $item['item_id'] ?>">
        <div class="menu-left">
          <div class="menu-emoji"><?= $emoji ?></div>
          <div class="menu-info">
            <div class="menu-cat"><?= htmlspecialchars($item['category']) ?></div>
            <div class="menu-name"><?= htmlspecialchars($item['item_name']) ?></div>
            <div class="menu-price">₹<?= number_format($item['price'],2) ?> <span class="menu-price-sub">per serving</span></div>
          </div>
        </div>
        <button class="btn-add" id="btn-<?= $item['item_id'] ?>"
          onclick="addToCart(<?= $item['item_id'] ?>,'<?= addslashes($item['item_name']) ?>',<?= $item['price'] ?>)">
          + Add
        </button>
      </div>
      <?php endwhile; ?>
      <?php if(empty($items)): ?>
      <div class="empty-state"><div>🍽️</div><p>No items available right now</p></div>
      <?php endif; ?>
    </div>
  </div>

  <div class="cart-sticky">
    <div class="cart-box">
      <div class="cart-header">
        <div class="cart-title">Your Cart 🛒</div>
        <span class="cart-count" id="cart-count">0</span>
      </div>
      <div class="cart-body">
        <div id="cart-empty" class="cart-empty">
          <div class="cart-empty-icon">🛒</div>
          <div class="cart-empty-text">Add items to get started!</div>
        </div>
        <div id="cart-items" style="display:none;"></div>
      </div>
      <div class="cart-footer" id="cart-footer" style="display:none;">
        <div class="cart-totals">
          <div class="total-row"><span>Subtotal</span><span id="subtotal">₹0</span></div>
          <div class="total-row"><span>Delivery fee</span><span>₹30</span></div>
          <div class="total-row"><span>Total</span><span id="total-amount">₹30</span></div>
        </div>
        <form method="POST" action="checkout.php">
          <input type="hidden" name="restaurant_id" value="<?= $restaurant_id ?>">
          <input type="hidden" name="cart_data" id="cart_data">
          <input type="hidden" name="total" id="form_total">
          <button type="submit" class="btn-checkout">Proceed to Checkout →</button>
        </form>
      </div>
    </div>
  </div>
</div>

<div class="footer">&copy; 2026 BITE — Best Instant Takeaway Experience</div>

<script>
let cart = {}, prices = {};
function addToCart(id, name, price) {
  prices[id] = price;
  if (cart[id]) { cart[id].qty += 1; }
  else {
    cart[id] = {name: name, qty: 1};
    document.getElementById('btn-'+id).outerHTML = '<button class="btn-added" id="btn-'+id+'" onclick="addToCart('+id+',\''+name.replace(/'/g,"\\'")+"',"+price+')">Added ✓</button>';
  }
  updateCart();
}
function changeQty(id, delta) {
  if (!cart[id]) return;
  cart[id].qty += delta;
  if (cart[id].qty <= 0) { delete cart[id]; location.reload(); return; }
  updateCart();
}
function updateCart() {
  let html = '', subtotal = 0, count = 0;
  for (let id in cart) {
    let item = cart[id], itemTotal = item.qty * prices[id];
    subtotal += itemTotal; count += item.qty;
    html += '<div class="cart-item">';
    html += '<div class="cart-item-name">'+item.name+'</div>';
    html += '<div class="cart-item-qty"><button class="qty-btn" onclick="changeQty('+id+',-1)">−</button><span class="qty-num">'+item.qty+'</span><button class="qty-btn" onclick="changeQty('+id+',1)">+</button></div>';
    html += '<div class="cart-item-price">₹'+itemTotal.toFixed(2)+'</div></div>';
  }
  if (count === 0) {
    document.getElementById('cart-empty').style.display='block';
    document.getElementById('cart-items').style.display='none';
    document.getElementById('cart-footer').style.display='none';
    document.getElementById('cart-count').textContent='0';
  } else {
    document.getElementById('cart-empty').style.display='none';
    document.getElementById('cart-items').style.display='block';
    document.getElementById('cart-items').innerHTML=html;
    document.getElementById('cart-footer').style.display='block';
    document.getElementById('cart-count').textContent=count;
    let total = subtotal + 30;
    document.getElementById('subtotal').textContent='₹'+subtotal.toFixed(2);
    document.getElementById('total-amount').textContent='₹'+total.toFixed(2);
    document.getElementById('cart_data').value=JSON.stringify(cart);
    document.getElementById('form_total').value=total.toFixed(2);
  }
}
</script>
</body>
</html>