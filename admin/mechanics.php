<?php
$pageTitle='Mechanics';
require_once __DIR__.'/includes/admin_header.php';
if($_GET['delete'] ?? false){
    $pdo->prepare("DELETE FROM mechanics WHERE mechanic_id=?")->execute([(int)$_GET['delete']]);
    header('Location: mechanics.php?msg=deleted'); exit;
}
$rows=$pdo->query("SELECT m.*, f.name AS specialty, g.name AS garage_name
                   FROM mechanics m
                   LEFT JOIN faults f ON f.fault_id=m.specialty_fault_id
                   LEFT JOIN garages g ON g.garage_id=m.garage_id
                   ORDER BY m.full_name")->fetchAll();
?>
<?php if($_GET['msg'] ?? false): ?><div class="alert alert-success alert-auto"><?= htmlspecialchars($_GET['msg']) ?></div><?php endif; ?>
<div class="d-flex justify-content-between mb-3">
  <h4 class="fw-bold mb-0">Mechanics (<?= count($rows) ?>)</h4>
  <a href="mechanic_edit.php" class="btn btn-warning fw-bold"><i class="bi bi-plus-lg"></i> Add Mechanic</a>
</div>
<div class="card p-3">
  <table class="table table-hover align-middle">
    <thead><tr><th>Photo</th><th>Name</th><th>Specialty</th><th>Garage</th><th>Phone</th><th>Available</th><th></th></tr></thead>
    <tbody>
    <?php foreach($rows as $m): ?>
      <tr>
        <td><?php if($m['photo_path']): ?><img src="<?= UPLOAD_URL.htmlspecialchars($m['photo_path']) ?>" style="width:45px;height:45px;object-fit:cover;border-radius:50%;"><?php else: ?><i class="bi bi-person-circle fs-3 text-muted"></i><?php endif; ?></td>
        <td><?= htmlspecialchars($m['full_name']) ?></td>
        <td><span class="badge bg-primary"><?= htmlspecialchars($m['specialty'] ?? 'General') ?></span></td>
        <td><?= htmlspecialchars($m['garage_name'] ?? 'Not set') ?></td>
        <td><?= htmlspecialchars($m['phone']) ?></td>
        <td><?= $m['is_available']?'<span class="badge bg-success">Yes</span>':'<span class="badge bg-secondary">No</span>' ?></td>
        <td class="text-end">
          <a href="mechanic_edit.php?id=<?= $m['mechanic_id'] ?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
          <a href="?delete=<?= $m['mechanic_id'] ?>" data-confirm="Delete this mechanic? This cannot be undone." class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></a>
        </td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
</div>
<?php require_once __DIR__.'/includes/admin_footer.php'; ?>