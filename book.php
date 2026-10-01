<?php
require_once __DIR__.'/config/db.php';
require_once __DIR__.'/includes/auth.php';
$pageTitle = 'Book Appointment';

$brands  = $pdo->query("SELECT * FROM brands ORDER BY name")->fetchAll();
$faults  = $pdo->query("SELECT * FROM faults ORDER BY name")->fetchAll();
$garages = $pdo->query("SELECT * FROM garages WHERE is_active=1 ORDER BY name")->fetchAll();

$preselectFault = (int)($_GET['fault'] ?? 0);
$preselectType  = $_GET['type']  ?? 'repair';
$success = false; $errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fields = ['client_name','client_phone','client_email','vehicle_brand_id',
               'fault_id','garage_id','mechanic_id','booking_type','description',
               'client_latitude','client_longitude','preferred_date','preferred_time'];
    $data = [];
    foreach ($fields as $f) $data[$f] = trim($_POST[$f] ?? '');

    if ($data['client_name'] === '')  $errors[] = 'Name is required';
    if ($data['client_phone'] === '') {
        $errors[] = 'Phone is required';
    } elseif (preg_match('/\d/', $data['client_phone'], $m, 0) === 0 || strlen(preg_replace('/\D/', '', $data['client_phone'])) < 9) {
        $errors[] = 'Please enter a valid phone number';
    }
    if ($data['client_email'] !== '' && !filter_var($data['client_email'], FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Please enter a valid email address';
    }
    if ($data['preferred_date'] === '') {
        $errors[] = 'Please pick a date';
    } elseif ($data['preferred_date'] < date('Y-m-d')) {
        $errors[] = 'Preferred date cannot be in the past';
    }
    if ($data['booking_type'] === 'repair' && $data['fault_id'] === '') {
        $errors[] = 'Please choose the fault you need fixed';
    }
    if ($data['booking_type'] === 'spare_part' && $data['description'] === '') {
        $errors[] = 'Please describe the part(s) you need';
    }

    if (!$errors) {
        $stmt = $pdo->prepare("INSERT INTO bookings
          (client_name, client_phone, client_email, vehicle_brand_id,
           fault_id, garage_id, mechanic_id, booking_type, description,
           client_latitude, client_longitude, preferred_date, preferred_time)
          VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?)");
        $stmt->execute([
            $data['client_name'], $data['client_phone'], $data['client_email'],
            $data['vehicle_brand_id'] ?: null,
            $data['fault_id'] ?: null,
            $data['garage_id'] ?: null,
            $data['mechanic_id'] ?: null,
            $data['booking_type'] ?: 'repair',
            $data['description'],
            $data['client_latitude'] ?: null,
            $data['client_longitude'] ?: null,
            $data['preferred_date'],
            $data['preferred_time'] ?: null
        ]);
        $success = true;
    }
}
include __DIR__.'/includes/header.php';
?>

<div class="container py-4">
  <div class="row justify-content-center">
    <div class="col-lg-8">

      <div class="card p-4">
        <h2 class="h4 fw-bold mb-1">
          <i class="bi bi-calendar-check text-primary"></i> Book an Appointment
        </h2>
        <p class="text-muted small">Just the essentials, our team handles the rest.</p>

        <?php if($success): ?>
          <div class="alert alert-success">
            <h5 class="mb-1"><i class="bi bi-check-circle"></i> Booking received!</h5>
            <p class="mb-0">Our team will contact you shortly on
              <strong><?= htmlspecialchars($_POST['client_phone']) ?></strong>.</p>
          </div>
        <?php endif; ?>

        <?php if($errors): ?>
          <div class="alert alert-danger">
            <ul class="mb-0">
              <?php foreach($errors as $e) echo '<li>'.htmlspecialchars($e).'</li>'; ?>
            </ul>
          </div>
        <?php endif; ?>

        <?php if(!$success): ?>
        <form method="post" id="bookForm" class="row g-3">

          <!-- ===== Booking type ===== -->
          <div class="col-12">
            <label class="form-label fw-bold">What do you want to do? *</label>
            <div class="row g-2">
              <?php
              $types = [
                'repair'     => ['Repair Service',   'wrench-adjustable'],
                'spare_part' => ['Buy a Spare Part',  'box-seam'],
                'inspection' => ['Inspection',        'clipboard-check'],
              ];
              foreach ($types as $key => $meta):
                $checked = ($preselectType === $key) ? 'checked' : '';
              ?>
                <div class="col-4">
                  <input type="radio" class="btn-check" name="booking_type"
                         id="bt_<?= $key ?>" value="<?= $key ?>" <?= $checked ?> required>
                  <label class="btn btn-outline-warning w-100 py-2" for="bt_<?= $key ?>">
                    <i class="bi bi-<?= $meta[1] ?> d-block mb-1"></i>
                    <small class="d-block fw-semibold"><?= $meta[0] ?></small>
                  </label>
                </div>
              <?php endforeach; ?>
            </div>
          </div>

          <!-- ===== Client info ===== -->
          <div class="col-md-6">
            <label class="form-label">Full Name *</label>
            <input name="client_name" class="form-control" required
                   value="<?= htmlspecialchars($_POST['client_name'] ?? '') ?>">
          </div>
          <div class="col-md-6">
            <label class="form-label">Phone *</label>
            <input name="client_phone" class="form-control" required
                   value="<?= htmlspecialchars($_POST['client_phone'] ?? '') ?>">
          </div>

          <div class="col-md-6" id="brandWrap">
            <label class="form-label">Vehicle Brand</label>
            <select name="vehicle_brand_id" class="form-select">
              <option value="">-- Select --</option>
              <?php foreach($brands as $b): ?>
                <option value="<?= $b['brand_id'] ?>"
                  <?= (($_POST['vehicle_brand_id'] ?? '') == $b['brand_id']) ? 'selected' : '' ?>>
                  <?= htmlspecialchars($b['name']) ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="col-md-6" id="faultWrap">
            <label class="form-label">Fault Needing Repair *</label>
            <select name="fault_id" id="faultSelect" class="form-select">
              <option value="">-- Select --</option>
              <?php foreach($faults as $f): ?>
                <option value="<?= $f['fault_id'] ?>"
                  <?= $preselectFault == $f['fault_id'] ? 'selected' : '' ?>>
                  <?= htmlspecialchars($f['name']) ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="col-md-6">
            <label class="form-label">Preferred Garage</label>
            <select name="garage_id" id="garageSelect" class="form-select">
              <option value="">-- Nearest / Let us choose --</option>
              <?php foreach($garages as $g): ?>
                <option value="<?= $g['garage_id'] ?>">
                  <?= htmlspecialchars($g['name']) ?>, <?= htmlspecialchars($g['county']) ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="col-md-6" id="mechanicWrap">
            <label class="form-label">Preferred Mechanic</label>
            <select name="mechanic_id" id="mechanicSelect" class="form-select">
              <option value="">-- Auto-assign --</option>
            </select>
          </div>

          <div class="col-md-5">
            <label class="form-label">Preferred Date *</label>
            <input type="date" name="preferred_date" class="form-control" required
                   min="<?= date('Y-m-d') ?>">
          </div>
          <div class="col-md-4">
            <label class="form-label">Time</label>
            <input type="time" name="preferred_time" class="form-control">
          </div>
          <div class="col-md-3">
            <label class="form-label d-block">&nbsp;</label>
            <button type="button" id="locBtn" class="btn btn-outline-secondary w-100">
              <i class="bi bi-geo-alt-fill"></i> <span id="locText">Location</span>
            </button>
            <input type="hidden" name="client_latitude"  id="lat">
            <input type="hidden" name="client_longitude" id="lng">
          </div>

          <div class="col-12">
            <label class="form-label" id="descLabel">Anything else we should know?</label>
            <textarea name="description" rows="3" class="form-control" id="descInput"
                      placeholder="Optional details..."><?= htmlspecialchars($_POST['description'] ?? '') ?></textarea>
          </div>

          <div class="col-12 text-end">
            <button class="btn btn-warning btn-lg fw-bold">
              <i class="bi bi-send"></i> Submit Booking
            </button>
          </div>
        </form>
        <?php endif; ?>

      </div>
    </div>
  </div>
</div>

<script>
const faultSelect  = document.getElementById('faultSelect');
const garageSelect = document.getElementById('garageSelect');
const mechSelect   = document.getElementById('mechanicSelect');
const typeRadios   = document.querySelectorAll('input[name="booking_type"]');
const faultWrap    = document.getElementById('faultWrap');
const mechanicWrap = document.getElementById('mechanicWrap');
const descLabel    = document.getElementById('descLabel');
const descInput    = document.getElementById('descInput');

function applyBookingType(){
  const type = document.querySelector('input[name="booking_type"]:checked')?.value || 'repair';
  if (type === 'spare_part') {
    faultWrap.classList.add('d-none');
    mechanicWrap.classList.add('d-none');
    faultSelect.required = false;
    descLabel.textContent = 'Which part(s) do you need? *';
    descInput.placeholder = 'e.g. 2 Michelin tyres 205/55 R16, 1 car battery...';
  } else {
    faultWrap.classList.remove('d-none');
    mechanicWrap.classList.remove('d-none');
    faultSelect.required = (type === 'repair');
    descLabel.textContent = 'Anything else we should know?';
    descInput.placeholder = 'Optional details...';
  }
}
typeRadios.forEach(r => r.addEventListener('change', applyBookingType));
applyBookingType();

async function loadMechanics(){
  if (!mechSelect) return;
  const f = faultSelect?.value || '';
  const g = garageSelect?.value || '';
  mechSelect.innerHTML = '<option value="">-- Auto-assign --</option>';
  if (!f && !g) return;
  const r = await fetch(`<?= SITE_URL ?>/api_mechanics.php?fault=${f}&garage=${g}`);
  const list = await r.json();
  list.forEach(m => {
    const o = document.createElement('option');
    o.value = m.mechanic_id;
    o.textContent = `${m.full_name}, ${m.specialty || 'General'} (${m.garage_name || 'Any'})`;
    mechSelect.appendChild(o);
  });
}
faultSelect?.addEventListener('change', loadMechanics);
garageSelect?.addEventListener('change', loadMechanics);

document.getElementById('locBtn')?.addEventListener('click', async () => {
  document.getElementById('locText').textContent = 'Locating...';
  const loc = await getLocation();
  if (loc) {
    document.getElementById('lat').value = loc.lat;
    document.getElementById('lng').value = loc.lng;
    document.getElementById('locText').textContent = 'Captured';
  } else {
    document.getElementById('locText').textContent = 'Denied';
  }
});
</script>

<?php include __DIR__.'/includes/footer.php'; ?>
