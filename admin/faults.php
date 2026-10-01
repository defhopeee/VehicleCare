<?php
$pageTitle='Faults & Services';
require_once __DIR__.'/includes/admin_header.php';

if($_GET['delete'] ?? false){
    $pdo->prepare("DELETE FROM faults WHERE fault_id=?")->execute([(int)$_GET['delete']]);
    header('Location: faults.php?msg=deleted'); exit;
}
if($_SERVER['REQUEST_METHOD']==='POST'){
    $id=(int)($_POST['fault_id'] ?? 0);
    $name=trim($_POST['name']); $desc=trim($_POST['description'] ?? '');
    if($id) $pdo->prepare("UPDATE faults SET name=?,description=? WHERE fault_id=?")->execute([$name,$desc,$id]);
    else    $pdo->prepare("INSERT INTO faults (name,description) VALUES (?,?)")->execute([$name,$desc]);
    header('Location: faults.php?msg=saved'); exit;
}
$rows=$pdo->query("SELECT * FROM faults ORDER BY name")->fetchAll();
$edit=null;
if($_GET['edit'] ?? false){ $s=$pdo->prepare("SELECT * FROM faults WHERE fault_id=?"); $s->execute([(int)$_GET['edit']]); $edit=$s->fetch(); }
?>
<?php if($_GET['msg'] ?? false): ?><div class="alert alert-success alert-auto"><?= htmlspecialchars($_GET['msg']) ?></div><?php endif; ?>
<div class="row g-3">
  <div class="col-md-4">
    <div class="card p-3">
      <h5 class="fw-bold"><?= $edit?'Edit':'Add' ?> Fault</h5>
      <form method="post">
        <input type="hidden" name="fault_id" value="<?= $edit['fault_id'] ?? '' ?>">
        <div class="mb-2"><label class="form-label">Name *</label><input name="name" class="form-control" required value="<?= htmlspecialchars($edit['name'] ?? '') ?>"></div>
        <div class="mb-2"><label class="form-label">Description</label><textarea name="description" class="form-control" rows="3"><?= htmlspecialchars($edit['description'] ?? '') ?></textarea></div>
        <button class="btn btn-warning w-100 fw-bold">Save</button>
        <?php if($edit): ?><a href="faults.php" class="btn btn-link w-100">Cancel</a><?php endif; ?>
      </form>
    </div>
  </div>
  <div class="col-md-8">
    <div class="card p-3">
      <h5 class="fw-bold">All Faults (<?= count($rows) ?>)</h5>
      <table class="table table-hover align-middle">
        <thead><tr><th>Name</th><th>Description</th><th></th></tr></thead>
        <tbody>
        <?php foreach($rows as $r): ?>
          <tr>
            <td><?= htmlspecialchars($r['name']) ?></td>
            <td class="small text-muted"><?= htmlspecialchars($r['description']) ?></td>
            <td class="text-end">
              <a href="?edit=<?= $r['fault_id'] ?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
              <a href="?delete=<?= $r['fault_id'] ?>" data-confirm="Delete this fault category? This cannot be undone." class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></a>
            </td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
<?php require_once __DIR__.'/includes/admin_footer.php'; ?>