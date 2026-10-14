<?php
declare(strict_types=1);

/** Decimal strings throughout; no binary floating point in money decisions. */
final class Money {
    public const SCALE = 18;

    public static function amount(mixed $value, int $decimals = 18, bool $allowZero = false): string {
        if (!is_string($value) && !is_int($value)) throw new InvalidArgumentException('Amounts must be decimal strings.');
        $value = (string)$value;
        if ($decimals < 0 || $decimals > self::SCALE || !preg_match('/^(0|[1-9][0-9]{0,17})(?:\.([0-9]{1,18}))?$/D', $value, $m)) {
            throw new InvalidArgumentException('Enter a valid decimal amount.');
        }
        if (strlen(rtrim($m[2] ?? '', '0')) > $decimals) throw new InvalidArgumentException('Amount has too many decimal places for this currency.');
        if (!$allowZero && bccomp($value, '0', self::SCALE) <= 0) throw new InvalidArgumentException('Amount must be greater than zero.');
        return bcadd($value, '0', self::SCALE);
    }

    public static function add(string $a, string $b): string { return bcadd($a, $b, self::SCALE); }
    public static function sub(string $a, string $b): string { return bcsub($a, $b, self::SCALE); }
    public static function compare(string $a, string $b): int { return bccomp($a, $b, self::SCALE); }
}
