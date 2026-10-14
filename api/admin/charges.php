<?php
declare(strict_types=1);

require_once __DIR__.'/../_admin.php';
header('Cache-Control: no-store');
$input = adminMethod(['GET','POST']);
$pdo = Database::getInstance();
[$session, $role] = requireAdmin($pdo, $input);
requireAdminRole($role, ['super_admin','finance'], 'manage charges');

$operations = [
    'merchant_payment'=>'Merchant payment',
    'payment_link'=>'Payment link',
    'bank_transfer'=>'Bank transfer',
    'crypto_swap'=>'Crypto swap',
];
$defaults = [
    ['operation'=>'merchant_payment','fee_type'=>'percent_plus_flat','flat_amount'=>'0','percent'=>'1.5','min_fee'=>'0','max_fee'=>null,'bearer'=>'on_top','enabled'=>1,'bands'=>[]],
    ['operation'=>'payment_link','fee_type'=>'percent_plus_flat','flat_amount'=>'0','percent'=>'1.5','min_fee'=>'0','max_fee'=>null,'bearer'=>'on_top','enabled'=>1,'bands'=>[]],
    ['operation'=>'bank_transfer','fee_type'=>'flat','flat_amount'=>'50','percent'=>'0','min_fee'=>'0','max_fee'=>null,'bearer'=>'deducted','enabled'=>1,'bands'=>[]],
    ['operation'=>'crypto_swap','fee_type'=>'percent','flat_amount'=>'0','percent'=>'0.75','min_fee'=>'0','max_fee'=>null,'bearer'=>'deducted','enabled'=>1,'bands'=>[]],
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $uuid = trim((string) ($input['uuid'] ?? ''));
    if (($input['action'] ?? '') !== 'update' || $uuid === '') jsonResponse(['success'=>false,'error'=>'A charge rule is required.'],422);
    if (!in_array($input['fee_type'] ?? '', ['flat','percent','percent_plus_flat','tiered'], true)) jsonResponse(['success'=>false,'error'=>'Invalid fee type.'],422);
    $bands = is_array($input['bands'] ?? null) ? $input['bands'] : [];
    $config = ['fee_type'=>(string)$input['fee_type'],'flat_amount'=>$input['flat_amount'] ?? null,'percent'=>$input['percent'] ?? null,'min_fee'=>$input['min_fee'] ?? null,'max_fee'=>$input['max_fee'] ?? null,'bearer'=>in_array($input['bearer'] ?? '', ['on_top','deducted'], true) ? $input['bearer'] : 'on_top','enabled'=>!empty($input['enabled']) ? 1 : 0,'bands'=>$bands];
    $current = $pdo->prepare('SELECT * FROM admin_charge_rules WHERE uuid=? LIMIT 1'); $current->execute([$uuid]); $old = $current->fetch();
    if (!$old) jsonResponse(['success'=>false,'error'=>'Charge rule not found.'],404);
    $stmt = $pdo->prepare('UPDATE admin_charge_rules SET fee_type=?,flat_amount=?,percent=?,min_fee=?,max_fee=?,bearer=?,bands=?,enabled=?,updated_by=?,updated_at=UTC_TIMESTAMP() WHERE uuid=?');
    $stmt->execute([$config['fee_type'],$config['flat_amount'],$config['percent'],$config['min_fee'],$config['max_fee'],$config['bearer'],json_encode($bands),$config['enabled'],(int)$session['admin']['id'],$uuid]);
    $history = $pdo->prepare('INSERT INTO admin_charge_changes(rule_uuid,admin_user_id,before_config,after_config) VALUES(?,?,?,?)');
    $history->execute([$uuid,(int)$session['admin']['id'],json_encode(['fee_type'=>$old['fee_type'],'flat_amount'=>$old['flat_amount'],'percent'=>$old['percent'],'min_fee'=>$old['min_fee'],'max_fee'=>$old['max_fee'],'bearer'=>$old['bearer'],'enabled'=>(int)$old['enabled'],'bands'=>json_decode((string)$old['bands'],true) ?: []]),json_encode($config)]);
    adminAudit($pdo,(int)$session['admin']['id'],'admin.charge_updated','charge_rule',$uuid,['operation'=>$old['operation']]);
    jsonResponse(['success'=>true,'message'=>'Charge saved.']);
}

$rules = [];
$stored = $pdo->query('SELECT * FROM admin_charge_rules ORDER BY operation')->fetchAll();
foreach ($stored as $row) { $row['bands'] = json_decode((string)$row['bands'], true) ?: []; $row['enabled'] = (int)$row['enabled']; $rules[] = $row; }
if (!$rules) {
    foreach ($defaults as $index => $row) { $row['uuid'] = 'default-'.$row['operation']; $row['updated_at'] = null; $row['updated_by'] = null; $rules[] = $row; }
}
$month = $pdo->query("SELECT COALESCE(SUM(fee_amount),0) FROM transactions WHERE status='completed' AND created_at>=DATE_FORMAT(UTC_TIMESTAMP(),'%Y-%m-01')")->fetchColumn();
$changes = $pdo->query("SELECT c.created_at,c.summary,au.full_name admin_name,r.operation FROM admin_charge_changes c INNER JOIN admin_users au ON au.id=c.admin_user_id INNER JOIN admin_charge_rules r ON r.uuid=c.rule_uuid ORDER BY c.created_at DESC LIMIT 25")->fetchAll();
foreach ($changes as &$change) $change['operation_label'] = $operations[$change['operation']] ?? $change['operation'];
unset($change);
jsonResponse(['success'=>true,'rules'=>$rules,'operations'=>$operations,'summary'=>['fees_this_month'=>(string)$month],'changes'=>$changes]);
