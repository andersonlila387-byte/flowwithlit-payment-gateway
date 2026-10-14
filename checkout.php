<?php
require_once __DIR__ . '/config/environment.php';
$token = strtoupper(trim((string)($_GET['token'] ?? '')));
$scriptDirectory = str_replace('\\', '/', dirname((string)($_SERVER['SCRIPT_NAME'] ?? '/checkout.php')));
$localApiBase = ($scriptDirectory === '/' || $scriptDirectory === '.') ? '/api' : rtrim($scriptDirectory, '/') . '/api';
$apiBase = rtrim((string)(getenv('FLOW_API_URL') ?: $localApiBase), '/');
$assetBase = ($scriptDirectory === '/' || $scriptDirectory === '.') ? '' : rtrim($scriptDirectory, '/');
$brandIcon = $assetBase . '/logo/flowwithlit-icon-192.png';
$checkoutBase = rtrim((string)(getenv('FLOW_CHECKOUT_URL') ?: ''), '/');
$host = preg_replace('/[^A-Za-z0-9.:-]/', '', (string)($_SERVER['HTTP_HOST'] ?? 'localhost')) ?: 'localhost';
$scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$publicBase = $checkoutBase !== '' ? $checkoutBase : $scheme . '://' . $host . $assetBase;
$canonicalUrl = $publicBase . '/checkout' . ($token !== '' ? '/' . rawurlencode($token) : '');
$shareTitle = 'Secure checkout | Flowwithlit';
$shareDescription = 'Complete your payment securely through Flowwithlit.';

// Payment links are intentionally noindex, but their share preview should still
// identify the merchant. This lookup never exposes data in the checkout URL.
if (preg_match('/^[A-F0-9]{24,64}$/', $token)) {
  try {
    $metaPdo = new PDO(
      'mysql:host=' . (getenv('FLOW_DB_HOST') ?: 'localhost') . ';port=' . (getenv('FLOW_DB_PORT') ?: '3306') . ';dbname=' . (getenv('FLOW_DB_NAME') ?: 'flowwithlit_db') . ';charset=utf8mb4',
      getenv('FLOW_DB_USER') ?: 'root', getenv('FLOW_DB_PASSWORD') ?: '',
      [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]
    );
    $metaQuery = $metaPdo->prepare("SELECT pl.title,COALESCE(NULLIF(b.trading_name,''),NULLIF(b.legal_name,''),'Flowwithlit merchant') merchant_name
      FROM payment_links pl LEFT JOIN businesses b ON b.id=pl.business_id WHERE pl.public_token=? LIMIT 1");
    $metaQuery->execute([$token]);
    if ($meta = $metaQuery->fetch()) {
      $merchantName = trim((string)$meta['merchant_name']);
      $shareTitle = $merchantName . ' | Secure checkout';
      $shareDescription = 'Pay ' . $merchantName . ' securely through Flowwithlit' . ($meta['title'] !== '' ? ' for ' . $meta['title'] : '') . '.';
    }
  } catch (Throwable $error) {
    error_log('Checkout share metadata lookup failed: ' . $error->getMessage());
  }
}
$shareImage = $publicBase . '/logo/flowwithlit-payment-success-social.png';
$structuredData = json_encode(['@context'=>'https://schema.org','@graph'=>[
  ['@type'=>'Organization','name'=>'Flowwithlit','url'=>$publicBase,'logo'=>$publicBase . '/logo/flowwithlit-icon-512.png'],
  ['@type'=>'WebPage','name'=>$shareTitle,'description'=>$shareDescription,'url'=>$canonicalUrl,'isPartOf'=>['@type'=>'WebSite','name'=>'Flowwithlit','url'=>$publicBase]],
  ['@type'=>'BreadcrumbList','itemListElement'=>[
    ['@type'=>'ListItem','position'=>1,'name'=>'Flowwithlit','item'=>$publicBase],
    ['@type'=>'ListItem','position'=>2,'name'=>'Secure checkout','item'=>$canonicalUrl],
  ]],
]], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
?><!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="robots" content="noindex,nofollow,noarchive">
  <meta name="referrer" content="strict-origin-when-cross-origin">
  <meta name="theme-color" content="#0b2545">
  <title><?php echo htmlspecialchars($shareTitle, ENT_QUOTES, 'UTF-8'); ?></title>
  <meta name="description" content="<?php echo htmlspecialchars($shareDescription, ENT_QUOTES, 'UTF-8'); ?>">
  <link rel="canonical" href="<?php echo htmlspecialchars($canonicalUrl, ENT_QUOTES, 'UTF-8'); ?>">
  <link rel="icon" href="<?php echo htmlspecialchars($assetBase . '/logo/flowwithlit-icon-32.png', ENT_QUOTES, 'UTF-8'); ?>" sizes="32x32" type="image/png">
  <link rel="icon" href="<?php echo htmlspecialchars($assetBase . '/logo/flowwithlit-icon-192.png', ENT_QUOTES, 'UTF-8'); ?>" sizes="192x192" type="image/png">
  <link rel="apple-touch-icon" href="<?php echo htmlspecialchars($assetBase . '/logo/flowwithlit-icon-192.png', ENT_QUOTES, 'UTF-8'); ?>">
  <meta property="og:type" content="website">
  <meta property="og:site_name" content="Flowwithlit">
  <meta property="og:title" content="<?php echo htmlspecialchars($shareTitle, ENT_QUOTES, 'UTF-8'); ?>">
  <meta property="og:description" content="<?php echo htmlspecialchars($shareDescription, ENT_QUOTES, 'UTF-8'); ?>">
  <meta property="og:url" content="<?php echo htmlspecialchars($canonicalUrl, ENT_QUOTES, 'UTF-8'); ?>">
  <meta property="og:image" content="<?php echo htmlspecialchars($shareImage, ENT_QUOTES, 'UTF-8'); ?>">
  <meta name="twitter:card" content="summary_large_image">
  <meta name="twitter:title" content="<?php echo htmlspecialchars($shareTitle, ENT_QUOTES, 'UTF-8'); ?>">
  <meta name="twitter:description" content="<?php echo htmlspecialchars($shareDescription, ENT_QUOTES, 'UTF-8'); ?>">
  <meta name="twitter:image" content="<?php echo htmlspecialchars($shareImage, ENT_QUOTES, 'UTF-8'); ?>">
  <script type="application/ld+json"><?php echo $structuredData; ?></script>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Smooch+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <style>
    body,.method,.card-field input,.card-small input,.pay-button,.mobile-back{font-family:'Smooch Sans',Arial,sans-serif}
    *{box-sizing:border-box}html,body{min-height:100%;margin:0}body{background:#fafafa;color:#14314a;font-family:Arial,Helvetica,sans-serif}.checkout-page{display:flex;min-height:100vh;align-items:center;justify-content:center;padding:18px}.checkout-wrap{width:600px}.checkout-box{display:grid;grid-template-columns:178px 1fr;min-height:405px;overflow:hidden;background:#fff;border:1px solid #e7e7e7;border-radius:4px;box-shadow:0 1px 6px rgba(0,0,0,.10)}.methods{background:#f7f7f7;border-right:1px solid #ececec;padding:20px 16px}.methods-title{margin:7px 0 17px;font-size:11px;font-weight:700;color:#29445a}.method{display:flex;width:100%;align-items:center;gap:12px;height:47px;border:0;border-top:1px solid #e1e1e1;background:transparent;color:#597084;font-size:13px;text-align:left}.method-icon{width:15px;text-align:center;color:#a2a7aa;font-size:15px}.method.active{color:#00a86b}.method.active .method-icon{color:#00a86b}.new-tag{margin-left:auto;border-radius:2px;background:#fb3346;padding:2px 4px;color:#fff;font-size:8px;font-weight:700}.checkout-main{min-width:0;background:#fff}.checkout-header{display:flex;align-items:center;justify-content:space-between;height:67px;border-bottom:1px solid #f0f0f0;padding:0 19px 0 23px}.mobile-back{display:none;border:0;background:transparent;color:#0b2545;font-size:12px;font-weight:700}.brand-bars{width:23px}.brand-bars i{display:block;width:21px;height:4px;margin:3px 0;border-radius:1px;background:#12a9d1}.merchant{max-width:184px;text-align:right}.merchant-name{overflow:hidden;text-overflow:ellipsis;white-space:nowrap;color:#a7a7a7;font-size:11px}.pay-amount{margin-top:4px;color:#67717c;font-size:13px}.pay-amount strong{color:#00a86b;font-size:14px}.checkout-content{padding:20px}.test-badge{display:block;width:max-content;margin:0 auto;border:1px solid #ffd77d;border-radius:2px;background:#fff3ca;padding:4px 9px;color:#8d6512;font-size:9px;font-weight:700}.payment-heading{margin:32px 0 24px;text-align:center;color:#113653;font-size:14px;font-weight:400}.card-field{position:relative;border:1px solid #349df4;border-radius:5px;height:56px;padding:10px 9px}.field-label{display:block;color:#2b83ca;font-size:9px}.card-field input,.card-small input{width:100%;border:0;background:transparent;color:#8292a0;font-size:14px;outline:0}.card-field input{margin-top:5px}.card-grid{display:grid;grid-template-columns:1fr 1fr;gap:13px;margin-top:10px}.card-small{position:relative;height:55px;border:1px solid #dedede;border-radius:5px;padding:10px 9px}.card-small .field-label{color:#687b8b}.help{position:absolute;right:9px;top:11px;color:#aeb7bd;font-size:9px}.pay-button{width:100%;height:42px;margin-top:10px;border:0;border-radius:4px;background:#68c887;color:#fff;font-size:14px;font-weight:700}.test-card{display:block;margin-top:14px;text-align:center;color:#14314a;font-size:12px}.secure-footer{display:flex;align-items:center;justify-content:center;gap:4px;margin-top:28px;color:#617180;font-size:12px}.secure-footer strong{font-weight:700;color:#40586c}.lock{font-size:13px}.loading{width:600px;border:1px solid #e7e7e7;border-radius:4px;background:#fff;padding:38px;text-align:center;color:#617180;font-size:13px}@media(max-width:530px){.checkout-page{padding:0;align-items:flex-start}.checkout-wrap{width:100%}.checkout-box{display:block;min-height:100vh;border:0;border-radius:0;box-shadow:none}.methods{min-height:100vh;padding:26px 24px;border:0}.methods-title{margin:5px 0 22px}.method{height:58px;font-size:14px}.checkout-main{display:none;min-height:100vh}.checkout-box.mobile-method-selected .methods{display:none}.checkout-box.mobile-method-selected .checkout-main{display:block}.mobile-back{display:inline-block}.checkout-header{height:62px;padding:0 18px}.brand-bars{display:none}.checkout-content{padding:26px 20px}.merchant{max-width:170px}.secure-footer{display:none!important}}
  </style>
  <style>
    #paymentForm{width:100%;margin:0}
    .card-field,.card-small{display:block;width:100%;border-color:#0b2545;background:#fff;box-shadow:0 1px 2px rgba(15,35,57,.04)}
    .card-field{border-color:#0b2545}
    .card-field:focus-within,.card-small:focus-within{border-color:#0b2545;box-shadow:0 0 0 3px rgba(11,37,69,.10)}
    .card-field input,.card-small input{display:block;margin:4px 0 0;padding:0;color:#14314a;line-height:18px}
    .field-label,.card-small .field-label{color:#4d6477;font-weight:700}
    .card-field:focus-within .field-label,.card-small:focus-within .field-label{color:#0b2545}
    .pay-button{display:block;width:100%;background:#0b2545;box-shadow:0 2px 5px rgba(11,37,69,.22)}
    .test-card{border:0;background:transparent;cursor:pointer}
    [hidden]{display:none!important}.cancel-checkout{display:grid;width:29px;height:29px;place-items:center;border:0;border-radius:50%;background:transparent;color:#5b7082;font:700 21px/1 Arial,sans-serif;cursor:pointer}.cancel-checkout:hover{background:#edf3f8;color:#0b2545}.merchant{margin-left:auto}
    .merchant-name{font-size:14px;font-weight:700}.pay-amount{font-size:16px;font-weight:700}.pay-amount strong{font-size:18px;font-weight:800}
    .checkout-box.result-mode{display:block;min-height:405px}.checkout-result{display:grid;min-height:405px;place-content:center;justify-items:center;padding:42px 26px;text-align:center}.result-icon{display:grid;width:62px;height:62px;place-items:center;border-radius:50%;font-size:32px;font-weight:800}.result-icon.success{background:#e8f8ee;color:#147a3d}.result-icon.failure{background:#fff0f0;color:#bd3030}.result-icon.cancelled{background:#eef3f7;color:#4b6275}.result-badge{margin-top:17px;border-radius:999px;padding:5px 10px;font-size:11px;font-weight:800;letter-spacing:.08em}.result-badge.success{background:#e8f8ee;color:#147a3d}.result-badge.failure{background:#fff0f0;color:#bd3030}.result-badge.cancelled{background:#eef3f7;color:#4b6275}.result-title{margin:14px 0 6px;color:#0b2545;font-size:24px;font-weight:800}.result-copy{max-width:340px;margin:0;color:#5d7487;font-size:15px;line-height:1.45}.result-reference{margin:12px 0 0;color:#71879a;font-size:12px}.result-actions{display:flex;justify-content:center;gap:9px;margin-top:22px}.result-button{min-height:40px;border:0;border-radius:6px;background:#0b2545;padding:0 16px;color:#fff;font:800 14px 'Smooch Sans',Arial,sans-serif;cursor:pointer}.result-button.secondary{border:1px solid #cbd8e2;background:#fff;color:#0b2545}.checkout-modal{position:fixed;z-index:20;inset:0;display:grid;place-items:center;background:rgba(7,23,40,.45);padding:20px}.cancel-dialog{width:min(100%,380px);border-radius:13px;background:#fff;padding:25px;box-shadow:0 18px 45px rgba(7,23,40,.28);text-align:center}.cancel-dialog h2{margin:0;color:#0b2545;font-size:22px;font-weight:800}.cancel-dialog p{margin:8px 0 0;color:#63798b;font-size:14px;line-height:1.45}.cancel-actions{display:flex;gap:9px;justify-content:flex-end;margin-top:22px}.cancel-actions button{min-height:39px;border-radius:6px;padding:0 15px;font:800 14px 'Smooch Sans',Arial,sans-serif;cursor:pointer}.cancel-no{border:1px solid #cbd8e2;background:#fff;color:#0b2545}.cancel-yes{border:0;background:#0b2545;color:#fff}@media(max-width:530px){.checkout-result{min-height:100vh;padding:36px 24px}.result-title{font-size:28px}.result-copy{font-size:17px}.cancel-checkout{width:34px;height:34px;font-size:24px}}
    .test-badge.environment-live{border-color:#9fdfbc;background:#e8f8ee;color:#147a3d}
    .pay-button:hover{background:#14365f}
    .methods{background:linear-gradient(180deg,rgba(230,243,255,.84),rgba(242,249,255,.70));border-right-color:rgba(129,180,221,.38)}
    .method{width:calc(100% + 32px);margin-left:-16px;padding-left:16px;padding-right:16px;border-top-color:rgba(129,180,221,.32);border-radius:0;font-weight:800}
    .method.active{background:rgba(11,37,69,.12)}
    .method:hover{background:rgba(255,255,255,.55)}
    .brand-logo{width:27px;height:27px;object-fit:contain;border-radius:7px;box-shadow:none!important}
    .mobile-method-brand{display:none}
    .checkout-wrap{width:630px}.checkout-box{grid-template-columns:205px 1fr}.methods-title{font-size:15px}.method{height:56px;font-size:17px}.mobile-method-brand span{font-family:'Smooch Sans',Arial,sans-serif!important}
    .pay-amount strong,.method.active,.method.active .method-icon{color:#0b2545}
    body,.bank-title,.bank-copy,.transfer-card,.transfer-note,.transfer-unavailable,.copy-status{font-family:'Smooch Sans',Arial,sans-serif!important}
    #bankTransferPanel{margin-top:10px}.bank-title{margin:28px 0 6px;text-align:center;color:#0b2545;font-size:20px;font-weight:800}.bank-copy{margin:0 auto 20px;max-width:360px;text-align:center;color:#62768a;font-size:14px;line-height:1.35}.transfer-card{border:1px solid #d6e0ea;border-radius:10px;background:#fff;padding:16px;box-shadow:0 2px 6px rgba(11,37,69,.05)}.transfer-label{color:#698094;font-size:12px;font-weight:700;text-transform:uppercase}.account-row{display:flex;align-items:center;justify-content:space-between;gap:12px;margin-top:6px}.account-number{color:#0b2545;font-size:24px;font-weight:800;letter-spacing:.04em}.copy-button{display:inline-flex;align-items:center;justify-content:center;width:34px;height:34px;border:1px solid #b9cad9;border-radius:7px;background:#f6faff;color:#0b2545;cursor:pointer}.copy-button:hover{background:#e8f2fb}.bank-name{margin-top:6px;color:#496175;font-size:14px}.transfer-breakdown{margin-top:14px;border-top:1px solid #e5edf3;padding-top:8px}.transfer-line{display:flex;justify-content:space-between;gap:14px;padding:7px 0;color:#657a8d;font-size:14px}.transfer-line strong{color:#0b2545;font-weight:800}.transfer-total{margin-top:3px;border-top:1px solid #dbe5ed;padding-top:12px;color:#0b2545;font-size:16px;font-weight:800}.transfer-note{margin:15px 0 0;border-radius:8px;background:rgba(225,241,255,.72);padding:11px 12px;color:#46657e;font-size:13px;line-height:1.35}.transfer-unavailable{margin-top:28px;border:1px solid #d3e0ea;border-radius:9px;background:#f7fbff;padding:18px;text-align:center;color:#526e85;font-size:14px;line-height:1.4}.copy-status{min-height:18px;margin:9px 0 0;text-align:center;color:#0b2545;font-size:12px;font-weight:700}@media(max-width:530px){.bank-title{font-size:24px}.bank-copy{font-size:16px}.transfer-card{padding:17px}.account-number{font-size:25px}.transfer-line{font-size:16px}.transfer-note{font-size:15px}}
    #cryptoPaymentPanel{margin-top:10px}.crypto-title{margin:28px 0 6px;text-align:center;color:#0b2545;font-size:20px;font-weight:800}.crypto-copy{margin:0 auto 20px;max-width:360px;text-align:center;color:#62768a;font-size:14px;line-height:1.35}.crypto-options{display:grid;grid-template-columns:1fr 1fr;gap:10px}.crypto-option{display:flex;align-items:center;gap:10px;min-height:59px;border:1px solid #cbdbe8;border-radius:9px;background:#fff;padding:11px;text-align:left;color:#0b2545;font:700 15px 'Smooch Sans',Arial,sans-serif}.crypto-icon{display:block;width:31px;height:31px;flex:0 0 31px;object-fit:contain}.crypto-option small{display:block;margin-top:2px;color:#70869a;font-size:11px;font-weight:600}.crypto-option.selected{border-color:#0b2545;background:#eef6fd;box-shadow:0 0 0 2px rgba(11,37,69,.09)}.crypto-next{display:block;width:100%;height:43px;margin-top:16px;border:0;border-radius:6px;background:#0b2545;color:#fff;font:700 16px 'Smooch Sans',Arial,sans-serif}.crypto-next:disabled{cursor:not-allowed;opacity:.45}.crypto-back{display:block;margin:14px auto 0;border:0;background:none;color:#0b2545;font:700 14px 'Smooch Sans',Arial,sans-serif}.crypto-address-card{border:1px solid #d6e0ea;border-radius:10px;background:#fff;padding:16px}.crypto-qr{display:grid;width:150px;height:150px;margin:8px auto 18px;place-items:center;border:1px solid #d3e0ea;background:#f8fbfd;color:#61788e;font-size:13px}.crypto-qr img{width:100%;height:100%;object-fit:contain}.crypto-address{margin-top:7px;overflow-wrap:anywhere;color:#0b2545;font:700 14px/1.35 'Smooch Sans',Arial,sans-serif}.crypto-unavailable{margin-top:24px;border:1px solid #d3e0ea;border-radius:9px;background:#f7fbff;padding:18px;text-align:center;color:#526e85;font:600 14px/1.4 'Smooch Sans',Arial,sans-serif}@media(max-width:530px){.crypto-title{font-size:24px}.crypto-copy{font-size:16px}.crypto-option{min-height:67px;font-size:18px}.crypto-icon{width:35px;height:35px;flex-basis:35px}.crypto-option small{font-size:13px}.crypto-next{height:48px;font-size:18px}.crypto-address{font-size:16px}}
    @media(max-width:530px){.brand-logo{display:none}.checkout-wrap{width:100%}.checkout-box{grid-template-columns:1fr}.methods{display:flex;flex-direction:column;justify-content:flex-start;padding-top:68px;background:linear-gradient(180deg,rgba(225,241,255,.94),rgba(244,250,255,.88))}.mobile-method-brand{display:flex;flex-direction:column;align-items:center;margin-bottom:42px;text-align:center}.mobile-method-brand img{width:50px;height:50px;border-radius:10px;box-shadow:none!important}.mobile-method-brand span{margin-top:9px;color:#0b2545;font-size:25px;font-weight:800;letter-spacing:0}.methods-title{margin-bottom:20px;font-size:17px}.method{width:calc(100% + 48px);height:68px;margin-left:-24px;padding:0 24px;border-radius:0;font-size:20px}.method-icon{width:22px;font-size:20px}.new-tag{font-size:10px}}
  </style>
</head>
<body>
  <main class="checkout-page">
    <div class="checkout-wrap">
      <section id="loading" class="loading">Preparing checkout…</section>
      <section id="checkout" class="checkout-box" hidden>
        <aside class="methods">
          <div class="mobile-method-brand"><img src="<?php echo htmlspecialchars($brandIcon, ENT_QUOTES, 'UTF-8'); ?>" alt=""><span>Flowwithlit</span></div>
          <p class="methods-title">PAY WITH</p>
          <button type="button" class="method active" data-method="Card"><span class="method-icon">▰</span><span>Card</span></button>
          <button type="button" class="method" data-method="Bank transfer"><span class="method-icon">♖</span><span>Bank transfer</span></button>
          <button type="button" class="method" data-method="Crypto payments"><span class="method-icon">₿</span><span>Crypto payments</span></button>
        </aside>
        <section class="checkout-main">
          <header class="checkout-header">
            <button id="mobileMethodBack" type="button" class="mobile-back">← Pay with</button>
            <img class="brand-logo" src="<?php echo htmlspecialchars($brandIcon, ENT_QUOTES, 'UTF-8'); ?>" alt="Flowwithlit">
            <div class="merchant"><div id="merchantName" class="merchant-name">Loading merchant</div><div class="pay-amount">Pay <strong id="checkoutAmount">NGN —</strong></div></div>
            <button id="cancelCheckout" class="cancel-checkout" type="button" aria-label="Cancel checkout">×</button>
          </header>
           <div class="checkout-content">
             <span id="environmentBadge" class="test-badge" hidden></span>
             <div id="customerFields" class="card-grid" style="margin:12px 0">
               <label class="card-small"><span class="field-label">NAME</span><input id="customerName" autocomplete="name" required></label>
               <label class="card-small"><span class="field-label">EMAIL</span><input id="customerEmail" type="email" autocomplete="email" required></label>
             </div>
            <p id="paymentHeading" class="payment-heading" data-card-only>Enter your card details to pay</p>
            <form id="paymentForm" novalidate data-card-only>
              <label class="card-field"><span class="field-label">CARD NUMBER</span><input id="cardNumber" aria-label="Card number" inputmode="numeric" placeholder="0000 0000 0000 0000"></label>
              <div class="card-grid">
                <label class="card-small"><span class="field-label">CARD EXPIRY</span><input id="cardExpiry" aria-label="Card expiry" inputmode="numeric" placeholder="MM / YY"></label>
                <label class="card-small"><span class="field-label">CVV</span><span class="help">HELP?</span><input id="cardCvv" aria-label="CVV" inputmode="numeric" placeholder="123"></label>
              </div>
              <button id="payButton" class="pay-button" type="button">Pay <span id="payButtonAmount">NGN —</span></button>
              <button id="testCardFillButton" type="button" class="test-card" hidden>Use a test card</button>
              <p id="cardPaymentStatus" class="copy-status" aria-live="polite"></p>
            </form>
            <section id="bankTransferPanel" hidden>
              <h1 class="bank-title">Make a bank transfer</h1>
              <p class="bank-copy">Transfer the exact total to the account below. Your payment will confirm automatically.</p>
              <div id="bankTransferUnavailable" class="transfer-unavailable" hidden>Bank transfer is not available for this merchant yet.</div>
              <div id="bankTransferDetails" hidden>
                <div class="transfer-card">
                  <span class="transfer-label">Account number</span>
                  <div class="account-row"><strong id="bankAccountNumber" class="account-number">—</strong><button type="button" class="copy-button" data-copy-value="bankAccountNumber" aria-label="Copy account number"><svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="9" y="9" width="11" height="11" rx="2"></rect><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path></svg></button></div>
                  <p id="bankAccountName" class="bank-name">—</p>
                  <p id="bankName" class="bank-name">—</p>
                  <div class="transfer-breakdown">
                    <div class="transfer-line"><span>Payment amount</span><span><strong id="transferAmount">—</strong> <button type="button" class="copy-button" data-copy-value="transferAmount" aria-label="Copy payment amount"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="9" y="9" width="11" height="11" rx="2"></rect><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2 2v1"></path></svg></button></span></div>
                    <div class="transfer-line"><span>Transfer charge</span><strong id="transferFee">—</strong></div>
                    <div class="transfer-line transfer-total"><span>Total to transfer</span><span><strong id="transferTotal">—</strong> <button type="button" class="copy-button" data-copy-value="transferTotal" aria-label="Copy total amount"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="9" y="9" width="11" height="11" rx="2"></rect><path d="M5 15H4a2 2 0 0 1 2 2v1"></path></svg></button></span></div>
                  </div>
                </div>
                <p class="transfer-note">Use the exact amount above. Transfers with a different amount may take longer to confirm.</p>
                <button id="bankTestPaidButton" type="button" class="crypto-next" hidden>I have paid</button>
                <p id="copyStatus" class="copy-status" aria-live="polite"></p>
              </div>
            </section>
            <section id="cryptoPaymentPanel" hidden>
              <div id="cryptoMethodStep">
                <h1 class="crypto-title">Choose a cryptocurrency</h1>
                <p class="crypto-copy">Select the asset you want to use, then continue to the payment address.</p>
                <div class="crypto-options" role="list">
                  <button type="button" class="crypto-option" data-crypto-code="USDT"><img class="crypto-icon" src="<?php echo htmlspecialchars($assetBase . '/logo/crypto/usdt.svg', ENT_QUOTES, 'UTF-8'); ?>" alt=""><span>USDT<small>Tether</small></span></button>
                  <button type="button" class="crypto-option" data-crypto-code="USDC"><img class="crypto-icon" src="<?php echo htmlspecialchars($assetBase . '/logo/crypto/usdc.png', ENT_QUOTES, 'UTF-8'); ?>" alt=""><span>USDC<small>USD Coin</small></span></button>
                  <button type="button" class="crypto-option" data-crypto-code="BTC"><img class="crypto-icon" src="<?php echo htmlspecialchars($assetBase . '/logo/crypto/btc.png', ENT_QUOTES, 'UTF-8'); ?>" alt=""><span>Bitcoin<small>BTC</small></span></button>
                  <button type="button" class="crypto-option" data-crypto-code="ETH"><img class="crypto-icon" src="<?php echo htmlspecialchars($assetBase . '/logo/crypto/eth.png', ENT_QUOTES, 'UTF-8'); ?>" alt=""><span>Ethereum<small>ETH</small></span></button>
                </div>
                <button id="cryptoNextButton" type="button" class="crypto-next" disabled>Next</button>
              </div>
              <div id="cryptoAddressStep" hidden>
                <h1 class="crypto-title">Send <span id="cryptoSelectedCode">crypto</span></h1>
                <p class="crypto-copy">Scan the QR code or copy the wallet address below.</p>
                <div id="cryptoAddressDetails" class="crypto-address-card" hidden>
                  <div id="cryptoQr" class="crypto-qr">QR code</div>
                  <span class="transfer-label">Wallet address</span>
                  <div class="account-row"><strong id="cryptoAddress" class="crypto-address">—</strong><button type="button" class="copy-button" data-copy-value="cryptoAddress" aria-label="Copy wallet address"><svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="9" y="9" width="11" height="11" rx="2"></rect><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2 2v1"></path></svg></button></div>
                  <p id="cryptoNetwork" class="bank-name">—</p>
                  <div class="transfer-breakdown"><div class="transfer-line transfer-total"><span>Amount to send</span><strong id="cryptoAmount">—</strong></div></div>
                </div>
                <div id="cryptoUnavailable" class="crypto-unavailable" hidden>A receiving wallet for this cryptocurrency has not been configured yet.</div>
                <button id="cryptoPaidButton" type="button" class="crypto-next" hidden>I have made payment</button>
                <p id="cryptoPaymentStatus" class="copy-status" aria-live="polite"></p>
                <button id="cryptoBackButton" type="button" class="crypto-back">← Choose another cryptocurrency</button>
              </div>
            </section>
          </div>
          <section id="checkoutResult" class="checkout-result" hidden aria-live="polite">
            <div id="resultIcon" class="result-icon success">✓</div>
            <span id="resultBadge" class="result-badge success">PAYMENT SUCCESSFUL</span>
            <h1 id="resultTitle" class="result-title">Payment successful</h1>
            <p id="resultCopy" class="result-copy">Your payment has been confirmed.</p>
            <p id="resultReference" class="result-reference" hidden></p>
            <div class="result-actions"><button id="retryPayment" class="result-button secondary" type="button" hidden>Try again</button><button id="returnToMerchant" class="result-button" type="button" hidden>Return to merchant</button></div>
          </section>
        </section>
      </section>
      <footer id="securityFooter" class="secure-footer" hidden><span class="lock">▣</span><span>Secured by <strong>Flowwithlit</strong></span></footer>
    </div>
  </main>
  <div id="cancelModal" class="checkout-modal" hidden role="dialog" aria-modal="true" aria-labelledby="cancelTitle">
    <section class="cancel-dialog"><h2 id="cancelTitle">Cancel payment?</h2><p>Your payment has not been completed. Are you sure you want to leave this checkout?</p><div class="cancel-actions"><button id="cancelNo" class="cancel-no" type="button">Continue payment</button><button id="cancelYes" class="cancel-yes" type="button">Yes, cancel</button></div></section>
  </div>
  <script>
    const token=<?php echo json_encode($token, JSON_HEX_TAG|JSON_HEX_APOS|JSON_HEX_QUOT|JSON_HEX_AMP); ?>,apiBase=<?php echo json_encode($apiBase, JSON_HEX_TAG|JSON_HEX_APOS|JSON_HEX_QUOT|JSON_HEX_AMP); ?>;
    const $=id=>document.getElementById(id);
     let checkoutData=null,selectedCrypto=null,testBankTransfer=null,paymentReference=null,statusTimer=null,statusSocket=null;
    function checkoutAmount(amount,currency){if(amount===null||amount===undefined||amount==='')return 'NGN —';const numeric=Number(amount);return `${currency||'NGN'} ${Number.isFinite(numeric)?numeric.toLocaleString('en-NG',{maximumFractionDigits:2}):amount}`;}
    function isTestCheckout(){return checkoutData?.origin==='api'&&String(checkoutData?.environment||'').toLowerCase()==='test';}
    function randomDigit(){if(window.crypto?.getRandomValues){const bytes=new Uint8Array(1);window.crypto.getRandomValues(bytes);return bytes[0]%10;}return Math.floor(Math.random()*10);}
    function testAccountNumber(){return `000${Array.from({length:7},randomDigit).join('')}`;}
    function luhnCheckDigit(value){let sum=0,doubleDigit=true;for(let index=value.length-1;index>=0;index--){let digit=Number(value[index]);if(doubleDigit){digit*=2;if(digit>9)digit-=9;}sum+=digit;doubleDigit=!doubleDigit;}return String((10-(sum%10))%10);}
    function testCardNumber(){const partial=`4${Array.from({length:14},randomDigit).join('')}`;return `${partial}${luhnCheckDigit(partial)}`.replace(/(.{4})/g,'$1 ').trim();}
    function getTestBankTransfer(){if(!testBankTransfer)testBankTransfer={account_number:testAccountNumber(),account_name:'Flowwithlit Test Merchant',bank_name:'Test Bank'};return testBankTransfer;}
    function testCryptoWallet(code){const amounts={USDT:'12.50',USDC:'12.50',BTC:'0.00025',ETH:'0.005'};return {code,network:'Test network — no blockchain transfer',address:`TEST-${code}-${Array.from({length:12},()=>randomDigit().toString(16)).join('').toUpperCase()}`,amount:amounts[code]||'1'};}
    let isLeavingCheckout=false,redirectTimer=null;
     function showCheckoutResult(type,copy,reference=''){const settings={success:{icon:'✓',badge:'PAYMENT SUCCESSFUL',title:'Payment successful'},failure:{icon:'!',badge:'PAYMENT FAILED',title:'Payment failed'},cancelled:{icon:'×',badge:'PAYMENT CANCELLED',title:'Payment cancelled'}}[type]||{};$('checkout').classList.add('result-mode');document.querySelector('.methods').hidden=true;document.querySelector('.checkout-header').hidden=true;document.querySelector('.checkout-content').hidden=true;$('checkoutResult').hidden=false;$('resultIcon').className=`result-icon ${type}`;$('resultIcon').textContent=settings.icon;$('resultBadge').className=`result-badge ${type}`;$('resultBadge').textContent=settings.badge;$('resultTitle').textContent=settings.title;$('resultCopy').textContent=copy;$('resultReference').hidden=!reference;$('resultReference').textContent=reference?`Reference: ${reference}`:'';$('retryPayment').hidden=type!=='failure';const returnUrl=checkoutData?.return_url;$('returnToMerchant').hidden=!returnUrl;$('returnToMerchant').textContent=type==='success'?'Return to merchant':'Return';if(type==='success'&&returnUrl){clearTimeout(redirectTimer);redirectTimer=setTimeout(()=>{isLeavingCheckout=true;window.location.assign(returnUrl);},2500);}}
    function showCheckoutFailure(message='Your payment could not be completed. Please try again or choose another payment method.') {showCheckoutResult('failure',message);}
    function resetCheckout(){clearTimeout(redirectTimer);$('checkout').classList.remove('result-mode');document.querySelector('.methods').hidden=false;document.querySelector('.checkout-header').hidden=false;document.querySelector('.checkout-content').hidden=false;$('checkoutResult').hidden=true;selectPaymentMethod(document.querySelector('.method.active'));}
    function openCancelModal(){$('cancelModal').hidden=false;$('cancelNo').focus();}
    function closeCancelModal(){$('cancelModal').hidden=true;}
    function confirmCancellation(){closeCancelModal();const destination=checkoutData?.cancel_url||checkoutData?.return_url;if(destination){isLeavingCheckout=true;window.location.assign(destination);return;}showCheckoutResult('cancelled','This checkout has been cancelled. You may close this tab.');window.close();}
     async function startPayment(method){const name=$('customerName').value.trim(),email=$('customerEmail').value.trim();if(!name||!email){$('cardPaymentStatus').textContent='Enter your name and email first.';return;}const response=await fetch(apiBase+'/checkout/start.php',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({token,customer_name:name,customer_email:email,method})});const result=await response.json();if(!response.ok||!result.success){showCheckoutFailure(result.error||'Payment could not be started.');return;}paymentReference=result.reference;$('cardPaymentStatus').textContent='Payment created. Waiting for provider confirmation…';watchPayment();}
     function applyPaymentStatus(status){if(status==='succeeded'){showCheckoutResult('success','Your payment has been confirmed.',paymentReference||'');}else if(status==='failed'){showCheckoutFailure('The payment provider declined or failed this payment.');}else if(status==='expired'){showCheckoutFailure('This payment expired before confirmation.');}}
     async function pollPayment(){try{const r=await fetch(apiBase+'/checkout/payment-status.php?token='+encodeURIComponent(token),{cache:'no-store'});const j=await r.json();if(j.payment){paymentReference=paymentReference||j.payment.reference;applyPaymentStatus(j.payment.status);}}catch(e){}}
     function watchPayment(){clearInterval(statusTimer);pollPayment();statusTimer=setInterval(pollPayment,5000);const wsBase=<?php echo json_encode((string)(getenv('FLOW_GO_WS_URL') ?: ''), JSON_HEX_TAG|JSON_HEX_APOS|JSON_HEX_QUOT|JSON_HEX_AMP); ?>;if(wsBase){try{statusSocket=new WebSocket(wsBase+'/'+encodeURIComponent(token));statusSocket.onmessage=e=>{try{applyPaymentStatus(JSON.parse(e.data).status)}catch(_){}};}catch(e){}}}
     function finishTestPayment(method){if(!isTestCheckout()){startPayment(method==='bank transfer'?'bank_transfer':method==='crypto'?'crypto':'card');return true;}if(method==='card'&&$('cardNumber').value.replace(/\D/g,'').endsWith('0002')){showCheckoutFailure('This test card was declined. No real money was moved.');return true;}showCheckoutResult('success',`Test ${method} payment successful. No real money was moved.`,`TEST-${Date.now().toString(36).toUpperCase()}`);return true;}
    function fillTestCard(){if(!isTestCheckout())return;$('cardNumber').value=testCardNumber();const now=new Date();$('cardExpiry').value=`${String(now.getMonth()+1).padStart(2,'0')} / ${String((now.getFullYear()+2)%100).padStart(2,'0')}`;$('cardCvv').value=String(100+randomDigit()*100+randomDigit()*10+randomDigit()).slice(0,3);$('cardPaymentStatus').textContent='A generated test card has been added.';}
    function setBankTransferDetails(){const test=isTestCheckout(),transfer=test?getTestBankTransfer():checkoutData?.bank_transfer,hasTransfer=Boolean(transfer?.account_number&&checkoutData?.amount!==null);$('bankTransferUnavailable').hidden=hasTransfer;$('bankTransferDetails').hidden=!hasTransfer;$('bankTestPaidButton').hidden=!(test&&hasTransfer);if(!hasTransfer)return;$('bankAccountNumber').textContent=transfer.account_number;$('bankAccountName').textContent=transfer.account_name||'Account name pending';$('bankName').textContent=transfer.bank_name||'Nigerian bank';$('transferAmount').textContent=checkoutAmount(checkoutData.amount,'NGN');$('transferFee').textContent=checkoutAmount(test?0:checkoutData.transfer_fee,'NGN');$('transferTotal').textContent=checkoutAmount(test?checkoutData.amount:checkoutData.total_amount,'NGN');}
    function resetCrypto(){selectedCrypto=null;document.querySelectorAll('.crypto-option').forEach(item=>item.classList.remove('selected'));$('cryptoNextButton').disabled=true;$('cryptoMethodStep').hidden=false;$('cryptoAddressStep').hidden=true;$('cryptoPaymentStatus').textContent='';}
    function cryptoWallet(code){return isTestCheckout()?testCryptoWallet(code):(checkoutData?.crypto_wallets||[]).find(wallet=>wallet.code===code)||null;}
    function setCryptoAddress(){const wallet=cryptoWallet(selectedCrypto),hasAddress=Boolean(wallet?.address),hasAmount=Boolean(wallet?.amount);$('cryptoSelectedCode').textContent=selectedCrypto||'crypto';$('cryptoAddressDetails').hidden=!(hasAddress&&hasAmount);$('cryptoPaidButton').hidden=!(hasAddress&&hasAmount);$('cryptoUnavailable').hidden=hasAddress&&hasAmount;if(!(hasAddress&&hasAmount)){const reason=hasAddress?'A locked exchange quote is not available for this payment yet.':'A receiving wallet for this cryptocurrency has not been configured yet.';$('cryptoUnavailable').textContent=reason;return;}$('cryptoAddress').textContent=wallet.address;$('cryptoNetwork').textContent=wallet.network||'Network will be confirmed by the provider.';$('cryptoAmount').textContent=checkoutAmount(wallet.amount,wallet.code);const qr=wallet.qr_code_data_url;if(typeof qr==='string'&&/^data:image\/(?:png|svg\+xml);base64,/i.test(qr)){$('cryptoQr').replaceChildren(Object.assign(new Image(),{src:qr,alt:`QR code for ${wallet.code} payment`}));}else{$('cryptoQr').textContent=isTestCheckout()?'Test address':'Copy address';}}
    function selectCrypto(code){selectedCrypto=code;document.querySelectorAll('.crypto-option').forEach(item=>item.classList.toggle('selected',item.dataset.cryptoCode===code));$('cryptoNextButton').disabled=false;}
    function showCryptoAddress(){if(!selectedCrypto)return;$('cryptoMethodStep').hidden=true;$('cryptoAddressStep').hidden=false;$('cryptoPaymentStatus').textContent='';setCryptoAddress();}
    function selectPaymentMethod(method){const bank=method.dataset.method==='Bank transfer',crypto=method.dataset.method==='Crypto payments';document.querySelectorAll('.method').forEach(item=>item.classList.toggle('active',item===method));document.querySelectorAll('[data-card-only]').forEach(item=>item.hidden=bank||crypto);$('bankTransferPanel').hidden=!bank;$('cryptoPaymentPanel').hidden=!crypto;if(bank)setBankTransferDetails();if(crypto)resetCrypto();if(window.matchMedia('(max-width: 530px)').matches)$('checkout').classList.add('mobile-method-selected');}
    async function copyCheckoutValue(id){const value=$(id).textContent.trim(),status=id==='cryptoAddress'?$('cryptoPaymentStatus'):$('copyStatus');if(!value||value==='—')return;try{await navigator.clipboard.writeText(value);status.textContent='Copied to clipboard.';}catch(error){status.textContent='Select and copy this value manually.';}}
    async function init(){try{const response=await fetch(apiBase+'/checkout/session.php?token='+encodeURIComponent(token),{headers:{Accept:'application/json'},cache:'no-store'}),result=await response.json();if(!response.ok||!result.success)throw new Error(result.error||'Checkout unavailable.');checkoutData=result.checkout;const amount=checkoutAmount(checkoutData.amount,checkoutData.currency),environment=String(checkoutData.environment||'').toLowerCase(),showEnvironment=['test','live'].includes(environment),isApiCheckout=checkoutData.origin==='api';$('merchantName').textContent=checkoutData.merchant_name||'Flowwithlit';$('checkoutAmount').textContent=amount;$('payButtonAmount').textContent=amount;$('environmentBadge').hidden=!showEnvironment;$('testCardFillButton').hidden=!(isApiCheckout&&environment==='test');if(showEnvironment){$('environmentBadge').textContent=environment.toUpperCase();$('environmentBadge').classList.toggle('environment-live',environment==='live');}history.pushState({flowwithlitCheckout:true},'',window.location.href);$('loading').hidden=true;$('checkout').hidden=false;$('securityFooter').hidden=false;}catch(error){$('loading').textContent=error.message;}}
    document.querySelectorAll('.method').forEach(method=>method.addEventListener('click',()=>selectPaymentMethod(method)));
    document.querySelectorAll('.crypto-option').forEach(option=>option.addEventListener('click',()=>selectCrypto(option.dataset.cryptoCode)));
    document.querySelectorAll('[data-copy-value]').forEach(button=>button.addEventListener('click',()=>copyCheckoutValue(button.dataset.copyValue)));
    $('cryptoNextButton').addEventListener('click',showCryptoAddress);
    $('cryptoBackButton').addEventListener('click',()=>{$('cryptoAddressStep').hidden=true;$('cryptoMethodStep').hidden=false;$('cryptoPaymentStatus').textContent='';});
    $('cryptoPaidButton').addEventListener('click',()=>{if(!finishTestPayment('crypto'))$('cryptoPaymentStatus').textContent='Waiting for confirmation from the payment provider.';});
    $('testCardFillButton').addEventListener('click',fillTestCard);
    $('payButton').addEventListener('click',()=>finishTestPayment('card'));
    $('bankTestPaidButton').addEventListener('click',()=>finishTestPayment('bank transfer'));
    $('mobileMethodBack').addEventListener('click',()=>$('checkout').classList.remove('mobile-method-selected'));
    $('cancelCheckout').addEventListener('click',openCancelModal);
    $('cancelNo').addEventListener('click',closeCancelModal);
    $('cancelYes').addEventListener('click',confirmCancellation);
    $('retryPayment').addEventListener('click',resetCheckout);
    $('returnToMerchant').addEventListener('click',()=>{if(checkoutData?.return_url){isLeavingCheckout=true;window.location.assign(checkoutData.return_url);}});
    $('cancelModal').addEventListener('click',event=>{if(event.target===$('cancelModal'))closeCancelModal();});
    window.addEventListener('popstate',()=>{if(!isLeavingCheckout&&checkoutData&&!$('checkoutResult').hidden){return;}if(!isLeavingCheckout&&checkoutData){history.pushState({flowwithlitCheckout:true},'',window.location.href);openCancelModal();}});
    window.FlowwithlitCheckout={success:(message,reference)=>showCheckoutResult('success',message||'Your payment has been confirmed.',reference||''),failure:message=>showCheckoutFailure(message)};
    init();
  </script>
</body>
</html>
