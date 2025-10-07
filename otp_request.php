<?php
require_once __DIR__ . '/config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  send_json([ 'error' => 'Method not allowed' ], 405);
}

$raw = file_get_contents('php://input');
$data = json_decode($raw, true);
$email = isset($data['email']) ? trim(strtolower($data['email'])) : '';
$purpose = isset($data['purpose']) ? trim($data['purpose']) : 'generic';
if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
  send_json([ 'error' => 'Valid email required' ], 422);
}
if (!in_array($purpose, ['signup','login','generic'], true)) {
  $purpose = 'generic';
}

try {
  $pdo = db_get_pdo();
  // generate 6-digit code
  $code = str_pad((string)random_int(0, 999999), 6, '0', STR_PAD_LEFT);
  $expiresAt = (new DateTimeImmutable('+10 minutes'))->format('Y-m-d H:i:s');

  $stmt = $pdo->prepare('INSERT INTO email_otps (email, code, purpose, expires_at) VALUES (:email, :code, :purpose, :expires_at)');
  $stmt->execute([
    ':email' => $email,
    ':code' => $code,
    ':purpose' => $purpose,
    ':expires_at' => $expiresAt,
  ]);

  $subject = 'Your verification code';
  $body = "Your OTP code is: $code (valid for 10 minutes).";
  $ok = send_mail_or_log($email, $subject, $body);

  send_json([ 'success' => true, 'delivery' => $ok ? 'sent' : 'logged' ]);
} catch (Throwable $e) {
  send_json([ 'error' => 'Server error: ' . $e->getMessage() ], 500);
}

?>


