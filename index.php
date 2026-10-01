<?php
require_once __DIR__.'/config/db.php';
$pageTitle = 'Home';

$faults      = $pdo->query("SELECT * FROM faults ORDER BY name LIMIT 10")->fetchAll();
$brandsShown = 8;
$brands      = $pdo->query("SELECT * FROM brands ORDER BY name LIMIT $brandsShown")->fetchAll();
$garageCount = $pdo->query("SELECT COUNT(*) c FROM garages")->fetch()['c'];
$partCount   = $pdo->query("SELECT COUNT(*) c FROM spare_parts WHERE is_active=1")->fetch()['c'];
$mechCount   = $pdo->query("SELECT COUNT(*) c FROM mechanics")->fetch()['c'];
$faultCount  = $pdo->query("SELECT COUNT(*) c FROM faults")->fetch()['c'];
$brandCount  = $pdo->query("SELECT COUNT(*) c FROM brands")->fetch()['c'];
$testimonials = $pdo->query("SELECT name, subject, message FROM feedback WHERE type='feedback' ORDER BY feedback_id DESC LIMIT 3")->fetchAll();

include __DIR__.'/includes/header.php';
?>

<!-- ================= HERO ================= -->
<section id="heroCarousel" class="carousel slide hero" data-bs-ride="carousel" data-bs-interval="4500">
  <div class="carousel-inner h-100">
    <div class="carousel-item active h-100">
      <div class="hero-slide" style="background-image:url('<?= SITE_URL ?>/assets/img/hero-repair.jpg')"></div>
    </div>
    <div class="carousel-item h-100">
      <div class="hero-slide" style="background-image:url('<?= SITE_URL ?>/assets/img/hero-tow.jpg')"></div>
    </div>
    <div class="carousel-item h-100">
      <div class="hero-slide" style="background-image:url('<?= SITE_URL ?>/assets/img/hero-parts.jpg')"></div>
    </div>
  </div>
  <div class="hero-overlay"></div>
  <div class="hero-content text-center">
    <div class="container">
      <h1 class="display-3">Vehicle Broken Down? We've Got You.</h1>
      <p class="lead mb-4 fs-4">Nationwide garages, 24/7 mechanics, genuine spare parts, book in seconds.</p>
      <a href="book.php" class="btn btn-warning btn-lg fw-bold me-2">
        <i class="bi bi-calendar-check"></i> Book Now
      </a>
      <a href="garages.php" class="btn btn-outline-light btn-lg">
        <i class="bi bi-geo-alt"></i> Find a Garage
      </a>
      <div class="hero-trust mt-4 d-flex flex-wrap justify-content-center gap-3">
        <span><i class="bi bi-patch-check-fill"></i> Verified Garages</span>
        <span><i class="bi bi-shield-check"></i> Trusted Mechanics</span>
        <span><i class="bi bi-truck"></i> Nationwide Coverage</span>
      </div>
    </div>
  </div>
  <div class="carousel-indicators">
    <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Repairs"></button>
    <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="1" aria-label="24/7 dispatch"></button>
    <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="2" aria-label="Spare parts"></button>
  </div>
</section>

<!-- ================= STATS STRIP ================= -->
<section class="stats-strip">
  <div class="container">
    <div class="stats-scroll d-flex flex-nowrap">
      <div class="stat-chip">
        <i class="bi bi-shop"></i>
        <div><strong><?= $garageCount ?>+</strong><span>Partner Garages</span></div>
      </div>
      <div class="stat-chip">
        <i class="bi bi-person-gear"></i>
        <div><strong><?= $mechCount ?>+</strong><span>Specialist Mechanics</span></div>
      </div>
      <div class="stat-chip">
        <i class="bi bi-box-seam"></i>
        <div><strong><?= $partCount ?>+</strong><span>Spare Parts In Stock</span></div>
      </div>
      <div class="stat-chip">
        <i class="bi bi-clock-history"></i>
        <div><strong>24/7</strong><span>Emergency Dispatch</span></div>
      </div>
      <div class="stat-chip">
        <i class="bi bi-wrench-adjustable-circle"></i>
        <div><strong><?= $faultCount ?></strong><span>Fault Categories</span></div>
      </div>
      <div class="stat-chip">
        <i class="bi bi-car-front"></i>
        <div><strong><?= $brandCount ?>+</strong><span>Vehicle Brands</span></div>
      </div>
    </div>
  </div>
</section>

<!-- ================= HOW IT WORKS ================= -->
<section class="py-5 bg-white">
  <div class="container">
    <h2 class="fw-bold mb-1 text-center">How It Works</h2>
    <p class="text-muted mb-4 text-center">Three steps, no paperwork, no payment through the site.</p>
    <div class="row g-4 text-center">
      <div class="col-md-4">
        <div class="rounded-circle bg-dark text-warning d-inline-flex align-items-center justify-content-center mb-3" style="width:64px;height:64px;">
          <i class="bi bi-ui-checks-grid fs-3"></i>
        </div>
        <h5 class="fw-bold">1. Choose What You Need</h5>
        <p class="text-muted small">Repair, spare part, or inspection, pick the fault or part and your preferred garage.</p>
      </div>
      <div class="col-md-4">
        <div class="rounded-circle bg-dark text-warning d-inline-flex align-items-center justify-content-center mb-3" style="width:64px;height:64px;">
          <i class="bi bi-send-check fs-3"></i>
        </div>
        <h5 class="fw-bold">2. Submit Your Booking</h5>
        <p class="text-muted small">We match you with the right mechanic and confirm your appointment time.</p>
      </div>
      <div class="col-md-4">
        <div class="rounded-circle bg-dark text-warning d-inline-flex align-items-center justify-content-center mb-3" style="width:64px;height:64px;">
          <i class="bi bi-tools fs-3"></i>
        </div>
        <h5 class="fw-bold">3. Get It Sorted</h5>
        <p class="text-muted small">Meet your mechanic at the garage, pay directly there, no middleman.</p>
      </div>
    </div>
  </div>
</section>

<!-- ================= FAULTS ================= -->
<section class="py-5">
  <div class="container">
    <h2 class="fw-bold mb-1">What's wrong with your vehicle?</h2>
    <p class="text-muted mb-4">Pick a fault, we'll match you to a specialist mechanic.</p>
    <div class="row g-3">
      <?php foreach($faults as $f): ?>
        <div class="col-6 col-md-3 col-lg-2">
          <a href="book.php?fault=<?= $f['fault_id'] ?>" class="text-decoration-none">
            <div class="card fault-card text-center p-3 h-100">
              <i class="bi bi-wrench-adjustable-circle text-primary"></i>
              <div class="fault-name mt-2"><?= htmlspecialchars($f['name']) ?></div>
            </div>
          </a>
        </div>
      <?php endforeach; ?>
      <?php if(!$faults): ?>
        <div class="col-12 text-muted small">No faults configured yet.</div>
      <?php endif; ?>
    </div>
  </div>
</section>

<!-- ================= BRANDS ================= -->
<section class="py-5 bg-white">
  <div class="container">
    <div class="d-flex flex-wrap align-items-end justify-content-between mb-4">
      <h2 class="fw-bold mb-0">Browse by Vehicle Brand</h2>
      <?php if($brandCount > $brandsShown): ?>
        <a href="brands.php" class="small fw-semibold">View All Brands <i class="bi bi-arrow-right"></i></a>
      <?php endif; ?>
    </div>
    <div class="row g-4">
      <?php foreach($brands as $b): ?>
        <div class="col-6 col-md-4 col-lg-3">
          <!-- NOTE: no h-100 here — brand-card has its own fixed height -->
          <div class="card brand-card">
            <div class="brand-logo-wrap">
              <?php if($b['logo_path']): ?>
                <img src="<?= UPLOAD_URL.htmlspecialchars($b['logo_path']) ?>"
                     alt="<?= htmlspecialchars($b['name']) ?>">
              <?php else: ?>
                <div class="brand-badge" style="background:<?= brand_badge_color($b['name']) ?>">
                  <?= strtoupper(substr($b['name'],0,2)) ?>
                </div>
              <?php endif; ?>
            </div>
            <div class="brand-name"><?= htmlspecialchars($b['name']) ?></div>
          </div>
        </div>
      <?php endforeach; ?>
      <?php if(!$brands): ?>
        <div class="col-12 text-muted small">No brands configured yet.</div>
      <?php endif; ?>
    </div>
  </div>
</section>

<?php if($testimonials): ?>
<!-- ================= TESTIMONIALS ================= -->
<section class="py-5">
  <div class="container">
    <h2 class="fw-bold mb-4 text-center">What Our Clients Say</h2>
    <div class="row g-4">
      <?php foreach($testimonials as $t): ?>
        <div class="col-md-4">
          <div class="card h-100 p-4">
            <div class="text-warning mb-2"><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i></div>
            <p class="fst-italic">"<?= htmlspecialchars($t['message']) ?>"</p>
            <div class="fw-bold mt-auto"><?= htmlspecialchars($t['name']) ?></div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<?php include __DIR__.'/includes/footer.php'; ?>