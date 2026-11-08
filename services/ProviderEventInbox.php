<?php
declare(strict_types=1);

/**
 * Durable inbox for provider webhooks (OnePipe, Breet).
 *
 * The webhook endpoint only records an event; it never moves money. The CLI
 * payment worker claims recorded events later and settles them through
 * DepositService, which is the single path that credits a wallet.
 */
final class ProviderEventInbox {
    public const MAX_ATTEMPTS = 10;

    /** Stores an authenticated event once. Returns false when it was already recorded. */
    public static function record(PDO $db, string $provider, string $rawBody, array $body): bool {
        $eventId = self::first($body, ['eventId', 'event_id', 'id']) ?: 'sha256-' . hash('sha256', $rawBody);
        $type = self::first($body, ['event', 'type']);
        $subject = self::first($body, ['reference', 'id']);
        $time = self::eventTime(self::first($body, ['eventTime', 'event_time']));

        $statement = $db->prepare(
            'INSERT IGNORE INTO payment_provider_events
                (provider, event_id, event_type, subject_reference, event_time, payload)
             VALUES (?, ?, ?, ?, ?, ?)'
        );
        $statement->execute([
            $provider,
            mb_substr($eventId, 0, 191),
            $type === '' ? null : mb_substr($type, 0, 100),
            $subject === '' ? null : mb_substr($subject, 0, 191),
            $time,
            $rawBody,
        ]);
        return $statement->rowCount() === 1;
    }

    /**
     * Locks the oldest unprocessed event that is due for an attempt.
     * The caller must already be inside a database transaction.
     * No SKIP LOCKED: the worker's GET_LOCK already guarantees a single consumer,
     * and SKIP LOCKED is unavailable before MySQL 8.0 / MariaDB 10.6.
     */
    public static function claimNext(PDO $db, string $provider): ?array {
        $statement = $db->prepare(
            "SELECT e.id, e.provider, e.event_id, e.payload, COALESCE(a.attempts, 0) AS attempts
             FROM payment_provider_events e
             LEFT JOIN payment_provider_event_attempts a ON a.event_row_id = e.id
             WHERE e.provider = ?
               AND e.processed_at IS NULL
               AND COALESCE(a.attempts, 0) < ?
               AND (a.next_attempt_at IS NULL OR a.next_attempt_at <= UTC_TIMESTAMP(6))
             ORDER BY e.id
             LIMIT 1
             FOR UPDATE"
        );
        $statement->execute([$provider, self::MAX_ATTEMPTS]);
        $event = $statement->fetch();
        return $event ?: null;
    }

    public static function markProcessed(PDO $db, int $eventRowId): void {
        $db->prepare('UPDATE payment_provider_events SET processed_at = UTC_TIMESTAMP(6) WHERE id = ?')
            ->execute([$eventRowId]);
    }

    /** Records a failed attempt and schedules a retry with exponential backoff (max 1 hour). */
    public static function markFailed(PDO $db, int $eventRowId, int $previousAttempts, string $reason): void {
        $attempts = $previousAttempts + 1;
        $delaySeconds = min(3600, 2 ** min($attempts + 4, 12));
        $db->prepare(
            'INSERT INTO payment_provider_event_attempts (event_row_id, attempts, last_error, next_attempt_at)
             VALUES (?, ?, ?, DATE_ADD(UTC_TIMESTAMP(6), INTERVAL ? SECOND))
             ON DUPLICATE KEY UPDATE attempts = VALUES(attempts), last_error = VALUES(last_error),
                                     next_attempt_at = VALUES(next_attempt_at)'
        )->execute([$eventRowId, $attempts, mb_substr($reason, 0, 255), $delaySeconds]);
    }

    private static function first(array $body, array $keys): string {
        foreach ($keys as $key) {
            if (isset($body[$key]) && is_scalar($body[$key]) && (string) $body[$key] !== '') {
                return (string) $body[$key];
            }
        }
        return '';
    }

    private static function eventTime(string $value): string {
        $timestamp = $value === '' ? false : strtotime($value);
        return gmdate('Y-m-d H:i:s', $timestamp === false ? time() : $timestamp);
    }
}
