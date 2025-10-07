<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/session.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  send_json([ 'error' => 'Method not allowed' ], 405);
}

$raw = file_get_contents('php://input');
$data = json_decode($raw, true);
$email = isset($data['email']) ? trim(strtolower($data['email'])) : '';
$code = isset($data['code']) ? trim($data['code']) : '';
$purpose = isset($data['purpose']) ? trim($data['purpose']) : 'generic';

if ($email === '' || $code === '') {
  send_json([ 'error' => 'Email and code required' ], 422);
}

try {
  $pdo = db_get_pdo();
  $stmt = $pdo->prepare('SELECT * FROM email_otps WHERE email = :email AND code = :code AND (purpose = :purpose OR purpose = "generic") ORDER BY id DESC LIMIT 1');
  $stmt->execute([
    ':email' => $email,
    ':code' => $code,
    ':purpose' => $purpose,
  ]);
  $row = $stmt->fetch();
  if (!$row) {
    send_json([ 'error' => 'Invalid code' ], 400);
  }
  if ($row['used_at'] !== null) {
    send_json([ 'error' => 'Code already used' ], 400);
  }
  if (strtotime($row['expires_at']) < time()) {
    send_json([ 'error' => 'Code expired' ], 400);
  }

  // mark used
  $upd = $pdo->prepare('UPDATE email_otps SET used_at = NOW() WHERE id = :id');
  $upd->execute([':id' => $row['id']]);

  // Optionally, auto-create a user on signup purpose
  if ($purpose === 'signup') {
    $u = $pdo->prepare('SELECT id, email FROM users WHERE email = :email');
    $u->execute([':email' => $email]);
    $user = $u->fetch();
    if (!$user) {
      $ins = $pdo->prepare('INSERT INTO users (email, password_hash) VALUES (:email, :hash)');
      $ins->execute([':email' => $email, ':hash' => password_hash(bin2hex(random_bytes(8)), PASSWORD_DEFAULT)]);
      $userId = $pdo->lastInsertId();
      $_SESSION['user'] = [ 'id' => $userId, 'email' => $email ];
    } else {
      $_SESSION['user'] = [ 'id' => $user['id'], 'email' => $user['email'] ];
    }
  } else {
    // For login or generic, just sign the user in if exists
    $u = $pdo->prepare('SELECT id, email FROM users WHERE email = :email');
    $u->execute([':email' => $email]);
    $user = $u->fetch();
    if ($user) {
      $_SESSION['user'] = [ 'id' => $user['id'], 'email' => $user['email'] ];
    }
  }

  send_json([ 'success' => true ]);
} catch (Throwable $e) {
  send_json([ 'error' => 'Server error: ' . $e->getMessage() ], 500);
}

?>


