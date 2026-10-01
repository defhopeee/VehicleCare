// ===== ADMIN LIVE CHAT (fixed: no duplicate messages) =====
(function () {
  const cfg = window.ADMIN_CHAT || {};
  const box = document.getElementById('adminChatMessages');
  if (!box || !cfg.session) {
    console.warn('[admin_chat] missing box or session', cfg);
    return;
  }

  const API = new URL('admin_chat_api.php', window.location.href).href;
  let lastId = 0;
  let sending = false;
  const rendered = new Set();   // <-- prevents duplicate rendering

  console.log('[admin_chat] API =', API, 'session =', cfg.session);

  function esc(s){
    return String(s).replace(/[&<>"']/g, c => (
      {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]
    ));
  }

  function render(m){
    if (m.message_id && rendered.has(m.message_id)) return;   // already shown
    if (m.message_id) rendered.add(m.message_id);

    const wrap = document.createElement('div');
    wrap.className = 'mb-2 d-flex ' + (m.sender === 'admin' ? 'justify-content-end' : 'justify-content-start');
    const bubbleClass = m.sender === 'admin' ? 'bg-warning' : 'bg-white border';
    const time = m.created_at
      ? new Date(String(m.created_at).replace(' ', 'T')).toLocaleTimeString()
      : '';
    wrap.innerHTML = `<div class="p-2 px-3 rounded-3 ${bubbleClass}" style="max-width:75%">
        <div>${esc(m.message)}</div>
        <small class="text-muted">${time}</small>
      </div>`;
    box.appendChild(wrap);
    box.scrollTop = box.scrollHeight;
  }

  function showError(text){
    const err = document.createElement('div');
    err.className = 'alert alert-danger py-1 px-2 small mb-2';
    err.textContent = '⚠ ' + text;
    box.appendChild(err);
    box.scrollTop = box.scrollHeight;
    console.error('[admin_chat]', text);
  }

  async function fetchMsgs(){
    try {
      const url = `${API}?action=fetch&session_id=${cfg.session}&after=${lastId}`;
      const r = await fetch(url, { credentials: 'same-origin' });
      const text = await r.text();
      let data;
      try { data = JSON.parse(text); }
      catch (e) { showError('Server returned non-JSON: ' + text.substring(0, 200)); return; }
      if (data && data.error) { showError(data.error); return; }
      if (!Array.isArray(data)) return;

      data.forEach(m => {
        render(m);
        if (m.message_id && m.message_id > lastId) lastId = m.message_id;
      });
    } catch (e) { showError('Fetch failed: ' + e.message); }
  }

  async function sendMsg(){
    if (sending) return;
    const inp = document.getElementById('adminMsgInput');
    if (!inp) return;
    const msg = inp.value.trim();
    if (!msg) return;

    sending = true;
    inp.value = '';

    const btn = document.getElementById('adminSendBtn');
    const oldHtml = btn ? btn.innerHTML : '';
    if (btn) { btn.disabled = true; btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span>'; }

    try {
      const fd = new FormData();
      fd.append('action', 'send');
      fd.append('session_id', cfg.session);
      fd.append('message', msg);
      const r = await fetch(API, { method: 'POST', body: fd, credentials: 'same-origin' });
      const text = await r.text();
      let data;
      try { data = JSON.parse(text); }
      catch (e) { showError('Send: server returned non-JSON: ' + text.substring(0, 200)); return; }
      if (data.error) { showError(data.error); return; }

      // Now fetch: this is the ONLY place the message gets rendered
      await fetchMsgs();
    } catch (e) {
      showError('Send failed: ' + e.message);
    } finally {
      sending = false;
      if (btn) { btn.disabled = false; btn.innerHTML = oldHtml; }
    }
  }

  document.getElementById('adminSendBtn')?.addEventListener('click', sendMsg);
  document.getElementById('adminMsgInput')?.addEventListener('keypress', e => {
    if (e.key === 'Enter') { e.preventDefault(); sendMsg(); }
  });

  fetchMsgs();
  setInterval(fetchMsgs, 3000);
})();