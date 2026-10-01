<?php
$pageTitle='Bookings';
require_once __DIR__.'/includes/admin_header.php';

if($_GET['delete'] ?? false){
    $pdo->prepare("DELETE FROM bookings WHERE booking_id=?")->execute([(int)$_GET['delete']]);
    header('Location: bookings.php?msg=deleted'); exit;
}
$status=$_GET['status'] ?? '';
$sql="SELECT b.*, f.name AS fault_name, g.name AS garage_name, m.full_name AS mech_name
      FROM bookings b
      LEFT JOIN faults f ON f.fault_id=b.fault_id
      LEFT JOIN garages g ON g.garage_id=b.garage_id
      LEFT JOIN mechanics m ON m.mechanic_id=b.mechanic_id";
$params=[];
if($status){ $sql.=" WHERE b.status=?"; $params[]=$status; }
$sql.=" ORDER BY b.booking_id DESC";
$stmt=$pdo->prepare($sql); $stmt->execute($params); $rows=$stmt->fetchAll();
?>
<?php if($_GET['msg'] ?? false): ?><div class="alert alert-success alert-auto"><?= htmlspecialchars($_GET['msg']) ?></div><?php endif; ?>
<div class="d-flex flex-wrap justify-content-between mb-3">
  <h4 class="fw-bold mb-0">Bookings (<?= count($rows) ?>)</h4>
  <div class="btn-group">
    <?php foreach([''=>'All','pending'=>'Pending','confirmed'=>'Confirmed','in_progress'=>'In Progress','completed'=>'Completed','cancelled'=>'Cancelled'] as $k=>$v): ?>
      <a href="?status=<?= $k ?>" class="btn btn-sm <?= $status===$k?'btn-primary':'btn-outline-secondary' ?>"><?= $v ?></a>
    <?php endforeach; ?>
  </div>
</div>
<div class="card p-3">
  <div class="table-responsive">
    <table class="table table-hover align-middle">
      <thead><tr><th>#</th><th>Client</th><th>Phone</th><th>Fault</th><th>Garage</th><th>Mechanic</th><th>Date</th><th>Status</th><th></th></tr></thead>
      <tbody>
      <?php foreach($rows as $r): ?>
        <tr>
          <td><?= $r['booking_id'] ?></td>
          <td><?= htmlspecialchars($r['client_name']) ?></td>
          <td><?= htmlspecialchars($r['client_phone']) ?></td>
          <td><?= htmlspecialchars($r['fault_name'] ?? 'Not set') ?></td>
          <td><?= htmlspecialchars($r['garage_name'] ?? 'Not set') ?></td>
          <td><?= htmlspecialchars($r['mech_name'] ?? 'Not set') ?></td>
          <td><?= $r['preferred_date'] ?></td>
          <td><span class="badge bg-<?= ['pending'=>'warning','confirmed'=>'primary','in_progress'=>'info','completed'=>'success','cancelled'=>'secondary'][$r['status']] ?>"><?= $r['status'] ?></span></td>
          <td class="text-end">
            <a href="booking_view.php?id=<?= $r['booking_id'] ?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-eye"></i></a>
            <a href="?delete=<?= $r['booking_id'] ?>" data-confirm="Delete this booking? This cannot be undone." class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></a>
          </td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>
<?php require_once __DIR__.'/includes/admin_footer.php'; ?>