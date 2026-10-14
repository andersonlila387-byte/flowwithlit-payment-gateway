<?php
require_once __DIR__ . '/../../config/response.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../services/PrivateStorage.php';

date_default_timezone_set('UTC');
header('Cache-Control: no-store, no-cache, must-revalidate');
header('Pragma: no-cache');

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'GET') {
    jsonResponse(['success' => false, 'error' => 'Method not allowed.'], 405);
}

$token = strtoupper(trim((string)($_GET['token'] ?? '')));
if (!preg_match('/^[A-F0-9]{24,64}$/', $token)) {
    jsonResponse(['success' => false, 'error' => 'This checkout link is invalid.'], 404);
}

$pdo = Database::getInstance();
$apiSession = $pdo->prepare("SELECT cs.user_id,cs.title,cs.description,cs.amount,cs.currency_code,cs.status,cs.expires_at,cs.return_url,cs.cancel_url,cs.origin,cs.environment,
        COALESCE(NULLIF(b.trading_name,''),NULLIF(b.legal_name,''),'Flowwithlit merchant') merchant_name,b.logo_url
    FROM checkout_sessions cs JOIN businesses b ON b.id=cs.business_id
    WHERE cs.public_token=? LIMIT 1");
$apiSession->execute([$token]);
$apiCheckout = $apiSession->fetch();
if ($apiCheckout) {
    if ($apiCheckout['status'] !== 'active' || strtotime($apiCheckout['expires_at']) <= time()) {
        jsonResponse(['success'=>false,'error'=>'This checkout session has expired or is no longer available.'],410);
    }
    jsonResponse(['success'=>true,'checkout'=>[
        'title'=>$apiCheckout['title'],'description'=>$apiCheckout['description'],'amount'=>(string)$apiCheckout['amount'],'currency'=>$apiCheckout['currency_code'],
        'merchant_name'=>$apiCheckout['merchant_name'],'logo_url'=>$apiCheckout['logo_url'] ?: null,'expires_at'=>$apiCheckout['expires_at'],
        'return_url'=>$apiCheckout['return_url'] ?: null,'cancel_url'=>$apiCheckout['cancel_url'] ?: null,'origin'=>'api','environment'=>$apiCheckout['environment'],
        'payment_methods'=>['card','bank_transfer','crypto'],'bank_transfer'=>null,'crypto_wallets'=>[],'transfer_fee'=>'0','total_amount'=>(string)$apiCheckout['amount'],
    ]]);
}

$stmt = $pdo->prepare(
    "SELECT pl.user_id, pl.title, pl.description, pl.amount, pl.currency_code, pl.status, pl.expires_at, pl.return_url, pl.cancel_url,
            COALESCE(NULLIF(b.trading_name,''), NULLIF(b.legal_name,''), 'Flowwithlit merchant') merchant_name,
            b.logo_url
     FROM payment_links pl
     LEFT JOIN businesses b ON b.id = COALESCE(pl.business_id, (
         SELECT owner_business.id
         FROM businesses owner_business
         WHERE owner_business.owner_user_id = pl.user_id
         ORDER BY owner_business.id ASC
         LIMIT 1
     ))
     WHERE pl.public_token = ? LIMIT 1"
);
$stmt->execute([$token]);
$link = $stmt->fetch();
if (!$link) {
    jsonResponse(['success' => false, 'error' => 'This checkout link could not be found.'], 404);
}

if ($link['status'] !== 'active' || ($link['expires_at'] && strtotime($link['expires_at']) <= time())) {
    jsonResponse(['success' => false, 'error' => 'This checkout link has expired or is no longer available.'], 410);
}

$bankTransfer = null;
if ($link['currency_code'] === 'NGN') {
    $accountQuery = $pdo->prepare("SELECT ra.account_name,ra.bank_name,ra.account_number_encrypted
        FROM wallet_receiving_accounts ra
        JOIN wallets w ON w.id=ra.wallet_id
        WHERE ra.user_id=? AND ra.currency_code='NGN' AND ra.status='active' AND w.status='active'
        ORDER BY ra.updated_at DESC LIMIT 1");
    $accountQuery->execute([(int)$link['user_id']]);
    $account = $accountQuery->fetch();
    if ($account && $account['account_number_encrypted']) {
        try {
            $bankTransfer = ['account_number'=>PrivateStorage::decrypt($account['account_number_encrypted'], 'wallet-account'), 'account_name'=>$account['account_name'], 'bank_name'=>$account['bank_name']];
        } catch (Throwable $error) {
            error_log('Checkout receiving account decrypt failed: ' . $error->getMessage());
        }
    }
}
$amount = $link['amount'] !== null ? (float)$link['amount'] : null;
$feePercent = max(0, (float)(getenv('FLOW_CHECKOUT_BANK_TRANSFER_FEE_PERCENT') ?: 0));
$feeFixed = max(0, (float)(getenv('FLOW_CHECKOUT_BANK_TRANSFER_FEE_FIXED_NGN') ?: 0));
$transferFee = $amount === null ? null : round(($amount * $feePercent / 100) + $feeFixed, 2);

// Addresses remain encrypted at rest. A checkout only receives an address when
// the merchant has an active crypto wallet and a provider-managed address.
$cryptoWallets = [];
$cryptoQuery = $pdo->prepare("SELECT w.currency_code,cda.network,cda.address_encrypted
    FROM wallets w
    JOIN currencies c ON c.code=w.currency_code AND c.kind='crypto' AND c.is_enabled=1
    JOIN crypto_deposit_addresses cda ON cda.wallet_id=w.id AND cda.status='active'
    WHERE w.user_id=? AND w.status='active'
    ORDER BY w.currency_code,cda.created_at DESC");
$cryptoQuery->execute([(int)$link['user_id']]);
foreach ($cryptoQuery->fetchAll() as $cryptoAddress) {
    try {
        $address = PrivateStorage::decrypt($cryptoAddress['address_encrypted'], 'crypto-address');
        if ($address === '') continue;
        $code = $cryptoAddress['currency_code'];
        // An exact crypto amount must be supplied by a future rate-lock provider
        // for NGN links. A link already denominated in the selected asset is exact.
        $cryptoWallets[$code] = [
            'code' => $code,
            'network' => $cryptoAddress['network'],
            'address' => $address,
            'amount' => $link['currency_code'] === $code ? (string)$link['amount'] : null,
        ];
    } catch (Throwable $error) {
        error_log('Checkout crypto address decrypt failed: ' . $error->getMessage());
    }
}

jsonResponse(['success' => true, 'checkout' => [
    'title' => $link['title'],
    'description' => $link['description'],
    'amount' => $link['amount'] !== null ? (string)$link['amount'] : null,
    'currency' => $link['currency_code'],
    'merchant_name' => $link['merchant_name'],
    'logo_url' => $link['logo_url'] ?: null,
    'expires_at' => $link['expires_at'],
    'return_url' => $link['return_url'] ?: null,
    'cancel_url' => $link['cancel_url'] ?: null,
    'origin' => 'payment_link',
    'environment' => 'live',
    'payment_methods' => ['bank_transfer', 'crypto'],
    'bank_transfer' => $bankTransfer,
    'crypto_wallets' => array_values($cryptoWallets),
    'transfer_fee' => $transferFee !== null ? (string)$transferFee : null,
    'total_amount' => $amount !== null ? (string)round($amount + $transferFee, 2) : null,
]]);
