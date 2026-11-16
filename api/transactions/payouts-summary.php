<?php
/**
 * Payments page stats: the signed-in user's outgoing money (withdrawals, transfers, payments)
 * in one currency over the last 30 days, computed in SQL so totals are exact for any volume.
 */
require_once __DIR__ . '/_common.php';
$pdo = Database::getInstance();
[$input, $userId] = transactionRequest($pdo);

$currency = strtoupper(trim((string) ($input['currency'] ?? 'NGN')));
$check = $pdo->prepare('SELECT code FROM currencies WHERE code = ? AND is_enabled = 1 LIMIT 1');
$check->execute([$currency]);
if (!$check->fetchColumn()) jsonResponse(['success' => false, 'error' => 'That currency is not available.'], 422);

$stmt = $pdo->prepare("SELECT
    COALESCE(SUM(CASE WHEN status = 'completed' THEN amount ELSE 0 END), 0) AS sent_total,
    SUM(status = 'completed') AS sent_count,
    COALESCE(SUM(CASE WHEN status IN ('initiated','pending','processing') THEN amount ELSE 0 END), 0) AS processing_total,
    SUM(status IN ('initiated','pending','processing')) AS processing_count,
    SUM(status IN ('failed','reversed','cancelled')) AS failed_count,
    COALESCE(SUM(CASE WHEN status = 'completed' THEN fee_amount ELSE 0 END), 0) AS fees_total
  FROM transactions
  WHERE user_id = ? AND currency_code = ? AND type IN ('withdrawal','transfer','payment')
    AND created_at >= UTC_TIMESTAMP() - INTERVAL 30 DAY");
$stmt->execute([$userId, $currency]);
$s = $stmt->fetch(PDO::FETCH_ASSOC) ?: [];

$sent = (int) ($s['sent_count'] ?? 0);
$failed = (int) ($s['failed_count'] ?? 0);
$finished = $sent + $failed;

jsonResponse(['success' => true, 'currency' => $currency, 'period_days' => 30, 'summary' => [
    'sent_total' => (string) ($s['sent_total'] ?? '0'),
    'sent_count' => $sent,
    'processing_total' => (string) ($s['processing_total'] ?? '0'),
    'processing_count' => (int) ($s['processing_count'] ?? 0),
    'failed_count' => $failed,
    'success_rate' => $finished ? round($sent / $finished * 100, 1) : null,
    'fees_total' => (string) ($s['fees_total'] ?? '0'),
]]);
