<?php
include 'db.php';
if (!isset($_SESSION['customer_id'])) {
    header("Location: login.php");
    exit();
}
$name = $_SESSION['name'];
$restaurants = mysqli_query($conn, "SELECT * FROM restaurants");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>BITE — Best Instant Takeaway Experience</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
<style>
*{margin:0;padding:0;box-sizing:border-box;}
body{background:#0a0a0a;color:#fff;font-family:'Inter',sans-serif;min-height:100vh;display:flex;flex-direction:column;overflow-x:hidden;}

/* NAVBAR */
.navbar{background:rgba(10,10,10,0.95);backdrop-filter:blur(20px);border-bottom:1px solid #1a1a1a;padding:16px 40px;display:flex;align-items:center;justify-content:space-between;position:sticky;top:0;z-index:100;}
.logo{font-size:26px;font-weight:900;color:#E63946;letter-spacing:4px;text-decoration:none;position:relative;}
.logo::after{content:'';position:absolute;bottom:-2px;left:0;width:100%;height:2px;background:#E63946;border-radius:2px;box-shadow:0 0 10px #E63946;}
.nav-right{display:flex;align-items:center;gap:24px;}
.nav-link-custom{color:#888;text-decoration:none;font-size:14px;font-weight:500;transition:color 0.2s;}
.nav-link-custom:hover{color:#fff;}
.nav-user{color:#fff;font-size:14px;font-weight:600;background:#1a1a1a;padding:8px 16px;border-radius:999px;border:1px solid #2a2a2a;}
.btn-logout{background:transparent;border:1px solid #E63946;color:#E63946;padding:8px 18px;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;text-decoration:none;transition:all 0.2s;}
.btn-logout:hover{background:#E63946;color:#fff;box-shadow:0 0 20px rgba(230,57,70,0.4);}

/* HERO */
.hero{position:relative;padding:80px 40px 60px;text-align:center;overflow:hidden;}
.hero-bg{position:absolute;inset:0;background:radial-gradient(ellipse at 50% 0%,rgba(230,57,70,0.15) 0%,transparent 70%);pointer-events:none;}
.hero-bg2{position:absolute;top:-100px;left:50%;transform:translateX(-50%);width:600px;height:600px;background:radial-gradient(circle,rgba(230,57,70,0.08) 0%,transparent 70%);pointer-events:none;animation:pulse 4s ease-in-out infinite;}
@keyframes pulse{0%,100%{transform:translateX(-50%) scale(1);}50%{transform:translateX(-50%) scale(1.1);}}
.hero-tag{display:inline-flex;align-items:center;gap:8px;background:#1a1a1a;border:1px solid #2a2a2a;color:#E63946;font-size:12px;padding:6px 18px;border-radius:999px;margin-bottom:24px;font-weight:600;letter-spacing:0.05em;animation:fadeDown 0.6s ease;}
.hero-title{font-size:56px;font-weight:900;color:#fff;line-height:1.1;margin-bottom:16px;animation:fadeUp 0.6s ease 0.1s both;}
.hero-title span{color:#E63946;text-shadow:0 0 40px rgba(230,57,70,0.5);}
.hero-sub{font-size:16px;color:#666;margin-bottom:40px;animation:fadeUp 0.6s ease 0.2s both;}
@keyframes fadeUp{from{opacity:0;transform:translateY(20px);}to{opacity:1;transform:translateY(0);}}
@keyframes fadeDown{from{opacity:0;transform:translateY(-10px);}to{opacity:1;transform:translateY(0);}}
.search-wrap{max-width:560px;margin:0 auto;position:relative;animation:fadeUp 0.6s ease 0.3s both;}
.search-input{width:100%;background:#1a1a1a;border:1px solid #2a2a2a;border-radius:14px;padding:16px 24px 16px 52px;color:#fff;font-size:15px;font-family:'Inter',sans-serif;transition:all 0.3s;}
.search-input:focus{outline:none;border-color:#E63946;box-shadow:0 0 0 3px rgba(230,57,70,0.1);}
.search-input::placeholder{color:#444;}
.search-icon{position:absolute;left:18px;top:50%;transform:translateY(-50%);color:#E63946;font-size:20px;}

/* FLOATING FOOD */
.floating-foods{position:absolute;inset:0;pointer-events:none;overflow:hidden;}
.float-item{position:absolute;font-size:32px;animation:float linear infinite;opacity:0.08;}
@keyframes float{0%{transform:translateY(100vh) rotate(0deg);opacity:0;}10%{opacity:0.08;}90%{opacity:0.08;}100%{transform:translateY(-100px) rotate(360deg);opacity:0;}}

/* CATEGORIES */
.cats-wrap{padding:28px 40px 0;display:flex;gap:10px;overflow-x:auto;scrollbar-width:none;}
.cats-wrap::-webkit-scrollbar{display:none;}
.cat-btn{background:#1a1a1a;border:1px solid #2a2a2a;color:#888;font-size:13px;font-weight:600;padding:9px 22px;border-radius:999px;white-space:nowrap;cursor:pointer;transition:all 0.2s;font-family:'Inter',sans-serif;}
.cat-btn:hover{border-color:#E63946;color:#E63946;}
.cat-btn.active{background:#E63946;color:#fff;border-color:#E63946;box-shadow:0 4px 20px rgba(230,57,70,0.3);}

/* STATS */
.stats-row{display:grid;grid-template-columns:repeat(3,1fr);gap:16px;padding:28px 40px;}
.stat-card{background:#111;border:1px solid #1a1a1a;border-radius:16px;padding:20px;text-align:center;transition:all 0.3s;position:relative;overflow:hidden;}
.stat-card::before{content:'';position:absolute;inset:0;background:radial-gradient(circle at 50% 0%,rgba(230,57,70,0.05),transparent 70%);pointer-events:none;}
.stat-card:hover{border-color:#E63946;transform:translateY(-2px);box-shadow:0 8px 30px rgba(230,57,70,0.1);}
.stat-num{font-size:32px;font-weight:900;color:#E63946;margin-bottom:4px;}
.stat-label{font-size:12px;color:#555;font-weight:500;text-transform:uppercase;letter-spacing:0.08em;}

/* RESTAURANTS */
.section-wrap{padding:8px 40px 40px;}
.section-header{display:flex;align-items:center;justify-content:space-between;margin-bottom:24px;}
.section-title{font-size:11px;font-weight:700;color:#E63946;text-transform:uppercase;letter-spacing:0.15em;}
.rest-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:20px;}
.rest-card{background:#111;border:1px solid #1a1a1a;border-radius:18px;overflow:hidden;cursor:pointer;transition:all 0.3s;text-decoration:none;display:block;position:relative;}
.rest-card:hover{border-color:#E63946;transform:translateY(-6px);box-shadow:0 20px 40px rgba(0,0,0,0.5),0 0 0 1px rgba(230,57,70,0.2);}
.rest-img{height:160px;background:linear-gradient(135deg,#1a1a1a,#111);display:flex;align-items:center;justify-content:center;font-size:64px;position:relative;overflow:hidden;}
.rest-img::after{content:'';position:absolute;inset:0;background:linear-gradient(to bottom,transparent 50%,rgba(0,0,0,0.5));pointer-events:none;}
.rest-img-emoji{transition:transform 0.3s;display:block;}
.rest-card:hover .rest-img-emoji{transform:scale(1.15);}
.rest-body{padding:16px 18px;}
.rest-name{font-size:16px;font-weight:700;color:#fff;margin-bottom:4px;}
.rest-cuisine{font-size:12px;color:#555;margin-bottom:12px;font-weight:500;}
.rest-footer{display:flex;align-items:center;justify-content:space-between;}
.rest-rating{font-size:13px;color:#E63946;font-weight:700;display:flex;align-items:center;gap:4px;}
.rest-time{font-size:12px;color:#555;background:#1a1a1a;padding:4px 12px;border-radius:999px;border:1px solid #222;}
.rest-badge{position:absolute;top:12px;left:12px;background:#E63946;color:#fff;font-size:10px;font-weight:700;padding:3px 10px;border-radius:999px;z-index:1;box-shadow:0 2px 10px rgba(230,57,70,0.4);}
.rest-open{position:absolute;top:12px;right:12px;background:rgba(0,0,0,0.7);color:#4caf50;font-size:10px;font-weight:600;padding:3px 10px;border-radius:999px;z-index:1;border:1px solid rgba(76,175,80,0.3);}

/* ORDER CTA */
.cta-wrap{margin:0 40px 40px;background:linear-gradient(135deg,#1a0a0a,#1a1a1a);border:1px solid #2a1a1a;border-radius:20px;padding:32px 40px;display:flex;align-items:center;justify-content:space-between;position:relative;overflow:hidden;}
.cta-wrap::before{content:'';position:absolute;right:-60px;top:-60px;width:200px;height:200px;background:radial-gradient(circle,rgba(230,57,70,0.15),transparent 70%);}
.cta-title{font-size:22px;font-weight:800;color:#fff;margin-bottom:6px;}
.cta-sub{font-size:14px;color:#666;}
.btn-cta{background:#E63946;color:#fff;text-decoration:none;border-radius:10px;padding:12px 28px;font-size:14px;font-weight:700;transition:all 0.2s;white-space:nowrap;}
.btn-cta:hover{background:#c1121f;color:#fff;box-shadow:0 8px 25px rgba(230,57,70,0.4);transform:translateY(-1px);}

.footer{background:#050505;border-top:1px solid #111;text-align:center;padding:24px;font-size:12px;color:#333;margin-top:auto;}
.footer span{color:#E63946;}
</style>
</head>
<body>

<nav class="navbar">
  <a href="index.php" class="logo">BITE</a>
  <div class="nav-right">
    <a href="my_orders.php" class="nav-link-custom">My Orders</a>
    <span class="nav-user">👋 <?= htmlspecialchars($name) ?></span>
    <a href="logout.php" class="btn-logout">Logout</a>
  </div>
</nav>

<div class="hero">
  <div class="hero-bg"></div>
  <div class="hero-bg2"></div>
  <div class="floating-foods">
    <span class="float-item" style="left:5%;animation-duration:12s;animation-delay:0s;">🍕</span>
    <span class="float-item" style="left:15%;animation-duration:15s;animation-delay:2s;">🍔</span>
    <span class="float-item" style="left:25%;animation-duration:11s;animation-delay:4s;">🍜</span>
    <span class="float-item" style="left:40%;animation-duration:14s;animation-delay:1s;">🌮</span>
    <span class="float-item" style="left:55%;animation-duration:13s;animation-delay:3s;">🍣</span>
    <span class="float-item" style="left:70%;animation-duration:16s;animation-delay:5s;">🍛</span>
    <span class="float-item" style="left:82%;animation-duration:10s;animation-delay:2s;">🥗</span>
    <span class="float-item" style="left:92%;animation-duration:14s;animation-delay:6s;">🍱</span>
  </div>
  <div class="hero-tag">🔥 Best Instant Takeaway Experience</div>
  <div class="hero-title">Hungry? Let's <span>BITE</span> it.</div>
  <div class="hero-sub">Order from the best restaurants in Nagpur — fast, fresh & delivered hot</div>
  <div class="search-wrap">
    <span class="search-icon">🔍</span>
    <input class="search-input" placeholder="Search restaurants or dishes..." id="searchInput" onkeyup="searchRestaurants()">
  </div>
</div>

<div class="cats-wrap">
  <button class="cat-btn active" onclick="filterCat('All',this)">🍽️ All</button>
  <button class="cat-btn" onclick="filterCat('Indian',this)">🍛 Indian</button>
  <button class="cat-btn" onclick="filterCat('Italian',this)">🍕 Italian</button>
  <button class="cat-btn" onclick="filterCat('Fast Food',this)">🍔 Fast Food</button>
  <button class="cat-btn" onclick="filterCat('Chinese',this)">🍜 Chinese</button>
  <button class="cat-btn" onclick="filterCat('Desserts',this)">🍰 Desserts</button>
</div>

<div class="stats-row">
  <div class="stat-card">
    <div class="stat-num"><?= mysqli_num_rows(mysqli_query($conn,"SELECT * FROM restaurants")) ?>+</div>
    <div class="stat-label">Restaurants</div>
  </div>
  <div class="stat-card">
    <div class="stat-num"><?= mysqli_num_rows(mysqli_query($conn,"SELECT * FROM menu_items")) ?>+</div>
    <div class="stat-label">Menu Items</div>
  </div>
  <div class="stat-card">
    <div class="stat-num"><?= mysqli_num_rows(mysqli_query($conn,"SELECT * FROM customers")) ?>+</div>
    <div class="stat-label">Happy Customers</div>
  </div>
</div>

<div class="section-wrap">
  <div class="section-header">
    <div class="section-title">🏪 Top Restaurants Near You</div>
  </div>
  <div class="rest-grid" id="restGrid">
    <?php
    $emojis = ['🍛','🍕','🍔','🍜','🌮','🍣','🥗','🍱'];
    $i = 0;
    mysqli_data_seek($restaurants, 0);
    while ($r = mysqli_fetch_assoc($restaurants)):
      $emoji = $emojis[$i % count($emojis)];
      $i++;
    ?>
    <a href="menu.php?id=<?= $r['restaurant_id'] ?>" class="rest-card" data-cuisine="<?= $r['cuisine_type'] ?>">
      <?php if($r['rating'] >= 4.4): ?>
      <div class="rest-badge">⭐ Top Rated</div>
      <?php endif; ?>
      <div class="rest-open">● Open Now</div>
      <div class="rest-img">
        <span class="rest-img-emoji"><?= $emoji ?></span>
      </div>
      <div class="rest-body">
        <div class="rest-name"><?= htmlspecialchars($r['name']) ?></div>
        <div class="rest-cuisine"><?= htmlspecialchars($r['cuisine_type']) ?> · Nagpur</div>
        <div class="rest-footer">
          <span class="rest-rating">★ <?= $r['rating'] ?></span>
          <span class="rest-time">25–35 min</span>
        </div>
      </div>
    </a>
    <?php endwhile; ?>
  </div>
</div>

<div class="cta-wrap">
  <div>
    <div class="cta-title">🚴 Lightning fast delivery</div>
    <div class="cta-sub">Your food arrives hot in 30 minutes or less — guaranteed</div>
  </div>
  <a href="#restGrid" class="btn-cta">Order Now →</a>
</div>

<div class="footer">
  &copy; 2026 <span>BITE</span> — Best Instant Takeaway Experience · Made with ❤️ in Nagpur
</div>

<script>
function searchRestaurants() {
  var input = document.getElementById('searchInput').value.toLowerCase();
  document.querySelectorAll('.rest-card').forEach(function(card) {
    var name = card.querySelector('.rest-name').textContent.toLowerCase();
    var cuisine = card.querySelector('.rest-cuisine').textContent.toLowerCase();
    card.style.display = (name.includes(input) || cuisine.includes(input)) ? 'block' : 'none';
  });
}
function filterCat(cat, btn) {
  document.querySelectorAll('.cat-btn').forEach(b => b.classList.remove('active'));
  btn.classList.add('active');
  document.querySelectorAll('.rest-card').forEach(function(card) {
    card.style.display = (cat === 'All' || card.dataset.cuisine === cat) ? 'block' : 'none';
  });
}
// Animate stats counting up
document.querySelectorAll('.stat-num').forEach(function(el) {
  var target = parseInt(el.textContent);
  var count = 0;
  var step = Math.ceil(target / 30);
  var timer = setInterval(function() {
    count += step;
    if (count >= target) { count = target; clearInterval(timer); }
    el.textContent = count + '+';
  }, 40);
});
</script>
</body>
</html>