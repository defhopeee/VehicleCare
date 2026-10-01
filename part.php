<?php
require_once __DIR__.'/config/db.php';
$id = (int)($_GET['id'] ?? 0);
$stmt = $pdo->prepare("SELECT sp.*, b.name AS brand_name, c.name AS cat_name, g.name AS garage_name,
                              g.phone AS garage_phone, g.whatsapp AS garage_wa, g.email AS garage_email,
                              g.county AS garage_county, g.physical_location AS garage_location
                       FROM spare_parts sp
                       LEFT JOIN brands b ON b.brand_id=sp.brand_id
                       LEFT JOIN part_categories c ON c.category_id=sp.category_id
                       LEFT JOIN garages g ON g.garage_id=sp.garage_id
                       WHERE sp.part_id=? AND sp.is_active=1");
$stmt->execute([$id]); $p = $stmt->fetch();
if(!$p){ header('Location: parts.php'); exit; }
$pageTitle = $p['name'];
include __DIR__.'/includes/header.php';
?>
<div class="container py-4">
  <a href="parts.php" class="btn btn-sm btn-outline-secondary mb-3"><i class="bi bi-arrow-left"></i> Back to Parts</a>
  <div class="row g-4">
    <div class="col-md-5">
      <div class="card">
        <img src="<?= part_thumb($p['category_id'], $p['image_path']) ?>" class="card-img-top" style="height:320px;object-fit:<?= $p['image_path']?'cover':'contain' ?>;background:#eef2f7;">

      </div>
    </div>
    <div class="col-md-7">
      <div class="text-muted small"><?= htmlspecialchars($p['cat_name'] ?? '') ?> · <?= htmlspecialchars($p['brand_name'] ?? '') ?></div>
      <h1 class="fw-bold"><?= htmlspecialchars($p['name']) ?></h1>
      <div class="mb-2"><code><?= htmlspecialchars($p['part_code']) ?></code></div>
      <h3 class="text-primary fw-bold">KES <?= number_format($p['price'],2) ?></h3>
      <p><?= nl2br(htmlspecialchars($p['description'])) ?></p>

      <?php if($p['garage_name']): ?>
      <div class="card p-3 mb-3 bg-light">
        <div class="fw-bold"><i class="bi bi-shop"></i> <?= htmlspecialchars($p['garage_name']) ?></div>
        <div class="small"><?= htmlspecialchars($p['garage_location']) ?>, <?= htmlspecialchars($p['garage_county']) ?></div>
        <div class="mt-2">
          <?php if($p['garage_phone']): ?><a href="tel:<?= $p['garage_phone'] ?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-telephone"></i> Call</a><?php endif; ?>
          <?php if($p['garage_wa']): ?><a href="https://wa.me/<?= preg_replace('/\D/','',$p['garage_wa']) ?>" target="_blank" class="btn btn-sm btn-success"><i class="bi bi-whatsapp"></i> WhatsApp</a><?php endif; ?>
          <?php if($p['garage_email']): ?><a href="mailto:<?= $p['garage_email'] ?>" class="btn btn-sm btn-outline-secondary"><i class="bi bi-envelope"></i> Email</a><?php endif; ?>
        </div>
      </div>
      <?php endif; ?>

      <a href="book.php?type=spare_part" class="btn btn-warning btn-lg fw-bold"><i class="bi bi-cart-check"></i> Order / Enquire</a>
    </div>
  </div>
</div>
<?php include __DIR__.'/includes/footer.php'; ?>