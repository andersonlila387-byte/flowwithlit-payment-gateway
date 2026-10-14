<?php
declare(strict_types=1);
if(PHP_SAPI!=='cli')exit(1);
require_once __DIR__.'/../config/database.php';
$db=Database::getInstance();
// Only the explicitly reviewed additive gateway migrations are in this runner.
// Historical migrations include USE and data cleanup; never auto-replay them here.
foreach(['022_payment_processing.sql','023_wallet_operations.sql','024_bank_verification.sql','025_gateway_jobs_and_deposits.sql'] as $file){
    $db->exec(file_get_contents(__DIR__.'/migrations/'.$file));
    echo 'Applied '.$file.PHP_EOL;
}
