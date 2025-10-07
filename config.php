<?php

// ✅ LIVE InfinityFree Database Credentials
$DB_HOST = 'sql104.infinityfree.com';
$DB_PORT = '3306';
$DB_NAME = 'if0_40115729_crudoperation';
$DB_USER = 'if0_40115729';
$DB_PASS = 'acafe2dxGlunCUI';  // ✅ Your actual vPanel password

$DSN = "mysql:host={$DB_HOST};port={$DB_PORT};dbname={$DB_NAME};charset=utf8mb4";

// ✅ PDO Connection Function
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

// ✅ JSON API Response Helper (optional, useful for AJAX/REST)
function send_json($data, int $status = 200): void {
    http_response_code($status);
    header('Content-Type: application/json');
    header('Access-Control-Allow-Origin: *');  // Optional CORS
    header('Access-Control-Allow-Headers: Content-Type');
    echo json_encode($data);
    exit;
}

?>
