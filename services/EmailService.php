<?php
require_once __DIR__ . '/../config/environment.php';
class EmailService {
    public static function config(): array {
        $localConfig = __DIR__ . '/../config/smtp.local.php';
        $local = is_file($localConfig) ? require $localConfig : [];
        return [
            'host' => $local['host'] ?? getenv('FLOW_MAIL_HOST'), 'port' => $local['port'] ?? (int)(getenv('FLOW_MAIL_PORT') ?: 587),
            'username' => $local['username'] ?? getenv('FLOW_MAIL_USERNAME'), 'password' => $local['password'] ?? getenv('FLOW_MAIL_PASSWORD'),
            'encryption' => $local['encryption'] ?? (getenv('FLOW_MAIL_ENCRYPTION') ?: 'tls'), 'from_address' => $local['from_address'] ?? (getenv('FLOW_MAIL_FROM_ADDRESS') ?: 'no-reply@flowwithlit.local'),
            'from_name' => $local['from_name'] ?? (getenv('FLOW_MAIL_FROM_NAME') ?: 'Flowwithlit')
        ];
    }

    public static function sendTest(string $to, array $smtp): bool {
        $subject = 'Flowwithlit SMTP test';
        $html = '<!doctype html><html><body style="margin:0;background:#f4f7fb;font-family:Arial,sans-serif;color:#102a43">'
            . '<div style="max-width:560px;margin:32px auto;background:#fff;border-radius:14px;padding:32px">'
            . '<h1 style="margin:0 0 12px;font-size:24px">SMTP connection confirmed</h1>'
            . '<p style="margin:0;line-height:1.6">This message was sent from your Flowwithlit administrator settings. Your SMTP configuration can deliver mail.</p>'
            . '</div></body></html>';
        return self::sendHtml($to, $subject, $html, $smtp);
    }

    public static function send(string $to, string $subject, string $template, array $data = []): bool {
        $path = __DIR__ . '/../emails/templates/' . $template . '.php';
        if (!is_file($path)) throw new RuntimeException('Unknown email template.');
        extract($data, EXTR_SKIP); ob_start(); require $path; $html = ob_get_clean();
        return self::sendHtml($to, $subject, $html, self::config());
    }

    private static function sendHtml(string $to, string $subject, string $html, array $config): bool {
        if (!filter_var($to, FILTER_VALIDATE_EMAIL)) throw new InvalidArgumentException('A valid recipient email is required.');
        if (empty($config['host']) || empty($config['username']) || empty($config['password']) || !filter_var((string)($config['from_address'] ?? ''), FILTER_VALIDATE_EMAIL)) {
            throw new RuntimeException('SMTP settings are incomplete.');
        }
        $autoload = __DIR__ . '/../vendor/autoload.php';
        if (is_file($autoload)) require_once $autoload;
        if (!class_exists('PHPMailer\\PHPMailer\\PHPMailer')) {
            throw new RuntimeException('PHPMailer is not installed. Run Composer install before sending email.');
        }
        $mail = new PHPMailer\PHPMailer\PHPMailer(true);
        $mail->isSMTP(); $mail->Host = (string)$config['host']; $mail->Port = (int)$config['port']; $mail->Timeout = 20;
        $mail->SMTPAuth = true; $mail->Username = (string)$config['username']; $mail->Password = (string)$config['password'];
        $mail->SMTPSecure = (string)$config['encryption']; $mail->setFrom((string)$config['from_address'], (string)$config['from_name']);
        $mail->addAddress($to); $mail->isHTML(true); $mail->Subject = $subject; $mail->Body = $html; $mail->AltBody = strip_tags($html);
        return $mail->send();
    }
}
