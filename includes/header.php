<?php require_once __DIR__.'/../config/db.php'; require_once __DIR__.'/auth.php'; ?>
<!DOCTYPE html><html lang="en"><head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= isset($pageTitle)? htmlspecialchars($pageTitle).' | '.SITE_NAME : SITE_NAME ?></title>
<link rel="icon" type="image/svg+xml" href="<?= SITE_URL ?>/assets/favicon.svg">
<link href="<?= SITE_URL ?>/assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
<link href="<?= SITE_URL ?>/assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
<link href="<?= SITE_URL ?>/assets/css/style.css" rel="stylesheet">
</head><body>

<nav class="navbar navbar-dark bg-dark sticky-top shadow-sm">
  <div class="container">
    <a class="navbar-brand fw-bold d-flex align-items-center gap-2" href="<?= SITE_URL ?>/index.php">
      <img src="<?= SITE_URL ?>/assets/favicon.svg" width="28" height="28" alt="<?= SITE_NAME ?>">
      <?= SITE_NAME ?>
    </a>

    <!-- Desktop: normal horizontal links, hidden below lg -->
    <ul class="navbar-nav flex-row gap-1 d-none d-lg-flex align-items-center">
      <li class="nav-item"><a class="nav-link" href="<?= SITE_URL ?>/garages.php">Garages</a></li>
      <li class="nav-item"><a class="nav-link" href="<?= SITE_URL ?>/parts.php">Parts</a></li>
      <li class="nav-item"><a class="nav-link" href="<?= SITE_URL ?>/mechanics.php">Mechanics</a></li>
      <li class="nav-item"><a class="nav-link" href="<?= SITE_URL ?>/services.php">Services</a></li>
      <li class="nav-item"><a class="nav-link" href="<?= SITE_URL ?>/chat.php"><i class="bi bi-chat-dots"></i> Contact</a></li>
      <li class="nav-item ms-2"><a class="btn btn-warning btn-sm fw-bold" href="<?= SITE_URL ?>/book.php">Book Now</a></li>
    </ul>

    <!-- Mobile: hamburger opens a drawer from the right, hidden at lg and up -->
    <button class="navbar-toggler d-lg-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#navDrawer" aria-controls="navDrawer" aria-label="Open menu">
      <span class="navbar-toggler-icon"></span>
    </button>
  </div>
</nav>

<div class="offcanvas offcanvas-end text-bg-dark" tabindex="-1" id="navDrawer" aria-labelledby="navDrawerLabel">
  <div class="offcanvas-header border-bottom border-secondary">
    <h5 class="offcanvas-title fw-bold d-flex align-items-center gap-2" id="navDrawerLabel">
      <img src="<?= SITE_URL ?>/assets/favicon.svg" width="24" height="24" alt=""> <?= SITE_NAME ?>
    </h5>
    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Close"></button>
  </div>
  <div class="offcanvas-body">
    <ul class="navbar-nav">
      <li class="nav-item"><a class="nav-link" href="<?= SITE_URL ?>/index.php">Home</a></li>
      <li class="nav-item"><a class="nav-link" href="<?= SITE_URL ?>/garages.php">Garages</a></li>
      <li class="nav-item"><a class="nav-link" href="<?= SITE_URL ?>/parts.php">Parts</a></li>
      <li class="nav-item"><a class="nav-link" href="<?= SITE_URL ?>/mechanics.php">Mechanics</a></li>
      <li class="nav-item"><a class="nav-link" href="<?= SITE_URL ?>/services.php">Services</a></li>
      <li class="nav-item"><a class="nav-link" href="<?= SITE_URL ?>/chat.php"><i class="bi bi-chat-dots"></i> Contact</a></li>
      <li class="nav-item mt-3"><a class="btn btn-warning fw-bold w-100" href="<?= SITE_URL ?>/book.php">Book Now</a></li>
    </ul>
  </div>
</div>

<main class="pb-5">