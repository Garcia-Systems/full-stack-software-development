<?php
declare(strict_types=1);

if (PHP_SAPI === 'cli-server' && parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) !== '/' && is_file(__DIR__ . parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH))) { return false; }
require __DIR__ . '/../src/Response.php';
require __DIR__ . '/../src/App.php';

use RelayDesk\App;

$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
if ($path === '/') { require __DIR__ . '/index.html'; return; }
$headers = array_change_key_case(function_exists('getallheaders') ? getallheaders() : [], CASE_LOWER);
$requestId = preg_match('/^[A-Za-z0-9._-]{1,64}$/', $headers['x-request-id'] ?? '') ? $headers['x-request-id'] : bin2hex(random_bytes(8));
$dsn = getenv('DB_DSN') ?: sprintf('mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4', getenv('DB_HOST') ?: 'db', getenv('DB_PORT') ?: '3306', getenv('DB_DATABASE') ?: 'relaydesk');
try {
    $pdo = new PDO($dsn, getenv('DB_USERNAME') ?: 'relaydesk', getenv('DB_PASSWORD') ?: 'relaydesk', [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    $app = new App($pdo, getenv('APP_ENV') === 'local' && getenv('LAB_FAULTS') === 'true');
    $response = $app->handle($_SERVER['REQUEST_METHOD'], $path, $headers, file_get_contents('php://input'), $requestId);
} catch (Throwable $error) {
    error_log(json_encode(['event' => 'database.unavailable', 'request_id' => $requestId, 'message' => $error->getMessage()]));
    $response = RelayDesk\Response::json(503, ['error' => 'Database unavailable'], $requestId);
}
http_response_code($response->status);
foreach ($response->headers as $name => $value) { header("$name: $value"); }
error_log(json_encode(['event' => 'request.complete', 'request_id' => $requestId, 'method' => $_SERVER['REQUEST_METHOD'], 'path' => $path, 'status' => $response->status]));
echo $response->body;
