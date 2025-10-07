<?php
require_once __DIR__ . '/config.php';

try {
  $pdo = db_get_pdo();
  $stmt = $pdo->query('SELECT id, title, description, created_at FROM courses ORDER BY created_at DESC');
  $rows = $stmt->fetchAll();
  send_json($rows);
} catch (Throwable $e) {
  send_json([ 'error' => 'Database error: ' . $e->getMessage() ], 500);
}

?>


