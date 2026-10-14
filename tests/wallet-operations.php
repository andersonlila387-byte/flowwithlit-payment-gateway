<?php
declare(strict_types=1);
if(PHP_SAPI!=='cli')exit(1);
require_once __DIR__.'/../services/WalletOperations.php';

// Creates a fresh, explicitly named test schema. Copies structure, never customer data.
// Deliberately retained for inspection; no DROP DATABASE or application schema mutations.
$testDb=getenv('FLOW_TEST_DB_NAME')?:'';
if(!preg_match('/^flowwithlit_test_[a-z0-9_]+$/D',$testDb))throw new RuntimeException('Set FLOW_TEST_DB_NAME=flowwithlit_test_<unique suffix>.');
$source=getenv('FLOW_TEST_SCHEMA_SOURCE')?:'flowwithlit_db';
if(!preg_match('/^[a-zA-Z0-9_]+$/D',$source))throw new RuntimeException('Invalid schema source.');
$db=new PDO('mysql:host='.(getenv('FLOW_DB_HOST')?:'127.0.0.1').';charset=utf8mb4',getenv('FLOW_DB_USER')?:'root',getenv('FLOW_DB_PASSWORD')?:'',[PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC,PDO::ATTR_EMULATE_PREPARES=>false]);
if(($argv[1]??'')==='--race') {
    $db->exec('USE `'.$testDb.'`');
    try { echo json_encode((new WalletOperations($db))->transfer((int)$argv[2],'@bob','NGN',$argv[3],$argv[4]),JSON_THROW_ON_ERROR); }
    catch(DomainException $e) { echo json_encode(['rejected'=>true,'message'=>$e->getMessage()]); }
    exit;
}
$db->exec('CREATE DATABASE `'.$testDb.'` CHARACTER SET utf8mb4');
$tables=$db->query('SHOW TABLES FROM `'.$source.'`')->fetchAll(PDO::FETCH_COLUMN);
$db->exec('USE `'.$testDb.'`');$db->exec('SET FOREIGN_KEY_CHECKS=0');
foreach($tables as $table){$create=$db->query('SHOW CREATE TABLE `'.$source.'`.`'.$table.'`')->fetch(PDO::FETCH_NUM);$db->exec($create[1]);}
$db->exec('SET FOREIGN_KEY_CHECKS=1');
foreach(['022_payment_processing.sql','023_wallet_operations.sql','024_bank_verification.sql'] as $migration)$db->exec(file_get_contents(__DIR__.'/../database/migrations/'.$migration));
// Verify migrations can run again without changing operation data.
foreach(['022_payment_processing.sql','023_wallet_operations.sql'] as $migration)$db->exec(file_get_contents(__DIR__.'/../database/migrations/'.$migration));
$privateRoot=sys_get_temp_dir().'/'.$testDb;
if(!mkdir($privateRoot,0700))throw new RuntimeException('Cannot create test encryption root.');
// Generated runtime test material, not a source edit. No real provider credentials.
file_put_contents($privateRoot.'/encryption.key',random_bytes(32));putenv('FLOW_PRIVATE_STORAGE='.$privateRoot);
$db->exec("INSERT INTO currencies(code,name,kind,decimals,is_enabled) VALUES('NGN','Naira','fiat',2,1),('USDT','Tether','crypto',6,1)");
$users=[];$wallets=[];
foreach(['alice','bob'] as $name){
    $db->prepare("INSERT INTO users(uuid,full_name,email,flow_tag,status,email_verified,withdrawal_pin_hash) VALUES(?,?,?,?,'active',1,?)")->execute([generateUUID(),ucfirst($name),$name.'@example.test','@'.$name,password_hash('1234',PASSWORD_DEFAULT)]);
    $users[$name]=(int)$db->lastInsertId();
    $db->prepare("INSERT INTO wallets(uuid,user_id,currency_code,wallet_type,available_balance) VALUES(?,?,'NGN','personal',1000)")->execute([generateUUID(),$users[$name]]);$wallets[$name]=(int)$db->lastInsertId();
}
$db->prepare("INSERT INTO admin_users(uuid,full_name,email,password_hash,role,status) VALUES(?,?,?,?,'finance','active')")->execute([generateUUID(),'Test Finance','finance@example.test',password_hash(bin2hex(random_bytes(20)),PASSWORD_DEFAULT)]);$admin=(int)$db->lastInsertId();
$ops=new WalletOperations($db);$checks=0;
function check(bool $ok,string $label):void{global $checks;if(!$ok)throw new RuntimeException('FAILED: '.$label);$checks++;echo 'PASS '.$label.PHP_EOL;}
function rejects(callable $fn,string $label):void{try{$fn();}catch(InvalidArgumentException|DomainException $e){check(true,$label);return;}throw new RuntimeException('FAILED (accepted): '.$label);}
function balance(PDO $db,int $id,string $field='available_balance'):string{return (string)$db->query('SELECT '.$field.' FROM wallets WHERE id='.(int)$id)->fetchColumn();}
foreach(['1e5','-1','0.001',1.1,'NaN'] as $v)rejects(fn()=>Money::amount($v,2),'invalid money rejected '.json_encode($v));
check(Money::add('99999999999999999.99','0.01')==='100000000000000000.000000000000000000','large decimal addition is exact');
$ops->authorize($users['alice'],'1234');
$r=$ops->transfer($users['alice'],'@bob','NGN','100.01','test-transfer-0001');
check($r['status']==='completed','internal transfer succeeds');
check(Money::compare(balance($db,$wallets['alice']),'899.99')===0 && Money::compare(balance($db,$wallets['bob']),'1100.01')===0,'both balances exact');
$dup=$ops->transfer($users['alice'],'@bob','NGN','100.01','test-transfer-0001');check(!empty($dup['duplicate'])&&$dup['reference']===$r['reference'],'duplicate transfer returns original');
rejects(fn()=>$ops->transfer($users['alice'],'@bob','NGN','101','test-transfer-0001'),'same key changed amount rejected');
rejects(fn()=>$ops->transfer($users['alice'],'@alice','NGN','1','test-self-transfer'),'self-transfer rejected');
rejects(fn()=>$ops->transfer($users['alice'],'@bob','NGN','900','test-no-funds-0001'),'insufficient funds rejected');
$dest=['account_number'=>'0123456789','bank_code'=>'058','account_name'=>'Test Receiver'];
// Fake HTTP only inside this test process; production clients require encrypted settings.
$db->prepare("INSERT INTO admin_integration_connections(provider_key,environment,enabled,values_json,secrets_encrypted) VALUES('paystack','live',1,'{}',?)")->execute([PrivateStorage::encrypt(json_encode(['secret_key'=>'sk_live_TEST_FIXTURE_ONLY']),'integration:paystack')]);
$fakeHttp=new ProviderHttp(static fn($method,$url,$headers,$body)=>[200,'{"status":true,"data":{"account_number":"0123456789","account_name":"Test Receiver"}}']);
$verify=new BankVerificationService($db,new PaystackClient(new ProviderConfig($db,'paystack','live'),$fakeHttp));
$verification=$verify->resolve($users['alice'],$dest['account_number'],$dest['bank_code']);$dest['verification_token']=$verification['verification_token'];
rejects(fn()=>$verify->verified($users['bob'],$dest['verification_token'],$dest['account_number'],$dest['bank_code']),'bank verification is user-scoped');
rejects(fn()=>$verify->verified($users['alice'],$dest['verification_token'],'9999999999',$dest['bank_code']),'changed bank account rejected');
rejects(fn()=>$ops->requestWithdrawal($users['alice'],'NGN','250',array_diff_key($dest,['verification_token'=>true]),'no-bank-verify-001'),'bank withdrawal requires verification');
$w=$ops->requestWithdrawal($users['alice'],'NGN','250',$dest,'test-withdraw-0001');
check($w['execution_mode']==='manual'&&abs(strtotime($w['due_at'].' UTC')-time()-86400)<5,'manual request due within 24 hours');
check(Money::compare(balance($db,$wallets['alice']),'649.99')===0&&Money::compare(balance($db,$wallets['alice'],'pending_balance'),'250')===0,'withdrawal reserves funds');
$d=$ops->requestWithdrawal($users['alice'],'NGN','250',$dest,'test-withdraw-0001');check(!empty($d['duplicate']),'duplicate withdrawal reserves once');
$cipher=$db->query('SELECT destination_encrypted FROM payout_requests WHERE id='.(int)$w['payout_id'])->fetchColumn();check(!str_contains($cipher,'0123456789'),'destination encrypted at rest');
rejects(fn()=>$ops->reviewWithdrawal($w['payout_id'],$admin,'paid','Sent through the bank',''),'paid requires external reference');
$ops->reviewWithdrawal($w['payout_id'],$admin,'approved','Checked the withdrawal details');
$ops->reviewWithdrawal($w['payout_id'],$admin,'paid','Paid through manual bank transfer','sandbox-bank-001');
check(Money::compare(balance($db,$wallets['alice'],'pending_balance'),'0')===0&&Money::compare(balance($db,$wallets['alice']),'649.99')===0,'paid consumes reservation');
$d=$ops->reviewWithdrawal($w['payout_id'],$admin,'paid','Paid through manual bank transfer','sandbox-bank-001');check(!empty($d['duplicate']),'repeated admin finalization has no balance effect');
rejects(fn()=>$ops->reviewWithdrawal($w['payout_id'],$admin,'failed','Trying to reverse paid payout'),'paid cannot become failed');
$f=$ops->requestWithdrawal($users['alice'],'NGN','100',$dest,'test-withdraw-0002');$ops->reviewWithdrawal($f['payout_id'],$admin,'failed','Confirmed no money was sent');$ops->reviewWithdrawal($f['payout_id'],$admin,'failed','Confirmed no money was sent');
check(Money::compare(balance($db,$wallets['alice']),'649.99')===0,'failed withdrawal refunds exactly once');
$unbalanced=$db->query("SELECT e.id FROM ledger_entries e JOIN ledger_lines l ON l.ledger_entry_id=e.id GROUP BY e.id HAVING SUM(CASE WHEN l.direction='debit' THEN l.amount ELSE -l.amount END)<>0")->fetchAll();check(!$unbalanced,'all journal entries balance');
check((int)$db->query('SELECT COUNT(*) FROM ledger_entries')->fetchColumn()===5,'expected five immutable postings');
for($i=0;$i<5;$i++)rejects(fn()=>$ops->authorize($users['bob'],'0000'),'bad PIN attempt '.($i+1));
rejects(fn()=>$ops->authorize($users['bob'],'1234'),'PIN lockout enforced for correct PIN too');
function race(array $commands):array {
    $processes=[];
    foreach($commands as $args){$pipes=[];$p=proc_open(array_merge([PHP_BINARY,__FILE__,'--race'],$args),[0=>['pipe','r'],1=>['pipe','w'],2=>['pipe','w']],$pipes);if(!is_resource($p))throw new RuntimeException('Cannot start race test.');fclose($pipes[0]);$processes[]=[$p,$pipes];}
    $results=[];
    foreach($processes as [$p,$pipes]){$out=stream_get_contents($pipes[1]);$err=stream_get_contents($pipes[2]);fclose($pipes[1]);fclose($pipes[2]);if(proc_close($p)!==0)throw new RuntimeException('Race process failed: '.$err);$results[]=json_decode($out,true,512,JSON_THROW_ON_ERROR);}
    return $results;
}
$before=balance($db,$wallets['alice']);
$results=race([[(string)$users['alice'],'10','race-duplicate-0001'],[(string)$users['alice'],'10','race-duplicate-0001']]);
check($results[0]['reference']===$results[1]['reference']&&Money::compare(balance($db,$wallets['alice']),Money::sub($before,'10'))===0,'concurrent duplicate debits only once');
$before=balance($db,$wallets['alice']);
$results=race([[(string)$users['alice'],'400','race-spend-000001'],[(string)$users['alice'],'400','race-spend-000002']]);
check(count(array_filter($results,fn($r)=>!empty($r['success'])))===1&&Money::compare(balance($db,$wallets['alice']),Money::sub($before,'400'))===0,'concurrent spending cannot overdraw');
$unbalanced=$db->query("SELECT e.id FROM ledger_entries e JOIN ledger_lines l ON l.ledger_entry_id=e.id GROUP BY e.id HAVING SUM(CASE WHEN l.direction='debit' THEN l.amount ELSE -l.amount END)<>0")->fetchAll();check(!$unbalanced,'concurrent journals balance');
echo "PASS {$checks} assertions; retained isolated schema {$testDb}; temporary key {$privateRoot}".PHP_EOL;
