<?php
// Buffer all output from here on, so that pages which include this header
// and THEN run a delete/update action further down (header('Location:...'))
// don't hit "headers already sent" — a real bug found live across every
// admin list page (feedback.php, bookings.php, garages.php, etc.).
ob_start();

// Robust path resolution - always find config/db.php no matter what folder we're in
$ROOT = dirname(__DIR__, 2); // goes up 2 levels from admin/includes/ → vehicle_care/

require_once $ROOT . '/config/db.php';
require_once $ROOT . '/includes/auth.php';

require_admin();

$pageTitle = $pageTitle ?? 'Admin';
$currentPage = basename($_SERVER['SCRIPT_NAME']);

// Light-weight counts for sidebar badges, computed fresh on every page load
// (not push/real-time, just current state, reusing the status columns that
// already exist rather than adding new "read" tracking).
$navCounts = [
    'bookings' => (int)$pdo->query("SELECT COUNT(*) c FROM bookings WHERE status='pending'")->fetch()['c'],
    'feedback' => (int)$pdo->query("SELECT COUNT(*) c FROM feedback WHERE status='new'")->fetch()['c'],
    'chat'     => (int)$pdo->query("SELECT COUNT(*) c FROM chat_sessions WHERE status='open'")->fetch()['c'],
];

// So the topbar avatar reflects a profile photo change immediately, not just after re-login.
$meStmt = $pdo->prepare("SELECT photo_path FROM users WHERE user_id=?");
$meStmt->execute([(int)$_SESSION['user']['id']]);
$myPhoto = $meStmt->fetchColumn();
?>
<!DOCTYPE html><html><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= htmlspecialchars($pageTitle) ?> | Admin</title>
<link rel="icon" type="image/svg+xml" href="<?= SITE_URL ?>/assets/favicon.svg">
<link href="<?= SITE_URL ?>/assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
<link href="<?= SITE_URL ?>/assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
<link href="<?= SITE_URL ?>/assets/css/style.css" rel="stylesheet">
<style>
body{background:#f4f6f9;}
.admin-sidebar{min-height:100vh;background:#1f2937;width:220px;flex-shrink:0;}
.admin-sidebar .brand-header{
  height:64px;display:flex;align-items:center;gap:.6rem;padding:0 18px;
  color:#fff;font-weight:700;font-size:1.05rem;
  border-bottom:1px solid rgba(255,255,255,.12);
}
.admin-sidebar a{color:#cbd5e1;text-decoration:none;display:flex;align-items:center;gap:.6rem;padding:11px 18px;border-left:3px solid transparent;font-size:.92rem;}
.admin-sidebar a:hover,.admin-sidebar a.active{background:#111827;color:#fff;border-left-color:#f59e0b;}
.admin-sidebar h6{color:#64748b;padding:14px 18px 4px;font-size:.7rem;text-transform:uppercase;letter-spacing:1px;margin:0;}
.admin-sidebar .nav-badge{margin-left:auto;background:#f59e0b;color:#111827;font-size:.7rem;font-weight:700;padding:1px 7px;border-radius:10px;}
@media (max-width:768px){ .admin-sidebar .nav-badge{display:none;} }
main{padding:22px;}
.topbar{background:#fff;height:64px;display:flex;align-items:center;justify-content:space-between;padding:0 22px;border-bottom:1px solid #e5e7eb;}
@media (max-width:768px){
  .admin-sidebar{width:64px;}
  .admin-sidebar .brand-header span, .admin-sidebar .nav-label, .admin-sidebar h6{display:none;}
  .admin-sidebar a{justify-content:center;padding:12px 0;}
  .admin-sidebar .brand-header{justify-content:center;padding:0;}
}
</style>
</head><body>
<div class="d-flex">
  <div class="admin-sidebar">
    <div class="brand-header">
      <img src="<?= SITE_URL ?>/assets/favicon.svg" width="26" height="26" alt="">
      <span><?= SITE_NAME ?></span>
    </div>
    <a href="dashboard.php" class="<?= $currentPage=='dashboard.php'?'active':'' ?>"><i class="bi bi-speedometer2"></i><span class="nav-label">Dashboard</span></a>
    <h6>Operations</h6>
    <a href="bookings.php?status=pending" class="<?= $currentPage=='bookings.php'||$currentPage=='booking_view.php'?'active':'' ?>">
      <i class="bi bi-calendar-check"></i><span class="nav-label">Bookings</span>
      <?php if($navCounts['bookings']): ?><span class="nav-badge"><?= $navCounts['bookings'] ?></span><?php endif; ?>
    </a>
    <a href="mechanics.php" class="<?= $currentPage=='mechanics.php'||$currentPage=='mechanic_edit.php'?'active':'' ?>"><i class="bi bi-person-gear"></i><span class="nav-label">Mechanics</span></a>
    <a href="garages.php" class="<?= $currentPage=='garages.php'||$currentPage=='garage_edit.php'?'active':'' ?>"><i class="bi bi-shop"></i><span class="nav-label">Garages</span></a>
    <h6>Catalog</h6>
    <a href="parts.php" class="<?= $currentPage=='parts.php'||$currentPage=='part_edit.php'?'active':'' ?>"><i class="bi bi-box-seam"></i><span class="nav-label">Spare Parts</span></a>
    <a href="faults.php" class="<?= $currentPage=='faults.php'?'active':'' ?>"><i class="bi bi-wrench"></i><span class="nav-label">Faults / Services</span></a>
    <a href="services.php" class="<?= $currentPage=='services.php'||$currentPage=='service_edit.php'?'active':'' ?>"><i class="bi bi-tools"></i><span class="nav-label">Services</span></a>
    <a href="brands.php" class="<?= $currentPage=='brands.php'?'active':'' ?>"><i class="bi bi-car-front"></i><span class="nav-label">Brands</span></a>
    <h6>Communication</h6>
    <a href="chat.php" class="<?= $currentPage=='chat.php'?'active':'' ?>">
      <i class="bi bi-chat-dots"></i><span class="nav-label">Live Chat</span>
      <?php if($navCounts['chat']): ?><span class="nav-badge"><?= $navCounts['chat'] ?></span><?php endif; ?>
    </a>
    <a href="feedback.php" class="<?= $currentPage=='feedback.php'?'active':'' ?>">
      <i class="bi bi-envelope"></i><span class="nav-label">Feedback</span>
      <?php if($navCounts['feedback']): ?><span class="nav-badge"><?= $navCounts['feedback'] ?></span><?php endif; ?>
    </a>
  </div>
  <div class="flex-grow-1" style="min-width:0;">
    <div class="topbar">
      <strong><?= htmlspecialchars($pageTitle) ?></strong>
      <div class="dropdown">
        <button class="btn btn-light btn-sm dropdown-toggle d-flex align-items-center gap-2" type="button" data-bs-toggle="dropdown" aria-expanded="false">
          <?php if($myPhoto): ?>
            <img src="<?= UPLOAD_URL.htmlspecialchars($myPhoto) ?>" style="width:22px;height:22px;object-fit:cover;border-radius:50%;">
          <?php else: ?>
            <i class="bi bi-person-circle"></i>
          <?php endif; ?>
          <span class="d-none d-sm-inline"><?= htmlspecialchars($_SESSION['user']['name']) ?></span>
        </button>
        <ul class="dropdown-menu dropdown-menu-end">
          <li><a class="dropdown-item" href="profile.php"><i class="bi bi-person-badge"></i> My Profile</a></li>
          <li><a class="dropdown-item" href="<?= SITE_URL ?>/index.php" target="_blank"><i class="bi bi-box-arrow-up-right"></i> View Site</a></li>
          <li><hr class="dropdown-divider"></li>
          <li><a class="dropdown-item text-danger" href="logout.php"><i class="bi bi-box-arrow-right"></i> Logout</a></li>
        </ul>
      </div>
    </div>
    <main>