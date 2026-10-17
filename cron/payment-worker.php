<?php
declare(strict_types=1);

/*
 * Runs from the Hostinger cron scheduler.  This deliberately has no HTTP
 * handler: payment maintenance must not be triggerable by the public web.
 */
if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

require_once dirname(__DIR__) . '/config/database.php';

const FLOW_PAYMENT_WORKER_LOCK = 'flowwithlit:payment-worker';
const FLOW_PAYMENT_WORKER_BATCH_SIZE = 250;

function workerOutput(array $payload, int $exitCode = 0): void {
    fwrite($exitCode === 0 ? STDOUT : STDERR, json_encode($payload, JSON_UNESCAPED_SLASHES) . PHP_EOL);
    exit($exitCode);
}

$pdo = Database::getInstance();
$lock = $pdo->prepare('SELECT GET_LOCK(?, 0)');
$lock->execute([FLOW_PAYMENT_WORKER_LOCK]);

if ((int) $lock->fetchColumn() !== 1) {
    workerOutput(['ok' => true, 'skipped' => 'another_worker_is_running']);
}

try {
    $pdo->beginTransaction();

    // A payment can originate either from a checkout session or a payment link.
    // Lock rows first so an incoming verified provider event cannot race an expiry.
    $expired = $pdo->prepare(
        "SELECT p.id, p.transaction_id
         FROM payments p
         INNER JOIN transactions t ON t.id = p.transaction_id
         LEFT JOIN checkout_sessions cs ON cs.id = p.checkout_session_id
         LEFT JOIN payment_links pl ON pl.id = p.payment_link_id
         WHERE p.status IN ('pending', 'processing')
           AND t.status IN ('pending', 'processing')
           AND (
             (cs.expires_at IS NOT NULL AND cs.expires_at <= UTC_TIMESTAMP())
             OR (pl.expires_at IS NOT NULL AND pl.expires_at <= UTC_TIMESTAMP())
           )
         ORDER BY p.id ASC
         LIMIT " . FLOW_PAYMENT_WORKER_BATCH_SIZE . " FOR UPDATE"
    );
    $expired->execute();
    $rows = $expired->fetchAll();

    $paymentIds = array_map(static fn(array $row): int => (int) $row['id'], $rows);
    $transactionIds = array_values(array_unique(array_map(static fn(array $row): int => (int) $row['transaction_id'], $rows)));

    if ($paymentIds !== []) {
        $paymentPlaceholders = implode(',', array_fill(0, count($paymentIds), '?'));
        $transactionPlaceholders = implode(',', array_fill(0, count($transactionIds), '?'));

        $updatePayments = $pdo->prepare(
            "UPDATE payments SET status = 'expired' WHERE id IN ({$paymentPlaceholders}) AND status IN ('pending', 'processing')"
        );
        $updatePayments->execute($paymentIds);

        $updateTransactions = $pdo->prepare(
            "UPDATE transactions SET status = 'cancelled' WHERE id IN ({$transactionPlaceholders}) AND status IN ('pending', 'processing')"
        );
        $updateTransactions->execute($transactionIds);

        $history = $pdo->prepare(
            "INSERT INTO payment_status_history (payment_id, status, source, details)
             VALUES (?, 'expired', 'php_cron', JSON_OBJECT('reason', 'checkout_expired'))"
        );
        foreach ($paymentIds as $paymentId) {
            $history->execute([$paymentId]);
        }
    }

    $pdo->commit();
    workerOutput(['ok' => true, 'expired_payments' => count($paymentIds)]);
} catch (Throwable $exception) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    error_log('Flowwithlit payment worker failed: ' . get_class($exception));
    workerOutput(['ok' => false, 'error' => 'payment_maintenance_failed'], 1);
} finally {
    $release = $pdo->prepare('DO RELEASE_LOCK(?)');
    $release->execute([FLOW_PAYMENT_WORKER_LOCK]);
}
