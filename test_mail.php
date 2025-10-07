<?php
require_once __DIR__ . '/config.php';

$to = isset($_GET['to']) ? $_GET['to'] : 'you@example.com';
$ok = send_mail_or_log($to, 'Test email from LearnLite', 'Hello! This is a test email.');
send_json([ 'success' => (bool)$ok, 'to' => $to ]);

?>


