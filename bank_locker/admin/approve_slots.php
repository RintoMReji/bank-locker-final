<?php
$page_title = "Approve Slots";
require_once '../includes/header_admin.php';
$conn = getDBConnection();
$msg = ''; $err = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $access_id = intval($_POST['access_id']);
    $action = $_POST['action'];
    $admin_name = 'Admin: ' . $_SESSION['admin_name'];
    
    if ($action === 'approve') {
        $stmt = $conn->prepare("UPDATE access_log SET status='approved', approved_by=? WHERE id=?");
        $stmt->bind_param("si", $admin_name, $access_id);
        if ($stmt->execute()) {
            $msg = "Slot booking approved successfully!";
            logActivity($conn, 'admin', $_SESSION['admin_id'], $_SESSION['admin_name'], "Approved access slot booking #$access_id");
        } else {
            $err = "Error: " . $conn->error;
        }
    } elseif ($action === 'reject') {
        $stmt = $conn->prepare("UPDATE access_log SET status='rejected', approved_by=? WHERE id=?");
        $stmt->bind_param("si", $admin_name, $access_id);
        if ($stmt->execute()) {
            $msg = "Slot booking rejected.";
            logActivity($conn, 'admin', $_SESSION['admin_id'], $_SESSION['admin_name'], "Rejected access slot booking #$access_id");
        } else {
            $err = "Error: " . $conn->error;
        }
    }
}

// Fetch pending slots
$pending = $conn->query("
    SELECT al.*, c.full_name, c.customer_id AS cid, c.phone, c.email, l.locker_number, l.locker_size, l.location
    FROM access_log al
    JOIN customers c ON al.customer_id=c.id
    JOIN lockers l ON al.locker_id=l.id
    WHERE al.status='pending'
    ORDER BY al.access_date ASC, al.access_time ASC
");

// Fetch recent decisions
$history = $conn->query("
    SELECT al.*, c.full_name, c.customer_id AS cid, l.locker_number
    FROM access_log al
    JOIN customers c ON al.customer_id=c.id
    JOIN lockers l ON al.locker_id=l.id
    WHERE al.status != 'pending'
    ORDER BY al.created_at DESC LIMIT 15
");
?>

<?php if($msg): ?><div class="alert alert-success">✅ <?= htmlspecialchars($msg) ?></div><?php endif; ?>
<?php if($err): ?><div class="alert alert-danger">⚠️ <?= htmlspecialchars($err) ?></div><?php endif; ?>

<div class="card mb-20">
  <div class="card-header"><h3>⏰ Pending Slot Bookings</h3></div>
  <div class="card-body">
    <?php $found=false; while($r=$pending->fetch_assoc()): $found=true; ?>
    <div class="approval-card">
      <div class="approval-header">
        <div>
          <div class="approval-title"><?= htmlspecialchars($r['full_name']) ?> (<?= htmlspecialchars($r['cid']) ?>)</div>
          <small style="color:#888;"><?= htmlspecialchars($r['email']) ?> | <?= htmlspecialchars($r['phone']) ?> | Requested <?= timeAgo($r['created_at']) ?></small>
        </div>
        <?= getStatusBadge($r['status']) ?>
      </div>
      <div class="approval-meta">
        <div><div class="meta-label">Locker Number</div><div class="meta-value"><?= htmlspecialchars($r['locker_number']) ?> (<?= getLockerSizeLabel($r['locker_size']) ?>)</div></div>
        <div><div class="meta-label">Locker Location</div><div class="meta-value"><?= htmlspecialchars($r['location']) ?></div></div>
        <div><div class="meta-label">Requested Date</div><div class="meta-value"><strong><?= $r['access_date'] ?></strong></div></div>
        <div><div class="meta-label">Requested Time</div><div class="meta-value"><strong><?= date('h:i A', strtotime($r['access_time'])) ?></strong></div></div>
        <div style="grid-column: span 2;"><div class="meta-label">Purpose of Visit</div><div class="meta-value"><?= htmlspecialchars($r['purpose'] ?: '—') ?></div></div>
      </div>
      <form method="POST" class="approval-actions">
        <input type="hidden" name="access_id" value="<?= $r['id'] ?>">
        <button type="submit" name="action" value="approve" class="btn btn-success">✅ Approve Slot</button>
        <button type="submit" name="action" value="reject" class="btn btn-danger">❌ Reject Slot</button>
      </form>
    </div>
    <?php endwhile; if(!$found): ?>
    <div class="text-center" style="padding:40px;color:#888;">No pending slot bookings found.</div>
    <?php endif; ?>
  </div>
</div>

<div class="card">
  <div class="card-header"><h3>📋 Recent Slot Decisions (Last 15)</h3></div>
  <div class="table-responsive">
    <table>
      <thead>
        <tr>
          <th>#</th>
          <th>Customer</th>
          <th>Locker</th>
          <th>Access Date</th>
          <th>Access Time</th>
          <th>Status</th>
          <th>Handled By</th>
        </tr>
      </thead>
      <tbody>
        <?php $i=1; $found_history=false; while($h=$history->fetch_assoc()): $found_history=true; ?>
        <tr>
          <td><?= $i++ ?></td>
          <td><?= htmlspecialchars($h['full_name']) ?> <small>(<?= htmlspecialchars($h['cid']) ?>)</small></td>
          <td><?= htmlspecialchars($h['locker_number']) ?></td>
          <td><?= $h['access_date'] ?></td>
          <td><?= date('h:i A', strtotime($h['access_time'])) ?></td>
          <td><?= getStatusBadge($h['status']) ?></td>
          <td><?= htmlspecialchars($h['approved_by'] ?: '—') ?></td>
        </tr>
        <?php endwhile; if(!$found_history): ?>
        <tr><td colspan="7" class="text-center" style="padding:30px;color:#888;">No recent decisions.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?php $conn->close(); require_once '../includes/footer_admin.php'; ?>
