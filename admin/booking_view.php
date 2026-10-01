<?php
$pageTitle = 'Booking Details';
require_once __DIR__.'/includes/admin_header.php';

$id = (int)($_GET['id'] ?? 0);
$s = $pdo->prepare("
    SELECT b.*,
           f.name  AS fault_name,
           br.name AS brand_name,
           g.name  AS garage_name,
           m.full_name AS mech_name
    FROM bookings b
    LEFT JOIN faults  f  ON f.fault_id  = b.fault_id
    LEFT JOIN brands  br ON br.brand_id = b.vehicle_brand_id
    LEFT JOIN garages g  ON g.garage_id = b.garage_id
    LEFT JOIN mechanics m ON m.mechanic_id = b.mechanic_id
    WHERE b.booking_id = ?
");
$s->execute([$id]);
$b = $s->fetch();
if (!$b) { header('Location: bookings.php'); exit; }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pdo->prepare("UPDATE bookings SET status=?, mechanic_id=?, admin_notes=? WHERE booking_id=?")
        ->execute([
            $_POST['status'],
            $_POST['mechanic_id'] ?: null,
            trim($_POST['admin_notes']),
            $id
        ]);
    header('Location: booking_view.php?id=' . $id . '&msg=saved'); exit;
}

$mechs = $pdo->query("SELECT * FROM mechanics ORDER BY full_name")->fetchAll();

$typeMeta = [
    'repair'     => ['label' => 'Repair Service',    'icon' => 'wrench-adjustable', 'bg' => 'warning'],
    'spare_part' => ['label' => 'Buying Spare Part', 'icon' => 'box-seam',          'bg' => 'info'],
    'inspection' => ['label' => 'Inspection',        'icon' => 'clipboard-check',   'bg' => 'secondary'],
];
$tm = $typeMeta[$b['booking_type']] ?? ['label'=>ucfirst($b['booking_type']),'icon'=>'question-circle','bg'=>'light'];
?>
<?php if($_GET['msg'] ?? false): ?>
  <div class="alert alert-success alert-auto">Saved.</div>
<?php endif; ?>

<div class="d-flex justify-content-between align-items-center mb-3">
  <h4 class="fw-bold mb-0">Booking #<?= $b['booking_id'] ?></h4>
  <a href="bookings.php" class="btn btn-outline-secondary">
    <i class="bi bi-arrow-left"></i> Back
  </a>
</div>

<div class="row g-3">
  <div class="col-md-7">

    <!-- Booking type banner -->
    <div class="alert alert-<?= $tm['bg'] ?> d-flex align-items-center mb-3">
      <i class="bi bi-<?= $tm['icon'] ?> fs-3 me-3"></i>
      <div>
        <div class="fw-bold"><?= $tm['label'] ?></div>
        <div class="small">This booking was submitted as a <strong><?= str_replace('_',' ', $b['booking_type']) ?></strong> request.</div>
      </div>
    </div>

    <div class="card p-4">
      <h6 class="fw-bold text-muted">Client</h6>
      <p class="mb-1"><strong><?= htmlspecialchars($b['client_name']) ?></strong></p>
      <p class="mb-1"><i class="bi bi-telephone"></i> <?= htmlspecialchars($b['client_phone']) ?></p>
      <?php if($b['client_email']): ?>
        <p class="mb-1"><i class="bi bi-envelope"></i> <?= htmlspecialchars($b['client_email']) ?></p>
      <?php endif; ?>

      <hr>

      <h6 class="fw-bold text-muted">Vehicle</h6>
      <p class="mb-1">Brand: <strong><?= htmlspecialchars($b['brand_name'] ?? 'Not set') ?></strong></p>
      <p class="mb-1">Transmission: <?= htmlspecialchars($b['transmission']) ?></p>
      <p class="mb-1">Plate: <?= htmlspecialchars($b['plate_number'] ?: 'Not set') ?></p>

      <hr>

      <h6 class="fw-bold text-muted">
        <?= $b['booking_type'] === 'spare_part' ? 'Item / Part requested' : 'Issue / Fault' ?>
      </h6>
      <p>Fault: <strong><?= htmlspecialchars($b['fault_name'] ?? 'Not set') ?></strong></p>
      <p><?= nl2br(htmlspecialchars($b['description'])) ?></p>

      <hr>

      <h6 class="fw-bold text-muted">Preferred</h6>
      <p class="mb-1">Date: <?= htmlspecialchars($b['preferred_date']) ?> <?= htmlspecialchars($b['preferred_time'] ?? '') ?></p>
      <p class="mb-1">Garage: <?= htmlspecialchars($b['garage_name'] ?? 'Not set') ?></p>

      <?php if($b['client_latitude'] && $b['client_longitude']): ?>
        <a href="https://www.google.com/maps?q=<?= $b['client_latitude'] ?>,<?= $b['client_longitude'] ?>"
           target="_blank" class="btn btn-sm btn-outline-primary mt-2">
          <i class="bi bi-geo-alt"></i> View client location
        </a>
      <?php endif; ?>
    </div>
  </div>

  <div class="col-md-5">
    <div class="card p-4">
      <h6 class="fw-bold mb-3">Manage Booking</h6>
      <form method="post">
        <label class="form-label">Status</label>
        <select name="status" class="form-select mb-2">
          <?php foreach(['pending','confirmed','in_progress','completed','cancelled'] as $st): ?>
            <option value="<?= $st ?>" <?= $b['status']===$st ? 'selected' : '' ?>>
              <?= ucfirst(str_replace('_',' ', $st)) ?>
            </option>
          <?php endforeach; ?>
        </select>

        <label class="form-label">Assign Mechanic</label>
        <select name="mechanic_id" class="form-select mb-2">
          <option value="">-- None --</option>
          <?php foreach($mechs as $m): ?>
            <option value="<?= $m['mechanic_id'] ?>"
              <?= $b['mechanic_id']==$m['mechanic_id'] ? 'selected' : '' ?>>
              <?= htmlspecialchars($m['full_name']) ?>
            </option>
          <?php endforeach; ?>
        </select>

        <label class="form-label">Admin Notes</label>
        <textarea name="admin_notes" rows="4" class="form-control mb-2"><?= htmlspecialchars($b['admin_notes']) ?></textarea>

        <button class="btn btn-warning w-100 fw-bold">
          <i class="bi bi-save"></i> Update Booking
        </button>
      </form>

      <hr>

      <div class="d-grid gap-2">
        <a href="tel:<?= htmlspecialchars($b['client_phone']) ?>" class="btn btn-outline-primary">
          <i class="bi bi-telephone"></i> Call Client
        </a>
        <?php if($b['client_phone']): ?>
          <a href="https://wa.me/<?= preg_replace('/\D/','', $b['client_phone']) ?>"
             target="_blank" class="btn btn-success">
            <i class="bi bi-whatsapp"></i> WhatsApp
          </a>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>

<?php require_once __DIR__.'/includes/admin_footer.php'; ?>