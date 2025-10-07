<?php

// Start session safely
if (session_status() !== PHP_SESSION_ACTIVE) {
  session_start([
    'cookie_httponly' => true,
    'cookie_secure' => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on',
    'cookie_samesite' => 'Lax',
    'use_strict_mode' => true,
  ]);
}

function auth_require_login(): void {
  if (!isset($_SESSION['user'])) {
    http_response_code(401);
    header('Content-Type: application/json');
    echo json_encode([ 'error' => 'Unauthorized' ]);
    exit;
  }
}

?>


