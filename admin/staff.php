<?php
declare(strict_types=1);

$localPreview = in_array($_SERVER['REMOTE_ADDR'] ?? '', ['127.0.0.1', '::1'], true) && ($_GET['preview'] ?? '') === '1';
$adminApiBase = rtrim((string) (getenv('FLOW_ADMIN_API_URL') ?: '../api/admin'), '/');
require_once __DIR__.'/components/layout.php';
admin_use_shared_layout('Admin Setup', $localPreview);
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><meta name="robots" content="noindex,nofollow,noarchive">
<title>Admin Setup · Flowwithlit Admin</title>
<script src="https://cdn.tailwindcss.com"></script>
<link rel="stylesheet" href="assets/dashboard.css?v=4"><link rel="stylesheet" href="assets/users-list.css?v=1">
<style>
  @media(min-width: 1101px) {
    .operation-metrics.metrics-4 {
      grid-template-columns: repeat(4, minmax(0, 1fr));
    }
  }
  .staff-tools { display: flex; gap: 1rem; flex-wrap: wrap; align-items: center; }
  .staff-search { position: relative; flex: 1; min-width: 250px; }
  .staff-search .icon { position: absolute; left: 0.75rem; top: 50%; transform: translateY(-50%); color: var(--text-muted); width: 18px; height: 18px; pointer-events: none; }
  .staff-search input { width: 100%; padding: 0.5rem 0.75rem 0.5rem 2.5rem; border: 1px solid var(--border-color); border-radius: 0.5rem; background: var(--bg-base); color: var(--text-base); font-size: 0.875rem; }
  .staff-search input:focus { outline: none; border-color: var(--brand-color); box-shadow: 0 0 0 2px var(--brand-color-alpha); }
  
  .staff-table { width: 100%; text-align: left; border-collapse: collapse; white-space: nowrap; }
  .staff-table th { padding: 0.75rem 1rem; border-bottom: 1px solid var(--border-color); color: var(--text-muted); font-size: 0.75rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; }
  .staff-table td { padding: 1rem; border-bottom: 1px solid var(--border-color); font-size: 0.875rem; color: var(--text-base); }
  .staff-table tbody tr:hover { background: var(--bg-hover); }
  
  .role-badge { display: inline-flex; align-items: center; padding: 0.2rem 0.6rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 600; text-transform: capitalize; }
  .role-badge.super_admin { background: rgba(112, 98, 255, 0.15); color: #7062ff; }
  .role-badge.finance { background: rgba(16, 185, 129, 0.15); color: #10b981; }
  .role-badge.support { background: rgba(59, 130, 246, 0.15); color: #3b82f6; }
  .role-badge.reviewer { background: rgba(245, 158, 11, 0.15); color: #f59e0b; }

  .actions-cell { position: relative; }
  .dots-btn { background: var(--soft); border: 1px solid var(--line); border-radius: 4px; padding: 5px 8px; color: var(--text); cursor: pointer; display: inline-flex; align-items: center; justify-content: center; }
  .dots-btn:hover { background: var(--lavender); color: var(--navy); }
  
  .dropdown-menu { position: fixed; z-index: 130; background: var(--surface); border: 1px solid var(--line); border-radius: 6px; box-shadow: 0 8px 24px rgba(0,0,0,0.18); min-width: 170px; padding: 4px; display: flex; flex-direction: column; gap: 2px; }
  .dropdown-item { width: 100%; text-align: left; padding: 7px 10px; border-radius: 4px; font-size: 0.8rem; background: transparent; color: var(--text); border: none; cursor: pointer; display: flex; align-items: center; gap: 8px; }
  .dropdown-item:hover { background: var(--lavender); color: var(--navy); }
  .dropdown-item.danger-item { color: #ef4444; }
  .dropdown-item.danger-item:hover { background: #fee2e2; color: #b91c1c; }

  /* Modal Dialog styles */
  .modal-backdrop { position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 100; display: flex; align-items: center; justify-content: center; padding: 1rem; }
  .modal-card { background: var(--surface); border: 1px solid var(--line); border-radius: 8px; width: 100%; max-width: 480px; box-shadow: 0 10px 25px rgba(0,0,0,0.15); overflow: hidden; }
  .modal-header { padding: 1rem 1.25rem; border-bottom: 1px solid var(--line); display: flex; justify-content: space-between; align-items: center; }
  .modal-header h3 { font-size: 1.1rem; font-weight: 600; margin: 0; color: var(--text); }
  .modal-body { padding: 1.25rem; display: flex; flex-direction: column; gap: 1rem; }
  .modal-footer { padding: 1rem 1.25rem; border-top: 1px solid var(--line); display: flex; justify-content: flex-end; gap: 0.5rem; background: var(--soft); }
  .form-group { display: flex; flex-direction: column; gap: 0.35rem; }
  .form-group label { font-size: 0.8rem; font-weight: 500; color: var(--muted); }
  .form-group input, .form-group select { padding: 0.5rem 0.75rem; border: 1px solid var(--line); border-radius: 4px; background: var(--surface); color: var(--text); font-size: 0.875rem; }
  .form-group input:focus, .form-group select:focus { outline: none; border-color: #7062ff; }
</style>
</head>
<body class="min-h-screen w-full antialiased">
<div class="dashboard-shell w-full min-h-screen"><aside class="sidebar" aria-label="Main navigation"></aside><div class="main-layout"><header class="topbar"></header>
<main>
<div class="page-heading">
  <div>
    <h1>Admin Setup</h1>
    <div class="breadcrumb"><span>Home</span><span>/</span><strong>Admin Setup</strong></div>
  </div>
  <div class="page-actions">
    <button class="button slate-button" id="open-add-admin-modal" type="button"><i data-lucide="user-plus" class="icon" aria-hidden="true"></i> Add New Admin</button>
  </div>
</div>

<section class="panel operations-panel" aria-labelledby="staff-summary-heading">
  <div class="panel-heading operations-panel-heading">
    <div>
      <h2 id="staff-summary-heading">Administrators Overview</h2>
      <p class="panel-description">Manage administrator accounts, roles, access status, and security permissions.</p>
    </div>
  </div>
  <div class="operation-metrics metrics-4">
    <article class="operation-metric">
      <span class="operation-icon"><i data-lucide="shield-check" class="icon" aria-hidden="true"></i></span>
      <span>Total Admins</span>
      <strong id="summary-total">—</strong>
      <small>Registered administrators</small>
    </article>
    <article class="operation-metric">
      <span class="operation-icon"><i data-lucide="crown" class="icon" aria-hidden="true"></i></span>
      <span>Super Admins</span>
      <strong id="summary-super">—</strong>
      <small>Full access control</small>
    </article>
    <article class="operation-metric">
      <span class="operation-icon"><i data-lucide="user-check" class="icon" aria-hidden="true"></i></span>
      <span>Active Accounts</span>
      <strong id="summary-active">—</strong>
      <small>Enabled administrators</small>
    </article>
    <article class="operation-metric">
      <span class="operation-icon danger"><i data-lucide="user-x" class="icon" aria-hidden="true"></i></span>
      <span>Disabled Accounts</span>
      <strong id="summary-disabled">—</strong>
      <small>Access restricted</small>
    </article>
  </div>
</section>

<section class="panel transaction-list-panel" aria-labelledby="staff-table-heading">
<div class="panel-heading transaction-list-heading">
  <div>
    <h2 id="staff-table-heading">Administrator Directory</h2>
    <p class="panel-description">Search by name or email, manage administrator access and privileges.</p>
  </div>
  <div class="staff-tools">
    <label class="staff-search">
      <i data-lucide="search" class="icon" aria-hidden="true"></i>
      <input id="staff-search-input" type="search" placeholder="Search admin by name or email..." autocomplete="off">
    </label>

    <label class="select-control">
      <select id="staff-filter-role" aria-label="Filter by role">
        <option value="">All Roles</option>
        <option value="super_admin">Super Admin</option>
        <option value="finance">Finance</option>
        <option value="support">Support</option>
        <option value="reviewer">Reviewer</option>
      </select>
      <i data-lucide="chevron-down" class="icon" aria-hidden="true"></i>
    </label>

    <label class="select-control">
      <select id="staff-filter-status" aria-label="Filter by status">
        <option value="">All Statuses</option>
        <option value="active">Active</option>
        <option value="disabled">Disabled</option>
      </select>
      <i data-lucide="chevron-down" class="icon" aria-hidden="true"></i>
    </label>
  </div>
</div>

<div id="staff-loading" class="dashboard-state" role="status"><span class="spinner" aria-hidden="true"></span>Loading administrators…</div>
<div id="staff-error" class="dashboard-state dashboard-error" role="alert" hidden><i data-lucide="circle-alert" class="icon" aria-hidden="true"></i><span>Administrators could not be loaded.</span><button class="button" type="button" data-staff-retry>Try again</button></div>

<div id="staff-content" hidden>
  <div class="table-scroll">
    <table class="staff-table">
      <thead>
        <tr>
          <th>Administrator</th>
          <th>Role</th>
          <th>Status</th>
          <th>2FA</th>
          <th>Last Login</th>
          <th>Created Date</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody id="staff-table-body">
        <!-- JS will populate rows -->
      </tbody>
    </table>
    <p id="staff-empty" class="dashboard-empty" hidden>No administrators match the current search filters.</p>
  </div>
</div>
</section>
</main></div></div>

<!-- Add Admin Modal -->
<div id="modal-add-admin" class="modal-backdrop" hidden>
  <div class="modal-card">
    <div class="modal-header">
      <h3>Add New Administrator</h3>
      <button type="button" class="close-modal" data-modal="modal-add-admin"><i data-lucide="x" class="icon" aria-hidden="true"></i></button>
    </div>
    <form id="form-add-admin">
      <div class="modal-body">
        <div class="form-group">
          <label for="add-full-name">Full Name</label>
          <input type="text" id="add-full-name" required placeholder="e.g. Alex Johnson">
        </div>
        <div class="form-group">
          <label for="add-email">Work Email Address</label>
          <input type="email" id="add-email" required placeholder="admin@flowwithlit.com">
        </div>
        <div class="form-group">
          <label for="add-role">Role Permission</label>
          <select id="add-role" required>
            <option value="support">Support Administrator</option>
            <option value="finance">Finance Administrator</option>
            <option value="reviewer">Identity Reviewer</option>
            <option value="super_admin">Super Administrator</option>
          </select>
        </div>
        <div class="form-group">
          <label for="add-password">Temporary Password (min. 12 chars)</label>
          <input type="password" id="add-password" required minlength="12" placeholder="••••••••••••">
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="button filter-button close-modal" data-modal="modal-add-admin">Cancel</button>
        <button type="submit" class="button slate-button" id="submit-add-admin">Create Admin</button>
      </div>
    </form>
  </div>
</div>

<!-- Change Role Modal -->
<div id="modal-edit-role" class="modal-backdrop" hidden>
  <div class="modal-card">
    <div class="modal-header">
      <h3>Update Administrator Role</h3>
      <button type="button" class="close-modal" data-modal="modal-edit-role"><i data-lucide="x" class="icon" aria-hidden="true"></i></button>
    </div>
    <form id="form-edit-role">
      <input type="hidden" id="edit-role-uuid">
      <div class="modal-body">
        <p id="edit-role-admin-info" class="text-sm color-muted" style="margin-bottom:0.5rem;"></p>
        <div class="form-group">
          <label for="edit-role-select">Select New Role</label>
          <select id="edit-role-select" required>
            <option value="support">Support Administrator</option>
            <option value="finance">Finance Administrator</option>
            <option value="reviewer">Identity Reviewer</option>
            <option value="super_admin">Super Administrator</option>
          </select>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="button filter-button close-modal" data-modal="modal-edit-role">Cancel</button>
        <button type="submit" class="button slate-button" id="submit-edit-role">Save Role</button>
      </div>
    </form>
  </div>
</div>

<!-- Reset Password Modal -->
<div id="modal-reset-password" class="modal-backdrop" hidden>
  <div class="modal-card">
    <div class="modal-header">
      <h3>Reset Admin Password</h3>
      <button type="button" class="close-modal" data-modal="modal-reset-password"><i data-lucide="x" class="icon" aria-hidden="true"></i></button>
    </div>
    <form id="form-reset-password">
      <input type="hidden" id="reset-password-uuid">
      <div class="modal-body">
        <p id="reset-password-admin-info" class="text-sm color-muted" style="margin-bottom:0.5rem;"></p>
        <div class="form-group">
          <label for="reset-new-password">New Password (min 12 characters)</label>
          <input type="password" id="reset-new-password" required minlength="12" placeholder="••••••••••••">
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="button filter-button close-modal" data-modal="modal-reset-password">Cancel</button>
        <button type="submit" class="button slate-button" id="submit-reset-password">Update Password</button>
      </div>
    </form>
  </div>
</div>

<!-- Delete Admin Confirmation Modal -->
<div id="modal-delete-admin" class="modal-backdrop" hidden>
  <div class="modal-card">
    <div class="modal-header">
      <h3>Delete Administrator</h3>
      <button type="button" class="close-modal" data-modal="modal-delete-admin"><i data-lucide="x" class="icon" aria-hidden="true"></i></button>
    </div>
    <form id="form-delete-admin">
      <input type="hidden" id="delete-admin-uuid">
      <div class="modal-body">
        <p id="delete-admin-info" style="color: #ef4444; font-weight: 500; margin: 0;"></p>
        <p style="font-size: 0.85rem; color: var(--muted); margin: 0.25rem 0 0;">This action will permanently delete this administrator account and revoke all their sessions. This cannot be undone.</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="button filter-button close-modal" data-modal="modal-delete-admin">Cancel</button>
        <button type="submit" class="button slate-button" style="background:#ef4444; border-color:#ef4444;" id="submit-delete-admin">Delete Account</button>
      </div>
    </form>
  </div>
</div>

<div id="toast" class="toast" role="status" hidden></div>
<script>window.dashboardConfig=<?= json_encode(['localPreview'=>$localPreview,'apiBase'=>$adminApiBase], JSON_HEX_TAG|JSON_HEX_AMP|JSON_HEX_APOS|JSON_HEX_QUOT) ?>;</script>
<script src="assets/dashboard.js?v=5"></script>
<script src="assets/staff.js?v=2"></script>
</body></html>
