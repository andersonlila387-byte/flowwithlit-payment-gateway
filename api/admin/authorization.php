<?php
declare(strict_types=1);

require_once __DIR__.'/../_admin.php';

$input = adminMethod(['GET', 'POST']);
$pdo = Database::getInstance();
[$session, $role] = requireAdmin($pdo, $input);
$adminId = (int) $session['admin']['id'];

$roles = [
    ['key'=>'support', 'label'=>'Support', 'locked'=>false],
    ['key'=>'reviewer', 'label'=>'Reviewer', 'locked'=>false],
    ['key'=>'finance', 'label'=>'Finance', 'locked'=>false],
    ['key'=>'super_admin', 'label'=>'Super Administrator', 'locked'=>true],
];
$permissions = [
    ['key'=>'overview', 'group'=>'Workspace', 'label'=>'Operations overview', 'kind'=>'access', 'description'=>'View the operational summary and attention queue.'],
    ['key'=>'customers', 'group'=>'Workspace', 'label'=>'Customer accounts', 'kind'=>'access', 'description'=>'Open customer profiles and account status.'],
    ['key'=>'verification', 'group'=>'Workspace', 'label'=>'Verification queue', 'kind'=>'access', 'description'=>'Review onboarding and identity submissions.'],
    ['key'=>'payouts', 'group'=>'Money operations', 'label'=>'Payouts', 'kind'=>'access', 'description'=>'Review and process payout requests.'],
    ['key'=>'transactions', 'group'=>'Money operations', 'label'=>'Transactions', 'kind'=>'access', 'description'=>'View payment and transaction records.'],
    ['key'=>'wallets', 'group'=>'Money operations', 'label'=>'Wallets', 'kind'=>'access', 'description'=>'View wallet status and balances.'],
    ['key'=>'staff', 'group'=>'Administration', 'label'=>'Team and access', 'kind'=>'action', 'description'=>'Add staff and change administrator roles.'],
    ['key'=>'authorization', 'group'=>'Administration', 'label'=>'Authorization settings', 'kind'=>'action', 'description'=>'Change permissions and approval rules.'],
    ['key'=>'audit', 'group'=>'Administration', 'label'=>'Audit log', 'kind'=>'access', 'description'=>'View administrator actions and decisions.'],
    ['key'=>'currency_controls', 'group'=>'Administration', 'label'=>'Currency controls', 'kind'=>'action', 'description'=>'Enable or disable supported currencies.'],
];
$defaults = [
    'support'=>['overview'=>'view','customers'=>'manage','verification'=>'none','payouts'=>'none','transactions'=>'view','wallets'=>'view','staff'=>'none','authorization'=>'none','audit'=>'none','currency_controls'=>'none'],
    'reviewer'=>['overview'=>'view','customers'=>'view','verification'=>'manage','payouts'=>'none','transactions'=>'view','wallets'=>'none','staff'=>'none','authorization'=>'none','audit'=>'view','currency_controls'=>'none'],
    'finance'=>['overview'=>'view','customers'=>'view','verification'=>'none','payouts'=>'manage','transactions'=>'manage','wallets'=>'manage','staff'=>'none','authorization'=>'none','audit'=>'view','currency_controls'=>'view'],
    'super_admin'=>array_fill_keys(array_column($permissions, 'key'), 'manage'),
];
$ruleDefaults = [
    ['key'=>'payout_review','label'=>'Payout review','description'=>'Require a second admin before a payout is approved.','enabled'=>1,'threshold'=>0],
    ['key'=>'manual_wallet_credit','label'=>'Manual wallet credit','description'=>'Require approval for an operator-created wallet credit.','enabled'=>1,'threshold'=>0],
    ['key'=>'staff_access_change','label'=>'Staff access changes','description'=>'Require approval before changing administrator access.','enabled'=>1],
    ['key'=>'currency_change','label'=>'Currency controls','description'=>'Require approval before enabling or disabling a currency.','enabled'=>0],
];

$tableExists = static function (PDO $db, string $table): bool {
    $stmt = $db->prepare('SELECT COUNT(*) FROM information_schema.tables WHERE table_schema=DATABASE() AND table_name=?');
    $stmt->execute([$table]);
    return (bool) $stmt->fetchColumn();
};

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    requireAdminRole($role, ['super_admin'], 'change authorization settings');
    $action = (string) ($input['action'] ?? '');
    if ($action === 'save_matrix') {
        if (!$tableExists($pdo, 'admin_permission_overrides')) jsonResponse(['success'=>false,'error'=>'Authorization storage is not installed. Run the latest database setup migration.'], 503);
        $matrix = is_array($input['matrix'] ?? null) ? $input['matrix'] : [];
        $allowedRoles = array_column($roles, 'key'); $allowedPermissions = array_column($permissions, 'key');
        $pdo->beginTransaction();
        try {
            $stmt = $pdo->prepare('INSERT INTO admin_permission_overrides(role,permission_key,level,updated_by) VALUES(?,?,?,?) ON DUPLICATE KEY UPDATE level=VALUES(level),updated_by=VALUES(updated_by),updated_at=UTC_TIMESTAMP()');
            foreach ($matrix as $roleKey => $values) {
                if (!in_array($roleKey, $allowedRoles, true) || $roleKey === 'super_admin' || !is_array($values)) continue;
                foreach ($values as $permissionKey => $level) if (in_array($permissionKey, $allowedPermissions, true) && in_array($level, ['none','view','manage'], true)) $stmt->execute([$roleKey, $permissionKey, $level, $adminId]);
            }
            $pdo->commit(); adminAudit($pdo, $adminId, 'admin.authorization_matrix_updated', 'authorization', null, ['roles'=>array_keys($matrix)]);
            jsonResponse(['success'=>true,'message'=>'Permissions saved.']);
        } catch (Throwable $e) { if ($pdo->inTransaction()) $pdo->rollBack(); throw $e; }
    }
    if ($action === 'save_rules') {
        if (!$tableExists($pdo, 'admin_approval_rules')) jsonResponse(['success'=>false,'error'=>'Authorization storage is not installed. Run the latest database setup migration.'], 503);
        $rules = is_array($input['rules'] ?? null) ? $input['rules'] : [];
        $allowed = array_column($ruleDefaults, 'key'); $stmt = $pdo->prepare('INSERT INTO admin_approval_rules(rule_key,enabled,threshold,updated_by) VALUES(?,?,?,?) ON DUPLICATE KEY UPDATE enabled=VALUES(enabled),threshold=VALUES(threshold),updated_by=VALUES(updated_by),updated_at=UTC_TIMESTAMP()');
        foreach ($rules as $rule) if (is_array($rule) && in_array($rule['key'] ?? '', $allowed, true)) $stmt->execute([$rule['key'], !empty($rule['enabled']) ? 1 : 0, isset($rule['threshold']) ? max(0, (float) $rule['threshold']) : null, $adminId]);
        adminAudit($pdo, $adminId, 'admin.approval_rules_updated', 'authorization');
        jsonResponse(['success'=>true,'message'=>'Approval rules saved.']);
    }
    if ($action === 'decide') {
        requireAdminRole($role, ['finance','super_admin'], 'decide approval requests');
        $uuid = (string) ($input['uuid'] ?? '');
        $decision = (string) ($input['decision'] ?? '');
        $payoutUuid = str_starts_with($uuid, 'payout-') ? substr($uuid, 7) : '';
        if ($payoutUuid === '' || !in_array($decision, ['approve','reject'], true)) jsonResponse(['success'=>false,'error'=>'Invalid approval decision.'], 422);
        $stmt = $pdo->prepare('SELECT id,reviewed_by_admin_id,status FROM payout_requests WHERE uuid=? LIMIT 1');
        $stmt->execute([$payoutUuid]); $payout = $stmt->fetch();
        if (!$payout || !in_array($payout['status'], ['pending','approved','processing'], true)) jsonResponse(['success'=>false,'error'=>'This request is no longer awaiting approval.'], 409);
        if ((int) ($payout['reviewed_by_admin_id'] ?? 0) === $adminId) jsonResponse(['success'=>false,'error'=>'You cannot approve your own request.'], 403);
        $newStatus = $decision === 'approve' ? 'approved' : 'cancelled';
        $note = substr(trim((string) ($input['note'] ?? '')), 0, 500) ?: null;
        if ($decision === 'reject' && $note === null) jsonResponse(['success'=>false,'error'=>'A rejection note is required.'], 422);
        $update = $pdo->prepare('UPDATE payout_requests SET status=?,reviewed_by_admin_id=?,reviewed_at=UTC_TIMESTAMP(),review_note=? WHERE id=? AND status IN ("pending","approved","processing")');
        $update->execute([$newStatus, $adminId, $note, (int) $payout['id']]);
        if (!$update->rowCount()) jsonResponse(['success'=>false,'error'=>'This request was already updated.'], 409);
        adminAudit($pdo, $adminId, 'admin.approval_decided', 'payout', (string) $payout['id'], ['decision'=>$decision]);
        jsonResponse(['success'=>true,'message'=>$decision === 'approve' ? 'Payout approved.' : 'Payout rejected.']);
    }
    jsonResponse(['success'=>false,'error'=>'Unknown authorization action.'], 422);
}

$matrix = $defaults;
if ($tableExists($pdo, 'admin_permission_overrides')) {
    $overrides = $pdo->query('SELECT role,permission_key,level FROM admin_permission_overrides')->fetchAll();
    foreach ($overrides as $item) if (isset($matrix[$item['role']][$item['permission_key']]) && $item['role'] !== 'super_admin') $matrix[$item['role']][$item['permission_key']] = $item['level'];
}
$rules = $ruleDefaults;
if ($tableExists($pdo, 'admin_approval_rules')) {
    $stored = $pdo->query('SELECT rule_key,enabled,threshold FROM admin_approval_rules')->fetchAll();
    foreach ($stored as $item) foreach ($rules as &$rule) if ($rule['key'] === $item['rule_key']) { $rule['enabled'] = (int) $item['enabled']; if (array_key_exists('threshold', $rule)) $rule['threshold'] = $item['threshold']; }
    unset($rule);
}

$approvals = [];
if (in_array($role, ['finance','super_admin'], true)) {
    $stmt = $pdo->query("SELECT p.id,p.uuid,p.amount,p.currency_code,p.created_at,p.reviewed_by_admin_id,u.full_name requester_name
        FROM payout_requests p INNER JOIN users u ON u.id=p.user_id
        WHERE p.status IN ('pending','approved','processing') ORDER BY p.created_at ASC LIMIT 50");
    foreach ($stmt->fetchAll() as $item) $approvals[] = ['uuid'=>'payout-'.$item['uuid'],'requested_at'=>$item['created_at'],'requester_name'=>$item['requester_name'],'action_label'=>'Payout review','summary'=>'Requested in '.$item['currency_code'],'amount'=>$item['amount'],'can_decide'=>(int)($item['reviewed_by_admin_id'] ?? 0) !== $adminId];
}

jsonResponse(['success'=>true,'roles'=>$roles,'permissions'=>$permissions,'matrix'=>$matrix,'rules'=>$rules,'approvals'=>$approvals]);
