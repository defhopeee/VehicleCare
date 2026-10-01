<?php
require_once __DIR__.'/config/db.php';
$pageTitle = 'Find a Specialist';
$faultId = (int)($_GET['fault'] ?? 0);
$faults = $pdo->query("SELECT * FROM faults ORDER BY name")->fetchAll();

$perPage = 9;
$page    = max(1, (int)($_GET['page'] ?? 1));

$where = "WHERE m.is_available=1";
$params = [];
if ($faultId){ $where .= " AND m.specialty_fault_id=?"; $params[]=$faultId; }

$total = $pdo->prepare("SELECT COUNT(*) c FROM mechanics m $where");
$total->execute($params);
$totalRows  = (int)$total->fetch()['c'];
$totalPages = max(1, (int)ceil($totalRows / $perPage));
$page       = min($page, $totalPages);
$offset     = ($page - 1) * $perPage;

$sql = "SELECT m.*, f.name AS specialty, g.name AS garage_name, g.county AS garage_county,
               g.phone AS garage_phone, g.whatsapp AS garage_wa
        FROM mechanics m
        LEFT JOIN faults f ON f.fault_id=m.specialty_fault_id
        LEFT JOIN garages g ON g.garage_id=m.garage_id
        $where
        ORDER BY m.rating DESC, m.full_name
        LIMIT $perPage OFFSET $offset";
$stmt = $pdo->prepare($sql); $stmt->execute($params); $mechs = $stmt->fetchAll();

function mechPageUrl($page) {
    $q = $_GET;
    $q['page'] = $page;
    return '?' . http_build_query($q);
}

include __DIR__.'/includes/header.php';
?>
<div class="container py-4">
  <div class="d-flex flex-wrap align-items-end justify-content-between gap-2 mb-3">
    <h1 class="h3 fw-bold mb-0"><i class="bi bi-person-gear text-primary"></i> Specialist Mechanics</h1>
    <span class="text-muted small"><?= $totalRows ?> mechanic<?= $totalRows==1?'':'s' ?> available</span>
  </div>
  <div class="d-flex flex-wrap gap-2 mb-4">
    <a href="mechanics.php" class="btn btn-sm <?= !$faultId?'btn-warning':'btn-outline-secondary' ?>">All</a>
    <?php foreach($faults as $f): ?>
      <a href="?fault=<?= $f['fault_id'] ?>" class="btn btn-sm <?= $faultId==$f['fault_id']?'btn-warning':'btn-outline-secondary' ?>"><?= htmlspecialchars($f['name']) ?></a>
    <?php endforeach; ?>
  </div>

  <div class="row g-3">
    <?php foreach($mechs as $m): ?>
      <div class="col-md-6 col-lg-4">
        <div class="card p-3 h-100">
          <div class="d-flex">
            <?php if($m['photo_path']): ?>
              <img src="<?= UPLOAD_URL.htmlspecialchars($m['photo_path']) ?>" style="width:70px;height:70px;object-fit:cover;border-radius:50%;">
            <?php else: ?>
              <div class="bg-light d-flex align-items-center justify-content-center" style="width:70px;height:70px;border-radius:50%;"><i class="bi bi-person fs-2 text-muted"></i></div>
            <?php endif; ?>
            <div class="ms-3">
              <div class="fw-bold"><?= htmlspecialchars($m['full_name']) ?></div>
              <div class="small text-primary"><?= htmlspecialchars($m['specialty'] ?? 'General') ?></div>
              <div class="text-warning small"><?php for($i=0;$i<round($m['rating']);$i++) echo '<i class="bi bi-star-fill"></i>'; ?></div>
            </div>
          </div>
          <p class="small mt-2 mb-2"><?= htmlspecialchars($m['bio']) ?></p>
          <?php if($m['garage_name']): ?><div class="small text-muted"><i class="bi bi-shop"></i> <?= htmlspecialchars($m['garage_name']) ?>, <?= htmlspecialchars($m['garage_county']) ?></div><?php endif; ?>
          <div class="mt-2 d-flex gap-1">
            <a href="book.php?mechanic=<?= $m['mechanic_id'] ?>&fault=<?= $m['specialty_fault_id'] ?>" class="btn btn-sm btn-warning fw-bold">Book</a>
            <?php if($m['phone']): ?><a href="tel:<?= $m['phone'] ?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-telephone"></i></a><?php endif; ?>
            <?php if($m['garage_wa']): ?><a href="https://wa.me/<?= preg_replace('/\D/','',$m['garage_wa']) ?>" target="_blank" class="btn btn-sm btn-success"><i class="bi bi-whatsapp"></i></a><?php endif; ?>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
    <?php if(!$mechs): ?><div class="col-12 text-center text-muted py-5">No mechanics found for that specialty.</div><?php endif; ?>
  </div>

  <?php if($totalPages > 1): ?>
  <nav class="mt-4" aria-label="Mechanic pages">
    <ul class="pagination justify-content-center flex-wrap">
      <li class="page-item <?= $page<=1?'disabled':'' ?>">
        <a class="page-link" href="<?= mechPageUrl(max(1,$page-1)) ?>">Prev</a>
      </li>
      <?php for($i=1;$i<=$totalPages;$i++): ?>
        <li class="page-item <?= $i==$page?'active':'' ?>">
          <a class="page-link" href="<?= mechPageUrl($i) ?>"><?= $i ?></a>
        </li>
      <?php endfor; ?>
      <li class="page-item <?= $page>=$totalPages?'disabled':'' ?>">
        <a class="page-link" href="<?= mechPageUrl(min($totalPages,$page+1)) ?>">Next</a>
      </li>
    </ul>
  </nav>
  <?php endif; ?>
</div>
<?php include __DIR__.'/includes/footer.php'; ?>
