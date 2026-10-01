<?php
require_once __DIR__.'/config/db.php';
$pageTitle = 'Live Chat';
include __DIR__.'/includes/header.php';
?>
<div class="container py-4" style="max-width:600px">
  <h1 class="h3 fw-bold mb-1"><i class="bi bi-chat-dots text-primary"></i> Live Chat Support</h1>
  <p class="text-muted small mb-2">Messages refresh every few seconds, replies usually come within minutes.</p>
  <div class="d-flex gap-2 mb-3">
    <a href="tel:+254700000000" class="btn btn-sm btn-outline-secondary"><i class="bi bi-telephone"></i> Call Us</a>
    <a href="https://wa.me/254700000000" target="_blank" class="btn btn-sm btn-success"><i class="bi bi-whatsapp"></i> WhatsApp</a>
  </div>

  <div class="card" id="chatBox">
    <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center py-2">
      <span><i class="bi bi-headset"></i> Support</span>
      <span class="badge bg-success" id="chatStatus">Connecting...</span>
    </div>
    <div class="card-body" id="chatMessages" style="height:320px;overflow-y:auto;background:#f9fafb;"></div>
    <div class="card-footer">
      <div id="chatForm" class="d-none">
        <div class="input-group">
          <input id="msgInput" class="form-control" placeholder="Type your message...">
          <button id="sendBtn" class="btn btn-warning fw-bold"><i class="bi bi-send"></i></button>
        </div>
      </div>
      <div id="startForm">
        <div class="row g-2">
          <div class="col-md-6"><input id="cName" class="form-control" placeholder="Your name *"></div>
          <div class="col-md-6"><input id="cEmail" class="form-control" placeholder="Email (optional)"></div>
          <div class="col-12 text-end"><button id="startBtn" class="btn btn-primary"><i class="bi bi-chat"></i> Start Chat</button></div>
        </div>
      </div>
    </div>
  </div>
</div>
<script>window.CHAT_API = '<?= SITE_URL ?>/chat_api.php';</script>
<script src="<?= SITE_URL ?>/assets/js/chat.js"></script>
<?php include __DIR__.'/includes/footer.php'; ?>