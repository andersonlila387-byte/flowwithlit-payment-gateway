<?php
declare(strict_types=1);
require_once __DIR__.'/../../config/response.php';
require_once __DIR__.'/../../config/security.php';
require_once __DIR__.'/../../services/PromoService.php';
header('Cache-Control: no-store');
if(($_SERVER['REQUEST_METHOD']??'GET')!=='POST')jsonResponse(['success'=>false,'error'=>'Method not allowed.'],405);
$db=Database::getInstance();$input=getJsonInput();$session=validateSession($db,getBearerToken()?:($input['token']??''),15);
if(!$session['valid']||!empty($session['is_2fa_pending']))jsonResponse(['success'=>false,'error'=>'Please sign in to continue.'],401);
try {jsonResponse((new PromoService($db))->redeemWallet((int)$session['user']['id'],(string)($input['code']??''),(string)($_SERVER['HTTP_IDEMPOTENCY_KEY']??$input['idempotency_key']??'')));}
catch(InvalidArgumentException|DomainException $e){jsonResponse(['success'=>false,'error'=>$e->getMessage()],422);}
catch(Throwable $e){error_log('Promo redemption failed: '.get_class($e));jsonResponse(['success'=>false,'error'=>'Unable to redeem this code. Retry using the same request key.'],500);}
