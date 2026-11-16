<?php
declare(strict_types=1);

/**
 * Countries approved for signup, managed by admins in Admin → Countries.
 * Falls back to Nigeria alone if the `countries` table is missing (migration
 * 029 not yet applied), so signup keeps working during a deploy.
 */

const FWL_FALLBACK_COUNTRIES = [['code' => 'NG', 'name' => 'Nigeria', 'dial_code' => '+234']];

/** Approved countries, in display order. */
function approvedCountries(PDO $pdo): array {
    try {
        $rows = $pdo->query('SELECT code, name, dial_code FROM countries WHERE is_enabled = 1 ORDER BY sort_order, name')->fetchAll(PDO::FETCH_ASSOC);
        return $rows ?: FWL_FALLBACK_COUNTRIES;
    } catch (PDOException $e) {
        return FWL_FALLBACK_COUNTRIES;
    }
}

/**
 * Dialling codes for the phone picker. Approved countries come first; the rest of
 * the catalogue follows so "Other countries" signups can still give a reachable number.
 */
function phoneDialCodes(PDO $pdo): array {
    try {
        $rows = $pdo->query('SELECT dial_code, MAX(is_enabled) enabled, MIN(sort_order) sort_order FROM countries GROUP BY dial_code ORDER BY enabled DESC, sort_order, CAST(SUBSTRING(dial_code, 2) AS UNSIGNED)')->fetchAll(PDO::FETCH_COLUMN);
        return $rows ?: ['+234'];
    } catch (PDOException $e) {
        return ['+234'];
    }
}
