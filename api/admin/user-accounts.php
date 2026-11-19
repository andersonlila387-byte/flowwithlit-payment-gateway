<?php
declare(strict_types=1);
/**
 * Admin → Users → User Accounts.
 *
 * GET               list customers with their generated NGN receiving account, crypto addresses,
 *                   withdrawal banks and country; summary stats; duplicate-account monitor.
 *                   Params: page, limit, q (name/email/phone/tag/account number/bank), filter.
 * GET ?uuid=        one customer's accounts in full, with the provisioning job state.
 * POST action=regenerate        {user_uuid}    open a receiving account for an approved customer with none.
 * POST action=delete_receiving  {account_uuid} delete a receiving account that has no deposit history.
 * POST action=retire_crypto     {address_id}   retire a crypto deposit address.
 */
require_once __DIR__.'/../_admin.php';
require_once __DIR__.'/../../services/ReceivingAccountProvisioner.php';
require_once __DIR__.'/../../services/PrivateStorage.php';

$input = adminMethod(['GET', 'POST']);
$pdo = Database::getInstance();
[$session, $role] = requireAdmin($pdo, $input);
requireAdminRole($role, ['support', 'finance', 'super_admin'], 'view customer accounts');

/** Full account number for admins; falls back to the masked value if it cannot be decrypted. */
function uaAccountNumber(array $r): string {
    if (!empty($r['account_number_encrypted'])) {
        try { return PrivateStorage::decrypt((string) $r['account_number_encrypted'], 'wallet-account'); } catch (Throwable) {}
    }
    return (string) ($r['account_number_display'] ?: ($r['account_number_last4'] ? '••••'.$r['account_number_last4'] : ''));
}
function uaReceiving(array $r): array {
    return ['uuid'=>$r['uuid'], 'provider'=>$r['provider'], 'currency_code'=>$r['currency_code'], 'account_name'=>$r['account_name'],
            'bank_name'=>$r['bank_name'], 'account_number'=>$r['status'] === 'failed' ? '' : uaAccountNumber($r), 'status'=>$r['status'],
            'created_at'=>$r['created_at'], 'updated_at'=>$r['updated_at'], 'duplicate'=>(bool) ($r['duplicate'] ?? false)];
}
/** Approved = able to receive an account: active, verified email, phone on file. */
const UA_APPROVED = "u.status='active' AND u.email_verified=1 AND u.phone IS NOT NULL AND u.phone<>''";

// Duplicate monitor: the same account number on more than one record, a customer holding more than one
// active account in one currency, or one crypto address on more than one wallet.
function uaDuplicates(PDO $pdo): array {
    $numbers = $pdo->query("SELECT r.uuid,r.user_id,u.uuid user_uuid,u.full_name,u.email,r.provider,r.bank_name,r.account_name,r.account_number_encrypted,r.account_number_display,r.account_number_last4,r.status,r.currency_code,r.created_at,r.updated_at,
            'same_account_number' reason
        FROM wallet_receiving_accounts r JOIN users u ON u.id=r.user_id
        WHERE r.account_number_hash IN (SELECT account_number_hash FROM wallet_receiving_accounts WHERE account_number_hash IS NOT NULL GROUP BY account_number_hash HAVING COUNT(*)>1)
        ORDER BY r.account_number_hash,r.id")->fetchAll();
    $multiple = $pdo->query("SELECT r.uuid,r.user_id,u.uuid user_uuid,u.full_name,u.email,r.provider,r.bank_name,r.account_name,r.account_number_encrypted,r.account_number_display,r.account_number_last4,r.status,r.currency_code,r.created_at,r.updated_at,
            'multiple_active_accounts' reason
        FROM wallet_receiving_accounts r JOIN users u ON u.id=r.user_id
        WHERE r.status='active' AND (r.user_id,r.currency_code) IN (SELECT user_id,currency_code FROM wallet_receiving_accounts WHERE status='active' GROUP BY user_id,currency_code HAVING COUNT(*)>1)
        ORDER BY r.user_id,r.id")->fetchAll();
    $crypto = $pdo->query("SELECT a.id,w.user_id,u.uuid user_uuid,u.full_name,u.email,w.currency_code,a.network,a.address_display,a.status,a.created_at
        FROM crypto_deposit_addresses a JOIN wallets w ON w.id=a.wallet_id JOIN users u ON u.id=w.user_id
        WHERE (a.network,a.address_display) IN (SELECT network,address_display FROM crypto_deposit_addresses GROUP BY network,address_display HAVING COUNT(*)>1)
        ORDER BY a.network,a.address_display")->fetchAll();
    $rows = [];
    foreach (array_merge($numbers, $multiple) as $r) {
        $rows[] = ['reason'=>$r['reason'], 'user_uuid'=>$r['user_uuid'], 'full_name'=>$r['full_name'], 'email'=>$r['email']] + uaReceiving($r);
    }
    foreach ($crypto as $c) {
        $rows[] = ['reason'=>'same_crypto_address', 'user_uuid'=>$c['user_uuid'], 'full_name'=>$c['full_name'], 'email'=>$c['email'],
                   'provider'=>$c['network'], 'currency_code'=>$c['currency_code'], 'bank_name'=>$c['network'], 'account_name'=>'',
                   'account_number'=>$c['address_display'], 'status'=>$c['status'], 'created_at'=>$c['created_at']];
    }
    $userIds = array_unique(array_merge(array_column($numbers, 'user_id'), array_column($multiple, 'user_id'), array_column($crypto, 'user_id')));
    return [$rows, array_map('intval', $userIds)];
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $userSelect = "SELECT u.id,u.uuid,u.full_name,u.email,u.phone,u.flow_tag,u.account_type,u.status,u.email_verified,u.created_at,
        p.country_code,c.name country_name, (".UA_APPROVED.") approved
        FROM users u LEFT JOIN user_profiles p ON p.user_id=u.id LEFT JOIN countries c ON c.code=p.country_code";

    $uuid = trim((string) ($input['uuid'] ?? ''));
    if ($uuid !== '') {
        if (!preg_match('/^[a-f0-9-]{36}$/i', $uuid)) jsonResponse(['success'=>false, 'error'=>'Invalid customer reference.'], 422);
        $q = $pdo->prepare($userSelect.' WHERE u.uuid=? LIMIT 1');
        $q->execute([$uuid]);
        $user = $q->fetch();
        if (!$user) jsonResponse(['success'=>false, 'error'=>'Customer not found.'], 404);
        $uid = (int) $user['id'];
        [, $dupUserIds] = uaDuplicates($pdo);

        $r = $pdo->prepare("SELECT r.*, (SELECT COUNT(*) FROM gateway_deposit_events e WHERE e.receiving_account_id=r.id) deposits,
                (r.account_number_hash IS NOT NULL AND (SELECT COUNT(*) FROM wallet_receiving_accounts d WHERE d.account_number_hash=r.account_number_hash)>1) duplicate
            FROM wallet_receiving_accounts r WHERE r.user_id=? ORDER BY (r.status='active') DESC, r.id DESC");
        $r->execute([$uid]);
        $receiving = array_map(fn ($row) => uaReceiving($row) + ['deposits'=>(int) $row['deposits']], $r->fetchAll());

        $crypto = $pdo->prepare('SELECT a.id,w.currency_code,a.network,a.address_display,a.tag_memo,a.status,a.created_at FROM crypto_deposit_addresses a JOIN wallets w ON w.id=a.wallet_id WHERE w.user_id=? ORDER BY w.currency_code,a.network');
        $crypto->execute([$uid]);
        $banks = $pdo->prepare('SELECT bank_name,account_name,account_number_last4,currency_code,status,created_at FROM bank_accounts WHERE user_id=? ORDER BY created_at DESC');
        $banks->execute([$uid]);
        $job = $pdo->prepare("SELECT status,attempts,last_error,available_at,updated_at FROM gateway_jobs WHERE job_type='provision_receiving_account' AND job_key=? LIMIT 1");
        $job->execute(['provision:user:'.$uid]);

        $hasActive = (bool) array_filter($receiving, fn ($a) => $a['status'] === 'active');
        unset($user['id']);
        jsonResponse(['success'=>true, 'user'=>$user + ['approved'=>(bool) $user['approved'], 'has_duplicates'=>in_array($uid, $dupUserIds, true)],
            'receiving_accounts'=>$receiving, 'crypto_addresses'=>$crypto->fetchAll(), 'withdrawal_accounts'=>$banks->fetchAll(),
            'provisioning_job'=>$job->fetch() ?: null, 'can_regenerate'=>(bool) $user['approved'] && !$hasActive,
            'active_provider'=>ReceivingAccountProvisioner::activeProvider($pdo)]);
    }

    $page = max(1, (int) ($input['page'] ?? 1));
    $limit = max(10, min(100, (int) ($input['limit'] ?? 25)));
    $filter = (string) ($input['filter'] ?? '');
    if (!in_array($filter, ['', 'with_account', 'missing', 'failed', 'duplicates'], true)) jsonResponse(['success'=>false, 'error'=>'Invalid filter.'], 422);
    $search = trim((string) ($input['q'] ?? ''));
    if (mb_strlen($search) > 100) jsonResponse(['success'=>false, 'error'=>'Search query is too long.'], 422);

    [$duplicates, $dupUserIds] = uaDuplicates($pdo);
    $hasActive = "EXISTS(SELECT 1 FROM wallet_receiving_accounts ra WHERE ra.user_id=u.id AND ra.status='active')";
    $conditions = [];
    $params = [];
    if ($filter === 'with_account') $conditions[] = $hasActive;
    if ($filter === 'missing') $conditions[] = UA_APPROVED.' AND NOT '.$hasActive;
    if ($filter === 'failed') $conditions[] = "EXISTS(SELECT 1 FROM wallet_receiving_accounts rf WHERE rf.user_id=u.id AND rf.status='failed') AND NOT ".$hasActive;
    if ($filter === 'duplicates') $conditions[] = $dupUserIds ? 'u.id IN ('.implode(',', $dupUserIds).')' : '0';
    if ($search !== '') {
        $needle = '%'.$search.'%';
        $match = ['u.full_name LIKE ?', 'u.email LIKE ?', 'u.phone LIKE ?', 'u.flow_tag LIKE ?', 'c.name LIKE ?', 'p.country_code=?',
                  'EXISTS(SELECT 1 FROM wallet_receiving_accounts rs WHERE rs.user_id=u.id AND (rs.bank_name LIKE ? OR rs.account_name LIKE ? OR rs.account_number_last4=?))',
                  'EXISTS(SELECT 1 FROM crypto_deposit_addresses cs JOIN wallets cw ON cw.id=cs.wallet_id WHERE cw.user_id=u.id AND cs.address_display LIKE ?)'];
        array_push($params, $needle, $needle, $needle, $needle, $needle, strtoupper($search), $needle, $needle, $search, $needle);
        // A full account number is stored only as a hash, so match it exactly.
        if (preg_match('/^[0-9]{10,20}$/D', $search)) {
            $match[] = 'EXISTS(SELECT 1 FROM wallet_receiving_accounts rh WHERE rh.user_id=u.id AND rh.account_number_hash=?)';
            $params[] = hash('sha256', $search);
        }
        $conditions[] = '('.implode(' OR ', $match).')';
    }
    $from = ' FROM users u LEFT JOIN user_profiles p ON p.user_id=u.id LEFT JOIN countries c ON c.code=p.country_code';
    $where = $conditions ? ' WHERE '.implode(' AND ', $conditions) : '';
    $count = $pdo->prepare('SELECT COUNT(*)'.$from.$where);
    $count->execute($params);
    $total = (int) $count->fetchColumn();
    $offset = ($page - 1) * $limit;
    $list = $pdo->prepare($userSelect.$where.' ORDER BY u.created_at DESC LIMIT '.$limit.' OFFSET '.$offset);
    $list->execute($params);
    $users = $list->fetchAll();

    // Attach each listed customer's accounts with three batched queries.
    $ids = array_map(fn ($u) => (int) $u['id'], $users);
    $byUser = ['receiving'=>[], 'crypto'=>[], 'banks'=>[]];
    if ($ids) {
        $in = implode(',', $ids);
        foreach ($pdo->query("SELECT * FROM wallet_receiving_accounts WHERE user_id IN ($in) ORDER BY (status='active') DESC, id DESC")->fetchAll() as $r) $byUser['receiving'][(int) $r['user_id']][] = uaReceiving($r);
        foreach ($pdo->query("SELECT w.user_id,w.currency_code,a.network,a.address_display,a.status FROM crypto_deposit_addresses a JOIN wallets w ON w.id=a.wallet_id WHERE w.user_id IN ($in) ORDER BY w.currency_code")->fetchAll() as $c) $byUser['crypto'][(int) $c['user_id']][] = $c;
        foreach ($pdo->query("SELECT user_id bank_user,bank_name,account_number_last4,status FROM bank_accounts WHERE user_id IN ($in) ORDER BY created_at DESC")->fetchAll() as $b) { $owner = (int) $b['bank_user']; unset($b['bank_user']); $byUser['banks'][$owner][] = $b; }
    }
    $rows = [];
    foreach ($users as $u) {
        $uid = (int) $u['id'];
        $receiving = $byUser['receiving'][$uid] ?? [];
        $hasAccount = (bool) array_filter($receiving, fn ($a) => $a['status'] === 'active');
        unset($u['id']);
        $rows[] = $u + ['approved'=>(bool) $u['approved'], 'receiving_accounts'=>$receiving, 'crypto_addresses'=>$byUser['crypto'][$uid] ?? [],
                        'withdrawal_accounts'=>$byUser['banks'][$uid] ?? [], 'has_duplicates'=>in_array($uid, $dupUserIds, true),
                        'can_regenerate'=>(bool) $u['approved'] && !$hasAccount];
    }

    $summary = $pdo->query("SELECT COUNT(*) total,
        COALESCE(SUM(EXISTS(SELECT 1 FROM wallet_receiving_accounts r WHERE r.user_id=u.id AND r.status='active')),0) with_account,
        COALESCE(SUM((".UA_APPROVED.") AND NOT EXISTS(SELECT 1 FROM wallet_receiving_accounts r WHERE r.user_id=u.id AND r.status='active')),0) missing,
        COALESCE(SUM(EXISTS(SELECT 1 FROM wallet_receiving_accounts r WHERE r.user_id=u.id AND r.status='failed') AND NOT EXISTS(SELECT 1 FROM wallet_receiving_accounts r WHERE r.user_id=u.id AND r.status='active')),0) failed
        FROM users u")->fetch() ?: [];
    $summary = array_map('intval', $summary);
    $summary['crypto'] = (int) $pdo->query("SELECT COUNT(*) FROM crypto_deposit_addresses WHERE status='active'")->fetchColumn();
    $summary['duplicates'] = count($dupUserIds);

    jsonResponse(['success'=>true, 'summary'=>$summary, 'users'=>$rows, 'duplicates'=>$duplicates,
        'active_provider'=>ReceivingAccountProvisioner::activeProvider($pdo),
        'pagination'=>['page'=>$page, 'limit'=>$limit, 'total'=>$total, 'total_pages'=>max(1, (int) ceil($total / $limit)),
                       'from'=>$total ? $offset + 1 : 0, 'to'=>min($offset + $limit, $total)]]);
}

// ---- Actions ----
$action = (string) ($input['action'] ?? '');
$adminId = (int) $session['admin']['id'];

if ($action === 'regenerate') {
    requireAdminRole($role, ['support', 'super_admin'], 'generate customer accounts');
    $uuid = trim((string) ($input['user_uuid'] ?? ''));
    if (!preg_match('/^[a-f0-9-]{36}$/i', $uuid)) jsonResponse(['success'=>false, 'error'=>'Invalid customer reference.'], 422);
    $q = $pdo->prepare('SELECT u.id,('.UA_APPROVED.') approved,EXISTS(SELECT 1 FROM wallet_receiving_accounts r WHERE r.user_id=u.id AND r.status=\'active\') has_account FROM users u WHERE u.uuid=? LIMIT 1');
    $q->execute([$uuid]);
    $user = $q->fetch();
    if (!$user) jsonResponse(['success'=>false, 'error'=>'Customer not found.'], 404);
    if (!$user['approved']) jsonResponse(['success'=>false, 'error'=>'Only approved customers (active, verified email and phone) can get an account number.'], 422);
    if ($user['has_account']) jsonResponse(['success'=>false, 'error'=>'This customer already has an active account number.'], 409);
    try {
        $result = (new ReceivingAccountProvisioner($pdo))->provision((int) $user['id'], 'NGN');
    } catch (DomainException $e) {
        jsonResponse(['success'=>false, 'error'=>$e->getMessage()], 409);
    } catch (Throwable $e) {
        adminAudit($pdo, $adminId, 'receiving_account.regenerate_failed', 'user', $uuid, ['error'=>mb_substr($e->getMessage(), 0, 200)]);
        jsonResponse(['success'=>false, 'error'=>'The provider could not create the account: '.mb_substr($e->getMessage(), 0, 200)], 502);
    }
    // The background job (if any) is no longer needed.
    $pdo->prepare("UPDATE gateway_jobs SET status='completed',completed_at=UTC_TIMESTAMP(6),last_error=NULL WHERE job_key=? AND status<>'completed'")->execute(['provision:user:'.(int) $user['id']]);
    adminAudit($pdo, $adminId, 'receiving_account.regenerated', 'user', $uuid, ['bank'=>$result['account']['bank_name'] ?? null, 'last4'=>substr((string) ($result['account']['account_number'] ?? ''), -4)]);
    jsonResponse(['success'=>true, 'message'=>'Account number generated.', 'account'=>$result['account']]);
}

if ($action === 'delete_receiving') {
    requireAdminRole($role, ['super_admin'], 'delete customer accounts');
    $uuid = trim((string) ($input['account_uuid'] ?? ''));
    if (!preg_match('/^[a-f0-9-]{36}$/i', $uuid)) jsonResponse(['success'=>false, 'error'=>'Invalid account reference.'], 422);
    $pdo->beginTransaction();
    try {
        $q = $pdo->prepare('SELECT r.id,r.user_id,r.provider,r.bank_name,r.account_number_last4,r.status,u.uuid user_uuid,
                (SELECT COUNT(*) FROM gateway_deposit_events e WHERE e.receiving_account_id=r.id) deposits
            FROM wallet_receiving_accounts r JOIN users u ON u.id=r.user_id WHERE r.uuid=? FOR UPDATE');
        $q->execute([$uuid]);
        $row = $q->fetch();
        if (!$row) { $pdo->rollBack(); jsonResponse(['success'=>false, 'error'=>'Account not found.'], 404); }
        if ((int) $row['deposits'] > 0) {
            $pdo->rollBack();
            jsonResponse(['success'=>false, 'error'=>'This account has received deposits, so it cannot be deleted. Its records must be kept.'], 409);
        }
        $pdo->prepare('DELETE FROM wallet_receiving_accounts WHERE id=?')->execute([(int) $row['id']]);
        adminAudit($pdo, $adminId, 'receiving_account.deleted', 'user', $row['user_uuid'],
            ['provider'=>$row['provider'], 'bank'=>$row['bank_name'], 'last4'=>$row['account_number_last4'], 'status'=>$row['status']]);
        $pdo->commit();
    } catch (Throwable $e) {
        if ($pdo->inTransaction()) $pdo->rollBack();
        error_log('Admin delete receiving account failed: '.$e->getMessage());
        jsonResponse(['success'=>false, 'error'=>'The account could not be deleted.'], 500);
    }
    jsonResponse(['success'=>true, 'message'=>'Account deleted.']);
}

if ($action === 'retire_crypto') {
    requireAdminRole($role, ['super_admin'], 'retire crypto addresses');
    $id = (int) ($input['address_id'] ?? 0);
    $q = $pdo->prepare("SELECT a.id,a.network,a.address_display,u.uuid user_uuid FROM crypto_deposit_addresses a JOIN wallets w ON w.id=a.wallet_id JOIN users u ON u.id=w.user_id WHERE a.id=? LIMIT 1");
    $q->execute([$id]);
    $row = $q->fetch();
    if (!$row) jsonResponse(['success'=>false, 'error'=>'Crypto address not found.'], 404);
    $pdo->prepare("UPDATE crypto_deposit_addresses SET status='retired' WHERE id=?")->execute([$id]);
    adminAudit($pdo, $adminId, 'crypto_address.retired', 'user', $row['user_uuid'], ['network'=>$row['network'], 'address'=>$row['address_display']]);
    jsonResponse(['success'=>true, 'message'=>'Crypto address retired.']);
}

jsonResponse(['success'=>false, 'error'=>'Unknown action.'], 422);
