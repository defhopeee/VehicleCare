<?php
require_once __DIR__.'/config/db.php';
$pageTitle = 'Vehicle Brands';

$perPage = 12;
$page    = max(1, (int)($_GET['page'] ?? 1));

$totalRows  = (int)$pdo->query("SELECT COUNT(*) c FROM brands")->fetch()['c'];
$totalPages = max(1, (int)ceil($totalRows / $perPage));
$page       = min($page, $totalPages);
$offset     = ($page - 1) * $perPage;

$stmt = $pdo->prepare("SELECT * FROM brands ORDER BY name LIMIT $perPage OFFSET $offset");
$stmt->execute();
$brands = $stmt->fetchAll();

include __DIR__.'/includes/header.php';
?>
<div class="container py-4">
  <div class="d-flex flex-wrap align-items-end justify-content-between gap-2 mb-3">
    <h1 class="h3 fw-bold mb-0"><i class="bi bi-car-front text-primary"></i> Vehicle Brands</h1>
    <span class="text-muted small"><?= $totalRows ?> brand<?= $totalRows==1?'':'s' ?> supported</span>
  </div>
  <div class="row g-4">
    <?php foreach($brands as $b): ?>
      <div class="col-6 col-md-4 col-lg-3">
        <a href="parts.php?brand=<?= $b['brand_id'] ?>" class="text-decoration-none">
          <div class="card brand-card">
            <div class="brand-logo-wrap">
              <?php if($b['logo_path']): ?>
                <img src="<?= UPLOAD_URL.htmlspecialchars($b['logo_path']) ?>" alt="<?= htmlspecialchars($b['name']) ?>">
              <?php else: ?>
                <div class="brand-badge" style="background:<?= brand_badge_color($b['name']) ?>">
                  <?= strtoupper(substr($b['name'],0,2)) ?>
                </div>
              <?php endif; ?>
            </div>
            <div class="brand-name"><?= htmlspecialchars($b['name']) ?></div>
          </div>
        </a>
      </div>
    <?php endforeach; ?>
    <?php if(!$brands): ?>
      <div class="col-12 text-muted small text-center py-5">No brands configured yet.</div>
    <?php endif; ?>
  </div>

  <?php if($totalPages > 1): ?>
  <nav class="mt-4" aria-label="Brand pages">
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
