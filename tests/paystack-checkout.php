<?php
declare(strict_types=1);
/**
 * Integration test for payment-link checkout settlement through Paystack (fake Paystack transport).
 * Creates a disposable schema flowwithlit_test_paystack_<timestamp> (structure only, copied from
 * FLOW_TEST_SCHEMA_SOURCE, never customer data) and drops only that schema at the end.
 * Usage: php tests/paystack-checkout.php
 */
if (PHP_SAPI !== 'cli') { http_response_code(404); exit(1); }

$root = dirname(__DIR__);
$source = getenv('FLOW_TEST_SCHEMA_SOURCE') ?: 'flowwithlit_db';
$testDb = 'flowwithlit_test_paystack_' . gmdate('YmdHis');
if (!preg_match('/^[A-Za-z0-9_]+$/D', $source)) throw new RuntimeException('Invalid schema source.');

$db = new PDO('mysql:host=' . (getenv('FLOW_DB_HOST') ?: '127.0.0.1') . ';charset=utf8mb4', getenv('FLOW_DB_USER') ?: 'root', getenv('FLOW_DB_PASSWORD') ?: '',
    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, PDO::ATTR_EMULATE_PREPARES => false]);
$failures = [];
function check(bool $ok, string $label, array &$failures): void { echo ($ok ? 'PASS ' : 'FAIL ') . $label . PHP_EOL; if (!$ok) $failures[] = $label; }

require_once $root . '/services/PaystackCheckout.php';

$db->exec("CREATE DATABASE `{$testDb}` CHARACTER SET utf8mb4");
try {
    $tables = $db->query("SHOW FULL TABLES FROM `{$source}` WHERE Table_type = 'BASE TABLE'")->fetchAll(PDO::FETCH_COLUMN);
    $db->exec("USE `{$testDb}`");
    $db->exec('SET FOREIGN_KEY_CHECKS=0');
    foreach ($tables as $table) $db->exec($db->query("SHOW CREATE TABLE `{$source}`.`{$table}`")->fetch(PDO::FETCH_NUM)[1]);
    foreach (['currencies'] as $table) $db->exec("INSERT INTO `{$table}` SELECT * FROM `{$source}`.`{$table}`");
    $db->exec('SET FOREIGN_KEY_CHECKS=1');
    foreach (['022_payment_processing.sql', '023_wallet_operations.sql', '025_gateway_jobs_and_deposits.sql', '026_php_payment_worker.sql'] as $migration) {
        $db->exec(file_get_contents($root . '/database/migrations/' . $migration));
    }

    // Paystack enabled in test mode with an encrypted secret key (as Admin → API Connect stores it).
    $db->prepare("INSERT INTO admin_integration_connections(provider_key,environment,enabled,values_json,secrets_encrypted) VALUES('paystack','test',1,'{}',?)")
        ->execute([PrivateStorage::encrypt(json_encode(['secret_key' => 'sk_test_' . str_repeat('a', 40)]), 'integration:paystack')]);

    $db->exec("INSERT INTO users(uuid,full_name,email,flow_tag,account_type,kyc_tier,email_verified,status) VALUES(UUID(),'Merchant Test','merchant.paystack@example.test','@merchant_ps','individual',1,1,'active')");
    $userId = (int) $db->lastInsertId();
    $db->exec("INSERT INTO wallets(uuid,user_id,currency_code,wallet_type,status) VALUES(UUID(),{$userId},'NGN','personal','active')");
    $walletId = (int) $db->lastInsertId();

    $makePayment = function (string $amount) use ($db, $userId, $walletId): string {
        $reference = PaystackCheckout::newReference();
        $db->prepare("INSERT INTO transactions(uuid,user_id,type,status,destination_wallet_id,currency_code,amount,fee_amount,reference,idempotency_key,provider) VALUES(UUID(),?,'payment','pending',?,'NGN',?,'0',?,?,'paystack')")
            ->execute([$userId, $walletId, $amount, 'CHK-' . bin2hex(random_bytes(8)), hash('sha256', $reference)]);
        $tx = (int) $db->lastInsertId();
        $db->prepare("INSERT INTO payments(transaction_id,customer_email,customer_name,payment_method,status,provider,provider_reference) VALUES(?,?,?,?,?,?,?)")
            ->execute([$tx, 'buyer@example.test', 'Buyer', 'card', 'pending', 'paystack', $reference]);
        return $reference;
    };
    // Fake Paystack: answers /transaction/verify/{ref} from this table.
    $verifyAnswers = [];
    $transport = function (string $method, string $url) use (&$verifyAnswers): array {
        $ref = rawurldecode(basename((string) parse_url($url, PHP_URL_PATH)));
        $data = $verifyAnswers[$ref] ?? ['status' => 'abandoned', 'reference' => $ref, 'amount' => 0, 'currency' => 'NGN'];
        return [200, json_encode(['status' => true, 'message' => 'ok', 'data' => $data])];
    };
    $client = new PaystackClient(new ProviderConfig($db, 'paystack', 'test'), new ProviderHttp(Closure::fromCallable($transport)));
    $checkout = new PaystackCheckout($db, $client);
    $balance = fn() => (string) $db->query("SELECT available_balance FROM wallets WHERE id={$walletId}")->fetchColumn();

    // 1. Paid in full -> credited once.
    $ref1 = $makePayment('5000.00');
    $verifyAnswers[$ref1] = ['status' => 'success', 'reference' => $ref1, 'amount' => 500000, 'currency' => 'NGN', 'channel' => 'card', 'fees' => 7500];
    check($checkout->settle($ref1, 'test')['status'] === 'succeeded', 'paid payment settles', $failures);
    check(bccomp($balance(), '5000', 2) === 0, 'merchant wallet credited 5000.00 (got ' . $balance() . ')', $failures);
    $again = $checkout->settle($ref1, 'test');
    check($again['status'] === 'succeeded' && $again['duplicate'] === true, 'second confirm/webhook is a duplicate', $failures);
    check(bccomp($balance(), '5000', 2) === 0, 'no double credit', $failures);
    $status = $db->prepare('SELECT p.status,t.status tx FROM payments p JOIN transactions t ON t.id=p.transaction_id WHERE p.provider_reference=?'); $status->execute([$ref1]); $row = $status->fetch();
    check($row['status'] === 'succeeded' && $row['tx'] === 'completed', 'payment succeeded and transaction completed', $failures);
    $ledger = (int) $db->query("SELECT COUNT(*) FROM ledger_entries WHERE posting_key='checkout:paystack:{$ref1}'")->fetchColumn();
    check($ledger === 1, 'exactly one ledger entry', $failures);

    // 2. Underpaid -> not credited, flagged for review.
    $ref2 = $makePayment('2000.00');
    $verifyAnswers[$ref2] = ['status' => 'success', 'reference' => $ref2, 'amount' => 100000, 'currency' => 'NGN'];
    check($checkout->settle($ref2, 'test')['status'] === 'pending', 'underpaid payment is not marked paid', $failures);
    check(bccomp($balance(), '5000', 2) === 0, 'underpaid payment not credited', $failures);

    // 3. Customer closed the window -> stays pending, nothing credited.
    $ref3 = $makePayment('1500.00');
    check($checkout->settle($ref3, 'test')['status'] === 'pending', 'abandoned payment stays pending', $failures);

    // 4. Declined -> failed.
    $ref4 = $makePayment('1500.00');
    $verifyAnswers[$ref4] = ['status' => 'failed', 'reference' => $ref4, 'amount' => 150000, 'currency' => 'NGN'];
    check($checkout->settle($ref4, 'test')['status'] === 'failed', 'declined payment is marked failed', $failures);

    // 5. Unknown or malformed references do nothing.
    check($checkout->settle('FWLP-' . str_repeat('A', 24), 'test')['status'] === 'unknown', 'unknown reference ignored', $failures);
    check($checkout->settle("x' OR 1=1 --", 'test')['status'] === 'unknown', 'malformed reference ignored', $failures);
    check(bccomp($balance(), '5000', 2) === 0, 'final balance is 5000.00', $failures);
} finally {
    $db->exec("DROP DATABASE IF EXISTS `{$testDb}`");
}
echo $failures ? PHP_EOL . count($failures) . ' FAILED' . PHP_EOL : PHP_EOL . 'ALL PASSED' . PHP_EOL;
exit($failures ? 1 : 0);
