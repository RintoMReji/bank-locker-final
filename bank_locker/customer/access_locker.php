<?php
$page_title = "Slot Booking";
require_once '../includes/header_customer.php';
$conn = getDBConnection();
$cid = $_SESSION['customer_id'];
$msg = ''; $err = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $alloc_id = intval($_POST['allocation_id']);
    $purpose = trim($_POST['purpose']);
    $access_date = trim($_POST['access_date']);
    $access_time = trim($_POST['access_time']);
    
    // Verify allocation belongs to this customer
    $alloc = $conn->query("SELECT a.*, l.locker_number FROM allocations a JOIN lockers l ON a.locker_id=l.id WHERE a.id=$alloc_id AND a.customer_id=$cid AND a.status='active'")->fetch_assoc();
    if ($alloc) {
        $status = 'pending';
        $approved = null;
        $stmt = $conn->prepare("INSERT INTO access_log (customer_id, locker_id, access_date, access_time, purpose, approved_by, status) VALUES (?,?,?,?,?,?,?)");
        $stmt->bind_param("iisssss", $cid, $alloc['locker_id'], $access_date, $access_time, $purpose, $approved, $status);
        if ($stmt->execute()) {
            $msg = "Locker access slot booked successfully! Waiting for admin approval. Locker: " . $alloc['locker_number'] . " on " . $access_date . " at " . date('h:i A', strtotime($access_time));
        } else {
            $err = "Failed to book slot: " . $conn->error;
        }
    } else {
        $err = "Invalid allocation or locker not active.";
    }
}

// Get active allocations
$allocs = $conn->query("
    SELECT a.id, a.allocation_no, l.locker_number, l.locker_size, l.location
    FROM allocations a JOIN lockers l ON a.locker_id=l.id
    WHERE a.customer_id=$cid AND a.status='active'
");
$rows = [];
while($r = $allocs->fetch_assoc()) $rows[] = $r;

// Recent access requests
$recent = $conn->query("
    SELECT al.*, l.locker_number FROM access_log al JOIN lockers l ON al.locker_id=l.id
    WHERE al.customer_id=$cid ORDER BY al.created_at DESC LIMIT 10
");
?>

<?php if($msg): ?><div class="alert alert-success">✅ <?= htmlspecialchars($msg) ?></div><?php endif; ?>
<?php if($err): ?><div class="alert alert-danger">⚠️ <?= htmlspecialchars($err) ?></div><?php endif; ?>

<?php if(empty($rows)): ?>
<div class="card mb-20">
  <div class="card-body text-center" style="padding:50px;">
    <div style="font-size:60px;margin-bottom:20px;">🔒</div>
    <h2 style="color:#1a3a5c;margin-bottom:10px;">No Active Locker</h2>
    <p style="color:#888;">You don't have an active locker to access. <a href="request_locker.php">Request one here</a>.</p>
  </div>
</div>
<?php else: ?>
<div class="card mb-20">
  <div class="card-header"><h3>🔐 Book Locker Access Slot</h3></div>
  <div class="card-body">
    <form method="POST">
      <div class="form-grid">
        <div class="form-group">
          <label class="form-label">Select Locker *</label>
          <select name="allocation_id" class="form-control" required>
            <?php foreach($rows as $a): ?>
            <option value="<?= $a['id'] ?>"><?= htmlspecialchars($a['locker_number']) ?> — <?= getLockerSizeLabel($a['locker_size']) ?> (<?= htmlspecialchars($a['location']) ?>)</option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="form-group">
          <label class="form-label">Purpose of Visit *</label>
          <input type="text" name="purpose" class="form-control" required placeholder="e.g. Deposit documents, Retrieve jewelry">
        </div>
        <div class="form-group">
          <label class="form-label">Preferred Date *</label>
          <input type="date" name="access_date" class="form-control" required min="<?= date('Y-m-d') ?>" value="<?= date('Y-m-d') ?>">
        </div>
        <div class="form-group">
          <label class="form-label">Preferred Time *</label>
          <input type="time" name="access_time" class="form-control" required value="<?= date('H:i') ?>">
        </div>
      </div>
      <button type="submit" class="btn btn-primary">🔐 Request Slot Booking</button>
    </form>
  </div>
</div>
<?php endif; ?>

<div class="card">
  <div class="card-header"><h3>📋 Recent Booked Slots (Last 10)</h3></div>
  <div class="table-responsive">
    <table>
      <thead>
        <tr>
          <th>#</th>
          <th>Locker</th>
          <th>Date</th>
          <th>Time</th>
          <th>Purpose</th>
          <th>Status</th>
          <th>Approved/Handled By</th>
        </tr>
      </thead>
      <tbody>
        <?php $i=1; $found=false; while($l=$recent->fetch_assoc()): $found=true; ?>
        <tr>
          <td><?= $i++ ?></td>
          <td><?= htmlspecialchars($l['locker_number']) ?></td>
          <td><?= $l['access_date'] ?></td>
          <td><?= date('h:i A', strtotime($l['access_time'])) ?></td>
          <td><?= htmlspecialchars($l['purpose']??'—') ?></td>
          <td><?= getStatusBadge($l['status']) ?></td>
          <td><?= htmlspecialchars($l['approved_by']??'—') ?></td>
        </tr>
        <?php endwhile; if(!$found): ?>
        <tr><td colspan="7" class="text-center" style="padding:30px;color:#888;">No slots booked yet.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?php $conn->close(); require_once '../includes/footer_customer.php'; ?>
