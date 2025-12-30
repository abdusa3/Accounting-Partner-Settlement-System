<?php
declare(strict_types=1);
require __DIR__ . '/config.php';
$user = require_login();
?>
<!doctype html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title><?= e(APP_NAME) ?></title>
  <style>
    :root{
      --bg:#f6f7fb;
      --card:#ffffff;
      --line:#e5e7eb;
      --muted:#6b7280;
      --text:#111827;

      --primary:#2563eb;
      --danger:#dc2626;
      --ok:#16a34a;
      --warn:#f59e0b;

      --shadow: 0 16px 40px rgba(17,24,39,.10);
      --radius: 18px;
    }
    *{box-sizing:border-box;-webkit-tap-highlight-color:transparent}
    body{
      margin:0;
      font-family:system-ui,-apple-system,"Segoe UI",Tahoma,Arial;
      background:
        radial-gradient(900px 520px at 10% 0%, #eef2ff 0%, transparent 55%),
        radial-gradient(900px 520px at 90% 10%, #ecfeff 0%, transparent 50%),
        var(--bg);
      color:var(--text);
    }

    .topbar{
      position:sticky; top:0; z-index:20;
      background: rgba(255,255,255,.85);
      backdrop-filter: blur(10px);
      border-bottom:1px solid var(--line);
    }
    .topbar-inner{
      max-width:1100px; margin:0 auto;
      padding:12px 14px;
      display:flex; align-items:center; justify-content:space-between; gap:10px;
    }
    .brand{
      display:flex; align-items:center; gap:10px;
    }
    .logo{
      width:38px; height:38px; border-radius:14px;
      background: linear-gradient(135deg, #2563eb, #22c55e);
      box-shadow: var(--shadow);
    }
    .brand .title{font-weight:950; line-height:1.1}
    .brand .sub{color:var(--muted); font-size:12px; margin-top:2px}

    .pill{
      display:inline-flex; align-items:center; gap:8px;
      padding:8px 10px;
      border:1px solid var(--line);
      border-radius:999px;
      background:#fff;
      color:var(--muted);
      font-size:12px;
      white-space:nowrap;
    }
    .pill a{color:inherit; text-decoration:none}
    .wrap{max-width:1100px;margin:0 auto;padding:14px}

    /* Tabs */
    .tabs{
      display:grid;
      grid-template-columns: repeat(3, 1fr);
      gap:10px;
      margin:10px 0 14px;
    }
    .tab{
      border:1px solid var(--line);
      background:#fff;
      border-radius: 14px;
      padding:10px 12px;
      font-weight:950;
      cursor:pointer;
      text-align:center;
      box-shadow: 0 8px 18px rgba(17,24,39,.05);
      touch-action:manipulation;
    }
    .tab.active{
      border-color: rgba(37,99,235,.35);
      background: linear-gradient(180deg, #ffffff, #f3f6ff);
      color: var(--primary);
    }

    /* Cards / layout */
    .grid{
      display:grid;
      grid-template-columns: 1fr;
      gap:12px;
    }
    @media (min-width: 980px){
      .grid.two{grid-template-columns: 1fr 1fr;}
      .tabs{grid-template-columns: repeat(3, max-content); justify-content:flex-start}
      .tab{min-width: 160px}
    }
    .card{
      background:var(--card);
      border:1px solid var(--line);
      border-radius: var(--radius);
      box-shadow: var(--shadow);
      padding:14px;
    }
    .card h2{
      margin:0 0 10px;
      font-size:15px;
      font-weight:950;
      display:flex; align-items:center; gap:8px;
    }
    .muted{color:var(--muted)}
    .small{font-size:12px}

    /* Form */
    .form{
      display:grid;
      grid-template-columns: 1fr 1fr;
      gap:10px;
    }
    @media (max-width: 720px){
      .form{grid-template-columns: 1fr;}
    }
    .field{
      border:1px solid var(--line);
      border-radius: 14px;
      background:#fff;
      padding:10px 12px;
      display:flex; gap:10px; align-items:center;
    }
    .field .icon{opacity:.55}
    .field input, .field select{
      border:0; outline:0; background:transparent; width:100%;
      font-size:14px; color:var(--text);
    }

    .btnrow{
      display:flex; gap:10px; flex-wrap:wrap; margin-top:10px;
    }
    .btn{
      border:0; border-radius:14px;
      padding:12px 14px;
      font-weight:950;
      cursor:pointer;
      touch-action:manipulation;
    }
    .btn.primary{background:var(--primary); color:#fff; box-shadow: 0 10px 20px rgba(37,99,235,.18)}
    .btn.ghost{background:#fff; border:1px solid var(--line); color:var(--text)}
    .btn.danger{background:var(--danger); color:#fff}
    .btn:active{transform: translateY(1px)}

    /* Segment */
    .segment{
      display:flex; gap:8px; flex-wrap:wrap;
      margin:8px 0 0;
    }
    .seg{
      border:1px solid var(--line);
      background:#fff;
      border-radius:999px;
      padding:8px 10px;
      font-weight:950;
      cursor:pointer;
      font-size:13px;
      touch-action:manipulation;
    }
    .seg.active{
      background: linear-gradient(180deg, #ffffff, #f3f6ff);
      border-color: rgba(37,99,235,.35);
      color: var(--primary);
    }

    /* List cards */
    .list{display:grid; gap:10px; margin-top:12px;}
    .item{
      border:1px solid var(--line);
      border-radius: 16px;
      background:#fff;
      padding:12px;
      display:grid;
      gap:10px;
    }
    .item-top{
      display:flex; align-items:flex-start; justify-content:space-between; gap:10px;
    }
    .badge{
      display:inline-flex; align-items:center; gap:8px;
      padding:6px 10px;
      border-radius:999px;
      font-weight:950;
      font-size:12px;
      border:1px solid var(--line);
      background:#f9fafb;
    }
    .badge.receipt{border-color: rgba(34,197,94,.35); background:#f0fdf4; color:#166534}
    .badge.expense{border-color: rgba(220,38,38,.30); background:#fef2f2; color:#991b1b}

    .meta{display:flex; gap:10px; flex-wrap:wrap; align-items:center; color:var(--muted); font-size:12px}
    .amount{
      font-weight:1000;
      font-size:18px;
      letter-spacing:.2px;
    }
    .note{color:var(--text); font-weight:800}

    .actions{
      display:flex; gap:10px; flex-wrap:wrap;
    }
    .actions .btn{padding:10px 12px; border-radius:12px}

    /* Messages */
    .msg{
      margin-top:10px;
      padding:10px 12px;
      border-radius:14px;
      border:1px solid var(--line);
      background:#fff;
      font-weight:900;
    }
    .msg.ok{border-color: rgba(34,197,94,.35); background:#f0fdf4; color:#166534}
    .msg.bad{border-color: rgba(220,38,38,.30); background:#fef2f2; color:#991b1b}

    /* Settlement */
    .kpis{
      display:grid;
      grid-template-columns: repeat(2, 1fr);
      gap:10px;
      margin-top:10px;
    }
    @media (min-width: 980px){ .kpis{grid-template-columns: repeat(4, 1fr);} }
    .kpi{
      background:#fff;
      border:1px solid var(--line);
      border-radius: 16px;
      padding:12px;
    }
    .kpi .k{color:var(--muted); font-weight:950; font-size:12px}
    .kpi .v{margin-top:6px; font-weight:1000; font-size:16px}

    /* Archive */
    .archive-grid{display:grid; gap:12px; margin-top:12px;}
    .archive-item{
      border:1px solid var(--line);
      background:#fff;
      border-radius:16px;
      padding:12px;
      display:flex; align-items:flex-start; justify-content:space-between; gap:10px;
      cursor:pointer;
      touch-action:manipulation;
    }
    .archive-item:hover{background:#fbfbfd}
    .archive-title{font-weight:1000}
    .archive-sub{color:var(--muted); font-size:12px; margin-top:4px}
    .tag{
      border:1px solid var(--line);
      background:#f9fafb;
      border-radius:999px;
      padding:6px 10px;
      font-weight:950;
      font-size:12px;
      white-space:nowrap;
      align-self:flex-start;
    }

    .user-pill{
      display:inline-flex; align-items:center; gap:8px;
      border:1px solid var(--line);
      background:#fff;
      border-radius:999px;
      padding:6px 10px;
      font-weight:950;
      font-size:12px;
    }
    .dot{width:10px; height:10px; border-radius:999px; background:#000; display:inline-block;}

    /* Modal */
    .overlay{
      position:fixed; inset:0; background: rgba(17,24,39,.45);
      display:none; align-items:flex-end; justify-content:center; z-index:50;
      padding:14px;
    }
    .overlay.open{display:flex}
    .sheet{
      width:min(720px, 100%);
      background:#fff;
      border:1px solid var(--line);
      border-radius: 22px;
      box-shadow: var(--shadow);
      padding:14px;
    }
    .sheet-head{
      display:flex; align-items:center; justify-content:space-between; gap:10px;
      margin-bottom:10px;
    }
    .sheet-title{font-weight:1000}
    .xbtn{
      width:40px; height:40px; border-radius:14px;
      border:1px solid var(--line); background:#fff; cursor:pointer; font-weight:1000;
    }

    /* ✅ الأرقام فقط LTR (غربية) */
    .num{
      direction:ltr;
      unicode-bidi:plaintext;
      text-align:left;
      font-variant-numeric: lining-nums;
      font-feature-settings: "lnum";
    }
  </style>
</head>
<body>

<div class="topbar">
  <div class="topbar-inner">
    <div class="brand">
      <div class="logo"></div>
      <div>
        <div class="title"><?= e(APP_NAME) ?></div>
        <div class="sub">
          المستخدم:
          <strong id="meName">...</strong>
          <span class="muted">(@<strong class="num"><?= e($user['username']) ?></strong>)</span>
        </div>
      </div>
    </div>
    <div class="pill"><a href="logout.php">تسجيل خروج</a></div>
  </div>
</div>

<div class="wrap">
  <div class="tabs">
    <button class="tab active" data-view="mine">صفحتي</button>
    <button class="tab" data-view="other">المستخدمين</button>
    <button class="tab" data-view="settlement">التسوية والأرشيف</button>
  </div>

  <!-- View: Mine -->
  <div id="view-mine" class="grid two">
    <div class="card">
      <h2>➕ إضافة عملية</h2>

      <div class="form">
        <div class="field">
          <div class="icon">🧾</div>
          <select id="type">
            <option value="receipt">استلام (مكاسب)</option>
            <option value="expense">مصروف (خسائر)</option>
          </select>
        </div>

        <div class="field">
          <div class="icon">💰</div>
          <input id="amount" class="num" type="number" step="0.01" min="0" placeholder="المبلغ">
        </div>

        <div class="field">
          <div class="icon">📅</div>
          <input id="tx_date" class="num" type="date">
        </div>

        <div class="field">
          <div class="icon">📝</div>
          <input id="note" type="text" placeholder="ملاحظة (اختياري)">
        </div>
      </div>

      <div class="btnrow">
        <button class="btn primary" id="addBtn">إضافة</button>
        <button class="btn ghost" id="quickRefresh">تحديث القوائم</button>
      </div>

      <div id="addMsg" class="msg" style="display:none"></div>
    </div>

    <div class="card">
      <h2>📚 سجلاتي</h2>
      <div class="segment">
        <button class="seg active" data-mytype="receipt">استلامات</button>
        <button class="seg" data-mytype="expense">مصروفات</button>
      </div>
      <div class="muted small" style="margin-top:8px">التعديل والحذف لسجلاتك فقط (والأرشيف لا يمكن تعديله).</div>
      <div id="myList" class="list"></div>
    </div>
  </div>

  <!-- View: Other -->
  <div id="view-other" class="grid" style="display:none">
    <div class="card">
      <h2>👀 سجلات المستخدم</h2>

      <div class="form" style="grid-template-columns: 1fr;">
        <div class="field">
          <div class="icon">👤</div>
          <select id="userSelect"></select>
        </div>
      </div>

      <div class="segment">
        <button class="seg active" data-othertype="receipt">استلامات</button>
        <button class="seg" data-othertype="expense">مصروفات</button>
      </div>
      <div class="muted small" style="margin-top:8px">
        قراءة فقط — المعروض الآن: <strong id="selectedUserName">—</strong>
      </div>
      <div id="otherList" class="list"></div>
    </div>
  </div>

  <!-- View: Settlement -->
  <div id="view-settlement" class="grid" style="display:none">
    <div class="card">
      <h2>⚖️ التسوية الحالية</h2>
      <div class="muted small">
        يتم تقسيم إجمالي الاستلامات والمصروفات على <strong id="usersCount">—</strong> مستخدم/مستخدمين تلقائيًا.
      </div>

      <div id="kpis" class="kpis"></div>

      <div id="transfersBox" class="msg" style="margin-top:12px"></div>

      <div class="form" style="margin-top:12px">
        <div class="field" style="grid-column:1/-1">
          <div class="icon">🏷️</div>
          <input id="settlementName" type="text" placeholder='اسم التسوية (مثال: "تسوية نهاية شهر 12")'>
        </div>
      </div>

      <div class="btnrow">
        <button class="btn primary" id="createSettlementBtn">تمت التسوية (أرشفة وقفل)</button>
        <button class="btn ghost" id="refreshSettle">تحديث</button>
      </div>

      <div class="muted small" style="margin-top:8px">
        عند “تمت التسوية” سيتم نقل كل السجلات الحالية للأرشيف ولا يمكن تعديلها بعد ذلك.
      </div>
    </div>

    <div class="card">
      <h2>🗂 الأرشيف</h2>
      <div class="muted small">اختر تسوية لعرض تفاصيلها (قراءة فقط).</div>
      <div id="archiveList" class="archive-grid"></div>
      <div id="archiveDetails" style="margin-top:12px"></div>
    </div>
  </div>
</div>

<!-- Modal: Edit -->
<div id="overlay" class="overlay" role="dialog" aria-modal="true">
  <div class="sheet">
    <div class="sheet-head">
      <div class="sheet-title">تعديل العملية</div>
      <button class="xbtn" id="closeSheet">✕</button>
    </div>

    <div class="form">
      <div class="field">
        <div class="icon">📅</div>
        <input id="edit_date" class="num" type="date">
      </div>
      <div class="field">
        <div class="icon">💰</div>
        <input id="edit_amount" class="num" type="number" step="0.01" min="0" placeholder="المبلغ">
      </div>
      <div class="field" style="grid-column:1/-1">
        <div class="icon">📝</div>
        <input id="edit_note" type="text" placeholder="ملاحظة">
      </div>
    </div>

    <div class="btnrow">
      <button class="btn primary" id="saveEdit">حفظ</button>
      <button class="btn danger" id="deleteEdit">حذف</button>
      <button class="btn ghost" id="cancelEdit">إلغاء</button>
    </div>

    <div id="editMsg" class="msg" style="display:none"></div>
  </div>
</div>

<script>
let CSRF = '';
let myType = 'receipt';
let otherType = 'receipt';
let editId = null;

let USERS = [];
let ME_ID = 0;
let selectedUserId = 0;

const USER_COLORS = ['#2563eb', '#16a34a', '#f59e0b', '#db2777', '#7c3aed', '#0ea5e9'];

const fmt = (n)=> (Number(n)||0).toLocaleString('en-US', {
  minimumFractionDigits: 2,
  maximumFractionDigits: 2
}) + ' SAR';

async function api(action, params = {}, opts = {}) {
  const url = new URL('api.php', window.location.href);
  url.searchParams.set('action', action);
  for (const [k, v] of Object.entries(params)) url.searchParams.set(k, v);

  const res = await fetch(url.toString(), { credentials: 'same-origin', ...opts });
  const data = await res.json().catch(() => ({ ok:false, error:'Bad JSON' }));
  if (!data.ok) throw new Error(data.error || 'Request failed');
  return data;
}

function showMsg(el, text, ok=true){
  el.style.display='block';
  el.className='msg ' + (ok?'ok':'bad');
  el.textContent=text;
  setTimeout(()=>{ el.style.display='none'; }, 2200);
}

// safe esc (no replaceAll)
function esc(s){
  s = String(s == null ? '' : s);
  return s.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}

function userLabel(u){
  const name = (u && (u.display_name || u.username)) ? (u.display_name || u.username) : '—';
  const un = u && u.username ? u.username : '';
  return un ? (name + ' (@' + un + ')') : name;
}

function userById(id){
  for (const x of USERS) if (Number(x.id) === Number(id)) return x;
  return null;
}

function userColorById(id){
  const idx = USERS.findIndex(x => Number(x.id) === Number(id));
  const c = USER_COLORS[(idx >= 0 ? idx : 0) % USER_COLORS.length];
  return c;
}

function itemCard(it, editable, type, ownerUser){
  const badgeClass = type === 'receipt' ? 'receipt' : 'expense';
  const badgeText  = type === 'receipt' ? 'استلام' : 'مصروف';

  const u = ownerUser;
  const uName = u ? (u.display_name || u.username) : '';
  const dotColor = u ? userColorById(u.id) : '#111827';

  const who = u ? `
    <span class="user-pill" title="${esc(userLabel(u))}">
      <span class="dot" style="background:${dotColor}"></span>
      <span>${esc(uName)}</span>
    </span>
  ` : '';

  return `
    <div class="item" data-id="${it.id}">
      <div class="item-top">
        <div style="display:flex; gap:8px; flex-wrap:wrap; align-items:center">
          <div class="badge ${badgeClass}">${badgeText}</div>
          ${who}
        </div>
        <div class="amount num">${fmt(it.amount)}</div>
      </div>

      <div class="meta">
        <span>📅 <span class="num">${esc(it.tx_date)}</span></span>
        ${it.updated_at ? `<span>✏️ <span class="num">${esc(it.updated_at)}</span></span>` : ''}
      </div>

      <div class="note">${esc(it.note || '—')}</div>

      ${editable ? `
        <div class="actions">
          <button class="btn ghost edit">تعديل</button>
          <button class="btn danger del">حذف</button>
        </div>
      ` : ''}
    </div>
  `;
}

function renderList(containerId, items, editable, type, ownerUser){
  const el = document.getElementById(containerId);
  if (!Array.isArray(items) || items.length === 0) {
    el.innerHTML = `<div class="muted" style="padding:10px">لا توجد سجلات.</div>`;
    return;
  }
  el.innerHTML = items.map(it => itemCard(it, editable, type, ownerUser)).join('');
}

async function loadMe(){
  const me = userById(ME_ID) || {id:ME_ID, username:'<?= e($user['username']) ?>', display_name:''};
  const d = await api('list', { type: myType, user_id: ME_ID, settlement_id: 'active' });
  renderList('myList', d.items || [], true, myType, me);

  document.querySelectorAll('#myList .item .edit').forEach(btn=>{
    btn.addEventListener('click', (e)=>{
      const item = e.target.closest('.item');
      openEditFromCard(item, item.getAttribute('data-id'));
    });
  });

  document.querySelectorAll('#myList .item .del').forEach(btn=>{
    btn.addEventListener('click', async (e)=>{
      const item = e.target.closest('.item');
      const id = item.getAttribute('data-id');
      if (!confirm('حذف السجل؟')) return;
      try{
        await api('delete', {}, {
          method:'POST',
          headers: {'Content-Type':'application/x-www-form-urlencoded', 'X-CSRF-Token': CSRF},
          body: new URLSearchParams({id, csrf: CSRF})
        });
        showMsg(document.getElementById('addMsg'), 'تم الحذف', true);
        await refreshAll();
      }catch(err){
        showMsg(document.getElementById('addMsg'), err.message, false);
      }
    });
  });
}

function openEditFromCard(cardEl, id){
  editId = id;
  const date = cardEl.querySelector('.meta .num') ? cardEl.querySelector('.meta .num').textContent.trim() : '';
  const amountText = cardEl.querySelector('.amount') ? cardEl.querySelector('.amount').textContent : '';
  const note = cardEl.querySelector('.note') ? cardEl.querySelector('.note').textContent : '';
  const amount = amountText.replace(/[^\d.]/g,'').trim();

  document.getElementById('edit_date').value = date;
  document.getElementById('edit_amount').value = amount;
  document.getElementById('edit_note').value = (note === '—') ? '' : note;

  openSheet();
}

async function loadOther(){
  if (!selectedUserId) return;
  const u = userById(selectedUserId);
  document.getElementById('selectedUserName').textContent = u ? userLabel(u) : '—';
  const d = await api('list', { type: otherType, user_id: selectedUserId, settlement_id: 'active' });
  renderList('otherList', d.items || [], false, otherType, u);
}

function renderTransfers(transfers){
  if (!Array.isArray(transfers) || transfers.length === 0) {
    return { html: '✅ لا توجد مبالغ للتسوية حالياً (كل شيء متوازن).', ok:true };
  }
  const lines = transfers.map(t =>
    `🔁 <span class="num">${esc(t.from)}</span> يدفع لـ <span class="num">${esc(t.to)}</span> مبلغ <span class="num">${fmt(t.amount)}</span>`
  );
  return { html: lines.join('<br>'), ok:false };
}

async function loadSettlement(){
  const d = await api('settlement');
  const n = (d.totals && d.totals.n) ? d.totals.n : (d.users ? d.users.length : 0);
  document.getElementById('usersCount').textContent = String(n);

  const t = d.totals || {};
  const k = document.getElementById('kpis');

  const headCards = `
    <div class="kpi"><div class="k">إجمالي الاستلامات</div><div class="v num">${fmt(t.receipts || 0)}</div></div>
    <div class="kpi"><div class="k">إجمالي المصروفات</div><div class="v num">${fmt(t.expenses || 0)}</div></div>
    <div class="kpi"><div class="k">نصيب الاستلامات لكل شخص</div><div class="v num">${fmt(t.share_receipts || 0)}</div></div>
    <div class="kpi"><div class="k">نصيب المصروفات لكل شخص</div><div class="v num">${fmt(t.share_expenses || 0)}</div></div>
  `;

  const perUserCards = (d.users || []).map(u=>{
    const color = userColorById(u.id);
    const nm = u.display_name || u.username;
    return `
      <div class="kpi">
        <div class="k" style="display:flex; gap:8px; align-items:center">
          <span class="dot" style="background:${color}"></span>
          ${esc(nm)} <span class="muted small">(@${esc(u.username)})</span>
        </div>
        <div class="v num">${fmt(u.net || 0)}</div>
        <div class="muted small" style="margin-top:6px">
          استلامات: <span class="num">${fmt(u.receipts || 0)}</span> — مصروفات: <span class="num">${fmt(u.expenses || 0)}</span>
        </div>
      </div>
    `;
  }).join('');

  k.innerHTML = headCards + perUserCards;

  const box = document.getElementById('transfersBox');
  const tr = renderTransfers(d.transfers || []);
  box.className = 'msg ' + (tr.ok ? 'ok' : '');
  box.innerHTML = tr.html;
}

async function loadArchiveList(){
  const d = await api('settlements');
  const box = document.getElementById('archiveList');

  if (!Array.isArray(d.items) || d.items.length === 0) {
    box.innerHTML = `<div class="muted">لا توجد تسويات مؤرشفة بعد.</div>`;
    document.getElementById('archiveDetails').innerHTML = '';
    return;
  }

  box.innerHTML = d.items.map(s=>{
    const by = (s.created_by_name || s.created_by_username || '—');
    return `
      <div class="archive-item" data-sid="${s.id}">
        <div>
          <div class="archive-title">${esc(s.name)}</div>
          <div class="archive-sub">📅 <span class="num">${esc(s.created_at)}</span> — بواسطة: ${esc(by)}</div>
        </div>
        <div class="tag">عرض</div>
      </div>
    `;
  }).join('');

  box.querySelectorAll('.archive-item').forEach(el=>{
    el.addEventListener('click', async ()=>{
      await openArchive(Number(el.getAttribute('data-sid')));
    });
  });
}

function renderArchiveUserSection(u, receiptItems, expenseItems){
  const color = userColorById(u.id);
  const name = u.display_name || u.username;

  const listBlock = (items, type) => {
    if (!Array.isArray(items) || items.length === 0) {
      return `<div class="muted small">لا توجد ${type==='receipt'?'استلامات':'مصروفات'}.</div>`;
    }
    return `
      <div class="list" style="margin-top:10px">
        ${items.map(it => itemCard(it, false, type, u)).join('')}
      </div>
    `;
  };

  return `
    <div class="card" style="margin-top:12px">
      <h2>
        <span class="dot" style="background:${color}"></span>
        ${esc(name)} <span class="muted small">(@${esc(u.username)})</span>
      </h2>
      ${listBlock(receiptItems, 'receipt')}
      ${listBlock(expenseItems, 'expense')}
    </div>
  `;
}

async function openArchive(sid){
  const d = await api('settlement_details', { id: sid });
  const s = d.settlement;
  const details = document.getElementById('archiveDetails');

  const calc = d.calc || {};
  const t = calc.totals || {};
  const transfers = (calc.transfers || []);

  const head = `
    <div class="card">
      <h2>🧾 ${esc(s.name)}</h2>
      <div class="muted small">
        تاريخ: <span class="num">${esc(s.created_at)}</span> — بواسطة:
        <strong>${esc(s.created_by_name || s.created_by_username || '—')}</strong>
      </div>

      <div class="kpis" style="margin-top:12px">
        <div class="kpi"><div class="k">عدد المستخدمين</div><div class="v num">${String(t.n || 0)}</div></div>
        <div class="kpi"><div class="k">إجمالي الاستلامات</div><div class="v num">${fmt(t.receipts || 0)}</div></div>
        <div class="kpi"><div class="k">إجمالي المصروفات</div><div class="v num">${fmt(t.expenses || 0)}</div></div>
        <div class="kpi"><div class="k">الصافي العادل لكل شخص</div><div class="v num">${fmt(t.fair_net_each || 0)}</div></div>
      </div>

      <div class="msg ${transfers.length===0?'ok':''}" style="margin-top:12px">
        ${renderTransfers(transfers).html}
      </div>
      <div class="muted small" style="margin-top:8px">الأرشيف قراءة فقط ولا يمكن التعديل عليه.</div>
    </div>
  `;

  const arch = d.archive || {};
  let usersSections = '';
  for (const uid in arch) {
    const block = arch[uid];
    usersSections += renderArchiveUserSection(block.user, block.receipt || [], block.expense || []);
  }

  details.innerHTML = head + usersSections;
}

async function refreshAll(){
  await Promise.all([loadMe(), loadOther(), loadSettlement(), loadArchiveList()]);
}

function openSheet(){ document.getElementById('overlay').classList.add('open'); }
function closeSheet(){ document.getElementById('overlay').classList.remove('open'); editId = null; }

(async function boot(){
  const d = await api('me');
  CSRF = d.csrf;
  USERS = d.users || [];
  ME_ID = d.user && d.user.id ? d.user.id : 0;
  document.getElementById('meName').textContent = (d.user && (d.user.display_name || d.user.username)) ? (d.user.display_name || d.user.username) : '—';

  const today = new Date();
  const yyyy = today.getFullYear();
  const mm = String(today.getMonth()+1).padStart(2,'0');
  const dd = String(today.getDate()).padStart(2,'0');
  document.getElementById('tx_date').value = `${yyyy}-${mm}-${dd}`;

  // user select (exclude me)
  const sel = document.getElementById('userSelect');
  sel.innerHTML = '';
  const others = USERS.filter(u => Number(u.id) !== Number(ME_ID));
  if (others.length === 0) {
    const opt = document.createElement('option');
    opt.value = String(ME_ID);
    opt.textContent = 'لا يوجد مستخدم آخر';
    sel.appendChild(opt);
    selectedUserId = ME_ID;
  } else {
    others.forEach(u=>{
      const opt = document.createElement('option');
      opt.value = String(u.id);
      opt.textContent = userLabel(u);
      sel.appendChild(opt);
    });
    selectedUserId = Number(others[0].id);
  }
  sel.addEventListener('change', async ()=>{ selectedUserId = Number(sel.value); await loadOther(); });

  // tabs
  document.querySelectorAll('.tab[data-view]').forEach(btn=>{
    btn.addEventListener('click', ()=>{
      document.querySelectorAll('.tab[data-view]').forEach(b=>b.classList.remove('active'));
      btn.classList.add('active');
      const v = btn.getAttribute('data-view');
      document.getElementById('view-mine').style.display = v==='mine' ? '' : 'none';
      document.getElementById('view-other').style.display = v==='other' ? '' : 'none';
      document.getElementById('view-settlement').style.display = v==='settlement' ? '' : 'none';
    });
  });

  // segments
  document.querySelectorAll('.seg[data-mytype]').forEach(btn=>{
    btn.addEventListener('click', async ()=>{
      document.querySelectorAll('.seg[data-mytype]').forEach(b=>b.classList.remove('active'));
      btn.classList.add('active');
      myType = btn.getAttribute('data-mytype');
      await loadMe();
    });
  });
  document.querySelectorAll('.seg[data-othertype]').forEach(btn=>{
    btn.addEventListener('click', async ()=>{
      document.querySelectorAll('.seg[data-othertype]').forEach(b=>b.classList.remove('active'));
      btn.classList.add('active');
      otherType = btn.getAttribute('data-othertype');
      await loadOther();
    });
  });

  // add
  document.getElementById('addBtn').addEventListener('click', async ()=>{
    const type = document.getElementById('type').value;
    const amount = document.getElementById('amount').value;
    const tx_date = document.getElementById('tx_date').value;
    const note = document.getElementById('note').value;
    const msg = document.getElementById('addMsg');
    try{
      await api('add', {}, {
        method:'POST',
        headers: {'Content-Type':'application/x-www-form-urlencoded', 'X-CSRF-Token': CSRF},
        body: new URLSearchParams({type, amount, tx_date, note, csrf: CSRF})
      });
      document.getElementById('amount').value='';
      document.getElementById('note').value='';
      showMsg(msg, 'تمت الإضافة', true);
      await refreshAll();
    }catch(err){
      showMsg(msg, err.message, false);
    }
  });

  document.getElementById('quickRefresh').addEventListener('click', refreshAll);
  document.getElementById('refreshSettle').addEventListener('click', loadSettlement);

  document.getElementById('createSettlementBtn').addEventListener('click', async ()=>{
    const name = (document.getElementById('settlementName').value || '').trim();
    if (!name) { alert('اكتب اسم التسوية'); return; }
    if (!confirm('سيتم أرشفة كل السجلات الحالية وقفلها نهائيًا. متابعة؟')) return;
    try{
      await api('create_settlement', {}, {
        method:'POST',
        headers: {'Content-Type':'application/x-www-form-urlencoded', 'X-CSRF-Token': CSRF},
        body: new URLSearchParams({name, csrf: CSRF})
      });
      document.getElementById('settlementName').value='';
      showMsg(document.getElementById('transfersBox'), '✅ تمت التسوية وتمت الأرشفة', true);
      await refreshAll();
    }catch(err){
      showMsg(document.getElementById('transfersBox'), err.message, false);
    }
  });

  // modal controls
  document.getElementById('closeSheet').addEventListener('click', closeSheet);
  document.getElementById('cancelEdit').addEventListener('click', closeSheet);
  document.getElementById('overlay').addEventListener('click', (e)=>{ if (e.target.id==='overlay') closeSheet(); });

  document.getElementById('saveEdit').addEventListener('click', async ()=>{
    const msg = document.getElementById('editMsg');
    const id = editId;
    const tx_date = document.getElementById('edit_date').value;
    const amount = document.getElementById('edit_amount').value;
    const note = document.getElementById('edit_note').value;
    try{
      await api('update', {}, {
        method:'POST',
        headers: {'Content-Type':'application/x-www-form-urlencoded', 'X-CSRF-Token': CSRF},
        body: new URLSearchParams({id, tx_date, amount, note, csrf: CSRF})
      });
      showMsg(msg, 'تم الحفظ', true);
      closeSheet();
      await refreshAll();
    }catch(err){
      showMsg(msg, err.message, false);
    }
  });

  document.getElementById('deleteEdit').addEventListener('click', async ()=>{
    const msg = document.getElementById('editMsg');
    const id = editId;
    if (!id) return;
    if (!confirm('حذف السجل؟')) return;
    try{
      await api('delete', {}, {
        method:'POST',
        headers: {'Content-Type':'application/x-www-form-urlencoded', 'X-CSRF-Token': CSRF},
        body: new URLSearchParams({id, csrf: CSRF})
      });
      showMsg(msg, 'تم الحذف', true);
      closeSheet();
      await refreshAll();
    }catch(err){
      showMsg(msg, err.message, false);
    }
  });

  await refreshAll();
})();
</script>
</body>
</html>
