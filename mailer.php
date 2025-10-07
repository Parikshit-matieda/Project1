<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/config.php';

function smtp_send_mail(string $to, string $subject, string $body): bool {
  $host = getenv('SMTP_HOST') ?: 'smtp.example.com';
  $port = (int)(getenv('SMTP_PORT') ?: 587);
  $user = getenv('SMTP_USER') ?: '';
  $pass = getenv('SMTP_PASS') ?: '';
  $from = getenv('SMTP_FROM') ?: 'no-reply@localhost';
  $fromName = getenv('SMTP_FROM_NAME') ?: 'LearnLite';
  $secure = getenv('SMTP_SECURE') ?: 'tls'; // tls or ssl

  $mail = new PHPMailer(true);
  try {
    $mail->isSMTP();
    $mail->Host = $host;
    $mail->Port = $port;
    $mail->SMTPAuth = !empty($user);
    if (!empty($user)) {
      $mail->Username = $user;
      $mail->Password = $pass;
    }
    if ($secure === 'ssl') {
      $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
    } else if ($secure === 'tls') {
      $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    }

    $mail->setFrom($from, $fromName);
    $mail->addAddress($to);
    $mail->Subject = $subject;
    $mail->Body = $body;
    $mail->AltBody = $body;
    return $mail->send();
  } catch (Exception $e) {
    return false;
  }
}

?>


