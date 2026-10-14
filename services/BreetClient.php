<?php
declare(strict_types=1);
require_once __DIR__.'/ProviderConfig.php';
require_once __DIR__.'/ProviderHttp.php';

final class BreetClient {
    public function __construct(private ProviderConfig $config,private ProviderHttp $http=new ProviderHttp()) {
        if($config->provider!=='breet')throw new LogicException('Wrong provider configuration.');
    }
    private function call(string $method,string $path,?array $body=null):array {
        $r=$this->http->request($method,'https://api.breet.io/v1'.$path,['x-app-id: '.$this->config->get('app_id'),'x-app-secret: '.$this->config->get('app_secret'),'X-Breet-Env: '.($this->config->environment==='test'?'sandbox':'production')],$body);
        if(($r['success']??false)!==true||!is_array($r['data']??null))throw new ProviderRequestException(200,false,$method!=='GET');
        return $r['data'];
    }
    public function assets():array{return $this->call('GET','/trades/assets');}
    public function generateAddress(string $assetId,string $label):array {
        if($assetId===''||$label===''||strlen($label)>100)throw new InvalidArgumentException('Asset and stable owner label are required.');
        return $this->call('POST','/trades/sell/assets/'.rawurlencode($assetId).'/generate-address',['label'=>$label,'autoSettlement'=>false]);
    }
    public function trade(string $id):array{return $this->call('GET','/trades/transactions/'.rawurlencode($id));}
    public function trades(int $page=1):array{return $this->call('GET','/trades/transactions?page='.max(1,$page));}
    public function wallets(int $page=1):array{return $this->call('GET','/trades/wallets?page='.max(1,$page));}
    public function rate(string $assetId,string $usd,string $currency='NGN'):array {
        if(!in_array($currency,['NGN','GHS'],true))throw new InvalidArgumentException('Unsupported settlement currency.');
        return $this->call('POST','/trades/pbc/sell/rate-calculator/'.rawurlencode($assetId),['amountInUSD'=>new JsonDecimal($usd),'currency'=>strtolower($currency)]);
    }
    public function mockTrade(string $address,string $asset,string $usd,string $crypto,string $reference):array {
        if($this->config->environment!=='test')throw new DomainException('Mock trades are sandbox-only.');
        return $this->call('POST','/trades/sell/mock-trade',['walletAddress'=>$address,'asset'=>$asset,'amountInUSD'=>new JsonDecimal($usd),'cryptoReceived'=>new JsonDecimal($crypto),'reference'=>$reference,'txHash'=>'mock-'.$reference]);
    }
}
