<?php
/**
 * Loads local deployment variables from the project .env file.
 * Server-provided environment variables always take precedence.
 */
if (!function_exists('flowwithlitLoadEnvironment')) {
    function flowwithlitLoadEnvironment(): void {
        static $loaded = false;
        if ($loaded) return;
        $loaded = true;

        $file = dirname(__DIR__) . '/.env';
        if (!is_readable($file)) return;
        $lines = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        if ($lines === false) return;
        foreach ($lines as $line) {
            $line = trim($line);
            if ($line === '' || $line[0] === '#' || !str_contains($line, '=')) continue;
            [$name, $value] = explode('=', $line, 2);
            $name = trim($name);
            $value = trim($value);
            if (!preg_match('/^[A-Z][A-Z0-9_]*$/', $name)) continue;
            if (strlen($value) >= 2 && (($value[0] === '"' && substr($value, -1) === '"') || ($value[0] === "'" && substr($value, -1) === "'"))) {
                $value = substr($value, 1, -1);
            }
            if (getenv($name) !== false) continue;
            putenv($name . '=' . $value);
            $_ENV[$name] = $value;
        }
    }
    flowwithlitLoadEnvironment();
}
