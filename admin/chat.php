<?php
$pageTitle = 'Live Chat';
require_once __DIR__ . '/includes/admin_header.php';

if (($_GET['close'] ?? false)) {
    $pdo->prepare("UPDATE chat_sessions SET status='closed' WHERE session_id=?")->execute([(int)$_GET['close']]);
    header('Location: chat.php?sid='.(int)$_GET['close']); exit;
}
if (($_GET['reopen'] ?? false)) {
    $pdo->prepare("UPDATE chat_sessions SET status='open' WHERE session_id=?")->execute([(int)$_GET['reopen']]);
    header('Location: chat.php?sid='.(int)$_GET['reopen']); exit;
}

// Load all chat sessions, newest activity first, with unread count and last message
$sessions = $pdo->query("
    SELECT s.*,
        (SELECT COUNT(*) FROM chat_messages
         WHERE session_id = s.session_id AND sender = 'client' AND is_read = 0) AS unread,
        (SELECT message FROM chat_messages
         WHERE session_id = s.session_id
         ORDER BY message_id DESC LIMIT 1) AS last_msg
    FROM chat_sessions s
    ORDER BY s.last_activity DESC
")->fetchAll();

// Figure out which session is currently selected
$sid = (int)($_GET['sid'] ?? 0);
if (!$sid && !empty($sessions)) {
    $sid = (int)$sessions[0]['session_id'];
}

$current = null;
foreach ($sessions as $s) {
    if ((int)$s['session_id'] === $sid) { $current = $s; break; }
}

// Mark client messages as read when admin opens the session
if ($current) {
    $pdo->prepare("UPDATE chat_messages SET is_read = 1
                   WHERE session_id = ? AND sender = 'client'")
        ->execute([$sid]);
}
?>

<div class="row g-3">
  <!-- ============ SESSIONS LIST ============ -->
  <div class="col-md-4">
    <div class="card">
      <div class="card-header fw-bold">
        <i class="bi bi-chat-dots"></i> Chat Sessions (<?= count($sessions) ?>)
      </div>
      <div class="list-group list-group-flush" style="max-height:75vh; overflow-y:auto;">
        <?php if (!$sessions): ?>
          <div class="p-3 text-muted small">No chat sessions yet.</div>
        <?php endif; ?>

        <?php foreach ($sessions as $s): ?>
          <a href="?sid=<?= (int)$s['session_id'] ?>"
             class="list-group-item list-group-item-action <?= ((int)$s['session_id'] === $sid) ? 'active' : '' ?>">
            <div class="d-flex justify-content-between align-items-center">
              <strong><?= htmlspecialchars($s['client_name']) ?></strong>
              <?php if ($s['unread'] > 0): ?>
                <span class="badge bg-danger rounded-pill"><?= (int)$s['unread'] ?></span>
              <?php endif; ?>
            </div>
            <div class="small text-truncate <?= ((int)$s['session_id'] === $sid) ? 'text-white-50' : 'text-muted' ?>">
              <?= htmlspecialchars($s['last_msg'] ?? 'No messages yet') ?>
            </div>
            <div class="small <?= ((int)$s['session_id'] === $sid) ? 'text-white-50' : 'text-muted' ?>">
              <?= htmlspecialchars($s['status']) ?> · <?= htmlspecialchars($s['last_activity']) ?>
            </div>
          </a>
        <?php endforeach; ?>
      </div>
    </div>
  </div>

  <!-- ============ CHAT PANEL ============ -->
  <div class="col-md-8">
    <div class="card">

      <?php if ($current): ?>

        <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
          <span>
            <i class="bi bi-person-circle"></i>
            <?= htmlspecialchars($current['client_name']) ?>
            <?php if ($current['client_email']): ?>
              <small class="text-muted">(<?= htmlspecialchars($current['client_email']) ?>)</small>
            <?php endif; ?>
          </span>
          <span class="d-flex align-items-center gap-2">
            <span class="badge bg-<?= $current['status'] === 'open' ? 'success' : 'secondary' ?>">
              <?= htmlspecialchars($current['status']) ?>
            </span>
            <?php if ($current['status'] === 'open'): ?>
              <a href="?sid=<?= $sid ?>&close=<?= $sid ?>" class="btn btn-sm btn-outline-light">Close Chat</a>
            <?php else: ?>
              <a href="?sid=<?= $sid ?>&reopen=<?= $sid ?>" class="btn btn-sm btn-outline-light">Reopen</a>
            <?php endif; ?>
          </span>
        </div>

        <div class="card-body" id="adminChatMessages"
             style="height:420px; overflow-y:auto; background:#f9fafb;"></div>

        <div class="card-footer">
          <div class="input-group">
            <input id="adminMsgInput" class="form-control"
                   placeholder="Type your reply and press Enter...">
            <button id="adminSendBtn" class="btn btn-warning fw-bold" type="button">
              <i class="bi bi-send"></i> Send
            </button>
          </div>
        </div>

      <?php else: ?>

        <div class="card-body text-center text-muted py-5">
          <i class="bi bi-chat-square-dots fs-1 d-block mb-2"></i>
          No chat selected. Pick a session on the left, or wait for a client to start one.
        </div>

      <?php endif; ?>

    </div>
  </div>
</div>

<?php if ($current): ?>
<script>
  window.ADMIN_CHAT = {
    api:      '<?= SITE_URL ?>/chat_api.php',
    session:  <?= (int)$sid ?>,
    adminApi: '<?= SITE_URL ?>/admin/admin_chat_api.php'
  };
  console.log('[chat.php] ADMIN_CHAT config:', window.ADMIN_CHAT);
</script>
<script src="<?= SITE_URL ?>/assets/js/admin_chat.js?v=<?= time() ?>"></script>
<?php endif; ?>

<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>