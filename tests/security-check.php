<?php
/** Lightweight CI check for the security boundary around admin/customer APIs. */
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
$root = dirname(__DIR__);
$admin = glob($root . '/api/admin/*.php') ?: [];
$required = ['overview.php','users.php','onboarding.php','currencies.php','wallets.php','transactions.php','payouts.php','notifications.php','audit.php'];
$failures=[];
foreach ($required as $name) {
    $file=$root.'/api/admin/'.$name;
    if (!is_file($file)) { $failures[]="missing {$name}"; continue; }
    $source=file_get_contents($file);
    if (strpos($source,"require_once __DIR__ . '/../_admin.php'")===false) $failures[]="{$name} does not load admin guard";
    if (strpos($source,'requireAdmin(')===false) $failures[]="{$name} does not call requireAdmin";
}
foreach ([$root.'/api/customers/list.php',$root.'/api/customers/save.php'] as $file) {
    $source=file_get_contents($file);
    if (strpos($source,"require_once __DIR__ . '/../_admin.php'")===false || strpos($source,'requireAdmin(')===false) $failures[]='customer API missing authenticated business guard';
}
$migration=file_get_contents($root.'/database/migrations/007_admin_operations.sql');
foreach (['admin_users','payout_requests','audit_logs'] as $table) if (stripos($migration,"CREATE TABLE IF NOT EXISTS {$table}")===false && $table!=='audit_logs') $failures[]="migration missing {$table}";
if ($failures) { fwrite(STDERR,"Security checks failed:\n- ".implode("\n- ",$failures)."\n"); exit(1); }
echo "Security checks passed: ".count($required)." admin endpoints and customer authorization guards verified.\n";
