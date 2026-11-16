<?php
$pageTitle = 'Notifications — Flowwithlit';
$activePage = 'notifications';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/sidebar.php';
?>

<main class="flex-1 h-full overflow-y-auto px-4 sm:px-6 lg:px-8 py-6 pb-28 md:pb-6">
  <div class="mx-auto max-w-4xl">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
      <div>
        <div class="flex items-center gap-2"><span class="grid h-8 w-8 place-items-center rounded-xl bg-[#faf5e8] text-[#8a6a1d]"><i data-lucide="bell-ring" class="w-4 h-4"></i></span><span class="text-xs font-bold uppercase tracking-wider text-[#0b2545] dark:text-gray-200">Notification center</span></div>
        <h1 class="mt-3 text-3xl font-bold tracking-tight text-gray-900 dark:text-white">Notifications</h1>
        <p class="mt-1 text-sm text-gray-600 dark:text-gray-300">Tap a notification to read the full message.</p>
      </div>
      <div class="flex flex-wrap gap-2">
        <button id="toggleSelect" type="button" class="inline-flex items-center justify-center gap-2 rounded-full border border-gray-200 dark:border-gray-700 bg-white dark:bg-[#0c1f36] px-4 py-2.5 text-xs font-bold text-gray-700 dark:text-gray-200 shadow-sm hover:bg-gray-50 dark:hover:bg-gray-800"><i data-lucide="list-checks" class="w-4 h-4 text-[#8a6a1d]"></i> <span>Select</span></button>
        <button id="markAllPageRead" type="button" class="inline-flex items-center justify-center gap-2 rounded-full border border-gray-200 dark:border-gray-700 bg-white dark:bg-[#0c1f36] px-4 py-2.5 text-xs font-bold text-gray-700 dark:text-gray-200 shadow-sm hover:bg-gray-50 dark:hover:bg-gray-800"><i data-lucide="check-check" class="w-4 h-4 text-emerald-500"></i> Mark all as read</button>
      </div>
    </div>

    <!-- Bulk actions, shown in select mode -->
    <div id="selectionBar" class="hidden sticky top-0 z-10 mt-5 flex flex-wrap items-center justify-between gap-3 rounded-2xl border border-gray-200 dark:border-gray-700 bg-white/95 dark:bg-[#0c1f36]/95 px-4 py-3 shadow-sm backdrop-blur">
      <label class="inline-flex cursor-pointer items-center gap-2 text-sm font-bold text-gray-800 dark:text-gray-100"><input id="selectAllVisible" type="checkbox" class="notification-check" /> Select all</label>
      <span id="selectedCount" class="text-sm font-medium text-gray-600 dark:text-gray-300">0 selected</span>
      <div class="flex gap-2">
        <button id="deleteSelected" type="button" disabled class="inline-flex items-center gap-1.5 rounded-full bg-rose-600 px-4 py-2 text-xs font-bold text-white hover:bg-rose-700 disabled:cursor-not-allowed disabled:opacity-40"><i data-lucide="trash-2" class="w-4 h-4"></i> Delete selected</button>
        <button id="cancelSelect" type="button" class="rounded-full px-4 py-2 text-xs font-bold text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-800">Cancel</button>
      </div>
    </div>

    <section class="mt-7 rounded-[28px] border border-gray-200/80 dark:border-gray-700 bg-white dark:bg-[#0c1f36] p-3 shadow-sm sm:p-4">
      <div class="flex flex-wrap gap-2" role="tablist" aria-label="Notification filters">
        <button type="button" data-notification-filter="all" class="notification-filter rounded-full px-4 py-2 text-xs font-bold">All <span data-count="all" class="ml-1 rounded-full bg-black/10 px-1.5 py-0.5">0</span></button>
        <button type="button" data-notification-filter="unread" class="notification-filter rounded-full px-4 py-2 text-xs font-bold">Unread <span data-count="unread" class="ml-1 rounded-full bg-black/10 px-1.5 py-0.5">0</span></button>
        <button type="button" data-notification-filter="payments" class="notification-filter rounded-full px-4 py-2 text-xs font-bold">Payments</button>
        <button type="button" data-notification-filter="security" class="notification-filter rounded-full px-4 py-2 text-xs font-bold">Security</button>
        <button type="button" data-notification-filter="system" class="notification-filter rounded-full px-4 py-2 text-xs font-bold">System</button>
      </div>
    </section>

    <section class="mt-6 space-y-7" id="notificationList" aria-live="polite">
      <p class="py-16 text-center text-sm text-gray-500 dark:text-gray-300">Loading notifications…</p>
    </section>
    <p id="noNotifications" class="hidden py-16 text-center text-sm text-gray-600 dark:text-gray-300">No notifications match this filter.</p>
  </div>
</main>

<script>
document.addEventListener('DOMContentLoaded', async () => {
  const token = localStorage.getItem('flow_token') || '';
  const list = document.getElementById('notificationList');
  const empty = document.getElementById('noNotifications');
  const filters = document.querySelectorAll('.notification-filter');
  const openId = new URLSearchParams(location.search).get('id');
  let items = [];
  let activeFilter = 'all';
  let selecting = false;
  const selected = new Set();
  const selectionBar = document.getElementById('selectionBar');
  const selectAllBox = document.getElementById('selectAllVisible');
  const deleteSelectedBtn = document.getElementById('deleteSelected');
  const visibleItems = () => items.filter((i) => activeFilter === 'all' || (activeFilter === 'unread' ? !i.read : category(i.type) === activeFilter));

  function updateSelectionUi() {
    const visibleIds = visibleItems().map((i) => String(i.id));
    const count = [...selected].filter((id) => visibleIds.includes(id)).length;
    document.getElementById('selectedCount').textContent = `${count} selected`;
    deleteSelectedBtn.disabled = count === 0;
    selectAllBox.checked = visibleIds.length > 0 && count === visibleIds.length;
    selectAllBox.indeterminate = count > 0 && count < visibleIds.length;
  }

  function setSelecting(on) {
    selecting = on;
    selected.clear();
    selectionBar.classList.toggle('hidden', !on);
    list.classList.toggle('is-selecting', on);
    document.querySelector('#toggleSelect span').textContent = on ? 'Done' : 'Select';
    render();
  }

  async function deleteIds(ids) {
    if (!ids.length) return;
    const label = ids.length === 1 ? 'this notification' : `these ${ids.length} notifications`;
    if (!window.confirm(`Delete ${label}? This cannot be undone.`)) return;
    try {
      const data = await api('delete.php', { ids: ids.map(Number) });
      if (!data.success) throw new Error(data.error || 'Delete failed');
      const gone = new Set(ids.map(String));
      items = items.filter((i) => !gone.has(String(i.id)));
      ids.forEach((id) => selected.delete(String(id)));
      updateCounts();
      render();
    } catch (e) {
      window.alert('Unable to delete right now. Please try again.');
    }
  }

  const api = (path, body) => fetch('../api/notifications/' + path, {
    method: 'POST',
    headers: { 'Content-Type': 'application/json', Authorization: `Bearer ${token}` },
    body: JSON.stringify(body || {}),
  }).then((r) => r.json());

  // Map a notification type (e.g. "transfer.received") to a filter category.
  const category = (type) => {
    const t = String(type || '').toLowerCase();
    if (/(transfer|deposit|payment|withdraw|payout|swap|wallet|checkout|refund)/.test(t)) return 'payments';
    if (/(login|security|password|2fa|two_factor|pin|otp|session)/.test(t)) return 'security';
    return 'system';
  };
  const icons = { payments: 'arrow-left-right', security: 'shield-check', system: 'bell' };
  const parseDate = (v) => new Date(String(v || '').replace(' ', 'T'));
  const dayGroup = (d) => {
    if (isNaN(d)) return 'Earlier';
    const start = new Date(); start.setHours(0, 0, 0, 0);
    const diff = Math.floor((start - new Date(d.getFullYear(), d.getMonth(), d.getDate())) / 864e5);
    return diff <= 0 ? 'Today' : diff === 1 ? 'Yesterday' : 'Earlier';
  };
  const timeLabel = (d) => isNaN(d) ? '' : d.toLocaleString(undefined, { month: 'short', day: 'numeric', hour: 'numeric', minute: '2-digit' });
  const el = (tag, cls, text) => { const n = document.createElement(tag); if (cls) n.className = cls; if (text !== undefined) n.textContent = text; return n; };
  // Only same-site relative links are allowed as the "action" link (no javascript: or external URLs).
  const safeAction = (url) => /^[a-z0-9_\-\/]+\.php(\?[\w=&%\-.]*)?$/i.test(String(url || '')) ? url : null;

  function updateCounts() {
    document.querySelector('[data-count="all"]').textContent = items.length;
    document.querySelector('[data-count="unread"]').textContent = items.filter((i) => !i.read).length;
  }

  async function markRead(item, card) {
    if (item.read) return;
    item.read = true;
    card.classList.remove('is-unread');
    card.querySelector('.unread-dot')?.remove();
    updateCounts();
    try { await api('mark-read.php', { id: item.id }); } catch (e) { /* retried on next open */ }
  }

  function toggle(item, card, forceOpen) {
    const open = forceOpen ?? !card.classList.contains('is-open');
    list.querySelectorAll('.notification-card.is-open').forEach((c) => { if (c !== card) { c.classList.remove('is-open'); c.querySelector('[aria-expanded]').setAttribute('aria-expanded', 'false'); } });
    card.classList.toggle('is-open', open);
    card.querySelector('[aria-expanded]').setAttribute('aria-expanded', String(open));
    if (open) markRead(item, card);
  }

  function render() {
    list.textContent = '';
    const visible = visibleItems();
    empty.classList.toggle('hidden', visible.length > 0);
    ['Today', 'Yesterday', 'Earlier'].forEach((group) => {
      const groupItems = visible.filter((i) => dayGroup(parseDate(i.created_at)) === group);
      if (!groupItems.length) return;
      const section = el('div');
      const head = el('div', 'mb-3 flex items-center gap-3');
      head.append(el('h2', 'text-sm font-bold text-gray-900 dark:text-white', group), el('span', 'h-px flex-1 bg-gray-200 dark:bg-gray-700'));
      const stack = el('div', 'space-y-3');
      groupItems.forEach((item) => {
        const cat = category(item.type);
        const card = el('article', 'notification-card rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-[#0c1f36] transition' + (item.read ? '' : ' is-unread'));
        card.id = 'notification-' + item.id;
        const button = el('button', 'flex w-full gap-4 p-4 text-left sm:p-5');
        button.type = 'button';
        button.setAttribute('aria-expanded', 'false');
        const icon = el('span', 'grid h-11 w-11 shrink-0 place-items-center rounded-2xl bg-[#faf5e8] text-[#8a6a1d]');
        icon.innerHTML = `<i data-lucide="${icons[cat]}" class="w-5 h-5"></i>`;
        const content = el('span', 'min-w-0 flex-1');
        const top = el('span', 'flex flex-wrap items-start justify-between gap-2');
        top.append(el('strong', 'font-bold text-gray-900 dark:text-white', item.title || 'Notification'), el('time', 'text-xs text-gray-500 dark:text-gray-400', timeLabel(parseDate(item.created_at))));
        content.append(top, el('span', 'notification-preview mt-1 text-sm text-gray-600 dark:text-gray-300', item.body || ''));
        button.append(icon, content);
        if (!item.read) button.append(el('span', 'unread-dot mt-1.5 h-2.5 w-2.5 shrink-0 rounded-full bg-sky-500'));
        // Full message, shown only when the card is opened.
        const full = el('div', 'notification-full px-4 pb-5 sm:px-5 sm:pl-[5.25rem]');
        full.append(el('p', 'whitespace-pre-line text-[15px] leading-relaxed text-gray-800 dark:text-gray-100', item.body || ''));
        const actions = el('div', 'mt-4 flex flex-wrap gap-2');
        const action = safeAction(item.action_url);
        if (action) {
          const link = el('a', 'inline-flex items-center gap-1.5 rounded-full bg-[#0b2545] px-4 py-2 text-xs font-bold text-white hover:bg-[#16355f]', 'Open related page');
          link.href = action;
          actions.append(link);
        }
        const del = el('button', 'inline-flex items-center gap-1.5 rounded-full border border-rose-200 dark:border-rose-900 px-4 py-2 text-xs font-bold text-rose-700 dark:text-rose-300 hover:bg-rose-50 dark:hover:bg-rose-950/40', 'Delete');
        del.type = 'button';
        del.addEventListener('click', () => deleteIds([item.id]));
        actions.append(del);
        full.append(actions);

        // Checkbox for select mode
        const check = el('input', 'notification-check mt-3.5 ml-4 sm:ml-5 shrink-0');
        check.type = 'checkbox';
        check.setAttribute('aria-label', `Select ${item.title || 'notification'}`);
        check.checked = selected.has(String(item.id));
        check.addEventListener('change', () => {
          check.checked ? selected.add(String(item.id)) : selected.delete(String(item.id));
          card.classList.toggle('is-selected', check.checked);
          updateSelectionUi();
        });
        card.classList.toggle('is-selected', check.checked);

        button.addEventListener('click', () => {
          if (selecting) { check.checked = !check.checked; check.dispatchEvent(new Event('change')); return; }
          toggle(item, card);
        });
        const row = el('div', 'flex items-start');
        if (selecting) row.append(check);
        row.append(button);
        card.append(row, full);
        stack.append(card);
      });
      section.append(head, stack);
      list.append(section);
    });
    window.lucide?.createIcons();
    updateSelectionUi();
  }

  document.getElementById('toggleSelect').addEventListener('click', () => setSelecting(!selecting));
  document.getElementById('cancelSelect').addEventListener('click', () => setSelecting(false));
  deleteSelectedBtn.addEventListener('click', () => {
    const visibleIds = visibleItems().map((i) => String(i.id));
    deleteIds([...selected].filter((id) => visibleIds.includes(id)));
  });
  selectAllBox.addEventListener('change', () => {
    visibleItems().forEach((i) => selectAllBox.checked ? selected.add(String(i.id)) : selected.delete(String(i.id)));
    render();
  });

  function setFilter(filter) {
    activeFilter = filter;
    filters.forEach((b) => {
      const on = b.dataset.notificationFilter === filter;
      b.classList.toggle('bg-[#0b2545]', on); b.classList.toggle('text-white', on);
      b.classList.toggle('text-gray-600', !on); b.classList.toggle('dark:text-gray-300', !on); b.classList.toggle('hover:bg-gray-100', !on);
    });
    render();
  }
  filters.forEach((b) => b.addEventListener('click', () => setFilter(b.dataset.notificationFilter)));

  document.getElementById('markAllPageRead').addEventListener('click', async () => {
    try { await api('mark-read.php'); } catch (e) {}
    items.forEach((i) => { i.read = true; });
    updateCounts(); render();
  });

  try {
    const data = await api('list.php');
    if (!data.success) throw new Error(data.error || 'Unable to load notifications');
    items = data.notifications || [];
    updateCounts();
    setFilter('all');
    if (openId) {
      const item = items.find((i) => String(i.id) === String(openId));
      const card = document.getElementById('notification-' + openId);
      if (item && card) {
        toggle(item, card, true);
        card.classList.add('is-highlighted');
        card.scrollIntoView({ behavior: 'smooth', block: 'center' });
        setTimeout(() => card.classList.remove('is-highlighted'), 2400);
      }
    }
  } catch (error) {
    list.innerHTML = '<p class="py-16 text-center text-sm text-gray-600">Unable to load notifications right now.</p>';
  }
});
</script>
<style>
  .notification-card.is-unread { border-color: #c7dcf2; background: rgba(240, 247, 255, 0.7); }
  .dark .notification-card.is-unread { border-color: rgba(12, 74, 110, 0.6); background: rgba(8, 47, 73, 0.25); }
  .notification-card:hover { border-color: #c9a44a; }
  .notification-full { display: none; }
  .notification-card.is-open .notification-full { display: block; }
  .notification-card.is-open .notification-preview { display: none; }
  .notification-card.is-highlighted { box-shadow: 0 0 0 3px rgba(201, 164, 74, 0.55); }
  .notification-card.is-selected { border-color: #0b2545; box-shadow: 0 0 0 1px #0b2545; }
  .dark .notification-card.is-selected { border-color: #c9a44a; box-shadow: 0 0 0 1px #c9a44a; }
  .is-selecting .notification-full { display: none !important; }
  .notification-check { width: 18px; height: 18px; accent-color: #0b2545; cursor: pointer; }
</style>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
