<?php
declare(strict_types=1);

require_once __DIR__.'/../_admin.php';

$input = adminMethod(['GET', 'POST']);
$pdo = Database::getInstance();
[$session, $role] = requireAdmin($pdo, $input);
requireAdminRole($role, ['support', 'super_admin'], 'view customer accounts');

$allowedStatuses = ['active', 'pending_verification', 'suspended', 'closed'];
$baseSelect = "SELECT u.id,u.uuid,u.full_name,u.email,u.phone,u.flow_tag,u.auth_provider,u.two_factor_enabled,u.account_type,u.kyc_tier,u.status,u.email_verified,u.created_at,u.last_login_at,
    b.uuid business_uuid,COALESCE(NULLIF(b.trading_name,''),b.legal_name) business_name,b.verification_status
    FROM users u LEFT JOIN businesses b ON b.owner_user_id=u.id";

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $uuid = trim((string) ($input['uuid'] ?? ''));
    if ($uuid !== '') {
        if (!preg_match('/^[a-f0-9-]{36}$/i', $uuid)) jsonResponse(['success'=>false, 'error'=>'Invalid customer reference.'], 422);
        $detail = $pdo->prepare($baseSelect.' WHERE u.uuid=? LIMIT 1');
        $detail->execute([$uuid]);
        $user = $detail->fetch();
        if (!$user) jsonResponse(['success'=>false, 'error'=>'Customer not found.'], 404);
        $wallets=$pdo->prepare('SELECT w.id,w.uuid,w.currency_code,w.wallet_type,w.available_balance,w.pending_balance,w.status,w.created_at FROM wallets w WHERE w.user_id=? ORDER BY w.wallet_type,w.currency_code');$wallets->execute([(int)$user['id']]);
        $receiving=$pdo->prepare('SELECT r.currency_code,r.provider,r.account_name,r.bank_name,r.account_number_last4,r.account_number_display,r.status FROM wallet_receiving_accounts r WHERE r.user_id=? ORDER BY r.created_at DESC');$receiving->execute([(int)$user['id']]);
        $banks=$pdo->prepare('SELECT bank_name,account_name,account_number_last4,currency_code,status,created_at FROM bank_accounts WHERE user_id=? ORDER BY created_at DESC');$banks->execute([(int)$user['id']]);
        $cards=$pdo->prepare('SELECT c.provider,c.card_type,c.last4,c.status,c.spending_limit,w.currency_code FROM cards c JOIN wallets w ON w.id=c.wallet_id WHERE c.user_id=? ORDER BY c.created_at DESC');$cards->execute([(int)$user['id']]);
        $crypto=$pdo->prepare('SELECT w.currency_code,a.network,a.address_display,a.tag_memo,a.status FROM crypto_deposit_addresses a JOIN wallets w ON w.id=a.wallet_id WHERE w.user_id=? ORDER BY w.currency_code,a.network');$crypto->execute([(int)$user['id']]);
        $walletRows=$wallets->fetchAll();$defaultCurrency=$walletRows[0]['currency_code']??null;unset($user['id']);
        jsonResponse(['success'=>true,'user'=>$user,'security'=>['two_factor_enabled'=>(bool)$user['two_factor_enabled'],'auth_provider'=>$user['auth_provider']],'default_currency'=>$defaultCurrency,'wallets'=>$walletRows,'receiving_accounts'=>$receiving->fetchAll(),'withdrawal_accounts'=>$banks->fetchAll(),'cards'=>$cards->fetchAll(),'crypto_addresses'=>$crypto->fetchAll()]);
    }

    $page = max(1, (int) ($input['page'] ?? 1));
    $limit = max(10, min(100, (int) ($input['limit'] ?? 25)));
    $status = (string) ($input['status'] ?? '');
    if ($status !== '' && !in_array($status, $allowedStatuses, true)) jsonResponse(['success'=>false, 'error'=>'Invalid customer status filter.'], 422);
    $search = trim((string) ($input['q'] ?? ''));
    if (mb_strlen($search) > 100) jsonResponse(['success'=>false, 'error'=>'Search query is too long.'], 422);

    $conditions = [];
    $params = [];
    if ($status !== '') { $conditions[] = 'u.status=?'; $params[] = $status; }
    if ($search !== '') {
        $conditions[] = "(u.full_name LIKE ? OR u.email LIKE ? OR u.phone LIKE ? OR u.flow_tag LIKE ? OR EXISTS(SELECT 1 FROM businesses sb WHERE sb.owner_user_id=u.id AND (sb.legal_name LIKE ? OR sb.trading_name LIKE ?)))";
        $needle = '%'.$search.'%';
        array_push($params, $needle, $needle, $needle, $needle, $needle, $needle);
    }
    $where = $conditions ? ' WHERE '.implode(' AND ', $conditions) : '';
    $count = $pdo->prepare('SELECT COUNT(*) FROM users u'.$where);
    $count->execute($params);
    $total = (int) $count->fetchColumn();

    $offset = ($page - 1) * $limit;
    $list = $pdo->prepare($baseSelect.$where.' ORDER BY u.created_at DESC LIMIT '.$limit.' OFFSET '.$offset);
    $list->execute($params);
    $summary = $pdo->query("SELECT
        COUNT(*) total,
        COALESCE(SUM(status='active'),0) active,
        COALESCE(SUM(status='pending_verification'),0) pending_verification,
        COALESCE(SUM(status='suspended'),0) suspended,
        COALESCE(SUM(created_at>=DATE_FORMAT(UTC_DATE(),'%Y-%m-01')),0) new_this_month
        FROM users")->fetch() ?: [];

    jsonResponse([
        'success'=>true,
        'summary'=>array_map('intval', $summary),
        'users'=>$list->fetchAll(),
        'pagination'=>[
            'page'=>$page, 'limit'=>$limit, 'total'=>$total,
            'total_pages'=>max(1, (int) ceil($total / $limit)),
            'from'=>$total ? $offset + 1 : 0, 'to'=>min($offset + $limit, $total),
        ],
    ]);
}

if (!in_array($role, ['support', 'super_admin'], true)) jsonResponse(['success'=>false, 'error'=>'Your role cannot change customer status.'], 403);
$uuid = trim((string) ($input['user_uuid'] ?? ''));
$status = (string) ($input['status'] ?? '');
if (!preg_match('/^[a-f0-9-]{36}$/i', $uuid) || !in_array($status, $allowedStatuses, true)) {
    jsonResponse(['success'=>false, 'error'=>'Invalid customer status update.'], 422);
}
if ($status === 'closed') jsonResponse(['success'=>false, 'error'=>'Closing a customer account requires the account-closure workflow.'], 422);

$update = $pdo->prepare('UPDATE users SET status=? WHERE uuid=? AND status<>?');
$update->execute([$status, $uuid, $status]);
if (!$update->rowCount()) {
    $exists = $pdo->prepare('SELECT id FROM users WHERE uuid=? LIMIT 1');
    $exists->execute([$uuid]);
    if (!$exists->fetchColumn()) jsonResponse(['success'=>false, 'error'=>'Customer not found.'], 404);
}
adminAudit($pdo, (int) $session['admin']['id'], 'user.status_changed', 'user', $uuid, ['status'=>$status]);
jsonResponse(['success'=>true, 'message'=>$status === 'suspended' ? 'Customer suspended.' : 'Customer status updated.']);

