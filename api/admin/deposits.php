<?php
declare(strict_types=1);

require_once __DIR__ . '/../_admin.php';

header('Cache-Control: no-store');
$input = adminMethod(['GET', 'POST']);
$pdo = Database::getInstance();
[$session, $role] = requireAdmin($pdo, $input);
requireAdminRole($role, ['super_admin', 'finance'], 'manage deposit operations');

function depositUuid(): string {
    $bytes = random_bytes(16);
    $bytes[6] = chr((ord($bytes[6]) & 0x0f) | 0x40);
    $bytes[8] = chr((ord($bytes[8]) & 0x3f) | 0x80);
    return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($bytes), 4));
}

function depositReference(string $prefix): string {
    return $prefix . '-' . strtoupper(bin2hex(random_bytes(7)));
}

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    if (($input['action'] ?? '') !== 'manual_credit') {
        jsonResponse(['success' => false, 'error' => 'Unknown deposit action.'], 422);
    }

    $walletUuid = trim((string) ($input['wallet_uuid'] ?? ''));
    $amount = trim((string) ($input['amount'] ?? ''));
    $externalReference = trim((string) ($input['external_reference'] ?? ''));
    $reason = trim((string) ($input['reason'] ?? ''));

    if (!preg_match('/^[a-f0-9]{8}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{12}$/i', $walletUuid)) {
        jsonResponse(['success' => false, 'error' => 'Choose the wallet to credit.'], 422);
    }
    if (!preg_match('/^\d{1,18}(?:\.\d{1,18})?$/', $amount) || (float) $amount <= 0) {
        jsonResponse(['success' => false, 'error' => 'Enter a valid positive amount.'], 422);
    }
    if ($externalReference === '' || strlen($externalReference) > 120) {
        jsonResponse(['success' => false, 'error' => 'A provider reference or support case reference is required.'], 422);
    }
    if (strlen($reason) < 10 || strlen($reason) > 500) {
        jsonResponse(['success' => false, 'error' => 'Add a reason between 10 and 500 characters.'], 422);
    }

    try {
        $pdo->beginTransaction();
        $walletStatement = $pdo->prepare('SELECT w.id,w.uuid,w.user_id,w.currency_code,w.available_balance,w.status,u.full_name,u.email
            FROM wallets w INNER JOIN users u ON u.id=w.user_id WHERE w.uuid=? FOR UPDATE');
        $walletStatement->execute([$walletUuid]);
        $wallet = $walletStatement->fetch();
        if (!$wallet) throw new RuntimeException('The selected wallet was not found.');
        if ($wallet['status'] !== 'active') throw new RuntimeException('Only an active wallet can receive a manual credit.');

        $duplicate = $pdo->prepare('SELECT id FROM manual_wallet_credits WHERE wallet_id=? AND external_reference=? LIMIT 1');
        $duplicate->execute([(int) $wallet['id'], $externalReference]);
        if ($duplicate->fetch()) throw new RuntimeException('That reference has already been credited to this wallet.');

        $reference = depositReference('ADJ');
        $transactionUuid = depositUuid();
        $idempotencyKey = hash('sha256', 'admin-manual-credit|' . strtolower($walletUuid) . '|' . strtolower($externalReference));
        $metadata = json_encode([
            'source' => 'admin_manual_credit',
            'reason' => $reason,
            'external_reference' => $externalReference,
            'admin_uuid' => $session['admin']['uuid'],
        ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
        $insertTransaction = $pdo->prepare("INSERT INTO transactions
            (uuid,user_id,type,status,destination_wallet_id,currency_code,amount,fee_amount,reference,idempotency_key,provider,provider_reference,metadata,completed_at)
            VALUES (?,?,'adjustment','completed',?,?,?,0,?,?, 'admin_manual_credit',?,?,UTC_TIMESTAMP())");
        $insertTransaction->execute([$transactionUuid, (int) $wallet['user_id'], (int) $wallet['id'], $wallet['currency_code'], $amount, $reference, $idempotencyKey, $externalReference, $metadata]);
        $transactionId = (int) $pdo->lastInsertId();

        $updateWallet = $pdo->prepare('UPDATE wallets SET available_balance=available_balance+? WHERE id=?');
        $updateWallet->execute([$amount, (int) $wallet['id']]);
        $balanceStatement = $pdo->prepare('SELECT available_balance FROM wallets WHERE id=?');
        $balanceStatement->execute([(int) $wallet['id']]);
        $newBalance = (string) $balanceStatement->fetchColumn();

        $insertCredit = $pdo->prepare('INSERT INTO manual_wallet_credits
            (uuid,transaction_id,wallet_id,user_id,admin_user_id,currency_code,amount,external_reference,reason)
            VALUES (?,?,?,?,?,?,?,?,?)');
        $insertCredit->execute([depositUuid(), $transactionId, (int) $wallet['id'], (int) $wallet['user_id'], (int) $session['admin']['id'], $wallet['currency_code'], $amount, $externalReference, $reason]);
        adminAudit($pdo, (int) $session['admin']['id'], 'deposit.manual_credit', 'transaction', $transactionUuid, [
            'reference' => $reference, 'wallet_uuid' => $walletUuid, 'currency' => $wallet['currency_code'],
            'amount' => $amount, 'external_reference' => $externalReference, 'reason' => $reason,
        ]);
        $pdo->commit();
        jsonResponse(['success' => true, 'message' => 'Manual credit recorded.', 'credit' => [
            'reference' => $reference, 'currency_code' => $wallet['currency_code'], 'amount' => $amount, 'available_balance' => $newBalance,
        ]]);
    } catch (Throwable $error) {
        if ($pdo->inTransaction()) $pdo->rollBack();
        $message = $error instanceof RuntimeException ? $error->getMessage() : 'Unable to record this manual credit.';
        jsonResponse(['success' => false, 'error' => $message], 422);
    }
}

$summary = $pdo->query("SELECT
    COALESCE(SUM(CASE WHEN type='deposit' AND status='completed' AND created_at>=UTC_DATE() THEN amount ELSE 0 END),0) AS deposits_today,
    SUM(CASE WHEN type='deposit' AND status='completed' AND created_at>=UTC_DATE() THEN 1 ELSE 0 END) AS deposit_count_today,
    COALESCE(SUM(CASE WHEN type='deposit' AND status='completed' AND created_at>=DATE_SUB(UTC_TIMESTAMP(), INTERVAL 30 DAY) THEN amount ELSE 0 END),0) AS deposits_30_days,
    COALESCE(SUM(CASE WHEN type='adjustment' AND provider='admin_manual_credit' AND created_at>=UTC_DATE() THEN amount ELSE 0 END),0) AS manual_credits_today,
    SUM(CASE WHEN type='adjustment' AND provider='admin_manual_credit' AND created_at>=UTC_DATE() THEN 1 ELSE 0 END) AS manual_credit_count
    FROM transactions")->fetch();
$unprocessedEvents = (int) $pdo->query('SELECT COUNT(*) FROM provider_webhook_events WHERE processed_at IS NULL')->fetchColumn();
$wallets = $pdo->query("SELECT w.uuid,w.currency_code,w.available_balance,u.full_name,u.email
    FROM wallets w INNER JOIN users u ON u.id=w.user_id
    WHERE w.status='active' ORDER BY u.full_name ASC,w.currency_code ASC LIMIT 500")->fetchAll();
$deposits = $pdo->query("SELECT t.uuid,t.reference,t.type,t.status,t.currency_code,t.amount,t.provider,t.provider_reference,t.created_at,
    u.full_name,u.email,mc.reason,mc.external_reference,au.full_name AS admin_name
    FROM transactions t
    INNER JOIN users u ON u.id=t.user_id
    LEFT JOIN manual_wallet_credits mc ON mc.transaction_id=t.id
    LEFT JOIN admin_users au ON au.id=mc.admin_user_id
    WHERE t.type='deposit' OR (t.type='adjustment' AND t.provider='admin_manual_credit')
    ORDER BY t.created_at DESC LIMIT 100")->fetchAll();

jsonResponse(['success' => true, 'summary' => [
    'deposits_today' => (string) $summary['deposits_today'],
    'deposit_count_today' => (int) $summary['deposit_count_today'],
    'deposits_30_days' => (string) $summary['deposits_30_days'],
    'manual_credits_today' => (string) $summary['manual_credits_today'],
    'manual_credit_count' => (int) $summary['manual_credit_count'],
    'unprocessed_events' => $unprocessedEvents,
], 'wallets' => $wallets, 'deposits' => $deposits]);
