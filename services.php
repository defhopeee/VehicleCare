<?php
require_once __DIR__.'/config/db.php';
$pageTitle = 'Services & Prices';

$perPage = 9;
$page    = max(1, (int)($_GET['page'] ?? 1));

$totalRows  = (int)$pdo->query("SELECT COUNT(*) c FROM services WHERE is_active=1")->fetch()['c'];
$totalPages = max(1, (int)ceil($totalRows / $perPage));
$page       = min($page, $totalPages);
$offset     = ($page - 1) * $perPage;

$stmt = $pdo->prepare("SELECT s.*, f.name AS fault_name FROM services s
                       LEFT JOIN faults f ON f.fault_id=s.fault_id
                       WHERE s.is_active=1 ORDER BY f.name, s.name
                       LIMIT $perPage OFFSET $offset");
$stmt->execute();
$services = $stmt->fetchAll();

include __DIR__.'/includes/header.php';
?>
<div class="container py-4">
  <div class="d-flex flex-wrap align-items-end justify-content-between gap-2 mb-3">
    <h1 class="h3 fw-bold mb-0"><i class="bi bi-wrench-adjustable text-primary"></i> Our Services</h1>
    <span class="text-muted small"><?= $totalRows ?> service<?= $totalRows==1?'':'s' ?>, pay at the garage</span>
  </div>
  <div class="row g-3">
    <?php foreach($services as $s): ?>
      <div class="col-md-6 col-lg-4">
        <div class="card h-100 p-3">
          <?php if($s['image_path']): ?>
            <img src="<?= UPLOAD_URL.htmlspecialchars($s['image_path']) ?>" class="card-img-top mb-2" style="height:150px;object-fit:cover;border-radius:8px;">
          <?php endif; ?>
          <div class="small text-muted"><?= htmlspecialchars($s['fault_name'] ?? 'Service') ?></div>
          <h5 class="fw-bold"><?= htmlspecialchars($s['name']) ?></h5>
          <p class="small text-muted"><?= htmlspecialchars($s['description']) ?></p>
          <div class="mt-auto d-flex justify-content-between align-items-center">
            <div class="fw-bold text-primary">KES <?= number_format($s['price'],2) ?></div>
            <a href="book.php?service=<?= $s['service_id'] ?>" class="btn btn-sm btn-warning fw-bold">Book</a>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
    <?php if(!$services): ?><div class="col-12 text-center text-muted py-5">No services listed yet.</div><?php endif; ?>
  </div>

  <?php if($totalPages > 1): ?>
  <nav class="mt-4" aria-label="Service pages">
    <ul class="pagination justify-content-center flex-wrap">
      <li class="page-item <?= $page<=1?'disabled':'' ?>">
        <a class="page-link" href="?page=<?= max(1,$page-1) ?>">Prev</a>
      </li>
      <?php for($i=1;$i<=$totalPages;$i++): ?>
        <li class="page-item <?= $i==$page?'active':'' ?>">
          <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
        </li>
      <?php endfor; ?>
      <li class="page-item <?= $page>=$totalPages?'disabled':'' ?>">
        <a class="page-link" href="?page=<?= min($totalPages,$page+1) ?>">Next</a>
      </li>
    </ul>
  </nav>
  <?php endif; ?>
</div>
<?php include __DIR__.'/includes/footer.php'; ?>
