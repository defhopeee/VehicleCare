<?php
$pageTitle='Garages';
require_once __DIR__.'/includes/admin_header.php';
if($_GET['delete'] ?? false){
    $pdo->prepare("DELETE FROM garages WHERE garage_id=?")->execute([(int)$_GET['delete']]);
    header('Location: garages.php?msg=deleted'); exit;
}
$rows=$pdo->query("SELECT * FROM garages ORDER BY name")->fetchAll();
?>
<?php if($_GET['msg'] ?? false): ?><div class="alert alert-success alert-auto"><?= htmlspecialchars($_GET['msg']) ?></div><?php endif; ?>
<div class="d-flex justify-content-between mb-3">
  <h4 class="fw-bold mb-0">Garages (<?= count($rows) ?>)</h4>
  <a href="garage_edit.php" class="btn btn-warning fw-bold"><i class="bi bi-plus-lg"></i> Add Garage</a>
</div>
<div class="card p-3">
  <table class="table table-hover align-middle">
    <thead><tr><th>Image</th><th>Name</th><th>County</th><th>Phone</th><th>Hours</th><th>Active</th><th></th></tr></thead>
    <tbody>
    <?php foreach($rows as $g): ?>
      <tr>
        <td><?php if($g['image_path']): ?><img src="<?= UPLOAD_URL.htmlspecialchars($g['image_path']) ?>" style="width:50px;height:50px;object-fit:cover;border-radius:6px;"><?php else: ?><i class="bi bi-shop fs-3 text-muted"></i><?php endif; ?></td>
        <td><?= htmlspecialchars($g['name']) ?></td>
        <td><?= htmlspecialchars($g['county']) ?></td>
        <td><?= htmlspecialchars($g['phone']) ?></td>
        <td><span class="badge bg-success"><?= htmlspecialchars($g['opening_hours']) ?></span></td>
        <td><?= $g['is_active']?'<span class="badge bg-success">Yes</span>':'<span class="badge bg-secondary">No</span>' ?></td>
        <td class="text-end">
          <a href="garage_edit.php?id=<?= $g['garage_id'] ?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
          <a href="?delete=<?= $g['garage_id'] ?>" data-confirm="Delete this garage? This cannot be undone." class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></a>
        </td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
</div>
<?php require_once __DIR__.'/includes/admin_footer.php'; ?>