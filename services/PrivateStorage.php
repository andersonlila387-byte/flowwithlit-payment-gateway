<?php
require_once __DIR__ . '/../config/environment.php';
/** Encrypted storage outside the document root. Provision using the CLI setup script. */
class PrivateStorage {
    public static function configuredRoot(): string {
        return getenv('FLOW_PRIVATE_STORAGE') ?: dirname(__DIR__, 3) . '/flowwithlit-private';
    }
    public static function root(): string {
        $root = realpath(self::configuredRoot());
        if ($root === false) throw new RuntimeException('Private storage is not configured.');
        foreach ([dirname(__DIR__), $_SERVER['DOCUMENT_ROOT'] ?? dirname(__DIR__, 2)] as $public) {
            if (!$public || !realpath($public)) continue;
            $parent = strtolower(str_replace('\\', '/', realpath($public)));
            $path = strtolower(str_replace('\\', '/', $root));
            if ($path === $parent || str_starts_with($path, rtrim($parent, '/') . '/')) throw new RuntimeException('Private storage must be outside the public document root.');
        }
        return $root;
    }
    private static function key(): string {
        $key = @file_get_contents(self::root() . '/encryption.key');
        if ($key === false || strlen($key) !== 32) throw new RuntimeException('Private storage key is unavailable.');
        return $key;
    }
    public static function encrypt(string $value, string $context): string {
        $iv = random_bytes(12);
        $encrypted = openssl_encrypt($value, 'aes-256-gcm', self::key(), OPENSSL_RAW_DATA, $iv, $tag, $context, 16);
        if ($encrypted === false) throw new RuntimeException('Encryption failed.');
        return 'FWL1' . $iv . $tag . $encrypted;
    }
    public static function decrypt(string $value, string $context): string {
        if (strlen($value) < 32 || substr($value, 0, 4) !== 'FWL1') throw new RuntimeException('Invalid encrypted data.');
        $plain = openssl_decrypt(substr($value, 32), 'aes-256-gcm', self::key(), OPENSSL_RAW_DATA, substr($value, 4, 12), substr($value, 16, 16), $context);
        if ($plain === false) throw new RuntimeException('Encrypted data failed authentication.');
        return $plain;
    }
    private static function path(string $key): string {
        if (!preg_match('/^[a-f0-9]{64}$/D', $key)) throw new InvalidArgumentException('Invalid storage key.');
        return self::root() . '/uploads/' . $key . '.enc';
    }
    public static function put(string $contents): string {
        $key = bin2hex(random_bytes(32));
        $bytes = self::encrypt($contents, 'file:' . $key);
        $path = self::path($key);
        $handle = @fopen($path, 'xb');
        if (!$handle) throw new RuntimeException('Unable to store upload.');
        try {
            $offset = 0;
            while ($offset < strlen($bytes)) {
                $written = fwrite($handle, substr($bytes, $offset));
                if (!$written) throw new RuntimeException('Incomplete upload write.');
                $offset += $written;
            }
        } catch (Throwable $error) {
            fclose($handle); @unlink($path); throw $error;
        }
        fclose($handle); chmod($path, 0600);
        return $key;
    }
    public static function get(string $key): string {
        $bytes = @file_get_contents(self::path($key));
        if ($bytes === false) throw new RuntimeException('Upload unavailable.');
        return self::decrypt($bytes, 'file:' . $key);
    }
    public static function remove(string $key): void {
        $path = self::path($key);
        if (is_file($path) && !unlink($path)) error_log('Private upload cleanup failed.');
    }
}
