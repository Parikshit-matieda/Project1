<?php
require_once __DIR__ . '/session.php';
require_once __DIR__ . '/config.php';

// Return current session user
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Content-Type');

if (isset($_SESSION['user'])) {
  echo json_encode([ 'user' => $_SESSION['user'] ]);
} else {
  echo json_encode([ 'user' => null ]);
}
exit;

?>


