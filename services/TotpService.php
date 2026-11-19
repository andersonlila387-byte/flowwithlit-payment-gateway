<?php
/** RFC 6238 TOTP (authenticator apps such as Google Authenticator, Microsoft Authenticator, Authy), implemented with PHP's built-in HMAC functions. */
class TotpService {
    private const ALPHABET = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
    public static function secret(int $length = 32): string { $out=''; for($i=0;$i<$length;$i++) $out .= self::ALPHABET[random_int(0,31)]; return $out; }
    private static function decode(string $value): string { $bits=''; foreach(str_split(strtoupper(preg_replace('/[^A-Z2-7]/','',$value))) as $char) $bits .= str_pad(decbin(strpos(self::ALPHABET,$char)),5,'0',STR_PAD_LEFT); $out=''; for($i=0;$i+8<=strlen($bits);$i+=8) $out.=chr(bindec(substr($bits,$i,8))); return $out; }
    public static function code(string $secret, ?int $time = null): string { $counter=intdiv($time ?? time(),30); $binary=pack('N*',0,$counter); $hash=hash_hmac('sha1',$binary,self::decode($secret),true); $offset=ord($hash[19])&15; $value=((ord($hash[$offset])&127)<<24)|((ord($hash[$offset+1])&255)<<16)|((ord($hash[$offset+2])&255)<<8)|(ord($hash[$offset+3])&255); return str_pad((string)($value%1000000),6,'0',STR_PAD_LEFT); }
    public static function verify(?string $secret, $code): bool {
        $code = preg_replace('/\s/', '', (string) $code);
        if (!$secret || !preg_match('/^\d{6}$/D', $code)) return false;
        for($i=-1;$i<=1;$i++) if(hash_equals(self::code($secret,time()+($i*30)),$code)) return true;
        return false;
    }
    public static function uri(string $email, string $secret): string { return 'otpauth://totp/'.rawurlencode('Flowwithlit:'.$email).'?secret='.$secret.'&issuer=Flowwithlit&algorithm=SHA1&digits=6&period=30'; }

    /** Encrypt a secret for users.two_factor_secret (base64 text, like other encrypted columns). */
    public static function protect(string $secret): string {
        require_once __DIR__.'/PrivateStorage.php';
        return base64_encode(PrivateStorage::encrypt($secret, 'totp-secret'));
    }

    /** Read users.two_factor_secret: encrypted values are decrypted; older plain-text secrets are returned as-is. */
    public static function reveal(?string $stored): ?string {
        $stored = (string) $stored;
        if ($stored === '') return null;
        if (str_starts_with($stored, 'RldMM')) {
            require_once __DIR__.'/PrivateStorage.php';
            try { return PrivateStorage::decrypt($stored, 'totp-secret'); }
            catch (Throwable $e) { error_log('TOTP secret decrypt failed: '.$e->getMessage()); return null; }
        }
        return preg_match('/^[A-Z2-7]{16,64}$/D', $stored) ? $stored : null;
    }

    /**
     * Whether sign-in must ask for an authenticator code. Old builds could switch 2FA on with an email code
     * and no authenticator secret, which made sign-in impossible; such accounts are switched back off so
     * the user can set up an authenticator app properly from Settings.
     */
    public static function signInRequiresCode(PDO $pdo, int $userId): bool {
        $q = $pdo->prepare('SELECT two_factor_enabled, two_factor_secret FROM users WHERE id=?');
        $q->execute([$userId]);
        $row = $q->fetch();
        if (!$row || (int) $row['two_factor_enabled'] !== 1) return false;
        if (self::reveal($row['two_factor_secret']) !== null) return true;
        $pdo->prepare('UPDATE users SET two_factor_enabled=0 WHERE id=?')->execute([$userId]);
        error_log('2FA switched off for user '.$userId.': enabled without an authenticator secret.');
        return false;
    }
}
