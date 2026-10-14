(() => {
  'use strict';
  const $ = (s, r = document) => r.querySelector(s);
  const api = `${window.dashboardConfig.apiBase}/reports.php`;
  const token = () => localStorage.getItem('flow_admin_token') || '';
  const icons = () => window.lucide?.createIcons({ attrs: { 'stroke-width': 1.8 } });
  const toast = m => window.dashboardShowToast?.(m);
  const esc = v => String(v ?? '').replace(/[&<>"']/g, c => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));
  let currency = 'NGN';
  const money = (n, d = 0) => new Intl.NumberFormat('en-NG', { style: 'currency', currency, minimumFractionDigits: d, maximumFractionDigits: d }).format(Number(n || 0));
  const int = n => Number(n || 0).toLocaleString('en-NG');
  const pct = (a, b) => (b ? `${(a / b * 100).toFixed(1)}%` : '—');
  const ymd = d => `${d.getFullYear()}-${String(d.getMonth() + 1).padStart(2, '0')}-${String(d.getDate()).padStart(2, '0')}`;
  const GROUPS = { day: 'Day', provider: 'Provider', type: 'Payment type', status: 'Status' };
  const MAX_DAYS = 366;
  const el = { preset: $('#report-preset'), from: $('#report-from'), to: $('#report-to'), group: $('#report-group') };
  let data = null, seq = 0;

  const applyPreset = () => {
    const now = new Date(), start = new Date(now);
    const p = el.preset.value; if (p === 'custom') return;
    if (p === '7d') start.setDate(now.getDate() - 6); else if (p === '30d') start.setDate(now.getDate() - 29); else if (p === 'month') start.setDate(1);
    el.from.value = ymd(start); el.to.value = ymd(now);
  };
  const label = (row, group) => group === 'day' && /^\d{4}-\d{2}-\d{2}/.test(row.label) ? new Date(`${row.label.slice(0, 10)}T00:00:00`).toLocaleDateString('en-NG', { day: 'numeric', month: 'short', year: 'numeric' }) : row.label;

  const chart = (rows, group) => {
    $('#report-chart-title').textContent = `Successful volume by ${GROUPS[group].toLowerCase()}`;
    if (!rows.length) { $('#report-chart').innerHTML = ''; return; }
    const W = 640, H = 220, pad = { l: 8, r: 8, t: 10, b: 28 }, max = Math.max(...rows.map(r => Number(r.volume) || 0), 1);
    const bw = (W - pad.l - pad.r) / rows.length, gap = Math.min(6, bw * .25), step = Math.ceil(rows.length / 12);
    const bars = rows.map((r, i) => {
      const h = (Number(r.volume) || 0) / max * (H - pad.t - pad.b), x = pad.l + i * bw + gap / 2, y = H - pad.b - h;
      const t = i % step === 0 ? `<text x="${x + (bw - gap) / 2}" y="${H - 10}" text-anchor="middle">${esc(group === 'day' ? String(r.label).slice(5, 10) : String(r.label).slice(0, 10))}</text>` : '';
      return `<rect class="bar" x="${x}" y="${y}" width="${Math.max(bw - gap, 1)}" height="${Math.max(h, 0)}" rx="2"><title>${esc(label(r, group))}: ${esc(money(r.volume))}</title></rect>${t}`;
    }).join('');
    $('#report-chart').innerHTML = `<svg viewBox="0 0 ${W} ${H}" role="img" aria-label="Bar chart of successful volume by ${esc(GROUPS[group].toLowerCase())}. The table below has the same figures."><line class="axis" x1="0" x2="${W}" y1="${H - pad.b}" y2="${H - pad.b}"/>${bars}</svg>`;
  };

  const render = d => {
    data = d; currency = d.currency || 'NGN';
    const s = d.summary || {}, rows = d.rows || [], group = d.group_by || el.group.value;
    $('#sum-volume').textContent = money(s.total_volume);
    $('#sum-count').textContent = `${int(s.successful_count)} successful payments`;
    $('#sum-rate').textContent = pct(s.successful_count, Number(s.successful_count || 0) + Number(s.failed_count || 0));
    $('#sum-failed').textContent = `${int(s.failed_count)} failed`;
    $('#sum-fees').textContent = money(s.fees_collected, 2);
    $('#sum-rewards').textContent = money(s.rewards_paid, 2);
    $('#sum-processing').textContent = int(s.processing_count);
    $('#report-col-label').textContent = GROUPS[group];
    $('#report-rows').innerHTML = rows.map(r => `<tr><td>${esc(label(r, group))}</td><td class="num">${int(r.payments)}</td><td class="num">${int(r.successful)}</td><td class="num">${int(r.failed)}</td><td class="num"><span class="report-rate">${pct(r.successful, Number(r.successful) + Number(r.failed))}</span></td><td class="num">${esc(money(r.volume))}</td><td class="num">${esc(money(r.fees, 2))}</td></tr>`).join('');
    const tot = rows.reduce((a, r) => ({ p: a.p + Number(r.payments || 0), s: a.s + Number(r.successful || 0), f: a.f + Number(r.failed || 0), v: a.v + Number(r.volume || 0), fe: a.fe + Number(r.fees || 0) }), { p: 0, s: 0, f: 0, v: 0, fe: 0 });
    $('#report-foot').innerHTML = rows.length ? `<tr><td>Total</td><td class="num">${int(tot.p)}</td><td class="num">${int(tot.s)}</td><td class="num">${int(tot.f)}</td><td class="num">${pct(tot.s, tot.s + tot.f)}</td><td class="num">${esc(money(tot.v))}</td><td class="num">${esc(money(tot.fe, 2))}</td></tr>` : '';
    $('#report-empty').hidden = rows.length > 0;
    $('#report-export').disabled = !rows.length;
    $('#report-updated').textContent = d.generated_at ? `Generated ${new Date(String(d.generated_at).replace(' ', 'T') + 'Z').toLocaleString()}` : '';
    chart(rows, group);
    $('#report-loading').hidden = true; $('#report-error').hidden = true; $('#report-content').hidden = false; icons();
  };

  const load = async () => {
    const from = el.from.value, to = el.to.value;
    if (!from || !to) return;
    if (from > to) { toast('The start date must be on or before the end date.'); return; }
    if ((new Date(to) - new Date(from)) / 864e5 >= MAX_DAYS) { toast(`Choose a range of ${MAX_DAYS} days or less.`); return; }
    const mine = ++seq; $('#report-error').hidden = true; if (!data) $('#report-loading').hidden = false;
    try {
      const res = await fetch(`${api}?${new URLSearchParams({ from, to, group_by: el.group.value })}`, { headers: { Authorization: `Bearer ${token()}` } });
      const body = await res.json().catch(() => ({}));
      if (!res.ok || !body.success) throw new Error(body.error || 'The report could not be loaded.');
      if (mine === seq) render({ ...body, group_by: body.group_by || el.group.value });   // ignore stale responses
    } catch (e) { if (mine !== seq) return; $('#report-loading').hidden = true; $('#report-content').hidden = true; $('#report-error').hidden = false; $('#report-error span').textContent = e.message; icons(); }
  };

  // CSV: a leading = + - @ can run as a formula in Excel, so those cells get a quote prefix.
  const cell = v => { let t = String(v ?? ''); if (/^[=+\-@\t\r]/.test(t)) t = `'${t}`; return `"${t.replace(/"/g, '""')}"`; };
  $('#report-export').addEventListener('click', () => {
    if (!data?.rows?.length) return;
    const group = data.group_by, head = [GROUPS[group], 'Payments', 'Successful', 'Failed', 'Success rate', `Volume (${currency})`, `Fees (${currency})`];
    const lines = [head, ...data.rows.map(r => [label(r, group), r.payments, r.successful, r.failed, pct(r.successful, Number(r.successful) + Number(r.failed)), r.volume, r.fees])];
    const blob = new Blob(['\ufeff' + lines.map(l => l.map(cell).join(',')).join('\r\n')], { type: 'text/csv;charset=utf-8' });
    const a = Object.assign(document.createElement('a'), { href: URL.createObjectURL(blob), download: `flowwithlit-report-${group}-${el.from.value}-${el.to.value}.csv` });
    document.body.append(a); a.click(); a.remove(); setTimeout(() => URL.revokeObjectURL(a.href), 1000);
  });

  el.preset.addEventListener('change', () => { applyPreset(); load(); });
  [el.from, el.to].forEach(i => i.addEventListener('change', () => { el.preset.value = 'custom'; load(); }));
  el.group.addEventListener('change', load);
  $('#report-retry').addEventListener('click', load);
  applyPreset(); load();
})();
