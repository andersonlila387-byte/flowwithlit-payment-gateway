<?php
require_once __DIR__ . '/../config/environment.php';
/** Server-side OnePipe REST client. Never expose these credentials to the browser. */
require_once __DIR__.'/ProviderConfig.php';
final class OnePipeClient {
    private string $baseUrl; private string $apiKey; private string $secret; private string $authProvider;
    public function __construct(?PDO $db=null,string $environment='live') {
        if($db){$config=new ProviderConfig($db,'onepipe',$environment);$this->baseUrl=rtrim($config->get('base_url',false)?:'https://api.onepipe.io','/');$this->apiKey=$config->get('api_key');$this->secret=$config->get('api_secret');$this->authProvider=$config->get('auth_provider');return;}
        $this->baseUrl = rtrim(getenv('ONEPIPE_API_URL') ?: 'https://api.onepipe.io', '/');
        $this->apiKey = trim((string)(getenv('ONEPIPE_API_KEY') ?: '')); $this->secret = trim((string)(getenv('ONEPIPE_API_SECRET') ?: '')); $this->authProvider = trim((string)(getenv('ONEPIPE_AUTH_PROVIDER') ?: ''));
    }
    public function configured(): bool { return $this->apiKey !== '' && $this->secret !== '' && $this->authProvider !== ''; }
    private function request(string $path, array $payload, string $requestRef): array {
        if (!$this->configured()) throw new RuntimeException('OnePipe is not configured.');
        $body=json_encode($payload, JSON_UNESCAPED_SLASHES);
        if ($body===false) throw new RuntimeException('Unable to encode OnePipe request.');
        $signature=md5($requestRef.';'.$this->secret);
        $ch=curl_init($this->baseUrl.'/v2/transact'.($path!==''?'/'.$path:''));
        curl_setopt_array($ch,[CURLOPT_POST=>true,CURLOPT_RETURNTRANSFER=>true,CURLOPT_CONNECTTIMEOUT=>8,CURLOPT_TIMEOUT=>25,CURLOPT_HTTPHEADER=>['Content-Type: application/json','Accept: application/json','Authorization: Bearer '.$this->apiKey,'request-ref: '.$requestRef,'Signature: '.$signature],CURLOPT_POSTFIELDS=>$body]);
        $raw=curl_exec($ch); $error=curl_error($ch); $code=(int)curl_getinfo($ch,CURLINFO_HTTP_CODE); curl_close($ch);
        if ($raw===false || $error) throw new RuntimeException('OnePipe request failed.');
        $decoded=json_decode((string)$raw,true);
        if (!is_array($decoded)) throw new RuntimeException('OnePipe returned an invalid response.');
        if ($code<200 || $code>=300) throw new RuntimeException((string)($decoded['message']??'OnePipe request failed.'));
        return $decoded;
    }
    private function secureValue(string $value): string {
        // OnePipe providers may require their configured encrypted secure value.
        // The key and mode remain server-side; never accept either from a request.
        $mode=strtolower(trim((string)(getenv('ONEPIPE_SECURE_MODE')?:'plain')));
        $key=(string)(getenv('ONEPIPE_SECURE_KEY')?:'');
        if ($mode==='3des' && $key!=='') {
            $key=substr(hash('sha256',$key,true),0,24); $iv=str_repeat("\0",8);
            $encrypted=openssl_encrypt($value,'des-ede3-cbc',$key,OPENSSL_RAW_DATA,$iv);
            if ($encrypted===false) throw new RuntimeException('Unable to encrypt OnePipe account details.');
            return base64_encode($encrypted);
        }
        return $value;
    }
    private function transactionPayload(string $requestType,string $requestRef,string $transactionRef,array $customer,array $details, float $amount=0): array {
        $parts=preg_split('/\s+/',trim((string)($customer['name']??'')),2);
        return ['request_ref'=>$requestRef,'request_type'=>$requestType,'request_mode'=>'transact','auth'=>['type'=>'bank.account','secure'=>$this->secureValue((string)($details['account_number']??'')),'auth_provider'=>$this->authProvider,'route_mode'=>null],'transaction'=>[
            'mock_mode'=>getenv('ONEPIPE_MOCK_MODE')?:'Live','transaction_ref'=>$transactionRef,'transaction_desc'=>'Flowwithlit Nigerian bank transfer','transaction_ref_parent'=>null,'amount'=>$amount,
            'customer'=>['customer_ref'=>(string)($customer['phone']??$customer['email']??$transactionRef),'firstname'=>$parts[0]??'Flowwithlit','surname'=>$parts[1]??'Customer','email'=>(string)($customer['email']??''),'mobile_no'=>(string)($customer['phone']??'')],
            'meta'=>['country'=>'NG'],'details'=>$details
        ]];
    }
    public function lookupAccount(string $accountNumber,string $bankCode,array $customer): array {
        $requestRef='FWL-LKP-'.strtoupper(bin2hex(random_bytes(12))); $transactionRef='FWL-LKP-TX-'.strtoupper(bin2hex(random_bytes(8)));
        $response=$this->request('', $this->transactionPayload('lookup_account_min',$requestRef,$transactionRef,$customer,['account_number'=>$accountNumber,'bank_code'=>$bankCode],0), $requestRef);
        return $response;
    }
    public function transferFunds(string $requestRef,string $transactionRef,array $customer,string $accountNumber,string $bankCode,float $amount,string $narration=''): array {
        $details=['destination_account'=>$accountNumber,'destination_bank_code'=>$bankCode,'account_number'=>$accountNumber,'bank_code'=>$bankCode,'narration'=>substr($narration,0,140)];
        return $this->request('', $this->transactionPayload('transfer_funds',$requestRef,$transactionRef,$customer,$details,$amount), $requestRef);
    }
    public function openAccount(array $customer, string $requestRef): array {
        if (!$this->configured()) throw new RuntimeException('OnePipe is not configured.');
        $transactionRef = 'FWL-' . strtoupper(bin2hex(random_bytes(10)));
        $payload = ['request_ref'=>$requestRef,'request_type'=>'account opening','auth'=>['auth_provider'=>$this->authProvider], 'transaction'=>[
            'mock_mode'=>getenv('ONEPIPE_MOCK_MODE') ?: 'Live','transaction_ref'=>$transactionRef,'transaction_desc'=>'Flowwithlit wallet account opening','transaction_ref_parent'=>'',
            'amount'=>0,'customer'=>['customer_ref'=>$customer['phone'] ?: $customer['email'],'firstname'=>$customer['first_name'],'surname'=>$customer['last_name'],'email'=>$customer['email'],'mobile_no'=>$customer['phone']],
            'meta'=>['expires_on'=>'2090-12-31'],'details'=>['name_on_account'=>$customer['name']]
        ]];
        $decoded=$this->request('', $payload, $requestRef);
        if (strtolower((string)($decoded['status']??''))!=='successful') throw new RuntimeException((string)($decoded['message']??'OnePipe could not open the account.'));
        return $decoded;
    }
    public static function extractBeneficiary(array $response): array {
        $data=$response['data']['provider_response']??$response['data']??[];
        return ['account_name'=>(string)($data['account_name']??$data['accountName']??$data['beneficiary_account_name']??''),'account_number'=>(string)($data['account_number']??$data['accountNumber']??$data['beneficiary_account_number']??''),'bank_name'=>(string)($data['bank_name']??$data['bankName']??''),'bank_code'=>(string)($data['bank_code']??$data['bankCode']??$data['destination_institution_code']??'')];
    }
    public static function extractAccount(array $response): array {
        $data=$response['data']['provider_response']??$response['data']??[]; return ['account_ref'=>(string)($data['accountReference']??$data['account_reference']??$data['accountNumber']??''),'account_number'=>(string)($data['accountNumber']??$data['account_number']??''),'account_name'=>(string)($data['accountName']??$data['account_name']??''),'bank_name'=>(string)($data['bankName']??$data['bank_name']??''),'bank_code'=>(string)($data['bankCode']??$data['bank_code']??'')];
    }
}
