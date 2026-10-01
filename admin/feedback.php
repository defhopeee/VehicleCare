<?php
$pageTitle='Feedback & Complaints';
require_once __DIR__.'/includes/admin_header.php';

if($_GET['delete'] ?? false){
    $pdo->prepare("DELETE FROM feedback WHERE feedback_id=?")->execute([(int)$_GET['delete']]);
    header('Location: feedback.php?msg=deleted'); exit;
}
if(($_GET['mark'] ?? false) && ($_GET['status'] ?? false)){
    $pdo->prepare("UPDATE feedback SET status=? WHERE feedback_id=?")->execute([$_GET['status'], (int)$_GET['mark']]);
    header('Location: feedback.php'); exit;
}
$rows=$pdo->query("SELECT * FROM feedback ORDER BY feedback_id DESC")->fetchAll();
?>
<?php if($_GET['msg'] ?? false): ?><div class="alert alert-success alert-auto"><?= htmlspecialchars($_GET['msg']) ?></div><?php endif; ?>
<h4 class="fw-bold mb-3">Feedback / Complaints (<?= count($rows) ?>)</h4>
<div class="card p-3">
  <table class="table table-hover align-middle">
    <thead><tr><th>Date</th><th>Name</th><th>Contact</th><th>Type</th><th>Subject</th><th>Message</th><th>Status</th><th></th></tr></thead>
    <tbody>
    <?php foreach($rows as $r): ?>
      <tr>
        <td class="small"><?= $r['created_at'] ?></td>
        <td><?= htmlspecialchars($r['name']) ?></td>
        <td class="small">
          <?= htmlspecialchars($r['email']) ?><br>
          <?= htmlspecialchars($r['phone']) ?>
        </td>
        <td><span class="badge bg-<?= ['feedback'=>'primary','complaint'=>'danger','inquiry'=>'info'][$r['type']] ?>"><?= $r['type'] ?></span></td>
        <td><?= htmlspecialchars($r['subject']) ?></td>
        <td class="small" style="max-width:320px;"><?= nl2br(htmlspecialchars($r['message'])) ?></td>
        <td>
          <span class="badge bg-<?= ['new'=>'warning','read'=>'secondary','resolved'=>'success'][$r['status']] ?>"><?= $r['status'] ?></span>
        </td>
        <td class="text-end">
          <?php if($r['status']!=='read'): ?><a href="?mark=<?= $r['feedback_id'] ?>&status=read" class="btn btn-sm btn-outline-secondary">Read</a><?php endif; ?>
          <?php if($r['status']!=='resolved'): ?><a href="?mark=<?= $r['feedback_id'] ?>&status=resolved" class="btn btn-sm btn-outline-success">Resolve</a><?php endif; ?>
          <a href="?delete=<?= $r['feedback_id'] ?>" data-confirm="Delete this feedback message? This cannot be undone." class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></a>
        </td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
</div>
<?php require_once __DIR__.'/includes/admin_footer.php'; ?>