<?php
$pageTitle = 'Services & Prices';
require_once __DIR__.'/includes/admin_header.php';

// Delete
if ($_GET['delete'] ?? false) {
    $id = (int)$_GET['delete'];
    $img = $pdo->prepare("SELECT image_path FROM services WHERE service_id=?");
    $img->execute([$id]);
    if ($s = $img->fetch()) {
        if ($s['image_path'] && file_exists(UPLOAD_DIR.$s['image_path'])) {
            @unlink(UPLOAD_DIR.$s['image_path']);
        }
    }
    $pdo->prepare("DELETE FROM services WHERE service_id=?")->execute([$id]);
    header('Location: services.php?msg=deleted'); exit;
}

// Toggle active
if (($_GET['toggle'] ?? false) && ($_GET['val'] ?? '') !== '') {
    $pdo->prepare("UPDATE services SET is_active=? WHERE service_id=?")
        ->execute([(int)$_GET['val'], (int)$_GET['toggle']]);
    header('Location: services.php'); exit;
}

$services = $pdo->query("
    SELECT s.*, f.name AS fault_name
    FROM services s
    LEFT JOIN faults f ON f.fault_id = s.fault_id
    ORDER BY f.name, s.name
")->fetchAll();
?>
<?php if($_GET['msg'] ?? false): ?>
  <div class="alert alert-success alert-auto"><?= htmlspecialchars($_GET['msg']) ?></div>
<?php endif; ?>

<div class="d-flex justify-content-between align-items-center mb-3">
  <h4 class="fw-bold mb-0">
    <i class="bi bi-wrench-adjustable text-primary"></i>
    Services (<?= count($services) ?>)
  </h4>
  <a href="service_edit.php" class="btn btn-warning fw-bold">
    <i class="bi bi-plus-lg"></i> Add New Service
  </a>
</div>

<div class="card p-3">
  <div class="table-responsive">
    <table class="table table-hover align-middle">
      <thead>
        <tr>
          <th>Image</th>
          <th>Service Name</th>
          <th>Category (Fault)</th>
          <th>Price (KES)</th>
          <th>Active</th>
          <th class="text-end">Actions</th>
        </tr>
      </thead>
      <tbody>
      <?php foreach($services as $s): ?>
        <tr>
          <td>
            <?php if($s['image_path']): ?>
              <img src="<?= UPLOAD_URL.htmlspecialchars($s['image_path']) ?>"
                   style="width:60px;height:60px;object-fit:cover;border-radius:6px;">
            <?php else: ?>
              <div class="bg-light d-flex align-items-center justify-content-center"
                   style="width:60px;height:60px;border-radius:6px;">
                <i class="bi bi-tools text-muted fs-4"></i>
              </div>
            <?php endif; ?>
          </td>
          <td>
            <strong><?= htmlspecialchars($s['name']) ?></strong>
            <?php if($s['description']): ?>
              <div class="small text-muted text-truncate" style="max-width:320px;">
                <?= htmlspecialchars($s['description']) ?>
              </div>
            <?php endif; ?>
          </td>
          <td>
            <?php if($s['fault_name']): ?>
              <span class="badge bg-primary"><?= htmlspecialchars($s['fault_name']) ?></span>
            <?php else: ?>
              <span class="text-muted small">Not set</span>
            <?php endif; ?>
          </td>
          <td><strong class="text-primary"><?= number_format($s['price'], 2) ?></strong></td>
          <td>
            <a href="?toggle=<?= $s['service_id'] ?>&val=<?= $s['is_active'] ? 0 : 1 ?>"
               class="badge bg-<?= $s['is_active'] ? 'success' : 'secondary' ?> text-decoration-none">
              <?= $s['is_active'] ? 'Yes' : 'No' ?>
            </a>
          </td>
          <td class="text-end">
            <a href="service_edit.php?id=<?= $s['service_id'] ?>"
               class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
            <a href="?delete=<?= $s['service_id'] ?>"
               data-confirm="Delete this service? This cannot be undone."
               class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></a>
          </td>
        </tr>
      <?php endforeach; ?>

      <?php if(!$services): ?>
        <tr>
          <td colspan="6" class="text-center text-muted py-5">
            <i class="bi bi-inbox fs-1 d-block mb-2"></i>
            No services yet. Click <strong>Add New Service</strong> to create one.
          </td>
        </tr>
      <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?php require_once __DIR__.'/includes/admin_footer.php'; ?>