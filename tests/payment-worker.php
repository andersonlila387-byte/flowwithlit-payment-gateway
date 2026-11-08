<?php
declare(strict_types=1);
/**
 * Integration test for the PHP-only payment pipeline:
 *   Breet webhook -> inbox, and OnePipe inbox event -> worker -> DepositService -> ledger + wallet.
 *
 * Creates a disposable schema named flowwithlit_test_worker_<timestamp> (structure copied from
 * FLOW_TEST_SCHEMA_SOURCE, never customer data) and drops only that schema at the end.
 * Usage: php tests/payment-worker.php
 */
if (PHP_SAPI !== 'cli') { http_response_code(404); exit(1); }

$root = dirname(__DIR__);
$php = PHP_BINARY;
$source = getenv('FLOW_TEST_SCHEMA_SOURCE') ?: 'flowwithlit_db';
$testDb = 'flowwithlit_test_worker_' . gmdate('YmdHis');
if (!preg_match('/^[A-Za-z0-9_]+$/D', $source)) throw new RuntimeException('Invalid schema source.');

$db = new PDO('mysql:host=' . (getenv('FLOW_DB_HOST') ?: '127.0.0.1') . ';charset=utf8mb4', getenv('FLOW_DB_USER') ?: 'root', getenv('FLOW_DB_PASSWORD') ?: '',
    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, PDO::ATTR_EMULATE_PREPARES => false]);

$failures = [];
function check(bool $ok, string $label, array &$failures): void { echo ($ok ? 'PASS ' : 'FAIL ') . $label . PHP_EOL; if (!$ok) $failures[] = $label; }

function runWorker(string $php, string $root, string $testDb): array {
    $env = array_merge(getenv(), ['FLOW_DB_NAME' => $testDb]);
    $proc = proc_open([$php, $root . '/cron/payment-worker.php'], [1 => ['pipe', 'w'], 2 => ['pipe', 'w']], $pipes, $root, $env);
    $stdout = stream_get_contents($pipes[1]);
    $stderr = stream_get_contents($pipes[2]); // error_log() lines; the JSON summary is the last line of either stream
    proc_close($proc);
    $lines = array_filter(array_map('trim', explode("\n", $stdout . "\n" . $stderr)), static fn($l) => str_starts_with($l, '{'));
    return json_decode((string) end($lines), true) ?: ['raw' => $stdout . $stderr];
}

$db->exec("CREATE DATABASE `{$testDb}` CHARACTER SET utf8mb4");
try {
    $tables = $db->query("SHOW FULL TABLES FROM `{$source}` WHERE Table_type = 'BASE TABLE'")->fetchAll(PDO::FETCH_COLUMN);
    $db->exec("USE `{$testDb}`");
    $db->exec('SET FOREIGN_KEY_CHECKS=0');
    foreach ($tables as $table) $db->exec($db->query("SHOW CREATE TABLE `{$source}`.`{$table}`")->fetch(PDO::FETCH_NUM)[1]);
    // Reference data only (currencies and account levels), never user data.
    foreach (['currencies', 'admin_account_levels'] as $table) $db->exec("INSERT INTO `{$table}` SELECT * FROM `{$source}`.`{$table}`");
    $db->exec('SET FOREIGN_KEY_CHECKS=1');
    foreach (['022_payment_processing.sql', '023_wallet_operations.sql', '024_bank_verification.sql', '025_gateway_jobs_and_deposits.sql', '026_php_payment_worker.sql'] as $migration) {
        $db->exec(file_get_contents($root . '/database/migrations/' . $migration));
    }

    // Seed one verified user with an NGN wallet and a OnePipe receiving account.
    $db->exec("INSERT INTO users(uuid,full_name,email,flow_tag,account_type,kyc_tier,email_verified,status)
               VALUES(UUID(),'Ada Test','ada.worker@example.test','@ada_test','individual',1,1,'active')");
    $userId = (int) $db->lastInsertId();
    $db->exec("INSERT INTO wallets(uuid,user_id,currency_code,wallet_type,status) VALUES(UUID(),{$userId},'NGN','personal','active')");
    $walletId = (int) $db->lastInsertId();
    $account = '0123456789';
    $db->prepare("INSERT INTO wallet_receiving_accounts(uuid,wallet_id,user_id,provider,currency_code,status,account_number_hash)
                  VALUES(UUID(),?,?,'onepipe','NGN','active',?)")->execute([$walletId, $userId, hash('sha256', $account)]);

    $insertEvent = $db->prepare('INSERT INTO payment_provider_events(provider,event_id,event_type,payload) VALUES(?,?,?,?)');
    // Amount arrives as a JSON number on purpose: it must still be credited exactly.
    $insertEvent->execute(['onepipe', 'evt-good-1', 'deposit', '{"data":{"account_number":"0123456789","amount":2500.75,"currency":"NGN","transaction_ref":"OP-REF-1"}}']);
    $insertEvent->execute(['onepipe', 'evt-bad-1', 'deposit', '{"data":{"account_number":"9999999999","amount":"100.00","currency":"NGN","transaction_ref":"OP-REF-2"}}']);

    // Run 1: the good event settles, the unmatched one is deferred.
    $run = runWorker($php, $root, $testDb);
    check(($run['ok'] ?? false) === true, 'worker run 1 succeeds', $failures);
    check(($run['onepipe_events']['settled'] ?? null) === 1, 'matched OnePipe event is settled', $failures);
    check(($run['onepipe_events']['deferred'] ?? null) === 1, 'unmatched OnePipe event is deferred, not credited', $failures);

    $balance = (string) $db->query("SELECT available_balance FROM wallets WHERE id={$walletId}")->fetchColumn();
    check(bccomp($balance, '2500.75', 2) === 0, "wallet credited exactly 2500.75 (got {$balance})", $failures);
    $ledger = $db->query("SELECT SUM(CASE WHEN direction='debit' THEN amount ELSE -amount END) net, COUNT(*) n FROM ledger_lines")->fetch();
    check((int) $ledger['n'] === 2 && bccomp((string) $ledger['net'], '0', 8) === 0, 'ledger has one balanced posting', $failures);
    $attempt = $db->query("SELECT a.attempts FROM payment_provider_event_attempts a JOIN payment_provider_events e ON e.id=a.event_row_id WHERE e.event_id='evt-bad-1'")->fetchColumn();
    check((int) $attempt === 1, 'deferred event has its attempt recorded', $failures);

    // Run 2: a redelivered duplicate of the good deposit must not credit twice.
    $insertEvent->execute(['onepipe', 'evt-good-1-redelivery', 'deposit', '{"data":{"account_number":"0123456789","amount":"2500.75","currency":"NGN","transaction_ref":"OP-REF-1"}}']);
    $run = runWorker($php, $root, $testDb);
    $balance = (string) $db->query("SELECT available_balance FROM wallets WHERE id={$walletId}")->fetchColumn();
    check(bccomp($balance, '2500.75', 2) === 0, "duplicate provider reference does not double-credit (balance {$balance})", $failures);
    check(($run['onepipe_events']['deferred'] ?? 1) === 0, 'deferred event waits for its backoff instead of retrying every minute', $failures);

    // A failing job is retried later, not lost.
    // Through the real enqueue path (explicit UTC available_at; the column default uses server-local time).
    require_once $root . '/services/GatewayJobQueue.php';
    GatewayJobQueue::enqueue($db, 'unknown_type', 'job-test-1', []);
    $run = runWorker($php, $root, $testDb);
    $job = $db->query("SELECT status,attempts,last_error FROM gateway_jobs WHERE job_key='job-test-1'")->fetch();
    check(($run['jobs']['failed'] ?? null) === 1 && $job['status'] === 'queued' && (int) $job['attempts'] === 1, 'failed job is re-queued with backoff', $failures);

    // Breet webhook through a real HTTP server.
    $port = 8099;
    $env = array_merge(getenv(), ['FLOW_DB_NAME' => $testDb, 'BREET_WEBHOOK_SECRET' => 'test-secret-123', 'BREET_ALLOWED_IPS' => '127.0.0.1']);
    $server = proc_open([$php, '-S', "127.0.0.1:{$port}", '-t', $root], [1 => ['file', 'NUL', 'w'], 2 => ['file', 'NUL', 'w']], $pipes, $root, $env);
    usleep(700000);
    $post = static function (string $secret, string $body) use ($port): int {
        $ctx = stream_context_create(['http' => ['method' => 'POST', 'ignore_errors' => true,
            'header' => "Content-Type: application/json\r\nx-webhook-secret: {$secret}\r\n", 'content' => $body]]);
        @file_get_contents("http://127.0.0.1:{$port}/api/webhooks/breet.php", false, $ctx);
        return (int) (explode(' ', $http_response_header[0] ?? 'HTTP/1.1 0')[1] ?? 0);
    };
    $body = '{"eventId":"breet-evt-1","event":"trade.completed","reference":"BRT-1","amount":"10.5"}';
    check($post('wrong-secret', $body) === 401, 'Breet webhook rejects a wrong secret', $failures);
    check($post('test-secret-123', $body) === 202, 'Breet webhook records a valid event', $failures);
    check($post('test-secret-123', $body) === 200, 'Breet webhook treats a redelivery as a duplicate', $failures);
    proc_terminate($server);
    $stored = (int) $db->query("SELECT COUNT(*) FROM payment_provider_events WHERE provider='breet'")->fetchColumn();
    check($stored === 1, 'exactly one Breet event stored', $failures);
    $balance = (string) $db->query("SELECT available_balance FROM wallets WHERE id={$walletId}")->fetchColumn();
    check(bccomp($balance, '2500.75', 2) === 0, 'Breet webhook never credits a wallet', $failures);
} finally {
    $db->exec("DROP DATABASE IF EXISTS `{$testDb}`");
}

echo $failures ? count($failures) . " check(s) failed\n" : "All payment worker checks passed\n";
exit($failures ? 1 : 0);
