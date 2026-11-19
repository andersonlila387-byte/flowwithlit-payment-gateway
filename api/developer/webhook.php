<?php
require_once __DIR__ . '/../../config/response.php'; require_once __DIR__ . '/../../config/security.php';
if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') jsonResponse(['success'=>false,'error'=>'Method not allowed.'],405);
$pdo=Database::getInstance(); $input=getJsonInput(); $session=validateSession($pdo,getBearerToken() ?: ($input['token']??''),15); if(!$session['valid']||!empty($session['is_2fa_pending'])) jsonResponse(['success'=>false,'error'=>'Please sign in to continue.'],401);
$business=$pdo->prepare('SELECT id FROM businesses WHERE owner_user_id=? LIMIT 1'); $business->execute([(int)$session['user']['id']]); $businessId=(int)$business->fetchColumn(); if(!$businessId) jsonResponse(['success'=>false,'error'=>'Complete business onboarding first.'],422);
$action=strtolower(trim((string)($input['action']??'save'))); $url=trim((string)($input['url']??'')); if(mb_strlen($url)>500||!filter_var($url,FILTER_VALIDATE_URL)||strtolower((string)parse_url($url,PHP_URL_SCHEME))!=='https'||parse_url($url,PHP_URL_USER)!==null) jsonResponse(['success'=>false,'error'=>'Enter a valid HTTPS webhook URL.'],422);
/**
 * Our server makes this request, so the address must be a public internet host: never localhost, a private
 * network, or a cloud metadata address (server-side request forgery). Returns the vetted IP to connect to,
 * which is pinned with CURLOPT_RESOLVE so DNS cannot change between this check and the request.
 */
function publicWebhookTarget(string $url): ?array {
    $host=strtolower(trim((string)parse_url($url,PHP_URL_HOST),'[]')); $port=(int)(parse_url($url,PHP_URL_PORT)?:443);
    if($host===''||$port!==443) return null;
    $ips=filter_var($host,FILTER_VALIDATE_IP)?[$host]:array_merge(array_column(@dns_get_record($host,DNS_A)?:[],'ip'),array_column(@dns_get_record($host,DNS_AAAA)?:[],'ipv6'));
    if(!$ips) return null;
    foreach($ips as $ip) if(!filter_var($ip,FILTER_VALIDATE_IP,FILTER_FLAG_NO_PRIV_RANGE|FILTER_FLAG_NO_RES_RANGE)) return null;
    return ['host'=>$host,'port'=>$port,'ip'=>$ips[0]];
}
$target=publicWebhookTarget($url); if(!$target) jsonResponse(['success'=>false,'error'=>'Use a public HTTPS address on port 443 for your webhook.'],422);
$hook=$pdo->prepare('SELECT id,secret_hash FROM webhook_endpoints WHERE business_id=? ORDER BY id DESC LIMIT 1'); $hook->execute([$businessId]); $existing=$hook->fetch();
if($action==='save') { $secret='whsec_'.bin2hex(random_bytes(24)); if($existing){$pdo->prepare('UPDATE webhook_endpoints SET url=?,secret_hash=?,status=\'active\' WHERE id=?')->execute([$url,hash('sha256',$secret),(int)$existing['id']]);$id=(int)$existing['id'];}else{$pdo->prepare('INSERT INTO webhook_endpoints(business_id,url,secret_hash,events,status) VALUES(?,?,?,?,\'active\')')->execute([$businessId,$url,hash('sha256',$secret),json_encode(['*'])]);$id=(int)$pdo->lastInsertId();} jsonResponse(['success'=>true,'webhook'=>['id'=>$id,'url'=>$url,'status'=>'active','secret'=>$secret]],200); }
if($action==='test') { $id=$existing?(int)$existing['id']:0; $payload=json_encode(['event'=>'webhook.test','id'=>'evt_'.bin2hex(random_bytes(8)),'created_at'=>gmdate('c')]); $ch=curl_init($url); curl_setopt_array($ch,[CURLOPT_POST=>true,CURLOPT_POSTFIELDS=>$payload,CURLOPT_RETURNTRANSFER=>true,CURLOPT_CONNECTTIMEOUT=>5,CURLOPT_TIMEOUT=>10,CURLOPT_FOLLOWLOCATION=>false,CURLOPT_PROTOCOLS=>CURLPROTO_HTTPS,CURLOPT_SSL_VERIFYPEER=>true,CURLOPT_SSL_VERIFYHOST=>2,CURLOPT_RESOLVE=>[$target['host'].':'.$target['port'].':'.(str_contains($target['ip'],':')?'['.$target['ip'].']':$target['ip'])],CURLOPT_HTTPHEADER=>['Content-Type: application/json','X-Flowwithlit-Event: webhook.test']]); curl_exec($ch); $error=curl_error($ch)!==''?'Could not connect to your webhook URL.':''; $status=(int)curl_getinfo($ch,CURLINFO_HTTP_CODE); curl_close($ch); $approved=$error===''&&$status>=200&&$status<300; if($id){$pdo->prepare('INSERT INTO webhook_deliveries(webhook_endpoint_id,event_type,payload,response_status,attempts,delivered_at) VALUES(?,?,?,?,?,?)')->execute([$id,'webhook.test',$payload,$status,1,$approved?gmdate('Y-m-d H:i:s'):null]);$pdo->prepare('UPDATE webhook_endpoints SET status=? WHERE id=?')->execute([$approved?'active':'disabled',$id]);} jsonResponse(['success'=>true,'approved'=>$approved,'status'=>$status,'message'=>$approved?'Webhook connection approved.':($error?:'Webhook returned a non-2xx response.')]); }
jsonResponse(['success'=>false,'error'=>'Unsupported webhook action.'],422);
