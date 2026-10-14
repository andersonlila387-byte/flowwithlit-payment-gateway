<?php
declare(strict_types=1);
require_once __DIR__.'/ProviderConfig.php';
require_once __DIR__.'/ProviderHttp.php';

final class PaystackClient {
    public function __construct(private ProviderConfig $config,private ProviderHttp $http=new ProviderHttp()){
        if($config->provider!=='paystack')throw new LogicException('Wrong provider configuration.');
        if(!str_starts_with($config->get('secret_key'),'sk_'.$config->environment.'_'))throw new DomainException('Paystack key does not match the environment.');
    }
    private function call(string $method,string $path,?array $body=null):array {
        $r=$this->http->request($method,'https://api.paystack.co'.$path,['Authorization: Bearer '.$this->config->get('secret_key')],$body);
        if(($r['status']??false)!==true||!is_array($r['data']??null))throw new ProviderRequestException(200,false,$method!=='GET');return $r['data'];
    }
    public function initialize(string $reference,string $email,string $amount,string $callback,string $method):array {
        Money::amount($amount,2);
        if(!in_array($method,['card','bank_transfer'],true))throw new InvalidArgumentException('Unsupported collection method.');
        return $this->call('POST','/transaction/initialize',['reference'=>$reference,'email'=>$email,'amount'=>bcmul($amount,'100',0),'currency'=>'NGN','callback_url'=>$callback,'channels'=>[$method]]);
    }
    public function verify(string $reference):array{return $this->call('GET','/transaction/verify/'.rawurlencode($reference));}
    public function customer(string $email,string $first,string $last,string $phone):array{return $this->call('POST','/customer',['email'=>$email,'first_name'=>$first,'last_name'=>$last,'phone'=>$phone]);}
    public function customerByEmail(string $email):array{return $this->call('GET','/customer/'.rawurlencode($email));}
    public function receivingProviders():array{return $this->call('GET','/dedicated_account/available_providers');}
    public function banks():array{return $this->call('GET','/bank?country=nigeria&currency=NGN&perPage=100');}
    public function resolveAccount(string $number,string $code):array{
        if(!preg_match('/^[0-9]{10}$/D',$number)||!preg_match('/^[0-9]{3,6}$/D',$code))throw new InvalidArgumentException('Invalid bank destination.');
        return $this->call('GET','/bank/resolve?'.http_build_query(['account_number'=>$number,'bank_code'=>$code]));
    }
    public function receivingAccount(string $customer,string $bank):array{return $this->call('POST','/dedicated_account',['customer'=>$customer,'preferred_bank'=>$bank]);}
    public function receivingAccounts(string $customer):array{return $this->call('GET','/dedicated_account?customer='.rawurlencode($customer));}
}
