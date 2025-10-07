<?php

// Change these to match your XAMPP/MySQL setup
$DB_HOST = getenv('DB_HOST') ?: '127.0.0.1';
$DB_PORT = getenv('DB_PORT') ?: '3306';
$DB_NAME = getenv('DB_NAME') ?: 'courses_db';
$DB_USER = getenv('DB_USER') ?: 'root';
$DB_PASS = getenv('DB_PASS') ?: '';

$DSN = "mysql:host={$DB_HOST};port={$DB_PORT};dbname={$DB_NAME};charset=utf8mb4";

function db_get_pdo(): PDO {
  global $DSN, $DB_USER, $DB_PASS;
  static $pdo = null;
  if ($pdo instanceof PDO) {
    return $pdo;
  }
  $options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
  ];
  $pdo = new PDO($DSN, $DB_USER, $DB_PASS, $options);
  return $pdo;
}

function send_json($data, int $status = 200): void {
  http_response_code($status);
  header('Content-Type: application/json');
  // Allow CORS during local dev (optional). Comment out in prod.
  header('Access-Control-Allow-Origin: *');
  header('Access-Control-Allow-Headers: Content-Type');
  echo json_encode($data);
  exit;
}

// Basic mail configuration: 'log' or 'mail'
$MAIL_MODE = getenv('MAIL_MODE') ?: 'log'; // 'log' file, 'mail' php mail(), 'smtp' PHPMailer
$MAIL_LOG_FILE = __DIR__ . '/../mail.log';

function send_mail_or_log(string $to, string $subject, string $body): bool {
  global $MAIL_MODE, $MAIL_LOG_FILE;
  if ($MAIL_MODE === 'mail') {
    $headers = 'From: no-reply@localhost' . "\r\n" . 'Content-Type: text/plain; charset=UTF-8';
    return @mail($to, $subject, $body, $headers);
  }
  if ($MAIL_MODE === 'smtp') {
    require_once __DIR__ . '/mailer.php';
    return smtp_send_mail($to, $subject, $body);
  }
  // default: log to file for local testing
  $line = '[' . date('c') . "] TO:" . $to . " SUBJECT:" . $subject . " BODY:" . str_replace(["\r", "\n"], ' ', $body) . "\n";
  return (bool)@file_put_contents($MAIL_LOG_FILE, $line, FILE_APPEND);
}

?>


