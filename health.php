<?php
require_once __DIR__ . '/config.php';

try {
  $pdo = db_get_pdo();
  // simple query to ensure DB and table exist
  $stmt = $pdo->query('SELECT COUNT(*) AS course_count FROM courses');
  $row = $stmt->fetch();
  send_json([
    'ok' => true,
    'db' => 'connected',
    'courses_count' => isset($row['course_count']) ? (int)$row['course_count'] : 0,
  ]);
} catch (Throwable $e) {
  send_json([
    'ok' => false,
    'db' => 'error',
    'error' => $e->getMessage(),
  ], 500);
}

?>


