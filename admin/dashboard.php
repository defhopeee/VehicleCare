<?php
$pageTitle='Dashboard';
require_once __DIR__.'/includes/admin_header.php';

$stats = [
  'bookings_pending' => $pdo->query("SELECT COUNT(*) c FROM bookings WHERE status='pending'")->fetch()['c'],
  'bookings_today'   => $pdo->query("SELECT COUNT(*) c FROM bookings WHERE DATE(created_at)=CURDATE()")->fetch()['c'],
  'mechanics'        => $pdo->query("SELECT COUNT(*) c FROM mechanics")->fetch()['c'],
  'parts'            => $pdo->query("SELECT COUNT(*) c FROM spare_parts")->fetch()['c'],
  'feedback_new'     => $pdo->query("SELECT COUNT(*) c FROM feedback WHERE status='new'")->fetch()['c'],
  'chat_open'        => $pdo->query("SELECT COUNT(*) c FROM chat_sessions WHERE status='open'")->fetch()['c'],
];
$recent = $pdo->query("SELECT b.*, f.name AS fault_name FROM bookings b LEFT JOIN faults f ON f.fault_id=b.fault_id ORDER BY b.booking_id DESC LIMIT 8")->fetchAll();
?>
<div class="row g-3 mb-4">
  <?php
  $cards = [
    ['Pending Bookings', $stats['bookings_pending'], 'calendar-x', 'warning', 'bookings.php'],
    ['Today\'s Bookings', $stats['bookings_today'], 'calendar-day', 'primary', 'bookings.php'],
    ['Mechanics', $stats['mechanics'], 'person-gear', 'success', 'mechanics.php'],
    ['Spare Parts', $stats['parts'], 'box-seam', 'info', 'parts.php'],
    ['New Feedback', $stats['feedback_new'], 'envelope-exclamation', 'danger', 'feedback.php'],
    ['Open Chats', $stats['chat_open'], 'chat-dots', 'dark', 'chat.php'],
  ];
  foreach($cards as $c): ?>
  <div class="col-md-4 col-lg-2">
    <a href="<?= $c[4] ?>" class="text-decoration-none">
      <div class="card p-3 h-100">
        <div class="d-flex justify-content-between">
          <div>
            <div class="text-muted small"><?= $c[0] ?></div>
            <h3 class="fw-bold mb-0 text-<?= $c[3] ?>"><?= $c[1] ?></h3>
          </div>
          <i class="bi bi-<?= $c[2] ?> fs-2 text-<?= $c[3] ?> opacity-50"></i>
        </div>
      </div>
    </a>
  </div>
  <?php endforeach; ?>
</div>

<div class="card p-4">
  <h5 class="fw-bold mb-3">Recent Bookings</h5>
  <div class="table-responsive">
    <table class="table table-hover align-middle">
      <thead><tr><th>#</th><th>Client</th><th>Phone</th><th>Fault</th><th>Date</th><th>Status</th><th></th></tr></thead>
      <tbody>
      <?php foreach($recent as $r): ?>
        <tr>
          <td><?= $r['booking_id'] ?></td>
          <td><?= htmlspecialchars($r['client_name']) ?></td>
          <td><?= htmlspecialchars($r['client_phone']) ?></td>
          <td><?= htmlspecialchars($r['fault_name'] ?? 'Not set') ?></td>
          <td><?= $r['preferred_date'] ?: 'Not set' ?></td>
          <td><span class="badge bg-<?= ['pending'=>'warning','confirmed'=>'primary','in_progress'=>'info','completed'=>'success','cancelled'=>'secondary'][$r['status']] ?>"><?= $r['status'] ?></span></td>
          <td><a class="btn btn-sm btn-outline-primary" href="booking_view.php?id=<?= $r['booking_id'] ?>">View</a></td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>
<?php require_once __DIR__.'/includes/admin_footer.php'; ?>