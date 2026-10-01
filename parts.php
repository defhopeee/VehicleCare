<?php
require_once __DIR__.'/config/db.php';
$pageTitle = 'Spare Parts';

$search   = trim($_GET['q'] ?? '');
$brand    = (int)($_GET['brand'] ?? 0);
$category = (int)($_GET['cat'] ?? 0);
$perPage  = 8;
$page     = max(1, (int)($_GET['page'] ?? 1));

$where = "WHERE sp.is_active = 1";
$params = [];
if ($search)   { $where .= " AND (sp.name LIKE ? OR sp.part_code LIKE ?)"; $params[]="%$search%"; $params[]="%$search%"; }
if ($brand)    { $where .= " AND sp.brand_id = ?"; $params[]=$brand; }
if ($category) { $where .= " AND sp.category_id = ?"; $params[]=$category; }

$total = $pdo->prepare("SELECT COUNT(*) c FROM spare_parts sp $where");
$total->execute($params);
$totalRows  = (int)$total->fetch()['c'];
$totalPages = max(1, (int)ceil($totalRows / $perPage));
$page       = min($page, $totalPages);
$offset     = ($page - 1) * $perPage;

$sql = "SELECT sp.*, b.name AS brand_name, c.name AS cat_name, g.name AS garage_name
        FROM spare_parts sp
        LEFT JOIN brands b ON b.brand_id = sp.brand_id
        LEFT JOIN part_categories c ON c.category_id = sp.category_id
        LEFT JOIN garages g ON g.garage_id = sp.garage_id
        $where
        ORDER BY sp.part_id DESC
        LIMIT $perPage OFFSET $offset";
$stmt = $pdo->prepare($sql); $stmt->execute($params);
$parts = $stmt->fetchAll();
$brands = $pdo->query("SELECT * FROM brands ORDER BY name")->fetchAll();
$cats   = $pdo->query("SELECT * FROM part_categories ORDER BY name")->fetchAll();

function partsPageUrl($page) {
    $q = $_GET;
    $q['page'] = $page;
    return '?' . http_build_query($q);
}

include __DIR__.'/includes/header.php';
?>
<div class="container py-4">
  <div class="d-flex flex-wrap align-items-end justify-content-between gap-2 mb-3">
    <h1 class="h3 fw-bold mb-0"><i class="bi bi-box-seam text-primary"></i> Spare Parts</h1>
    <span class="text-muted small"><?= $totalRows ?> part<?= $totalRows==1?'':'s' ?> available</span>
  </div>
  <form class="row g-2 mb-4" id="partsFilterForm">
    <div class="col-md-5">
      <input name="q" id="partsSearch" class="form-control" placeholder="Search part name or code..." value="<?= htmlspecialchars($search) ?>">
    </div>
    <div class="col-md-3">
      <select name="brand" class="form-select" onchange="this.form.submit()">
        <option value="">All Brands</option>
        <?php foreach($brands as $b): ?>
          <option value="<?= $b['brand_id'] ?>" <?= $brand==$b['brand_id']?'selected':'' ?>><?= htmlspecialchars($b['name']) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="col-md-3">
      <select name="cat" class="form-select" onchange="this.form.submit()">
        <option value="">All Categories</option>
        <?php foreach($cats as $c): ?>
          <option value="<?= $c['category_id'] ?>" <?= $category==$c['category_id']?'selected':'' ?>><?= htmlspecialchars($c['name']) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="col-md-1"><button class="btn btn-warning w-100"><i class="bi bi-search"></i></button></div>
  </form>

  <div class="row g-3">
    <?php if(!$parts): ?>
      <div class="col-12 text-center text-muted py-5">
        <i class="bi bi-search fs-1 d-block mb-2"></i>
        No parts match that search.
      </div>
    <?php endif; ?>
    <?php foreach($parts as $p): ?>
      <div class="col-6 col-md-4 col-lg-3">
        <a href="part.php?id=<?= $p['part_id'] ?>" class="text-decoration-none">
          <div class="card part-card h-100">
            <img src="<?= part_thumb($p['category_id'], $p['image_path']) ?>" class="card-img-top" style="height:160px;object-fit:<?= $p['image_path']?'cover':'contain' ?>;background:#eef2f7;">
            <div class="card-body">
              <div class="small text-muted"><?= htmlspecialchars($p['cat_name'] ?? 'Part') ?></div>
              <div class="fw-semibold text-dark"><?= htmlspecialchars($p['name']) ?></div>
              <div class="small text-muted"><?= htmlspecialchars($p['brand_name'] ?? '') ?></div>
              <div class="mt-2 fw-bold text-primary">KES <?= number_format($p['price'],2) ?></div>
              <div class="small <?= $p['stock']>0?'text-success':'text-danger' ?>">
                <?= $p['stock']>0 ? "In stock ({$p['stock']})" : 'Out of stock' ?>
              </div>
            </div>
          </div>
        </a>
      </div>
    <?php endforeach; ?>
  </div>

  <?php if($totalPages > 1): ?>
  <nav class="mt-4" aria-label="Spare parts pages">
    <ul class="pagination justify-content-center flex-wrap">
      <li class="page-item <?= $page<=1?'disabled':'' ?>">
        <a class="page-link" href="<?= partsPageUrl(max(1,$page-1)) ?>">Prev</a>
      </li>
      <?php for($i=1;$i<=$totalPages;$i++): ?>
        <li class="page-item <?= $i==$page?'active':'' ?>">
          <a class="page-link" href="<?= partsPageUrl($i) ?>"><?= $i ?></a>
        </li>
      <?php endfor; ?>
      <li class="page-item <?= $page>=$totalPages?'disabled':'' ?>">
        <a class="page-link" href="<?= partsPageUrl(min($totalPages,$page+1)) ?>">Next</a>
      </li>
    </ul>
  </nav>
  <?php endif; ?>
</div>
<script>
let partsSearchTimer;
document.getElementById('partsSearch')?.addEventListener('input', function(){
  clearTimeout(partsSearchTimer);
  partsSearchTimer = setTimeout(() => document.getElementById('partsFilterForm').submit(), 500);
});
</script>
<?php include __DIR__.'/includes/footer.php'; ?>
