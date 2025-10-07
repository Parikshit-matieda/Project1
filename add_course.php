<?php
require_once __DIR__ . '/config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  send_json([ 'error' => 'Method not allowed' ], 405);
}

$raw = file_get_contents('php://input');
$data = json_decode($raw, true);
$title = isset($data['title']) ? trim($data['title']) : '';
$description = isset($data['description']) ? trim($data['description']) : '';

if ($title === '') {
  send_json([ 'error' => 'Title is required' ], 422);
}

try {
  $pdo = db_get_pdo();
  $stmt = $pdo->prepare('INSERT INTO courses (title, description) VALUES (:title, :description)');
  $stmt->execute([
    ':title' => $title,
    ':description' => $description,
  ]);
  send_json([ 'success' => true, 'id' => $pdo->lastInsertId() ]);
} catch (Throwable $e) {
  send_json([ 'error' => 'Database error: ' . $e->getMessage() ], 500);
}

?>


