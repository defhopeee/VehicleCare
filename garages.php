<?php
require_once __DIR__.'/config/db.php';
$pageTitle = 'Our Garages';

$perPage = 9;
$page    = max(1, (int)($_GET['page'] ?? 1));

$totalRows  = (int)$pdo->query("SELECT COUNT(*) c FROM garages WHERE is_active=1")->fetch()['c'];
$totalPages = max(1, (int)ceil($totalRows / $perPage));
$page       = min($page, $totalPages);
$offset     = ($page - 1) * $perPage;

$stmt = $pdo->prepare("SELECT * FROM garages WHERE is_active=1 ORDER BY name LIMIT $perPage OFFSET $offset");
$stmt->execute();
$garages = $stmt->fetchAll();

include __DIR__.'/includes/header.php';
?>
<div class="container py-4">
  <div class="d-flex flex-wrap align-items-end justify-content-between gap-2 mb-3">
    <h1 class="h3 fw-bold mb-0"><i class="bi bi-shop text-primary"></i> Partner Garages</h1>
    <span class="text-muted small"><?= $totalRows ?> garage<?= $totalRows==1?'':'s' ?> nationwide, all open 24/7</span>
  </div>
  <div class="row g-3">
    <?php foreach($garages as $g): ?>
      <div class="col-md-6 col-lg-4">
        <div class="card h-100">
          <?php if($g['image_path']): ?>
            <img src="<?= UPLOAD_URL.htmlspecialchars($g['image_path']) ?>" class="card-img-top" style="height:180px;object-fit:cover;">
          <?php else: ?>
            <div class="bg-light d-flex align-items-center justify-content-center" style="height:140px;"><i class="bi bi-shop fs-1 text-muted"></i></div>
          <?php endif; ?>
          <div class="card-body">
            <h5 class="fw-bold"><?= htmlspecialchars($g['name']) ?></h5>
            <div class="small text-muted mb-2"><i class="bi bi-geo-alt"></i> <?= htmlspecialchars($g['physical_location']) ?>, <?= htmlspecialchars($g['county']) ?></div>
            <span class="badge badge-24-7 text-white mb-2"><?= htmlspecialchars($g['opening_hours']) ?></span>
            <p class="small"><?= htmlspecialchars($g['description']) ?></p>
            <div class="d-flex flex-wrap gap-1">
              <a href="garage.php?id=<?= $g['garage_id'] ?>" class="btn btn-sm btn-primary">View</a>
              <?php if($g['phone']): ?><a href="tel:<?= $g['phone'] ?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-telephone"></i></a><?php endif; ?>
              <?php if($g['whatsapp']): ?><a href="https://wa.me/<?= preg_replace('/\D/','',$g['whatsapp']) ?>" target="_blank" class="btn btn-sm btn-success"><i class="bi bi-whatsapp"></i></a><?php endif; ?>
            </div>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
    <?php if(!$garages): ?>
      <div class="col-12 text-center text-muted py-5">No garages found.</div>
    <?php endif; ?>
  </div>

  <?php if($totalPages > 1): ?>
  <nav class="mt-4" aria-label="Garage pages">
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
