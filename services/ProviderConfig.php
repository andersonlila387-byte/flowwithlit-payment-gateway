<?php
declare(strict_types=1);
require_once __DIR__.'/PrivateStorage.php';

/** Server-only credential resolver. Never serialize this object into an API response. */
final class ProviderConfig {
    private array $values;
    public function __construct(PDO $db,public readonly string $provider,public readonly string $environment) {
        if(!in_array($environment,['test','live'],true))throw new InvalidArgumentException('Invalid provider environment.');
        $q=$db->prepare('SELECT environment,enabled,values_json,secrets_encrypted FROM admin_integration_connections WHERE provider_key=?');$q->execute([$provider]);$row=$q->fetch();
        if(!$row||!$row['enabled']||$row['environment']!==$environment)throw new DomainException('Provider is not enabled for this environment.');
        $public=json_decode((string)$row['values_json'],true,512,JSON_THROW_ON_ERROR)??[];
        $secret=json_decode(PrivateStorage::decrypt((string)$row['secrets_encrypted'],'integration:'.$provider),true,512,JSON_THROW_ON_ERROR);
        $this->values=array_merge($public,$secret);
    }
    public function get(string $key,bool $required=true):string {
        $v=$this->values[$key]??'';
        if(!is_scalar($v))throw new DomainException('Invalid provider configuration.');$v=(string)$v;
        if(($required&&$v==='')||str_contains($v,"\r")||str_contains($v,"\n"))throw new DomainException('Provider credentials are incomplete.');
        return $v;
    }
    /** The environment ('test' or 'live') an enabled provider is configured for in Admin → API Connect, or null. */
    public static function activeEnvironment(PDO $db,string $provider):?string {
        $q=$db->prepare('SELECT environment FROM admin_integration_connections WHERE provider_key=? AND enabled=1 LIMIT 1');$q->execute([$provider]);
        $env=$q->fetchColumn();
        return in_array($env,['test','live'],true)?$env:null;
    }
    public function __debugInfo():array{return ['provider'=>$this->provider,'environment'=>$this->environment,'credentials'=>'[redacted]'];}
}
