<?php
require_once __DIR__.'/config/db.php';
$id = (int)($_GET['id'] ?? 0);
$s = $pdo->prepare("SELECT * FROM garages WHERE garage_id=? AND is_active=1"); $s->execute([$id]);
$g = $s->fetch(); if(!$g){ header('Location: garages.php'); exit; }

$mechs = $pdo->prepare("SELECT m.*, f.name AS specialty FROM mechanics m
                        LEFT JOIN faults f ON f.fault_id=m.specialty_fault_id
                        WHERE m.garage_id=? AND m.is_available=1");
$mechs->execute([$id]); $mechanics = $mechs->fetchAll();
$pageTitle = $g['name'];
include __DIR__.'/includes/header.php';
?>
<div class="container py-4">
  <a href="garages.php" class="btn btn-sm btn-outline-secondary mb-3"><i class="bi bi-arrow-left"></i> All Garages</a>
  <div class="row g-4">
    <div class="col-md-7">
      <h1 class="fw-bold"><?= htmlspecialchars($g['name']) ?></h1>
      <p class="text-muted"><i class="bi bi-geo-alt"></i> <?= htmlspecialchars($g['physical_location']) ?>, <?= htmlspecialchars($g['county']) ?></p>
      <span class="badge badge-24-7 text-white"><?= htmlspecialchars($g['opening_hours']) ?></span>
      <p class="mt-3"><?= nl2br(htmlspecialchars($g['description'])) ?></p>

      <div class="mt-3">
        <?php if($g['phone']): ?><a href="tel:<?= $g['phone'] ?>" class="btn btn-primary"><i class="bi bi-telephone"></i> <?= htmlspecialchars($g['phone']) ?></a><?php endif; ?>
        <?php if($g['whatsapp']): ?><a href="https://wa.me/<?= preg_replace('/\D/','',$g['whatsapp']) ?>" target="_blank" class="btn btn-success"><i class="bi bi-whatsapp"></i> WhatsApp</a><?php endif; ?>
        <?php if($g['email']): ?><a href="mailto:<?= $g['email'] ?>" class="btn btn-outline-secondary"><i class="bi bi-envelope"></i> Email</a><?php endif; ?>
      </div>

      <h4 class="mt-4 fw-bold">Our Mechanics</h4>
      <div class="row g-2">
        <?php foreach($mechanics as $m): ?>
          <div class="col-md-6">
            <div class="card p-3">
              <div class="fw-bold"><?= htmlspecialchars($m['full_name']) ?></div>
              <div class="small text-primary"><?= htmlspecialchars($m['specialty'] ?? 'General') ?></div>
              <?php if($m['phone']): ?><div class="small"><i class="bi bi-telephone"></i> <?= htmlspecialchars($m['phone']) ?></div><?php endif; ?>
            </div>
          </div>
        <?php endforeach; ?>
        <?php if(!$mechanics): ?><div class="text-muted small">No mechanics listed yet.</div><?php endif; ?>
      </div>
    </div>
    <div class="col-md-5">
      <?php if($g['latitude'] && $g['longitude']): ?>
      <div class="card">
        <iframe width="100%" height="380" style="border:0" loading="lazy"
          src="https://www.google.com/maps?q=<?= $g['latitude'] ?>,<?= $g['longitude'] ?>&output=embed"></iframe>
      </div>
      <?php endif; ?>
    </div>
  </div>
</div>
<?php include __DIR__.'/includes/footer.php'; ?>