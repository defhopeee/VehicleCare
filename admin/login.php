<?php
require_once __DIR__.'/../config/db.php';
if (session_status()===PHP_SESSION_NONE) session_start();

$err='';
$maxAttempts = 5;
$lockoutSeconds = 60;
$_SESSION['login_attempts'] = $_SESSION['login_attempts'] ?? 0;
$_SESSION['login_locked_until'] = $_SESSION['login_locked_until'] ?? 0;
$locked = time() < $_SESSION['login_locked_until'];

if($_SERVER['REQUEST_METHOD']==='POST' && !$locked){
    $u = trim($_POST['username']??'');
    $p = $_POST['password']??'';
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username=? AND role='admin' LIMIT 1");
    $stmt->execute([$u]);
    $user = $stmt->fetch();
    if($user && password_verify($p,$user['password_hash'])){
        session_regenerate_id(true);
        $_SESSION['user'] = ['id'=>$user['user_id'],'name'=>$user['full_name'],'role'=>$user['role']];
        unset($_SESSION['login_attempts'], $_SESSION['login_locked_until']);
        header('Location: dashboard.php'); exit;
    } else {
        $_SESSION['login_attempts']++;
        if($_SESSION['login_attempts'] >= $maxAttempts){
            $_SESSION['login_locked_until'] = time() + $lockoutSeconds;
            $_SESSION['login_attempts'] = 0;
        }
        $err='Invalid username or password';
    }
}
$locked = time() < $_SESSION['login_locked_until'];
$waitSeconds = $locked ? ($_SESSION['login_locked_until'] - time()) : 0;

$stats = [
    'garages'  => (int)$pdo->query("SELECT COUNT(*) c FROM garages")->fetch()['c'],
    'mechanics'=> (int)$pdo->query("SELECT COUNT(*) c FROM mechanics")->fetch()['c'],
    'bookings' => (int)$pdo->query("SELECT COUNT(*) c FROM bookings")->fetch()['c'],
];
?>
<!DOCTYPE html><html><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Admin Login | <?= SITE_NAME ?></title>
<link rel="icon" type="image/svg+xml" href="<?= SITE_URL ?>/assets/favicon.svg">
<link href="<?= SITE_URL ?>/assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
<link href="<?= SITE_URL ?>/assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
<link href="<?= SITE_URL ?>/assets/css/style.css" rel="stylesheet">
<style>
body{min-height:100vh;}
.login-split{min-height:100vh;}
.login-brand{
  background:
    radial-gradient(circle at 20% 20%, rgba(245,158,11,.25), transparent 45%),
    radial-gradient(circle at 85% 80%, rgba(245,158,11,.18), transparent 50%),
    linear-gradient(135deg, #111827 0%, #1f2937 55%, #111827 100%);
  color:#fff;
  min-height:42vh;
}
@media (min-width:768px){ .login-brand{ min-height:100vh; } }
.login-form-side{ background:#f7f8fa; min-height:58vh; }
@media (min-width:768px){ .login-form-side{ min-height:100vh; } }
.login-feature{ display:flex; align-items:flex-start; gap:.75rem; margin-bottom:1rem; text-align:left; }
.login-feature i{ font-size:1.25rem; color:#f59e0b; margin-top:.15rem; }
.login-feature strong{ display:block; }
.login-feature span{ font-size:.85rem; opacity:.8; }
.login-stats{ display:flex; gap:1.5rem; margin-top:1.5rem; padding-top:1.5rem; border-top:1px solid rgba(255,255,255,.15); }
.login-stats div strong{ display:block; font-size:1.3rem; }
.login-stats div span{ font-size:.72rem; text-transform:uppercase; letter-spacing:.4px; opacity:.75; }
.login-icon-input{ background:#1f2937; color:#f59e0b; border-color:#1f2937; }
</style>
</head><body>
<div class="row g-0 login-split">
  <div class="col-md-6 login-brand d-flex align-items-center justify-content-center p-4 p-lg-5">
    <div style="max-width:380px;">
      <img src="<?= SITE_URL ?>/assets/favicon.svg" width="48" height="48" alt="">
      <h3 class="fw-bold mt-3 mb-1"><?= SITE_NAME ?> Admin</h3>
      <p class="mb-4" style="opacity:.85;">Everything needed to run a nationwide garage and parts network from one dashboard.</p>

      <div class="login-feature">
        <i class="bi bi-calendar-check"></i>
        <div><strong>Bookings</strong><span>Track every repair, inspection and parts request end to end.</span></div>
      </div>
      <div class="login-feature">
        <i class="bi bi-shop"></i>
        <div><strong>Garages &amp; Mechanics</strong><span>Manage your nationwide network and specialist staff.</span></div>
      </div>
      <div class="login-feature">
        <i class="bi bi-box-seam"></i>
        <div><strong>Spare Parts Catalog</strong><span>Keep stock, pricing and categories up to date.</span></div>
      </div>
      <div class="login-feature">
        <i class="bi bi-chat-dots"></i>
        <div><strong>Live Chat &amp; Feedback</strong><span>Respond to customers directly from the dashboard.</span></div>
      </div>

      <div class="login-stats">
        <div><strong><?= $stats['garages'] ?></strong><span>Garages</span></div>
        <div><strong><?= $stats['mechanics'] ?></strong><span>Mechanics</span></div>
        <div><strong><?= $stats['bookings'] ?></strong><span>Bookings</span></div>
      </div>
    </div>
  </div>
  <div class="col-md-6 login-form-side d-flex align-items-center justify-content-center p-4">
    <div style="max-width:380px;width:100%;">
      <a href="<?= SITE_URL ?>/index.php" class="d-inline-flex align-items-center gap-1 text-decoration-none text-muted small mb-4">
        <i class="bi bi-arrow-left"></i> Back to <?= SITE_NAME ?>
      </a>
      <h4 class="fw-bold mb-1"><i class="bi bi-shield-lock text-primary"></i> Admin Sign In</h4>
      <p class="text-muted small mb-4">Enter your credentials to access the dashboard.</p>

      <?php if($locked): ?>
        <div class="alert alert-warning py-2"><i class="bi bi-hourglass-split"></i> Too many failed attempts. Try again in <span id="waitCount"><?= $waitSeconds ?></span>s.</div>
      <?php elseif($err): ?>
        <div class="alert alert-danger py-2"><?= htmlspecialchars($err) ?></div>
      <?php endif; ?>

      <form method="post" id="loginForm" class="<?= $locked?'opacity-50':'' ?>">
        <div class="mb-3">
          <label class="form-label">Username</label>
          <div class="input-group">
            <span class="input-group-text login-icon-input"><i class="bi bi-person-fill"></i></span>
            <input name="username" class="form-control" required autofocus <?= $locked?'disabled':'' ?>>
          </div>
        </div>
        <div class="mb-3">
          <label class="form-label">Password</label>
          <div class="input-group">
            <span class="input-group-text login-icon-input"><i class="bi bi-shield-lock-fill"></i></span>
            <input type="password" name="password" id="pwInput" class="form-control" required <?= $locked?'disabled':'' ?>>
            <button type="button" class="btn btn-outline-secondary" id="pwToggle" <?= $locked?'disabled':'' ?>><i class="bi bi-eye" id="pwIcon"></i></button>
          </div>
        </div>
        <button class="btn btn-warning w-100 fw-bold py-2" <?= $locked?'disabled':'' ?>>Sign In</button>
      </form>
    </div>
  </div>
</div>
<script src="<?= SITE_URL ?>/assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script>
document.getElementById('pwToggle')?.addEventListener('click', function(){
  const input = document.getElementById('pwInput');
  const icon  = document.getElementById('pwIcon');
  const show  = input.type === 'password';
  input.type = show ? 'text' : 'password';
  icon.className = show ? 'bi bi-eye-slash' : 'bi bi-eye';
});
<?php if($locked): ?>
let left = <?= $waitSeconds ?>;
const waitEl = document.getElementById('waitCount');
const t = setInterval(() => {
  left--;
  if (waitEl) waitEl.textContent = left;
  if (left <= 0) { clearInterval(t); location.reload(); }
}, 1000);
<?php endif; ?>
</script>
</body></html>
