<?php
require_once __DIR__ . '/../../config/response.php';
require_once __DIR__ . '/../../config/security.php';

function transactionRequest(PDO $pdo): array {
    header('Cache-Control: no-store');
    if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'GET' && ($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
        jsonResponse(['success'=>false,'error'=>'Method not allowed.'],405);
    }
    $input = ($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST' ? getJsonInput() : $_GET;
    $session = validateSession($pdo, getBearerToken() ?: ($input['token'] ?? ''), 15);
    if (!$session['valid'] || !empty($session['is_2fa_pending'])) jsonResponse(['success'=>false,'error'=>'Please sign in to continue.'],401);
    return [$input, (int)$session['user']['id']];
}

function transactionFilters(array $input, PDO $pdo): array {
    $range = (string)($input['range'] ?? '30d');
    if (!in_array($range, ['today','7d','30d','90d','all'], true)) $range = '30d';
    $status = strtolower(trim((string)($input['status'] ?? 'all')));
    if (!in_array($status, ['all','successful','pending','failed','refunded'], true)) $status = 'all';
    $channel = strtolower(trim((string)($input['channel'] ?? 'all')));
    if (!in_array($channel, ['all','card','apple_pay','bank_transfer','crypto'], true)) $channel = 'all';
    $currency = strtoupper(trim((string)($input['currency'] ?? 'all')));
    if ($currency !== 'all') {
        $stmt = $pdo->prepare('SELECT code FROM currencies WHERE code=? AND is_enabled=1 LIMIT 1');
        $stmt->execute([$currency]);
        if (!$stmt->fetchColumn()) $currency = 'all';
    }
    $type = strtolower(trim((string)($input['type'] ?? 'all')));
    if (!in_array($type, ['all','deposit','withdrawal','transfer','swap','payment','refund','fee','adjustment'], true)) $type = 'all';
    $search = trim((string)($input['search'] ?? ''));
    $search = function_exists('mb_substr') ? mb_substr($search, 0, 80, 'UTF-8') : substr($search, 0, 80);
    $page = max(1, min(100000, (int)($input['page'] ?? 1)));
    $pageSize = max(5, min(50, (int)($input['page_size'] ?? 10)));
    return compact('range','status','channel','currency','type','search','page','pageSize');
}

function transactionWhere(array $f, int $userId, array &$params): string {
    $where = ["(t.user_id = ? OR (JSON_UNQUOTE(JSON_EXTRACT(t.metadata, '$.channel'))='internal' AND EXISTS (SELECT 1 FROM wallets recipient_wallet WHERE recipient_wallet.id=t.destination_wallet_id AND recipient_wallet.user_id=?)))"]; $params = [$userId,$userId];
    if ($f['range'] !== 'all') {
        $interval = ['today'=>'0 DAY','7d'=>'7 DAY','30d'=>'30 DAY','90d'=>'90 DAY'][$f['range']];
        $where[] = "t.created_at >= DATE_SUB(UTC_TIMESTAMP(), INTERVAL {$interval})";
    }
    if ($f['currency'] !== 'all') { $where[] = 't.currency_code = ?'; $params[] = $f['currency']; }
    if ($f['type'] !== 'all') { $where[] = 't.type = ?'; $params[] = $f['type']; }
    if ($f['search'] !== '') {
        $like = '%' . $f['search'] . '%';
        $where[] = '(t.reference LIKE ? OR p.customer_name LIKE ? OR p.customer_email LIKE ? OR t.currency_code LIKE ?)';
        array_push($params, $like, $like, $like, $like);
    }
    if ($f['status'] === 'successful') $where[] = "t.status = 'completed'";
    elseif ($f['status'] === 'pending') $where[] = "t.status IN ('initiated','pending','processing')";
    elseif ($f['status'] === 'failed') $where[] = "t.status IN ('failed','reversed','cancelled')";
    elseif ($f['status'] === 'refunded') $where[] = "(t.type='refund' OR p.status='refunded')";
    if ($f['channel'] === 'card') $where[] = 'ct.id IS NOT NULL';
    elseif ($f['channel'] === 'crypto') $where[] = "(c.kind='crypto' OR t.type='swap')";
    elseif ($f['channel'] === 'apple_pay') $where[] = "JSON_UNQUOTE(JSON_EXTRACT(t.metadata, '$.channel')) = 'apple_pay'";
    elseif ($f['channel'] === 'bank_transfer') $where[] = "ct.id IS NULL AND c.kind='fiat' AND JSON_UNQUOTE(JSON_EXTRACT(t.metadata, '$.channel')) <> 'apple_pay'";
    return implode(' AND ', $where);
}

function transactionBaseSql(string $where): string {
    return "FROM transactions t
        LEFT JOIN payments p ON p.transaction_id=t.id
        LEFT JOIN card_transactions ct ON ct.transaction_id=t.id
        LEFT JOIN cards card ON card.id=ct.card_id
        LEFT JOIN currencies c ON c.code=t.currency_code
        WHERE {$where}";
}

function transactionStatus(string $status, string $type, ?string $paymentStatus): string {
    if ($type === 'refund' || $paymentStatus === 'refunded') return 'refunded';
    if ($status === 'completed') return 'successful';
    if (in_array($status, ['initiated','pending','processing'], true)) return 'pending';
    return 'failed';
}

function maskEmail(?string $email): string {
    if (!$email || !str_contains($email, '@')) return '—';
    [$name,$domain] = explode('@', $email, 2);
    return (strlen($name) <= 2 ? substr($name,0,1) : substr($name,0,2)) . '•••@' . $domain;
}
