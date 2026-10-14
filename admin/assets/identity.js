(() => {
  const $ = (selector, root = document) => root.querySelector(selector);
  const $$ = (selector, root = document) => [...root.querySelectorAll(selector)];
  const apiBase = window.dashboardConfig?.apiBase;
  if (!apiBase || !$('#identity-table-body')) return;

  const state = { page: 1, totalPages: 1, timer: null, submission: null };
  const table = $('#identity-table-body');
  const loading = $('#identity-loading');
  const error = $('#identity-error');
  const content = $('#identity-content');
  const search = $('#identity-search');
  const clearSearch = $('#clear-identity-search');
  const status = $('#identity-status');
  const drawer = $('#identity-drawer');
  const backdrop = $('#identity-drawer-backdrop');

  const request = async (path, options = {}) => {
    const token = localStorage.getItem('flow_admin_token');
    const headers = { Accept: 'application/json', ...(options.headers || {}) };
    if (token) headers.Authorization = `Bearer ${token}`;
    const response = await fetch(`${apiBase}/onboarding.php${path}`, { credentials: 'same-origin', ...options, headers });
    const data = await response.json().catch(() => ({}));
    if (!response.ok || !data.success) throw new Error(data.error || 'The request could not be completed.');
    return data;
  };
  const addText = (parent, tag, value, className) => { const node = document.createElement(tag); if (className) node.className = className; node.textContent = value; parent.append(node); return node; };
  const clear = (node) => { while (node?.firstChild) node.removeChild(node.firstChild); };
  const title = (value) => String(value || '').replace(/_/g, ' ').replace(/\b\w/g, (letter) => letter.toUpperCase());
  const date = (value) => value ? new Date(`${value.replace(' ', 'T')}Z`).toLocaleDateString() : '—';
  const fileLabel = (value) => ({
    identityDocument: 'Identity document', addressDocument: 'Address document', registrationDocument: 'Registration document',
    bankStatement: 'Bank statement', businessLogo: 'Business logo',
  }[value] || 'Supporting document');

  const render = (data) => {
    Object.entries(data.summary || {}).forEach(([key, value]) => $$(`[data-identity-summary="${key}"]`).forEach((node) => { node.textContent = String(value); }));
    clear(table); const submissions = data.submissions || [];
    $('#identity-empty').hidden = submissions.length > 0;
    submissions.forEach((item) => {
      const row = document.createElement('tr');
      const customer = document.createElement('td'); const initials = document.createElement('span'); initials.className = 'identity-initials';
      initials.textContent = (item.full_name || '?').split(/\s+/).slice(0, 2).map((word) => word[0]).join('').toUpperCase();
      const identity = document.createElement('span'); identity.className = 'identity-person';
      addText(identity, 'strong', item.full_name || 'Unnamed customer'); addText(identity, 'small', item.email || 'No email address'); customer.append(initials, identity); row.append(customer);
      addText(row, 'td', item.trading_name || item.legal_name || 'Personal account');
      addText(row, 'td', item.account_type === 'business' ? 'Business' : 'Individual');
      const documents = document.createElement('td'); addText(documents, 'span', `${item.document_count || 0} file${Number(item.document_count) === 1 ? '' : 's'}`, 'document-count'); row.append(documents);
      addText(row, 'td', date(item.submitted_at || item.updated_at));
      const statusCell = document.createElement('td'); addText(statusCell, 'span', title(item.status), `identity-status ${item.status}`); row.append(statusCell);
      const action = document.createElement('td'); action.className = 'identity-actions'; const button = document.createElement('button'); button.type = 'button'; button.dataset.submissionId = item.id;
      button.setAttribute('aria-label', `Review ${item.full_name}`); button.innerHTML = '<i data-lucide="eye" class="icon" aria-hidden="true"></i>'; action.append(button); row.append(action); table.append(row);
    });
    const pagination = data.pagination || {}; state.totalPages = pagination.total_pages || 1;
    $('#identity-pagination-summary').textContent = pagination.total ? `Showing ${pagination.from}–${pagination.to} of ${pagination.total} submissions` : 'No submissions found';
    $('#identity-page-current').textContent = `Page ${pagination.page || 1} of ${state.totalPages}`;
    $('#identity-previous').disabled = state.page <= 1; $('#identity-next').disabled = state.page >= state.totalPages;
    window.lucide?.createIcons({ attrs: { 'stroke-width': 1.8 } });
  };
  const load = async () => {
    loading.hidden = false; error.hidden = true; content.hidden = true;
    try {
      const params = new URLSearchParams({ page: String(state.page), limit: '25', q: search.value.trim(), status: status.value });
      render(await request(`?${params}`)); content.hidden = false;
    } catch (requestError) { error.querySelector('span').textContent = requestError.message || 'Identity submissions could not be loaded.'; error.hidden = false; }
    finally { loading.hidden = true; }
  };
  const closeDrawer = () => { drawer.classList.remove('open'); drawer.setAttribute('aria-hidden', 'true'); backdrop.classList.remove('open'); backdrop.hidden = true; };
  const addDetail = (list, label, value) => { const row = document.createElement('div'); addText(row, 'dt', label); addText(row, 'dd', value || '—'); list.append(row); };
  const renderDrawer = (data) => {
    const submission = data.submission; state.submission = submission;
    $('#identity-drawer-title').textContent = data.identity.owner_name || submission.full_name || 'Identity review';
    const root = $('#identity-drawer-content'); clear(root);
    const decision = document.createElement('div'); decision.className = 'identity-decision-state';
    addText(decision, 'span', title(submission.status), `identity-status ${submission.status}`);
    if (submission.review_note) addText(decision, 'p', `Previous note: ${submission.review_note}`);
    root.append(decision);
    const personalHeading = addText(root, 'h3', 'Personal details');
    personalHeading.className = 'identity-section-title';
    const personal = document.createElement('dl'); personal.className = 'identity-detail-list';
    addDetail(personal, 'Full name', data.identity.owner_name); addDetail(personal, 'Email address', data.identity.owner_email); addDetail(personal, 'Phone number', data.identity.phone); addDetail(personal, 'Account type', submission.account_type === 'business' ? 'Business' : 'Individual'); root.append(personal);
    const businessHeading = addText(root, 'h3', 'Business details'); businessHeading.className = 'identity-section-title';
    const business = document.createElement('dl'); business.className = 'identity-detail-list';
    addDetail(business, 'Business name', data.business.name); addDetail(business, 'Business type', title(data.business.type)); addDetail(business, 'Industry', data.business.industry); addDetail(business, 'Website', data.business.website); root.append(business);
    const docHeading = addText(root, 'h3', 'Submitted documents'); docHeading.className = 'identity-section-title'; root.append(docHeading);
    const docs = document.createElement('div'); docs.className = 'identity-documents';
    if (!(data.documents || []).length) addText(docs, 'p', 'No documents were submitted.', 'dashboard-empty');
    else data.documents.forEach((file) => { const item = document.createElement('div'); const details = document.createElement('span'); addText(details, 'strong', fileLabel(file.field_name)); addText(details, 'small', `${file.mime_type} · ${Math.ceil(Number(file.byte_size || 0) / 1024)} KB`); const view = document.createElement('button'); view.type = 'button'; view.dataset.fileId = file.id; view.innerHTML = '<i data-lucide="external-link" class="icon" aria-hidden="true"></i>View'; item.append(details, view); docs.append(item); }); root.append(docs);
    if (submission.status !== 'approved') {
      const reviewHeading = addText(root, 'h3', 'Review decision'); reviewHeading.className = 'identity-section-title'; root.append(reviewHeading);
      const note = document.createElement('label'); note.className = 'identity-note'; addText(note, 'span', 'Note for the customer'); const area = document.createElement('textarea'); area.id = 'identity-review-note'; area.maxLength = 500; area.placeholder = 'Required when requesting changes.'; note.append(area); root.append(note);
      const actions = document.createElement('div'); actions.className = 'identity-review-actions';
      if (submission.status === 'submitted') { const begin = document.createElement('button'); begin.type = 'button'; begin.dataset.identityDecision = 'under_review'; begin.className = 'button secondary-review'; begin.textContent = 'Start review'; actions.append(begin); }
      const requestChanges = document.createElement('button'); requestChanges.type = 'button'; requestChanges.dataset.identityDecision = 'rejected'; requestChanges.className = 'button request-changes'; requestChanges.textContent = 'Request changes';
      const approve = document.createElement('button'); approve.type = 'button'; approve.dataset.identityDecision = 'approved'; approve.className = 'button approve-identity'; approve.textContent = 'Approve identity';
      actions.append(requestChanges, approve); root.append(actions);
    }
    window.lucide?.createIcons({ attrs: { 'stroke-width': 1.8 } });
  };
  const openDrawer = async (id) => {
    drawer.setAttribute('aria-hidden', 'false'); drawer.classList.add('open'); backdrop.hidden = false; backdrop.classList.add('open');
    $('#identity-drawer-title').textContent = 'Loading identity…'; clear($('#identity-drawer-content'));
    try { renderDrawer(await request(`?id=${encodeURIComponent(id)}`)); }
    catch (requestError) { $('#identity-drawer-title').textContent = 'Identity details unavailable'; addText($('#identity-drawer-content'), 'p', requestError.message || 'This submission could not be loaded.', 'dashboard-empty'); }
  };
  const viewDocument = async (fileId) => {
    const viewer = window.open('', '_blank');
    if (!viewer) return window.dashboardShowToast?.('Allow pop-ups to view identity documents.');
    viewer.opener = null;
    try {
      const token = localStorage.getItem('flow_admin_token'); const headers = {};
      if (token) headers.Authorization = `Bearer ${token}`;
      const response = await fetch(`${apiBase}/onboarding-file.php?file_id=${encodeURIComponent(fileId)}`, { headers, credentials: 'same-origin' });
      if (!response.ok) throw new Error('The identity document is unavailable.');
      const url = URL.createObjectURL(await response.blob()); viewer.location.href = url; window.setTimeout(() => URL.revokeObjectURL(url), 60000);
    } catch (requestError) { viewer.close(); window.dashboardShowToast?.(requestError.message || 'The identity document is unavailable.'); }
  };
  const decide = async (button) => {
    const reviewStatus = button.dataset.identityDecision; if (!state.submission) return;
    const note = $('#identity-review-note')?.value.trim() || '';
    if (reviewStatus === 'rejected' && note.length < 5) return window.dashboardShowToast?.('Add a clear reason before requesting changes.');
    const message = reviewStatus === 'approved' ? 'Approve this identity submission?' : reviewStatus === 'rejected' ? 'Request changes from this customer?' : 'Move this submission into review?';
    if (!window.confirm(message)) return;
    const original = button.textContent; button.disabled = true; button.setAttribute('aria-busy', 'true'); button.innerHTML = '<span class="spinner" aria-hidden="true"></span>Saving…';
    try {
      const data = await request('', { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify({ submission_id: state.submission.id, status: reviewStatus, review_note: note }) });
      window.dashboardShowToast?.(data.message || 'Review saved.'); closeDrawer(); await load();
    } catch (requestError) { window.dashboardShowToast?.(requestError.message || 'The review could not be saved.'); }
    finally { button.disabled = false; button.removeAttribute('aria-busy'); button.textContent = original; }
  };

  table.addEventListener('click', (event) => { const button = event.target.closest('button[data-submission-id]'); if (button) openDrawer(button.dataset.submissionId); });
  $('#identity-drawer-content').addEventListener('click', (event) => { const file = event.target.closest('button[data-file-id]'); if (file) viewDocument(file.dataset.fileId); const decision = event.target.closest('button[data-identity-decision]'); if (decision) decide(decision); });
  $('#close-identity-drawer').addEventListener('click', closeDrawer); backdrop.addEventListener('click', closeDrawer);
  document.addEventListener('keydown', (event) => { if (event.key === 'Escape') closeDrawer(); });
  search.addEventListener('input', () => { clearSearch.hidden = !search.value; clearTimeout(state.timer); state.timer = setTimeout(() => { state.page = 1; load(); }, 300); });
  clearSearch.addEventListener('click', () => { search.value = ''; clearSearch.hidden = true; state.page = 1; load(); search.focus(); });
  status.addEventListener('change', () => { state.page = 1; load(); });
  $('#identity-previous').addEventListener('click', () => { if (state.page > 1) { state.page -= 1; load(); } });
  $('#identity-next').addEventListener('click', () => { if (state.page < state.totalPages) { state.page += 1; load(); } });
  $('[data-identity-retry]').addEventListener('click', load);
  load();
})();

