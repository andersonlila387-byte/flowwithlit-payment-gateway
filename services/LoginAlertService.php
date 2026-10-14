<?php
require_once __DIR__ . '/EmailService.php';

class LoginAlertService {
    public static function send(PDO $pdo, int $userId, string $device): void {
        $time = gmdate('d M Y, H:i UTC');
        $device = mb_substr($device, 0, 500);
        try {
            $pdo->prepare('INSERT INTO notifications (user_id, type, title, body, action_url) VALUES (?, ?, ?, ?, ?)')->execute([
                $userId, 'new_login', 'New sign-in to your account',
                "A new sign-in occurred at {$time}. Device: {$device}. If this was not you, reset your password.", 'settings.php'
            ]);
        } catch (Throwable $error) {
            error_log('Login notification failed for user ' . $userId . ': ' . $error->getCode());
        }
        try {
            // Always use the account's stored email, including for federated sign-ins.
            $stmt = $pdo->prepare('SELECT email, full_name FROM users WHERE id = ?');
            $stmt->execute([$userId]);
            $user = $stmt->fetch();
            if (!$user || !EmailService::send($user['email'], 'New sign-in to your Flowwithlit account', 'new-login', ['name' => $user['full_name'], 'device' => $device, 'time' => $time])) {
                error_log('Login email could not be sent for user ' . $userId);
            }
        } catch (Throwable $error) {
            error_log('Login email failed for user ' . $userId . ': ' . $error->getCode());
        }
    }
}
