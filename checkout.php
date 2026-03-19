<?php
include 'db.php';
if (!isset($_SESSION['customer_id'])) { header("Location: login.php"); exit(); }
if ($_SERVER["REQUEST_METHOD"] != "POST") { header("Location: index.php"); exit(); }
$customer_id = $_SESSION['customer_id'];
$restaurant_id = $_POST['restaurant_id'];
$cart_data = json_decode($_POST['cart_data'], true);
$total = $_POST['total'];
$customer = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM customers WHERE customer_id=$customer_id"));
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>BITE — Checkout</title>
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

/* PAGE */
.page-wrap{max-width:1000px;margin:0 auto;padding:40px 24px;flex:1;width:100%;}
.page-header{margin-bottom:32px;}
.page-title{font-size:30px;font-weight:900;color:#fff;margin-bottom:6px;}
.page-sub{font-size:14px;color:#555;}

/* STEPS */
.checkout-steps{display:flex;align-items:center;gap:0;margin-bottom:36px;}
.cs{display:flex;align-items:center;gap:8px;font-size:13px;font-weight:600;color:#333;}
.cs.active{color:#fff;}
.cs.done{color:#E63946;}
.cs-num{width:28px;height:28px;border-radius:50%;background:#1a1a1a;border:1px solid #2a2a2a;display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:700;flex-shrink:0;}
.cs.active .cs-num{background:#E63946;border-color:#E63946;color:#fff;}
.cs.done .cs-num{background:#E63946;border-color:#E63946;color:#fff;}
.cs-line{flex:1;height:1px;background:#1a1a1a;margin:0 12px;max-width:60px;}

/* GRID */
.checkout-grid{display:grid;grid-template-columns:1fr 380px;gap:24px;}
.section-title{font-size:11px;font-weight:700;color:#E63946;text-transform:uppercase;letter-spacing:0.15em;margin-bottom:18px;}

/* CARDS */
.card{background:#111;border:1px solid #1a1a1a;border-radius:20px;padding:26px;margin-bottom:18px;position:relative;overflow:hidden;}
.card::before{content:'';position:absolute;top:0;left:0;right:0;height:2px;background:linear-gradient(90deg,#E63946,transparent);}
.form-label{color:#666;font-size:13px;font-weight:500;margin-bottom:8px;display:block;}
.form-control{background:#0f0f0f;border:1px solid #1a1a1a;color:#fff;border-radius:12px;padding:13px 16px;font-size:14px;width:100%;font-family:'Inter',sans-serif;transition:all 0.3s;margin-bottom:16px;}
.form-control:focus{outline:none;border-color:#E63946;box-shadow:0 0 0 3px rgba(230,57,70,0.08);}
.form-control::placeholder{color:#333;}
.row-grid{display:grid;grid-template-columns:1fr 1fr;gap:14px;}

/* PAYMENT */
.pay-option{display:flex;align-items:center;gap:14px;background:#0f0f0f;border:1px solid #1a1a1a;border-radius:12px;padding:14px 18px;margin-bottom:10px;cursor:pointer;transition:all 0.2s;}
.pay-option:hover{border-color:#333;}
.pay-option input[type="radio"]{accent-color:#E63946;width:16px;height:16px;flex-shrink:0;}
.pay-option input[type="radio"]:checked~.pay-info .pay-name{color:#E63946;}
.pay-icon{font-size:22px;flex-shrink:0;}
.pay-name{font-size:14px;color:#fff;font-weight:600;margin-bottom:2px;}
.pay-sub{font-size:12px;color:#444;}
.pay-option:has(input:checked){border-color:#E63946;background:rgba(230,57,70,0.05);}

/* ORDER SUMMARY */
.summary-item{display:flex;align-items:center;justify-content:space-between;padding:13px 0;border-bottom:1px solid #1a1a1a;}
.summary-item:last-child{border-bottom:none;}
.si-left{display:flex;align-items:center;gap:10px;}
.si-dot{width:8px;height:8px;background:#E63946;border-radius:50%;flex-shrink:0;}
.si-name{font-size:14px;color:#ccc;font-weight:500;}
.si-qty{font-size:11px;color:#444;margin-top:2px;}
.si-price{font-size:14px;color:#E63946;font-weight:700;}
.totals-wrap{background:#0f0f0f;border-radius:12px;padding:18px;margin-top:8px;}
.total-row{display:flex;justify-content:space-between;font-size:13px;color:#555;margin-bottom:10px;}
.total-row.final{color:#fff;font-weight:800;font-size:17px;padding-top:12px;border-top:1px solid #1a1a1a;margin-top:4px;margin-bottom:0;}
.btn-place{width:100%;background:#E63946;color:#fff;border:none;border-radius:14px;padding:17px;font-size:16px;font-weight:800;cursor:pointer;font-family:'Inter',sans-serif;transition:all 0.3s;margin-top:16px;position:relative;overflow:hidden;}
.btn-place::before{content:'';position:absolute;inset:0;background:linear-gradient(135deg,rgba(255,255,255,0.1),transparent);pointer-events:none;}
.btn-place:hover{background:#c1121f;box-shadow:0 10px 30px rgba(230,57,70,0.4);transform:translateY(-2px);}
.btn-place:active{transform:translateY(0);}
.secure-note{display:flex;align-items:center;justify-content:center;gap:6px;margin-top:12px;font-size:12px;color:#333;}

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
    <div class="page-title">Checkout 🛒</div>
    <div class="page-sub">You're almost there! Review and place your order</div>
  </div>

  <div class="checkout-steps">
    <div class="cs done"><div class="cs-num">✓</div> Cart</div>
    <div class="cs-line"></div>
    <div class="cs active"><div class="cs-num">2</div> Checkout</div>
    <div class="cs-line"></div>
    <div class="cs"><div class="cs-num">3</div> Track Order</div>
  </div>

  <form method="POST" action="place_order.php">
  <input type="hidden" name="restaurant_id" value="<?= $restaurant_id ?>">
  <input type="hidden" name="cart_data" value="<?= htmlspecialchars($_POST['cart_data']) ?>">
  <input type="hidden" name="total" value="<?= $total ?>">

  <div class="checkout-grid">
    <div>
      <div class="card">
        <div class="section-title">Delivery Details</div>
        <label class="form-label">Full name</label>
        <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($customer['name']) ?>" required>
        <div class="row-grid">
          <div>
            <label class="form-label">Phone number</label>
            <input type="text" name="phone" class="form-control" value="<?= htmlspecialchars($customer['phone']) ?>" required>
          </div>
          <div>
            <label class="form-label">City</label>
            <input type="text" class="form-control" value="Nagpur" readonly style="color:#555;">
          </div>
        </div>
        <label class="form-label">Delivery address</label>
        <textarea name="address" class="form-control" rows="3" required><?= htmlspecialchars($customer['address']) ?></textarea>
      </div>

      <div class="card">
        <div class="section-title">Payment Method</div>
        <label class="pay-option">
          <input type="radio" name="payment" value="Cash on Delivery" checked>
          <span class="pay-icon">💵</span>
          <div class="pay-info"><div class="pay-name">Cash on Delivery</div><div class="pay-sub">Pay when your order arrives</div></div>
        </label>
        <label class="pay-option">
          <input type="radio" name="payment" value="UPI">
          <span class="pay-icon">📱</span>
          <div class="pay-info"><div class="pay-name">UPI / GPay / PhonePe</div><div class="pay-sub">Instant payment via UPI</div></div>
        </label>
        <label class="pay-option">
          <input type="radio" name="payment" value="Card">
          <span class="pay-icon">💳</span>
          <div class="pay-info"><div class="pay-name">Credit / Debit Card</div><div class="pay-sub">All major cards accepted</div></div>
        </label>
      </div>
    </div>

    <div>
      <div class="card">
        <div class="section-title">Order Summary</div>
        <?php
        $subtotal = 0;
        foreach ($cart_data as $item_id => $item):
          $menu = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM menu_items WHERE item_id=$item_id"));
          $item_total = $menu['price'] * $item['qty'];
          $subtotal += $item_total;
        ?>
        <div class="summary-item">
          <div class="si-left">
            <div class="si-dot"></div>
            <div>
              <div class="si-name"><?= htmlspecialchars($menu['item_name']) ?></div>
              <div class="si-qty">x<?= $item['qty'] ?></div>
            </div>
          </div>
          <div class="si-price">₹<?= number_format($item_total,2) ?></div>
        </div>
        <?php endforeach; ?>
        <div class="totals-wrap">
          <div class="total-row"><span>Subtotal</span><span>₹<?= number_format($subtotal,2) ?></span></div>
          <div class="total-row"><span>Delivery fee</span><span>₹30.00</span></div>
          <div class="total-row final"><span>Total</span><span>₹<?= number_format($subtotal+30,2) ?></span></div>
        </div>
        <button type="submit" class="btn-place">🚀 Place Order Now</button>
        <div class="secure-note">🔒 Secure checkout — your data is safe</div>
      </div>
    </div>
  </div>
  </form>
</div>

<div class="footer">&copy; 2026 BITE — Best Instant Takeaway Experience</div>
</body>
</html>