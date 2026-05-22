<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/functions.php';
if (session_status() === PHP_SESSION_NONE) session_start();
requireCustomerLogin();
$page_title = isset($page_title) ? $page_title . ' | Customer Portal' : 'Customer Portal';
$base = BASE_URL;
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= htmlspecialchars($page_title) ?></title>
<link rel="stylesheet" href="<?= $base ?>/css/style.css">
</head>
<body>
<div class="wrapper">
<aside class="sidebar sidebar-customer">
  <div class="sidebar-header">
    <div class="bank-icon">🏦</div>
    <h2>SecureBank</h2>
    <p>Customer Portal</p>
  </div>
  <nav class="sidebar-nav">
    <div class="nav-section-label">My Account</div>
    <a href="<?= $base ?>/customer/dashboard.php" <?= (basename($_SERVER['PHP_SELF'])=='dashboard.php')?'class="active"':'' ?>>
      <span class="icon">📊</span> My Dashboard
    </a>
    <a href="<?= $base ?>/customer/my_locker.php" <?= (basename($_SERVER['PHP_SELF'])=='my_locker.php')?'class="active"':'' ?>>
      <span class="icon">🔒</span> My Locker
    </a>
    <a href="<?= $base ?>/customer/access_locker.php" <?= (basename($_SERVER['PHP_SELF'])=='access_locker.php')?'class="active"':'' ?>>
      <span class="icon">🔐</span> Slot Booking
    </a>
    <a href="<?= $base ?>/customer/access_log.php" <?= (basename($_SERVER['PHP_SELF'])=='access_log.php')?'class="active"':'' ?>>
      <span class="icon">📝</span> Access History
    </a>
    <div class="nav-section-label">Services</div>
    <a href="<?= $base ?>/customer/request_locker.php" <?= (basename($_SERVER['PHP_SELF'])=='request_locker.php')?'class="active"':'' ?>>
      <span class="icon">📩</span> Request New Locker
    </a>
    <a href="<?= $base ?>/customer/surrender_request.php" <?= (basename($_SERVER['PHP_SELF'])=='surrender_request.php')?'class="active"':'' ?>>
      <span class="icon">📥</span> Surrender Locker
    </a>
    <a href="<?= $base ?>/customer/contact.php" <?= (basename($_SERVER['PHP_SELF'])=='contact.php')?'class="active"':'' ?>>
      <span class="icon">📞</span> Contact Bank
    </a>
    <div class="nav-section-label">Account</div>
    <a href="<?= $base ?>/customer/profile.php" <?= (basename($_SERVER['PHP_SELF'])=='profile.php')?'class="active"':'' ?>>
      <span class="icon">👤</span> My Profile
    </a>
    <a href="<?= $base ?>/customer/logout.php"><span class="icon">🚪</span> Logout</a>
  </nav>
</aside>
<div class="main-content">
<div class="topbar topbar-customer">
  <h1><?= $page_title ?></h1>
  <div class="topbar-right">
    <div class="user-info">
      <div class="user-avatar user-avatar-customer"><?= strtoupper(substr($_SESSION['customer_name']??'C',0,1)) ?></div>
      <div>
        <div style="font-weight:600;color:#1e293b;"><?= htmlspecialchars($_SESSION['customer_name']??'Customer') ?></div>
        <div style="font-size:11px;"><?= htmlspecialchars($_SESSION['customer_cid']??'') ?></div>
      </div>
    </div>
  </div>
</div>
<div class="content">
