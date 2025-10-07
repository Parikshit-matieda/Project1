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
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
  send_json([ 'error' => 'Invalid email' ], 422);
}
if (strlen($password) < 4) {
  send_json([ 'error' => 'Password must be at least 4 characters' ], 422);
}

try {
  $pdo = db_get_pdo();
  $hash = password_hash($password, PASSWORD_DEFAULT);
  $stmt = $pdo->prepare('INSERT INTO users (email, password_hash) VALUES (:email, :hash)');
  $stmt->execute([':email' => $email, ':hash' => $hash]);
  $_SESSION['user'] = [ 'id' => $pdo->lastInsertId(), 'email' => $email ];
  // Send welcome email (logged or mailed depending on MAIL_MODE)
  send_mail_or_log($email, 'Welcome to LearnLite', 'Your account was created successfully.');
  send_json([ 'success' => true ]);
} catch (PDOException $e) {
  if ($e->getCode() === '23000') { // duplicate
    send_json([ 'error' => 'Email already registered' ], 409);
  }
  send_json([ 'error' => 'Database error: ' . $e->getMessage() ], 500);
}

?>


