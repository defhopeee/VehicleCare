<?php
$pageTitle = 'Add / Edit Service';
require_once __DIR__.'/includes/admin_header.php';

$id = (int)($_GET['id'] ?? 0);
$svc = [
    'name' => '', 'fault_id' => '', 'price' => '',
    'description' => '', 'image_path' => '', 'is_active' => 1
];

if ($id) {
    $s = $pdo->prepare("SELECT * FROM services WHERE service_id=?");
    $s->execute([$id]);
    $svc = $s->fetch() ?: $svc;
}

$faults = $pdo->query("SELECT * FROM faults ORDER BY name")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name  = trim($_POST['name'] ?? '');
    $fid   = $_POST['fault_id'] !== '' ? (int)$_POST['fault_id'] : null;
    $price = (float)($_POST['price'] ?? 0);
    $desc  = trim($_POST['description'] ?? '');
    $active = isset($_POST['is_active']) ? 1 : 0;

    $img = $svc['image_path'];
    if (!empty($_FILES['image']['name'])) {
        $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        if (in_array($ext, ['jpg','jpeg','png','webp','gif'])) {
            $img = 'service_' . uniqid() . '.' . $ext;
            move_uploaded_file($_FILES['image']['tmp_name'], UPLOAD_DIR.$img);
            if ($svc['image_path'] && file_exists(UPLOAD_DIR.$svc['image_path'])) {
                @unlink(UPLOAD_DIR.$svc['image_path']);
            }
        }
    }

    if ($id) {
        $pdo->prepare("UPDATE services SET name=?, fault_id=?, price=?, description=?, image_path=?, is_active=?
                       WHERE service_id=?")
            ->execute([$name, $fid, $price, $desc, $img, $active, $id]);
    } else {
        $pdo->prepare("INSERT INTO services (name, fault_id, price, description, image_path, is_active)
                       VALUES (?,?,?,?,?,?)")
            ->execute([$name, $fid, $price, $desc, $img, $active]);
    }
    header('Location: services.php?msg=saved'); exit;
}
?>
<div class="d-flex justify-content-between align-items-center mb-3">
  <h4 class="fw-bold mb-0">
    <i class="bi bi-wrench-adjustable text-primary"></i>
    <?= $id ? 'Edit Service' : 'Add New Service' ?>
  </h4>
  <a href="services.php" class="btn btn-outline-secondary">
    <i class="bi bi-arrow-left"></i> Back
  </a>
</div>

<div class="card p-4">
  <form method="post" enctype="multipart/form-data" class="row g-3">

    <div class="col-md-8">
      <label class="form-label">Service Name *</label>
      <input name="name" class="form-control" required
             placeholder="e.g. Engine Oil Change (synthetic)"
             value="<?= htmlspecialchars($svc['name']) ?>">
    </div>

    <div class="col-md-4">
      <label class="form-label">Category (Fault) *</label>
      <select name="fault_id" class="form-select" required>
        <option value="">-- Select category --</option>
        <?php foreach($faults as $f): ?>
          <option value="<?= $f['fault_id'] ?>"
            <?= $svc['fault_id'] == $f['fault_id'] ? 'selected' : '' ?>>
            <?= htmlspecialchars($f['name']) ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>

    <div class="col-md-4">
      <label class="form-label">Price (KES) *</label>
      <input type="number" step="0.01" min="0" name="price"
             class="form-control" required
             value="<?= htmlspecialchars($svc['price']) ?>">
    </div>

    <div class="col-md-8">
      <label class="form-label">Image (optional)</label>
      <input type="file" name="image" class="form-control" accept="image/*">
      <?php if($svc['image_path']): ?>
        <div class="mt-2">
          <img src="<?= UPLOAD_URL.htmlspecialchars($svc['image_path']) ?>"
               style="max-height:100px;border-radius:8px;">
        </div>
      <?php endif; ?>
    </div>

    <div class="col-12">
      <label class="form-label">Description</label>
      <textarea name="description" rows="4" class="form-control"
                placeholder="What's included, turnaround time, what the client gets..."><?= htmlspecialchars($svc['description']) ?></textarea>
    </div>

    <div class="col-12 form-check ms-2">
      <input type="checkbox" name="is_active" class="form-check-input" id="ia"
             <?= $svc['is_active'] ? 'checked' : '' ?>>
      <label class="form-check-label" for="ia">
        Active (visible to clients on services.php)
      </label>
    </div>

    <div class="col-12 text-end">
      <button class="btn btn-warning fw-bold">
        <i class="bi bi-save"></i> Save Service
      </button>
    </div>

  </form>
</div>

<?php require_once __DIR__.'/includes/admin_footer.php'; ?>