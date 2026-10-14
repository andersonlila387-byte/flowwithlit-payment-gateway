<?php
declare(strict_types=1);

require_once __DIR__.'/../_admin.php';
header('Cache-Control: no-store');
$input = adminMethod(['GET', 'POST']);
$pdo = Database::getInstance();
[$session, $role] = requireAdmin($pdo, $input);
requireAdminRole($role, ['super_admin','finance','reviewer'], 'manage payment links');

$checkoutUrl = static function (string $token): string {
    $configured = rtrim((string) (getenv('FLOW_CHECKOUT_URL') ?: ''), '/');
    if ($configured !== '') return $configured.'/'.rawurlencode($token);
    $host = preg_replace('/[^A-Za-z0-9.:-]/', '', (string) ($_SERVER['HTTP_HOST'] ?? 'localhost')) ?: 'localhost';
    $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $base = rtrim(str_replace('/api/admin/payment-links.php', '', (string) ($_SERVER['SCRIPT_NAME'] ?? '')), '/');
    return $scheme.'://'.$host.$base.'/checkout/'.rawurlencode($token);
};

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    requireAdminRole($role, ['super_admin','finance'], 'create or disable payment links');
    $action = (string) ($input['action'] ?? '');
    if ($action === 'create') {
        $businessId = (int) ($input['business_id'] ?? 0);
        $title = trim((string) ($input['title'] ?? ''));
        $currency = strtoupper(trim((string) ($input['currency'] ?? 'NGN')));
        $amount = $input['amount'] ?? null;
        $expires = trim((string) ($input['expires_at'] ?? ''));
        if ($businessId < 1 || $title === '') jsonResponse(['success'=>false,'error'=>'Merchant and link title are required.'], 422);
        if (!in_array($currency, ['NGN','USDT','USDC','BTC','ETH','SOL'], true)) jsonResponse(['success'=>false,'error'=>'Unsupported currency.'], 422);
        if ($amount !== null && $amount !== '' && (!is_numeric($amount) || (float) $amount <= 0)) jsonResponse(['success'=>false,'error'=>'Amount must be positive.'], 422);
        $expiresAt = null;
        if ($expires !== '') { $timestamp = strtotime($expires); if ($timestamp === false || $timestamp <= time()) jsonResponse(['success'=>false,'error'=>'Expiry must be in the future.'], 422); $expiresAt = gmdate('Y-m-d H:i:s', $timestamp); }
        $merchant = $pdo->prepare('SELECT b.id,b.owner_user_id,COALESCE(NULLIF(b.trading_name,\'\'),NULLIF(b.legal_name,\'\')) merchant_name FROM businesses b WHERE b.id=? LIMIT 1');
        $merchant->execute([$businessId]); $merchant = $merchant->fetch();
        if (!$merchant) jsonResponse(['success'=>false,'error'=>'Merchant not found.'], 404);
        $uuid = generateUUID(); $token = strtoupper(bin2hex(random_bytes(32)));
        $stmt = $pdo->prepare('INSERT INTO payment_links(uuid,user_id,business_id,public_token,title,description,amount,currency_code,status,expires_at) VALUES(?,?,?,?,?,?,?,?,?,?)');
        $stmt->execute([$uuid,(int) $merchant['owner_user_id'],$businessId,$token,$title,substr(trim((string) ($input['description'] ?? '')),0,1000),$amount === null || $amount === '' ? null : (string) $amount,$currency,'active',$expiresAt]);
        adminAudit($pdo,(int) $session['admin']['id'],'admin.payment_link_created','payment_link',(string) $pdo->lastInsertId(),['business_id'=>$businessId,'currency'=>$currency]);
        jsonResponse(['success'=>true,'message'=>'Payment link created.','link'=>['uuid'=>$uuid,'public_token'=>$token,'title'=>$title,'merchant_name'=>$merchant['merchant_name'],'amount'=>$amount === null || $amount === '' ? null : (string) $amount,'currency_code'=>$currency,'status'=>'active','expires_at'=>$expiresAt,'url'=>$checkoutUrl($token)]],201);
    }
    if ($action === 'disable') {
        $uuid = trim((string) ($input['uuid'] ?? '')); if ($uuid === '') jsonResponse(['success'=>false,'error'=>'Payment link reference is required.'],422);
        $stmt = $pdo->prepare("UPDATE payment_links SET status='disabled' WHERE uuid=? AND status='active'"); $stmt->execute([$uuid]);
        if (!$stmt->rowCount()) jsonResponse(['success'=>false,'error'=>'Payment link not found or already disabled.'],409);
        adminAudit($pdo,(int) $session['admin']['id'],'admin.payment_link_disabled','payment_link',$uuid);
        jsonResponse(['success'=>true,'message'=>'Payment link disabled.']);
    }
    jsonResponse(['success'=>false,'error'=>'Unknown payment-link action.'],422);
}

$businesses = $pdo->query("SELECT b.id,COALESCE(NULLIF(b.trading_name,''),NULLIF(b.legal_name,''),'Unnamed business') merchant_name,u.email FROM businesses b INNER JOIN users u ON u.id=b.owner_user_id WHERE u.status<>'closed' ORDER BY merchant_name ASC LIMIT 500")->fetchAll();
$links = $pdo->query("SELECT pl.uuid,pl.public_token,pl.title,pl.amount,pl.currency_code,pl.status,pl.expires_at,pl.created_at,COALESCE(NULLIF(b.trading_name,''),NULLIF(b.legal_name,''),'Unnamed business') merchant_name,COALESCE(SUM(CASE WHEN p.status='succeeded' THEN t.amount ELSE 0 END),0) paid_amount,COUNT(p.id) payment_count FROM payment_links pl INNER JOIN businesses b ON b.id=pl.business_id LEFT JOIN payments p ON p.payment_link_id=pl.id LEFT JOIN transactions t ON t.id=p.transaction_id GROUP BY pl.id ORDER BY pl.created_at DESC LIMIT 200")->fetchAll();
foreach ($links as &$link) $link['url'] = $checkoutUrl((string) $link['public_token']);
unset($link);
jsonResponse(['success'=>true,'role'=>$role,'can_create'=>in_array($role,['super_admin','finance'],true),'businesses'=>$businesses,'links'=>$links,'currencies'=>['NGN','USDT','USDC','BTC','ETH','SOL']]);
