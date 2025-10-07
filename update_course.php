<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/session.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  send_json([ 'error' => 'Method not allowed' ], 405);
}

auth_require_login();

$raw = file_get_contents('php://input');
$data = json_decode($raw, true);
$id = isset($data['id']) ? (int)$data['id'] : 0;
$title = isset($data['title']) ? trim($data['title']) : '';
$description = isset($data['description']) ? trim($data['description']) : '';

if ($id <= 0) {
  send_json([ 'error' => 'Valid id is required' ], 422);
}
if ($title === '') {
  send_json([ 'error' => 'Title is required' ], 422);
}

try {
  $pdo = db_get_pdo();
  $stmt = $pdo->prepare('UPDATE courses SET title = :title, description = :description WHERE id = :id');
  $stmt->execute([
    ':id' => $id,
    ':title' => $title,
    ':description' => $description,
  ]);
  send_json([ 'success' => true ]);
} catch (Throwable $e) {
  send_json([ 'error' => 'Database error: ' . $e->getMessage() ], 500);
}

?>



