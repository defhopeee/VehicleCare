<?php
$pageTitle='Spare Parts';
require_once __DIR__.'/includes/admin_header.php';

if($_GET['delete'] ?? false){
    $id=(int)$_GET['delete'];
    $img=$pdo->prepare("SELECT image_path FROM spare_parts WHERE part_id=?"); $img->execute([$id]);
    if($p=$img->fetch()) if($p['image_path'] && file_exists(UPLOAD_DIR.$p['image_path'])) unlink(UPLOAD_DIR.$p['image_path']);
    $pdo->prepare("DELETE FROM spare_parts WHERE part_id=?")->execute([$id]);
    header('Location: parts.php?msg=deleted'); exit;
}

$parts = $pdo->query("SELECT sp.*, c.name AS cat, b.name AS brand, g.name AS garage
                      FROM spare_parts sp
                      LEFT JOIN part_categories c ON c.category_id=sp.category_id
                      LEFT JOIN brands b ON b.brand_id=sp.brand_id
                      LEFT JOIN garages g ON g.garage_id=sp.garage_id
                      ORDER BY sp.part_id DESC")->fetchAll();
?>
<?php if($_GET['msg'] ?? false): ?><div class="alert alert-success alert-auto"><?= htmlspecialchars($_GET['msg']) ?></div><?php endif; ?>
<div class="d-flex justify-content-between mb-3">
  <h4 class="fw-bold mb-0">Spare Parts (<?= count($parts) ?>)</h4>
  <a href="part_edit.php" class="btn btn-warning fw-bold"><i class="bi bi-plus-lg"></i> Add New Part</a>
</div>
<div class="card p-3">
  <div class="table-responsive">
    <table class="table table-hover align-middle">
      <thead><tr><th>Image</th><th>Code</th><th>Name</th><th>Category</th><th>Brand</th><th>Price (KES)</th><th>Stock</th><th>Actions</th></tr></thead>
      <tbody>
      <?php foreach($parts as $p): ?>
        <tr>
          <td><?php if($p['image_path']): ?><img src="<?= UPLOAD_URL.htmlspecialchars($p['image_path']) ?>" style="width:55px;height:55px;object-fit:cover;border-radius:6px;"><?php else: ?><i class="bi bi-image text-muted fs-3"></i><?php endif; ?></td>
          <td><code><?= htmlspecialchars($p['part_code']) ?></code></td>
          <td><?= htmlspecialchars($p['name']) ?></td>
          <td><?= htmlspecialchars($p['cat'] ?? 'Not set') ?></td>
          <td><?= htmlspecialchars($p['brand'] ?? 'Not set') ?></td>
          <td><strong><?= number_format($p['price'],2) ?></strong></td>
          <td><?= $p['stock'] ?></td>
          <td>
            <a href="part_edit.php?id=<?= $p['part_id'] ?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
            <a href="?delete=<?= $p['part_id'] ?>" data-confirm="Delete this spare part? This cannot be undone." class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></a>
          </td>
        </tr>
      <?php endforeach; ?>
      <?php if(!$parts): ?><tr><td colspan="8" class="text-center text-muted py-4">No parts yet. Click "Add New Part".</td></tr><?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
<?php require_once __DIR__.'/includes/admin_footer.php'; ?>