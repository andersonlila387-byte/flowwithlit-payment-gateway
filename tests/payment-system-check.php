<?php
// Offline regression checks for the payment surface. DB/provider tests require sandbox credentials.
$root = dirname(__DIR__);
$required = [
    'database/migrations/022_payment_processing.sql',
    'api/checkout/payment-status.php',
    'api/webhooks/onepipe.php',
    'go/cmd/api/main.go',
];
foreach ($required as $file) {
    if (!is_file($root . DIRECTORY_SEPARATOR . $file)) throw new RuntimeException("Missing {$file}");
}
$migration = file_get_contents($root . '/database/migrations/022_payment_processing.sql');
foreach (['payment_provider_events','payment_status_history','payment_outbox','uq_payment_provider_event'] as $needle) {
    if (strpos($migration, $needle) === false) throw new RuntimeException("Migration missing {$needle}");
}
$go = file_get_contents($root . '/go/cmd/api/main.go');
foreach (['INSERT IGNORE INTO payment_provider_events','allowedBreetIP','expirePayments','checkoutWS'] as $needle) {
    if (strpos($go, $needle) === false) throw new RuntimeException("Go service missing {$needle}");
}
echo "payment-system static checks passed\n";
