<?php
require_once __DIR__ . '/../../config/response.php';
require_once __DIR__ . '/../../config/security.php';

header('Cache-Control: no-store');
$pdo = Database::getInstance();
$input = $_SERVER['REQUEST_METHOD'] === 'POST' ? getJsonInput() : $_GET;
$session = validateSession($pdo, getBearerToken() ?: ($input['token'] ?? ''), 15);
if (!$session['valid'] || !empty($session['is_2fa_pending'])) jsonResponse(['success'=>false,'error'=>'Please sign in to continue.'], 401);

$from = strtoupper(trim((string)($input['from_currency'] ?? '')));
$to = strtoupper(trim((string)($input['to_currency'] ?? '')));
$amount = isset($input['amount']) && is_numeric($input['amount']) ? (float)$input['amount'] : null;
if ($from === '' || $to === '' || $from === $to) jsonResponse(['success'=>false,'error'=>'Choose two different currencies.'], 422);
if ($amount !== null && $amount <= 0) jsonResponse(['success'=>false,'error'=>'Enter a valid positive amount.'], 422);

$currencies = $pdo->prepare("SELECT code,name,kind,decimals FROM currencies WHERE code IN (?,?) AND is_enabled=1 AND (code='NGN' OR kind='crypto')");
$currencies->execute([$from, $to]);
$currencyRows = $currencies->fetchAll();
if (count($currencyRows) !== 2) jsonResponse(['success'=>false,'error'=>'Only NGN and enabled crypto currencies are supported.'], 422);

$wallets = $pdo->prepare("SELECT currency_code,available_balance,pending_balance FROM wallets WHERE user_id=? AND currency_code IN (?,?) AND status IN ('active','frozen')");
$wallets->execute([(int)$session['user']['id'], $from, $to]);
$balances = [];
foreach ($wallets->fetchAll() as $wallet) $balances[$wallet['currency_code']] = ['available'=>(string)$wallet['available_balance'], 'pending'=>(string)$wallet['pending_balance']];

$providerUrl = trim((string)(getenv('FLOW_RATE_PROVIDER_URL') ?: getenv('EXCHANGE_RATES_URL') ?: ''));
$rate = null;
$provider = null;
if ($providerUrl !== '') {
    $query = http_build_query(['base'=>$from, 'quote'=>$to]);
    $url = $providerUrl . (str_contains($providerUrl, '?') ? '&' : '?') . $query;
    $ch = curl_init($url);
    curl_setopt_array($ch, [CURLOPT_RETURNTRANSFER=>true, CURLOPT_TIMEOUT=>5, CURLOPT_CONNECTTIMEOUT=>3, CURLOPT_HTTPHEADER=>['Accept: application/json']]);
    $body = curl_exec($ch);
    $http = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    $data = is_string($body) ? json_decode($body, true) : null;
    $candidate = is_array($data) ? ($data['rate'] ?? ($data['data']['rate'] ?? ($data['rates'][$to] ?? null))) : null;
    if (is_numeric($candidate) && (float)$candidate > 0 && $http >= 200 && $http < 300) {
        $rate = (float)$candidate;
        $provider = 'configured';
    }
}

$response = [
    'success'=>true,
    'available'=>$rate !== null,
    'from_currency'=>$from,
    'to_currency'=>$to,
    'rate'=>$rate !== null ? rtrim(rtrim(number_format($rate, 18, '.', ''), '0'), '.') : null,
    'amount'=>$amount !== null ? (string)$amount : null,
    'to_amount'=>$rate !== null && $amount !== null ? rtrim(rtrim(number_format($amount * $rate, 18, '.', ''), '0'), '.') : null,
    'provider'=>$provider,
    'balances'=>$balances,
    'message'=>$rate === null ? 'Live exchange rates are unavailable until a rate provider is configured.' : null,
];
jsonResponse($response);
