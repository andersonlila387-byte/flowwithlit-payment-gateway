<?php
declare(strict_types=1);

/*
 * PHP payment worker. Runs once per minute from the server's cron scheduler.
 * It replaces the retired Go service: PHP is the only process that changes
 * payment, wallet or ledger state.
 *
 * Tasks (each isolated, so one failure never blocks the others):
 *   1. expire overdue checkout sessions and payment links;
 *   2. run queued gateway jobs (e.g. provisioning NGN receiving accounts);
 *   3. settle verified OnePipe deposit events from the webhook inbox.
 *
 * This file has no HTTP entry point: payment maintenance must never be
 * triggerable from the public web.
 */
if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

require_once dirname(__DIR__) . '/config/database.php';
require_once dirname(__DIR__) . '/services/GatewayJobQueue.php';
require_once dirname(__DIR__) . '/services/ReceivingAccountProvisioner.php';
require_once dirname(__DIR__) . '/services/ProviderEventInbox.php';
require_once dirname(__DIR__) . '/services/OnePipeEventSettler.php';

const FLOW_PAYMENT_WORKER_LOCK = 'flowwithlit:payment-worker';
const FLOW_PAYMENT_WORKER_BATCH_SIZE = 250;
const FLOW_PAYMENT_WORKER_MAX_JOBS = 20;
const FLOW_PAYMENT_WORKER_MAX_EVENTS = 50;
const FLOW_PAYMENT_WORKER_TIME_BUDGET_SECONDS = 50; // finish before the next cron minute

function workerOutput(array $payload, int $exitCode = 0): void {
    fwrite($exitCode === 0 ? STDOUT : STDERR, json_encode($payload, JSON_UNESCAPED_SLASHES) . PHP_EOL);
    exit($exitCode);
}

function workerTimeLeft(float $startedAt): bool {
    return (microtime(true) - $startedAt) < FLOW_PAYMENT_WORKER_TIME_BUDGET_SECONDS;
}

/** Task 1: expire pending payments whose checkout session or payment link has expired. */
function expireOverduePayments(PDO $pdo): int {
    $pdo->beginTransaction();
    try {
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

            $pdo->prepare("UPDATE payments SET status = 'expired' WHERE id IN ({$paymentPlaceholders}) AND status IN ('pending', 'processing')")
                ->execute($paymentIds);
            $pdo->prepare("UPDATE transactions SET status = 'cancelled' WHERE id IN ({$transactionPlaceholders}) AND status IN ('pending', 'processing')")
                ->execute($transactionIds);

            $history = $pdo->prepare(
                "INSERT INTO payment_status_history (payment_id, status, source, details)
                 VALUES (?, 'expired', 'php_cron', JSON_OBJECT('reason', 'checkout_expired'))"
            );
            foreach ($paymentIds as $paymentId) {
                $history->execute([$paymentId]);
            }
        }

        $pdo->commit();
        return count($paymentIds);
    } catch (Throwable $exception) {
        if ($pdo->inTransaction()) $pdo->rollBack();
        throw $exception;
    }
}

/** Task 2: run due gateway jobs with leased claims and exponential-backoff retries. */
function runGatewayJobs(PDO $pdo, float $startedAt): array {
    $summary = ['reclaimed' => GatewayJobQueue::reclaimStale($pdo), 'completed' => 0, 'failed' => 0];
    $worker = 'php-cron:' . gethostname() . ':' . getmypid();

    for ($i = 0; $i < FLOW_PAYMENT_WORKER_MAX_JOBS && workerTimeLeft($startedAt); $i++) {
        $job = GatewayJobQueue::claim($pdo, $worker);
        if ($job === null) break;
        try {
            $payload = json_decode((string) $job['payload'], true, 512, JSON_THROW_ON_ERROR);
            match ($job['job_type']) {
                'provision_receiving_account' => (new ReceivingAccountProvisioner($pdo))
                    ->provision((int) $payload['user_id'], (string) ($payload['currency'] ?? 'NGN')),
                default => throw new DomainException('Unknown gateway job type.'),
            };
            GatewayJobQueue::complete($pdo, (int) $job['id']);
            $summary['completed']++;
        } catch (Throwable $exception) {
            if ($pdo->inTransaction()) $pdo->rollBack();
            // Attempts was already incremented by claim().
            GatewayJobQueue::fail($pdo, (int) $job['id'], get_class($exception) . ': ' . $exception->getMessage(), (int) $job['attempts'] + 1);
            $summary['failed']++;
        }
    }
    return $summary;
}

/** Task 3: settle verified OnePipe deposit events; unmatched events retry, then wait for review. */
function settleOnePipeEvents(PDO $pdo, float $startedAt): array {
    $summary = ['settled' => 0, 'deferred' => 0];
    $settler = new OnePipeEventSettler($pdo);

    for ($i = 0; $i < FLOW_PAYMENT_WORKER_MAX_EVENTS && workerTimeLeft($startedAt); $i++) {
        // Claim and release the row lock quickly; DepositService opens its own transaction.
        $pdo->beginTransaction();
        $event = ProviderEventInbox::claimNext($pdo, 'onepipe');
        $pdo->commit();
        if ($event === null) break;

        try {
            $settler->settle((string) $event['event_id'], (string) $event['payload']);
            ProviderEventInbox::markProcessed($pdo, (int) $event['id']);
            $summary['settled']++;
        } catch (Throwable $exception) {
            if ($pdo->inTransaction()) $pdo->rollBack();
            ProviderEventInbox::markFailed($pdo, (int) $event['id'], (int) $event['attempts'], get_class($exception) . ': ' . $exception->getMessage());
            error_log('OnePipe event deferred for reconciliation: ' . get_class($exception));
            $summary['deferred']++;
        }
    }
    return $summary;
}

$startedAt = microtime(true);
$pdo = Database::getInstance();
$lock = $pdo->prepare('SELECT GET_LOCK(?, 0)');
$lock->execute([FLOW_PAYMENT_WORKER_LOCK]);
if ((int) $lock->fetchColumn() !== 1) {
    workerOutput(['ok' => true, 'skipped' => 'another_worker_is_running']);
}

$result = ['ok' => true];
try {
    $tasks = [
        'expired_payments' => static fn() => expireOverduePayments($pdo),
        'jobs' => static fn() => runGatewayJobs($pdo, $startedAt),
        'onepipe_events' => static fn() => settleOnePipeEvents($pdo, $startedAt),
    ];
    foreach ($tasks as $name => $task) {
        try {
            $result[$name] = $task();
        } catch (Throwable $exception) {
            if ($pdo->inTransaction()) $pdo->rollBack();
            error_log("Flowwithlit payment worker task {$name} failed: " . get_class($exception));
            $result['ok'] = false;
            $result[$name] = ['error' => 'task_failed'];
        }
    }
} finally {
    $pdo->prepare('DO RELEASE_LOCK(?)')->execute([FLOW_PAYMENT_WORKER_LOCK]);
}

$result['seconds'] = round(microtime(true) - $startedAt, 2);
workerOutput($result, $result['ok'] ? 0 : 1);
