<?php 
require_once __DIR__ . '/includes/config.php';
session_start(); 
$base = BASE_URL;
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Bank Locker Management System</title>
<link rel="stylesheet" href="<?= $base ?>/css/style.css">
<style>
.landing{min-height:100vh;display:flex;flex-direction:column;align-items:center;justify-content:center;background:linear-gradient(rgba(15,40,69,0.75), rgba(26,58,92,0.85)), url('<?= $base ?>/images/hero.png') no-repeat center center/cover;color:white;text-align:center;padding:20px}
.landing-logo{font-size:80px;margin-bottom:20px;animation:float 3s ease-in-out infinite}
@keyframes float{0%,100%{transform:translateY(0)}50%{transform:translateY(-10px)}}
.landing h1{font-size:36px;font-weight:700;margin-bottom:10px}
.landing p{font-size:16px;opacity:.75;margin-bottom:40px;max-width:500px}
.landing-buttons{display:flex;gap:15px;flex-wrap:wrap;justify-content:center}
.btn-land{display:inline-flex;align-items:center;gap:8px;padding:14px 30px;border-radius:10px;font-size:15px;font-weight:600;text-decoration:none;transition:all .3s;border:none;cursor:pointer}
.btn-land:hover{transform:translateY(-3px);box-shadow:0 8px 25px rgba(0,0,0,.3)}
.btn-admin{background:#e8a020;color:#1a3a5c}
.btn-admin:hover{background:#f0b030}
.btn-cust{background:rgba(255,255,255,.15);color:white;border:2px solid rgba(255,255,255,.3)}
.btn-cust:hover{background:rgba(255,255,255,.25)}
.btn-subbanker{background:#0d9488;color:white;border:2px solid #14b8a6}
.btn-subbanker:hover{background:#14b8a6}
.btn-register{background:#2563eb;color:white;border:2px solid #3b82f6}
.btn-register:hover{background:#3b82f6}
.features{display:flex;gap:20px;margin-top:50px;flex-wrap:wrap;justify-content:center}
.feature{background:rgba(255,255,255,.08);border-radius:12px;padding:20px 25px;text-align:center;max-width:200px;transition:transform .2s}
.feature:hover{transform:translateY(-4px);background:rgba(255,255,255,.12)}
.feature .f-icon{font-size:32px;margin-bottom:10px}
.feature h3{font-size:14px;font-weight:600;margin-bottom:5px}
.feature p{font-size:12px;opacity:.65}
.modules{display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:20px;margin-top:50px;max-width:1000px;width:100%;padding:0 20px}
.module-card{background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.1);border-radius:16px;padding:25px;text-align:left;transition:all .3s}
.module-card:hover{background:rgba(255,255,255,.12);transform:translateY(-4px);box-shadow:0 10px 30px rgba(0,0,0,.2)}
.module-card .mod-icon{font-size:36px;margin-bottom:12px}
.module-card h3{font-size:16px;font-weight:700;margin-bottom:8px}
.module-card ul{list-style:none;font-size:11px;opacity:.7;line-height:1.8}
.module-card ul li::before{content:'✓ ';color:#5eead4}
</style>
</head>
<body>
<div class="landing">
  <div class="landing-logo">🏦</div>
  <h1>Bank Locker Management System</h1>
  <p>Secure, digital management of bank locker services for customers, sub bankers, and administrators</p>
  <div class="landing-buttons">
    <a href="<?= $base ?>/admin/login.php" class="btn-land btn-admin">🔐 Banker Login</a>
    <a href="<?= $base ?>/sub_banker/login.php" class="btn-land btn-subbanker">🏛️ Sub Banker Login</a>
    <a href="<?= $base ?>/customer/login.php" class="btn-land btn-cust">👤 Customer Login</a>
    <a href="<?= $base ?>/new_locker_request.php" class="btn-land btn-register">📩 New Locker Request</a>
  </div>


  <div class="features">
    <div class="feature"><div class="f-icon">🔒</div><h3>Secure Lockers</h3><p>Multiple sizes to fit your needs</p></div>
    <div class="feature"><div class="f-icon">📊</div><h3>Real-time Status</h3><p>Instant locker tracking</p></div>
    <div class="feature"><div class="f-icon">📈</div><h3>Reports</h3><p>Detailed analytics & reports</p></div>
    <div class="feature"><div class="f-icon">⚡</div><h3>Fast Service</h3><p>Quick allocation & management</p></div>
  </div>
  
  <p style="margin-top:40px;font-size:12px;opacity:.4">Koshys Institute of Management Studies | BCA Project by Rinto M Reji</p>
</div>
</body>
</html>
