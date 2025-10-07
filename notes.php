<?php
require_once __DIR__ . '/config.php';

// GET /php/notes.php?subject=html|css|javascript (optional)
try {
  $pdo = db_get_pdo();
  $subject = isset($_GET['subject']) ? trim(strtolower($_GET['subject'])) : '';
  if ($subject !== '') {
    $stmt = $pdo->prepare('SELECT id, subject, title, content, created_at FROM notes WHERE subject = :subject ORDER BY created_at DESC');
    $stmt->execute([':subject' => $subject]);
  } else {
    $stmt = $pdo->query('SELECT id, subject, title, content, created_at FROM notes ORDER BY created_at DESC');
  }
  $rows = $stmt->fetchAll();
  send_json($rows);
} catch (Throwable $e) {
  send_json([ 'error' => 'Database error: ' . $e->getMessage() ], 500);
}

?>



