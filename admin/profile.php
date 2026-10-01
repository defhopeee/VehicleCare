<?php
$pageTitle = 'My Profile';
require_once __DIR__.'/includes/admin_header.php';

$uid = (int)$_SESSION['user']['id'];
$stmt = $pdo->prepare("SELECT * FROM users WHERE user_id=?");
$stmt->execute([$uid]);
$me = $stmt->fetch();

$err = ''; $ok = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $formType = $_POST['form_type'] ?? '';

    if ($formType === 'details') {
        $username  = trim($_POST['username'] ?? '');
        $full_name = trim($_POST['full_name'] ?? '');
        $email     = trim($_POST['email'] ?? '');
        $phone     = trim($_POST['phone'] ?? '');

        if ($username === '' || $full_name === '') {
            $err = 'Username and full name are required';
        } elseif ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $err = 'Please enter a valid email address';
        } else {
            $dupe = $pdo->prepare("SELECT user_id FROM users WHERE username=? AND user_id<>?");
            $dupe->execute([$username, $uid]);
            if ($dupe->fetch()) {
                $err = 'That username is already taken';
            } else {
                $photo = $me['photo_path'];
                if (!empty($_FILES['photo']['name'])) {
                    $ext = strtolower(pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION));
                    if (in_array($ext, ['jpg','jpeg','png','webp','gif'])) {
                        $photo = 'admin_'.uniqid().'.'.$ext;
                        move_uploaded_file($_FILES['photo']['tmp_name'], UPLOAD_DIR.$photo);
                        if ($me['photo_path'] && file_exists(UPLOAD_DIR.$me['photo_path'])) @unlink(UPLOAD_DIR.$me['photo_path']);
                    }
                } elseif (!empty($_POST['remove_photo']) && $me['photo_path']) {
                    if (file_exists(UPLOAD_DIR.$me['photo_path'])) @unlink(UPLOAD_DIR.$me['photo_path']);
                    $photo = null;
                }
                $pdo->prepare("UPDATE users SET username=?, full_name=?, email=?, phone=?, photo_path=? WHERE user_id=?")
                    ->execute([$username, $full_name, $email ?: null, $phone ?: null, $photo, $uid]);
                $_SESSION['user']['name'] = $full_name;
                $ok = 'Profile updated';
                $stmt->execute([$uid]); $me = $stmt->fetch();
            }
        }
    }

    if ($formType === 'password') {
        $current = $_POST['current_password'] ?? '';
        $new1    = $_POST['new_password'] ?? '';
        $new2    = $_POST['confirm_password'] ?? '';

        if (!password_verify($current, $me['password_hash'])) {
            $err = 'Current password is incorrect';
        } elseif (strlen($new1) < 6) {
            $err = 'New password must be at least 6 characters';
        } elseif ($new1 !== $new2) {
            $err = 'New passwords do not match';
        } else {
            $pdo->prepare("UPDATE users SET password_hash=? WHERE user_id=?")
                ->execute([password_hash($new1, PASSWORD_DEFAULT), $uid]);
            $ok = 'Password changed';
        }
    }
}
?>
<div class="row g-3">
  <div class="col-lg-7">
    <div class="card p-4 mb-3">
      <h5 class="fw-bold mb-3"><i class="bi bi-person-badge"></i> Profile Details</h5>
      <?php if($err): ?><div class="alert alert-danger py-2"><?= htmlspecialchars($err) ?></div><?php endif; ?>
      <?php if($ok): ?><div class="alert alert-success py-2 alert-auto"><?= htmlspecialchars($ok) ?></div><?php endif; ?>

      <form method="post" enctype="multipart/form-data" class="row g-3">
        <input type="hidden" name="form_type" value="details">
        <div class="col-12 d-flex align-items-center gap-3">
          <?php if($me['photo_path']): ?>
            <img src="<?= UPLOAD_URL.htmlspecialchars($me['photo_path']) ?>" style="width:64px;height:64px;object-fit:cover;border-radius:50%;">
          <?php else: ?>
            <div class="bg-light d-flex align-items-center justify-content-center" style="width:64px;height:64px;border-radius:50%;"><i class="bi bi-person fs-3 text-muted"></i></div>
          <?php endif; ?>
          <div class="flex-grow-1">
            <label class="form-label mb-1">Profile Picture</label>
            <input type="file" name="photo" class="form-control" accept="image/*">
            <?php if($me['photo_path']): ?>
              <div class="form-check mt-2">
                <input type="checkbox" name="remove_photo" value="1" class="form-check-input" id="removePhoto">
                <label class="form-check-label small text-muted" for="removePhoto">Remove current photo</label>
              </div>
            <?php endif; ?>
          </div>
        </div>
        <div class="col-md-6"><label class="form-label">Username *</label><input name="username" class="form-control" required value="<?= htmlspecialchars($me['username']) ?>"></div>
        <div class="col-md-6"><label class="form-label">Full Name *</label><input name="full_name" class="form-control" required value="<?= htmlspecialchars($me['full_name']) ?>"></div>
        <div class="col-md-6"><label class="form-label">Email</label><input type="email" name="email" class="form-control" value="<?= htmlspecialchars($me['email'] ?? '') ?>"></div>
        <div class="col-md-6"><label class="form-label">Phone</label><input name="phone" class="form-control" value="<?= htmlspecialchars($me['phone'] ?? '') ?>"></div>
        <div class="col-12 text-end"><button class="btn btn-warning fw-bold"><i class="bi bi-save"></i> Save Changes</button></div>
      </form>
    </div>
  </div>

  <div class="col-lg-5">
    <div class="card p-4">
      <h5 class="fw-bold mb-3"><i class="bi bi-shield-lock"></i> Change Password</h5>
      <form method="post" class="row g-3">
        <input type="hidden" name="form_type" value="password">
        <div class="col-12"><label class="form-label">Current Password *</label><input type="password" name="current_password" class="form-control" required></div>
        <div class="col-12"><label class="form-label">New Password *</label><input type="password" name="new_password" class="form-control" required minlength="6"></div>
        <div class="col-12"><label class="form-label">Confirm New Password *</label><input type="password" name="confirm_password" class="form-control" required minlength="6"></div>
        <div class="col-12 text-end"><button class="btn btn-outline-primary fw-bold"><i class="bi bi-key"></i> Update Password</button></div>
      </form>
    </div>
  </div>
</div>
<?php require_once __DIR__.'/includes/admin_footer.php'; ?>
