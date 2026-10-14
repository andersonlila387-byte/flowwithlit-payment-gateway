<?php
require_once __DIR__ . '/../config/environment.php';
class EmailService {
    private static function config(): array {
        $localConfig = __DIR__ . '/../config/smtp.local.php';
        $local = is_file($localConfig) ? require $localConfig : [];
        return [
            'host' => $local['host'] ?? getenv('FLOW_MAIL_HOST'), 'port' => $local['port'] ?? (int)(getenv('FLOW_MAIL_PORT') ?: 587),
            'username' => $local['username'] ?? getenv('FLOW_MAIL_USERNAME'), 'password' => $local['password'] ?? getenv('FLOW_MAIL_PASSWORD'),
            'encryption' => $local['encryption'] ?? (getenv('FLOW_MAIL_ENCRYPTION') ?: 'tls'), 'from_address' => $local['from_address'] ?? (getenv('FLOW_MAIL_FROM_ADDRESS') ?: 'no-reply@flowwithlit.local'),
            'from_name' => $local['from_name'] ?? (getenv('FLOW_MAIL_FROM_NAME') ?: 'Flowwithlit')
        ];
    }
    public static function send(string $to, string $subject, string $template, array $data = []): bool {
        $path = __DIR__ . '/../emails/templates/' . $template . '.php';
        if (!is_file($path)) throw new RuntimeException('Unknown email template.');
        extract($data, EXTR_SKIP); ob_start(); require $path; $html = ob_get_clean();
        $autoload = __DIR__ . '/../vendor/autoload.php';
        if (is_file($autoload)) require_once $autoload;
        $config = self::config();
        if (class_exists('PHPMailer\\PHPMailer\\PHPMailer') && !empty($config['host'])) {
            $mail = new PHPMailer\PHPMailer\PHPMailer(true);
            $mail->isSMTP(); $mail->Host = $config['host']; $mail->Port = (int)$config['port'];
            $mail->SMTPAuth = true; $mail->Username = $config['username']; $mail->Password = $config['password'];
            $mail->SMTPSecure = $config['encryption']; $mail->setFrom($config['from_address'], $config['from_name']);
            $mail->addAddress($to); $mail->isHTML(true); $mail->Subject = $subject; $mail->Body = $html; $mail->AltBody = strip_tags($html); return $mail->send();
        }
        $directory = __DIR__ . '/../storage/mail'; if (!is_dir($directory)) mkdir($directory, 0775, true);
        file_put_contents($directory . '/' . date('Ymd_His') . '_' . bin2hex(random_bytes(3)) . '.html', "<!-- To: {$to}; Subject: {$subject} -->\n" . $html);
        return true;
    }
}
