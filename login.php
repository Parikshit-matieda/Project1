<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/session.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  send_json([ 'error' => 'Method not allowed' ], 405);
}

$raw = file_get_contents('php://input');
$data = json_decode($raw, true);
$email = isset($data['email']) ? trim(strtolower($data['email'])) : '';
$password = isset($data['password']) ? (string)$data['password'] : '';

if ($email === '' || $password === '') {
  send_json([ 'error' => 'Email and password are required' ], 422);
}

try {
  $pdo = db_get_pdo();
  $stmt = $pdo->prepare('SELECT id, email, password_hash FROM users WHERE email = :email');
  $stmt->execute([':email' => $email]);
  $user = $stmt->fetch();
  if (!$user || !password_verify($password, $user['password_hash'])) {
    send_json([ 'error' => 'Invalid credentials' ], 401);
  }
  $_SESSION['user'] = [ 'id' => $user['id'], 'email' => $user['email'] ];
  // Send login notification (logged or mailed depending on MAIL_MODE)
  send_mail_or_log($email, 'New login to LearnLite', 'A new login to your account was detected.');
  send_json([ 'success' => true ]);
} catch (Throwable $e) {
  send_json([ 'error' => 'Database error: ' . $e->getMessage() ], 500);
}

?>


