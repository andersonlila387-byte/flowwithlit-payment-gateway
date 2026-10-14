<?php
/** RFC 6238 TOTP, implemented with PHP's built-in HMAC functions. */
class TotpService {
    private const ALPHABET = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
    public static function secret(int $length = 32): string { $out=''; for($i=0;$i<$length;$i++) $out .= self::ALPHABET[random_int(0,31)]; return $out; }
    private static function decode(string $value): string { $bits=''; foreach(str_split(strtoupper(preg_replace('/[^A-Z2-7]/','',$value))) as $char) $bits .= str_pad(decbin(strpos(self::ALPHABET,$char)),5,'0',STR_PAD_LEFT); $out=''; for($i=0;$i+8<=strlen($bits);$i+=8) $out.=chr(bindec(substr($bits,$i,8))); return $out; }
    public static function code(string $secret, ?int $time = null): string { $counter=intdiv($time ?? time(),30); $binary=pack('N*',0,$counter); $hash=hash_hmac('sha1',$binary,self::decode($secret),true); $offset=ord($hash[19])&15; $value=((ord($hash[$offset])&127)<<24)|((ord($hash[$offset+1])&255)<<16)|((ord($hash[$offset+2])&255)<<8)|(ord($hash[$offset+3])&255); return str_pad((string)($value%1000000),6,'0',STR_PAD_LEFT); }
    public static function verify(string $secret, string $code): bool { for($i=-1;$i<=1;$i++) if(hash_equals(self::code($secret,time()+($i*30)),preg_replace('/\s/','',$code))) return true; return false; }
    public static function uri(string $email, string $secret): string { return 'otpauth://totp/'.rawurlencode('Flowwithlit:'.$email).'?secret='.$secret.'&issuer=Flowwithlit&algorithm=SHA1&digits=6&period=30'; }
}
