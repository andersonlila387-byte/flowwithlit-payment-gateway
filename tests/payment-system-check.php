<?php
// Offline regression checks for the payment surface. DB/provider tests require sandbox credentials.
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
$root = dirname(__DIR__);
$required = [
    'database/migrations/022_payment_processing.sql',
    'database/migrations/026_php_payment_worker.sql',
    'api/checkout/payment-status.php',
    'api/webhooks/onepipe.php',
    'api/webhooks/breet.php',
    'cron/payment-worker.php',
    'services/ProviderEventInbox.php',
    'services/OnePipeEventSettler.php',
];
foreach ($required as $file) {
    if (!is_file($root . DIRECTORY_SEPARATOR . $file)) throw new RuntimeException("Missing {$file}");
}
$migration = file_get_contents($root . '/database/migrations/022_payment_processing.sql');
foreach (['payment_provider_events','payment_status_history','payment_outbox','uq_payment_provider_event'] as $needle) {
    if (strpos($migration, $needle) === false) throw new RuntimeException("Migration missing {$needle}");
}
// PHP is the only payment processor: the retired Go service and its internal bridge must stay gone.
foreach (['go', 'api/internal'] as $retired) {
    if (file_exists($root . DIRECTORY_SEPARATOR . $retired)) throw new RuntimeException("Retired Go path still present: {$retired}");
}
$breet = file_get_contents($root . '/api/webhooks/breet.php');
foreach (['hash_equals', 'BREET_ALLOWED_IPS', 'ProviderEventInbox::record'] as $needle) {
    if (strpos($breet, $needle) === false) throw new RuntimeException("Breet webhook missing {$needle}");
}
$worker = file_get_contents($root . '/cron/payment-worker.php');
foreach (["PHP_SAPI !== 'cli'", 'GET_LOCK', 'expireOverduePayments', 'runGatewayJobs', 'settleOnePipeEvents'] as $needle) {
    if (strpos($worker, $needle) === false) throw new RuntimeException("Payment worker missing {$needle}");
}
echo "payment-system static checks passed\n";
