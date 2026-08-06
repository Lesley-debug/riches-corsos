<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/../vendor/autoload.php';

// Load .env if not already loaded elsewhere (safe to call more than once).
if (getenv('MAIL_USERNAME') === false) {
    $publicHtml = $_SERVER['DOCUMENT_ROOT'] ?? '';
    $candidates = [
        $publicHtml ? dirname($publicHtml) . '/.env' : '',
        __DIR__ . '/../.env',
    ];
    foreach ($candidates as $envPath) {
        if ($envPath && file_exists($envPath)) {
            foreach (file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
                if (str_starts_with(trim($line), '#') || !str_contains($line, '=')) continue;
                [$key, $value] = explode('=', $line, 2);
                putenv(trim($key) . '=' . trim($value));
            }
            break;
        }
    }
}

function sendEmail($to, $toName, $subject, $htmlContent, ?string $replyToEmail = null, ?string $replyToName = null) {
    $mail = new PHPMailer(true);

    try {
        $username = getenv('MAIL_USERNAME') ?: '';
        $password = getenv('MAIL_PASSWORD') ?: '';

        if ($username === '' || $password === '') {
            error_log('Email failed: MAIL_USERNAME or MAIL_PASSWORD is not configured.');
            return false;
        }

        $mail->isSMTP();
        $mail->Host = getenv('MAIL_HOST') ?: 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = $username;
        $mail->Password = $password;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = (int)(getenv('MAIL_PORT') ?: 587);

        $fromName = getenv('MAIL_FROM_NAME') ?: 'Riches Corsos';
        $fromEmail = getenv('MAIL_FROM_EMAIL') ?: $username;
        $mail->setFrom($fromEmail, $fromName);
        $mail->addAddress($to, $toName);

        if ($replyToEmail && filter_var($replyToEmail, FILTER_VALIDATE_EMAIL)) {
            $mail->addReplyTo($replyToEmail, $replyToName ?: $replyToEmail);
        } else {
            $mail->addReplyTo($fromEmail, $fromName);
        }
        
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = getEmailTemplate($htmlContent);
        $mail->AltBody = strip_tags($htmlContent);
        
        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Email failed: {$mail->ErrorInfo}");
        return false;
    }
}

function getEmailTemplate($content) {
    return '
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body { margin: 0; padding: 0; font-family: Arial, sans-serif; background-color: #f4f4f4; }
        .container { max-width: 600px; margin: 0 auto; background: #ffffff; }
        .header { background: linear-gradient(135deg, #1a1f2e 0%, #2d3748 100%); padding: 40px 20px; text-align: center; }
        .header h1 { color: #ffffff; margin: 0; font-size: 28px; }
        .content { padding: 40px 30px; color: #333333; line-height: 1.6; }
        .content h2 { color: #1a1f2e; margin-top: 0; }
        .button { display: inline-block; padding: 14px 30px; background: #c19a6b; color: #ffffff !important; text-decoration: none; border-radius: 8px; margin: 20px 0; font-weight: bold; }
        .info-box { background: #f9f9f9; padding: 20px; border-radius: 8px; margin: 20px 0; border-left: 4px solid #c19a6b; }
        .footer { background: #f9f9f9; padding: 30px; text-align: center; color: #666666; font-size: 14px; }
        ul { padding-left: 20px; }
        li { margin: 10px 0; }
        @media only screen and (max-width: 600px) {
            .content { padding: 30px 20px; }
            .header h1 { font-size: 24px; }
            .header { padding: 30px 15px; }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🐾 Riches Corsos</h1>
        </div>
        <div class="content">
            ' . $content . '
        </div>
        <div class="footer">
            <p><strong>Riches Corsos</strong></p>
            <p>Premium Cane Corso Puppies</p>
            <p>Email: barbarapettra@gmail.com</p>
            <p style="font-size: 12px; color: #999999; margin-top: 20px;">
                This email was sent because you registered or placed an order on our website.
            </p>
        </div>
    </div>
</body>
</html>
    ';
}
