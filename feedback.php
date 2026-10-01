<?php
require_once __DIR__.'/config/db.php';
$pageTitle = 'Feedback & Complaints';
$sent = false; $errors = [];
if($_SERVER['REQUEST_METHOD']==='POST'){
    $name    = trim($_POST['name'] ?? '');
    $email   = trim($_POST['email'] ?? '');
    $phone   = trim($_POST['phone'] ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');
    $type    = $_POST['type'] ?? 'feedback';

    if ($name === '')    $errors[] = 'Name is required';
    if ($message === '') $errors[] = 'Message is required';
    if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Please enter a valid email address';
    if (!in_array($type, ['feedback','complaint','inquiry'], true)) $type = 'feedback';

    if (!$errors) {
        $stmt = $pdo->prepare("INSERT INTO feedback (name,email,phone,subject,message,type) VALUES (?,?,?,?,?,?)");
        $stmt->execute([$name, $email ?: null, $phone ?: null, $subject ?: null, $message, $type]);
        $sent = true;
    }
}
include __DIR__.'/includes/header.php';
?>
<div class="container py-5" style="max-width:720px">
  <h1 class="fw-bold mb-1"><i class="bi bi-envelope-paper text-primary"></i> Feedback & Complaints</h1>
  <p class="text-muted">We read every message. Admin will get back to you.</p>

  <?php if($sent): ?>
    <div class="alert alert-success"><i class="bi bi-check-circle"></i> Thank you! We've received your message and will get back to you soon.</div>
  <?php endif; ?>

  <?php if($errors): ?>
    <div class="alert alert-danger">
      <ul class="mb-0"><?php foreach($errors as $e) echo '<li>'.htmlspecialchars($e).'</li>'; ?></ul>
    </div>
  <?php endif; ?>

  <?php if(!$sent): ?>
  <div class="card p-4">
    <form method="post" class="row g-3">
      <div class="col-md-6"><label class="form-label">Full Name *</label><input name="name" class="form-control" required value="<?= htmlspecialchars($_POST['name'] ?? '') ?>"></div>
      <div class="col-md-6"><label class="form-label">Email</label><input type="email" name="email" class="form-control" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"></div>
      <div class="col-md-6"><label class="form-label">Phone</label><input name="phone" class="form-control" value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>"></div>
      <div class="col-md-6"><label class="form-label">Type</label>
        <select name="type" class="form-select">
          <option value="feedback">Feedback</option>
          <option value="complaint">Complaint</option>
          <option value="inquiry">Inquiry</option>
        </select>
      </div>
      <div class="col-12"><label class="form-label">Subject</label><input name="subject" class="form-control" value="<?= htmlspecialchars($_POST['subject'] ?? '') ?>"></div>
      <div class="col-12"><label class="form-label">Message *</label><textarea name="message" rows="5" class="form-control" required><?= htmlspecialchars($_POST['message'] ?? '') ?></textarea></div>
      <div class="col-12 text-end"><button class="btn btn-warning fw-bold"><i class="bi bi-send"></i> Send</button></div>
    </form>
  </div>
  <?php endif; ?>
</div>
<?php include __DIR__.'/includes/footer.php'; ?>