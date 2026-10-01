// ===== CLIENT LIVE CHAT (fixed: no duplicate messages) =====
let chatSessionId = sessionStorage.getItem('chat_session');
let lastMsgId = 0;
let polling = null;
let sending = false;
const rendered = new Set();

function escapeHtml(s){
  return String(s).replace(/[&<>"']/g, c => (
    {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]
  ));
}

function renderMsg(m){
  if (m.message_id && rendered.has(m.message_id)) return;
  if (m.message_id) rendered.add(m.message_id);

  const div = document.createElement('div');
  div.className = 'mb-2 d-flex ' + (m.sender === 'client' ? 'justify-content-end' : 'justify-content-start');
  const time = m.created_at
    ? new Date(String(m.created_at).replace(' ', 'T')).toLocaleTimeString()
    : '';
  div.innerHTML = `<div class="p-2 px-3 rounded-3 ${m.sender === 'client' ? 'bg-warning' : 'bg-white border'}" style="max-width:75%">
      <div>${escapeHtml(m.message)}</div>
      <small class="text-muted">${time}</small>
    </div>`;
  document.getElementById('chatMessages').appendChild(div);
  document.getElementById('chatMessages').scrollTop = 1e9;
}

async function fetchMessages(){
  if (!chatSessionId) return;
  try {
    const r = await fetch(`${window.CHAT_API}?action=fetch&session_id=${chatSessionId}&after=${lastMsgId}`);
    const list = await r.json();
    if (!Array.isArray(list)) return;
    list.forEach(m => {
      renderMsg(m);
      if (m.message_id && m.message_id > lastMsgId) lastMsgId = m.message_id;
    });
  } catch (e) { console.error('[chat] fetch error', e); }
}

document.addEventListener('DOMContentLoaded', () => {
  const startForm = document.getElementById('startForm');
  const chatForm  = document.getElementById('chatForm');

  if (chatSessionId) {
    startForm.classList.add('d-none');
    chatForm.classList.remove('d-none');
    document.getElementById('chatStatus').textContent = 'Connected';
    fetchMessages();
    polling = setInterval(fetchMessages, 3000);
  }

  document.getElementById('startBtn')?.addEventListener('click', async () => {
    const name  = document.getElementById('cName').value.trim();
    const email = document.getElementById('cEmail').value.trim();
    if (!name) return alert('Enter your name');
    const fd = new FormData();
    fd.append('action', 'start');
    fd.append('name', name);
    fd.append('email', email);
    const r = await fetch(window.CHAT_API, { method: 'POST', body: fd });
    const d = await r.json();
    if (d.session_id) {
      chatSessionId = d.session_id;
      sessionStorage.setItem('chat_session', chatSessionId);
      startForm.classList.add('d-none');
      chatForm.classList.remove('d-none');
      document.getElementById('chatStatus').textContent = 'Connected';
      polling = setInterval(fetchMessages, 3000);
      fetchMessages();
    }
  });

  const send = async () => {
    if (sending) return;
    const input = document.getElementById('msgInput');
    const msg = input.value.trim();
    if (!msg) return;
    sending = true;
    input.value = '';
    try {
      const fd = new FormData();
      fd.append('action', 'send');
      fd.append('session_id', chatSessionId);
      fd.append('message', msg);
      await fetch(window.CHAT_API, { method: 'POST', body: fd });
      await fetchMessages();
    } catch (e) { console.error('[chat] send error', e); }
    finally { sending = false; }
  };

  document.getElementById('sendBtn')?.addEventListener('click', send);
  document.getElementById('msgInput')?.addEventListener('keypress', e => {
    if (e.key === 'Enter') { e.preventDefault(); send(); }
  });
});