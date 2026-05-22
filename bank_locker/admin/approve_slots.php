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

// Status filter for all booked slots
$status_filter = isset($_GET['status']) ? $_GET['status'] : 'all';
if (!in_array($status_filter, ['all','pending','approved','rejected'])) $status_filter = 'all';
$where_sql = $status_filter !== 'all' ? "WHERE al.status='" . $conn->real_escape_string($status_filter) . "'" : "";

$all_slots = $conn->query("
    SELECT al.*, c.full_name, c.customer_id AS cid, c.phone, c.email, l.locker_number, l.locker_size, l.location
    FROM access_log al
    JOIN customers c ON al.customer_id=c.id
    JOIN lockers l ON al.locker_id=l.id
    $where_sql
    ORDER BY al.access_date DESC, al.access_time DESC
");

// Counts per status
$cnt_res = $conn->query("SELECT status, COUNT(*) as cnt FROM access_log GROUP BY status");
$count_map = ['all'=>0,'pending'=>0,'approved'=>0,'rejected'=>0];
while($cr = $cnt_res->fetch_assoc()) {
    $count_map[$cr['status']] = (int)$cr['cnt'];
    $count_map['all'] += (int)$cr['cnt'];
}
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

<style>
.slot-filter-tabs{display:flex;gap:8px;flex-wrap:wrap;}
.filter-tab{padding:6px 16px;border-radius:20px;text-decoration:none;font-size:13px;font-weight:600;background:#f0f4f8;color:#555;transition:all 0.2s;border:1px solid #dde6f0;}
.filter-tab.active{background:#1a3a5c;color:#fff;border-color:#1a3a5c;}
.filter-tab:hover:not(.active){background:#dbe8f4;color:#1a3a5c;}
.tab-count{background:rgba(0,0,0,0.12);padding:1px 8px;border-radius:10px;font-size:11px;margin-left:5px;}
.filter-tab.active .tab-count{background:rgba(255,255,255,0.25);}
</style>

<div class="card">
  <div class="card-header" style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:12px;">
    <h3>📋 All Customer Booked Slots</h3>
    <div class="slot-filter-tabs">
      <a href="?status=all" class="filter-tab <?= $status_filter==='all'?'active':'' ?>">All <span class="tab-count"><?= $count_map['all'] ?></span></a>
      <a href="?status=pending" class="filter-tab <?= $status_filter==='pending'?'active':'' ?>">⏳ Pending <span class="tab-count"><?= $count_map['pending'] ?></span></a>
      <a href="?status=approved" class="filter-tab <?= $status_filter==='approved'?'active':'' ?>">✅ Approved <span class="tab-count"><?= $count_map['approved'] ?></span></a>
      <a href="?status=rejected" class="filter-tab <?= $status_filter==='rejected'?'active':'' ?>">❌ Rejected <span class="tab-count"><?= $count_map['rejected'] ?></span></a>
    </div>
  </div>
  <div class="table-responsive">
    <table>
      <thead>
        <tr>
          <th>#</th>
          <th>Customer</th>
          <th>Phone</th>
          <th>Locker</th>
          <th>Location</th>
          <th>Access Date</th>
          <th>Access Time</th>
          <th>Purpose</th>
          <th>Status</th>
          <th>Handled By</th>
        </tr>
      </thead>
      <tbody>
        <?php $i=1; $found_all=false; while($h=$all_slots->fetch_assoc()): $found_all=true; ?>
        <tr>
          <td><?= $i++ ?></td>
          <td><?= htmlspecialchars($h['full_name']) ?> <small>(<?= htmlspecialchars($h['cid']) ?>)</small></td>
          <td><?= htmlspecialchars($h['phone']) ?></td>
          <td><?= htmlspecialchars($h['locker_number']) ?> <small style="color:#888;">(<?= getLockerSizeLabel($h['locker_size']) ?>)</small></td>
          <td style="font-size:12px;"><?= htmlspecialchars($h['location']) ?></td>
          <td><strong><?= $h['access_date'] ?></strong></td>
          <td><?= date('h:i A', strtotime($h['access_time'])) ?></td>
          <td><?= htmlspecialchars($h['purpose'] ?: '—') ?></td>
          <td><?= getStatusBadge($h['status']) ?></td>
          <td><?= htmlspecialchars($h['approved_by'] ?: '—') ?></td>
        </tr>
        <?php endwhile; if(!$found_all): ?>
        <tr><td colspan="10" class="text-center" style="padding:30px;color:#888;">No booked slots found<?= $status_filter!=='all'?' for this status':'' ?>.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?php $conn->close(); require_once '../includes/footer_admin.php'; ?>
