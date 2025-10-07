<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/session.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  send_json([ 'error' => 'Method not allowed' ], 405);
}

auth_require_login();

$raw = file_get_contents('php://input');
$data = json_decode($raw, true);
$subject = isset($data['subject']) ? trim(strtolower($data['subject'])) : '';
$title = isset($data['title']) ? trim($data['title']) : '';
$content = isset($data['content']) ? trim($data['content']) : '';

$allowed = ['html','css','javascript'];
if (!in_array($subject, $allowed, true)) {
  send_json([ 'error' => 'Invalid subject' ], 422);
}
if ($title === '' || $content === '') {
  send_json([ 'error' => 'Title and content are required' ], 422);
}

try {
  $pdo = db_get_pdo();
  $stmt = $pdo->prepare('INSERT INTO notes (subject, title, content) VALUES (:subject, :title, :content)');
  $stmt->execute([
    ':subject' => $subject,
    ':title' => $title,
    ':content' => $content,
  ]);
  send_json([ 'success' => true, 'id' => $pdo->lastInsertId() ]);
} catch (Throwable $e) {
  send_json([ 'error' => 'Database error: ' . $e->getMessage() ], 500);
}

?>



