<?php
require_once '../includes/config.php';
session_start();
$base = BASE_URL;
if (isset($_SESSION['customer_id'])) { header("Location: dashboard.php"); exit(); }
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $conn = getDBConnection();
    $email = trim($_POST['email'] ?? '');
    $pass  = $_POST['password'] ?? '';
    if ($email && $pass) {
        $stmt = $conn->prepare("SELECT * FROM customers WHERE email=? AND status='active'");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $cust = $stmt->get_result()->fetch_assoc();
        if ($cust && password_verify($pass, $cust['password'])) {
            $_SESSION['customer_id']  = $cust['id'];
            $_SESSION['customer_name']= $cust['full_name'];
            $_SESSION['customer_cid'] = $cust['customer_id'];
            header("Location: dashboard.php"); exit();
        } else { $error = "Invalid email/password or account inactive."; }
    } else { $error = "Please fill all fields."; }
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Customer Login | Bank Locker</title>
<link rel="stylesheet" href="<?= $base ?>/css/style.css">
</head>
<body>
<div class="login-page">
  <div class="login-box">
    <div class="login-logo">
      <div class="icon">👤</div>
      <h1>Customer Login</h1>
      <p>Bank Locker Management System</p>
    </div>
    <?php if($error): ?>
    <div class="alert alert-danger">⚠️ <?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <form method="POST">
      <div class="form-group">
        <label class="form-label">Email Address</label>
        <input type="email" name="email" class="form-control" placeholder="your@email.com" required>
      </div>
      <div class="form-group">
        <label class="form-label">Password</label>
        <input type="password" name="password" class="form-control" placeholder="Your password" required>
      </div>
      <button type="submit" class="btn btn-primary btn-lg" style="width:100%;justify-content:center;">🔐 Login</button>
    </form>
    <div class="text-center mt-15" style="font-size:13px;">
      New customer? <a href="<?= $base ?>/new_locker_request.php" style="color:#1a3a5c;font-weight:600;">Request a locker</a>
      &nbsp;|&nbsp; <a href="<?= $base ?>/index.php" style="color:#888;">← Home</a>
    </div>
  </div>
</div>
</body>
</html>
