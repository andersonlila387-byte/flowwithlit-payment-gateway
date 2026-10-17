<?php
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../services/PrivateStorage.php';
$root = PrivateStorage::configuredRoot();
if (!is_dir($root) && !mkdir($root, 0700, true)) throw new RuntimeException('Unable to create private storage.');
$root = PrivateStorage::root();
if (!is_dir($root . '/uploads') && !mkdir($root . '/uploads', 0700)) throw new RuntimeException('Unable to create upload storage.');
if (!is_file($root . '/encryption.key')) {
    if (glob($root . '/uploads/*.enc')) throw new RuntimeException('Restore the existing encryption key; do not replace it.');
    $handle = fopen($root . '/encryption.key', 'xb');
    if (!$handle) throw new RuntimeException('Unable to create encryption key.');
    if (fwrite($handle, random_bytes(32)) !== 32) throw new RuntimeException('Unable to write encryption key.');
    fclose($handle); chmod($root . '/encryption.key', 0600);
}
PrivateStorage::decrypt(PrivateStorage::encrypt('check', 'setup'), 'setup');
$pdo = Database::getInstance();
$pdo->exec(file_get_contents(__DIR__ . '/migrations/004_business_onboarding.sql'));
$pdo->exec(file_get_contents(__DIR__ . '/migrations/005_launch_currencies.sql'));
$pdo->exec(file_get_contents(__DIR__ . '/migrations/006_provider_wallets.sql'));
$pdo->exec(file_get_contents(__DIR__ . '/migrations/007_admin_operations.sql'));
$pdo->exec(file_get_contents(__DIR__ . '/migrations/008_referrals.sql'));
$pdo->exec(file_get_contents(__DIR__ . '/migrations/009_checkout_redirects.sql'));
$pdo->exec(file_get_contents(__DIR__ . '/migrations/010_checkout_sessions.sql'));
$pdo->exec(file_get_contents(__DIR__ . '/migrations/010_link_business_ownership.sql'));
$pdo->exec(file_get_contents(__DIR__ . '/migrations/011_onboarding_review_notes.sql'));
$pdo->exec(file_get_contents(__DIR__ . '/migrations/012_separate_admin_identity.sql'));
$pdo->exec(file_get_contents(__DIR__ . '/migrations/013_cleanup_legacy_admin_users.sql'));
$pdo->exec(file_get_contents(__DIR__ . '/migrations/014_admin_reviewer_references.sql'));
$pdo->exec(file_get_contents(__DIR__ . '/migrations/015_admin_profile_avatar.sql'));
$pdo->exec(file_get_contents(__DIR__ . '/migrations/016_manual_wallet_credits.sql'));
$pdo->exec(file_get_contents(__DIR__ . '/migrations/017_admin_authorization.sql'));
$pdo->exec(file_get_contents(__DIR__ . '/migrations/018_admin_charges.sql'));
$pdo->exec(file_get_contents(__DIR__ . '/migrations/019_admin_transfer_followups.sql'));
$pdo->exec(file_get_contents(__DIR__ . '/migrations/020_admin_console_live_data.sql'));
$pdo->exec(file_get_contents(__DIR__ . '/migrations/021_admin_promo_codes.sql'));
$pdo->exec(file_get_contents(__DIR__ . '/migrations/022_payment_processing.sql'));
$pdo->exec(file_get_contents(__DIR__ . '/migrations/023_wallet_operations.sql'));
$pdo->exec(file_get_contents(__DIR__ . '/migrations/024_bank_verification.sql'));
$pdo->exec(file_get_contents(__DIR__ . '/migrations/025_gateway_jobs_and_deposits.sql'));
$pdo->exec(file_get_contents(__DIR__ . '/migrations/027_account_level_runtime_defaults.sql'));
echo "Onboarding and provider wallet schema and private encrypted storage are ready.\n";
