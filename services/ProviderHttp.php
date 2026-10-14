<?php
declare(strict_types=1);
require_once __DIR__.'/Money.php';

final class ProviderRequestException extends RuntimeException {
    public function __construct(public readonly int $httpStatus,public readonly bool $retryable,public readonly bool $ambiguous) {
        parent::__construct('Provider request did not complete successfully.');
    }
}

final class JsonDecimal {
    public readonly string $value;
    public function __construct(string $value){$this->value=Money::amount($value);}
}

final class ProviderJson {
    /** Preserve every JSON number as a string before PHP can round it to float. */
    public static function decode(string $body):array {
        json_decode($body,true,512,JSON_THROW_ON_ERROR); // Validate the original grammar first.
        $safe=preg_replace_callback('/"(?:\\\\.|[^"\\\\])*"(*SKIP)(*F)|-?\d+(?:\.\d+)?(?:[eE][+-]?\d+)?/',static fn($m)=>'"'.$m[0].'"',$body);
        $data=json_decode($safe,true,512,JSON_THROW_ON_ERROR);
        if(!is_array($data))throw new UnexpectedValueException('Invalid provider response.');return $data;
    }
    public static function encode(mixed $value):string {
        if($value instanceof JsonDecimal)return $value->value;
        if(is_array($value)){
            $list=array_is_list($value);$parts=[];
            foreach($value as $k=>$v)$parts[]=($list?'':json_encode((string)$k,JSON_THROW_ON_ERROR).':').self::encode($v);
            return ($list?'[':'{').implode(',',$parts).($list?']':'}');
        }
        if(is_float($value))throw new InvalidArgumentException('Use JsonDecimal for provider monetary numbers.');
        return json_encode($value,JSON_THROW_ON_ERROR|JSON_UNESCAPED_SLASHES);
    }
}

final class ProviderHttp {
    /** Test injection is code-only; no request parameter or admin URL can change the host. */
    public function __construct(private ?Closure $transport=null){}
    public function request(string $method,string $url,array $headers,?array $payload=null):array {
        $body=$payload===null?null:ProviderJson::encode($payload);
        if($this->transport){[$status,$raw]=($this->transport)($method,$url,$headers,$body);}
        else {
            $host=parse_url($url,PHP_URL_HOST);
            if(parse_url($url,PHP_URL_SCHEME)!=='https'||!in_array($host,['api.breet.io','api.paystack.co','api.onepipe.io'],true))throw new LogicException('Unexpected provider host.');
            $raw='';$curl=curl_init($url);
            curl_setopt_array($curl,[CURLOPT_CUSTOMREQUEST=>$method,CURLOPT_HTTPHEADER=>array_merge(['Accept: application/json','Content-Type: application/json'],$headers),CURLOPT_CONNECTTIMEOUT=>5,CURLOPT_TIMEOUT=>20,CURLOPT_FOLLOWLOCATION=>false,CURLOPT_PROTOCOLS=>CURLPROTO_HTTPS,CURLOPT_SSL_VERIFYPEER=>true,CURLOPT_SSL_VERIFYHOST=>2,
                CURLOPT_WRITEFUNCTION=>static function($handle,string $chunk)use(&$raw):int{if(strlen($raw)+strlen($chunk)>2097152)return 0;$raw.=$chunk;return strlen($chunk);}]);
            if($body!==null)curl_setopt($curl,CURLOPT_POSTFIELDS,$body);
            $ok=curl_exec($curl);$status=(int)curl_getinfo($curl,CURLINFO_HTTP_CODE);curl_close($curl);
            if($ok===false)throw new ProviderRequestException($status,true,$method!=='GET');
        }
        if($status<200||$status>=300)throw new ProviderRequestException($status,$status===429||$status>=500,$method!=='GET'&&($status===408||$status>=500));
        try{return ProviderJson::decode($raw);}catch(Throwable $e){throw new ProviderRequestException($status,true,$method!=='GET');}
    }
}
