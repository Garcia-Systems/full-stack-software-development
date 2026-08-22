<?php
declare(strict_types=1);
require __DIR__ . '/../app/src/Response.php';
require __DIR__ . '/../app/src/App.php';
use RelayDesk\App;

function expect(bool $condition, string $message): void { if (!$condition) { fwrite(STDERR, "FAIL: $message\n"); exit(1); } }
$db = new PDO('sqlite::memory:', null, null, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
$db->exec('CREATE TABLE tickets (id INTEGER PRIMARY KEY AUTOINCREMENT, subject VARCHAR(120) NOT NULL, created_at DATETIME DEFAULT CURRENT_TIMESTAMP)');
$db->exec("INSERT INTO tickets (subject) VALUES ('Seed ticket')");
$app = new App($db, true);
$created = $app->handle('POST', '/api/tickets', ['content-type' => 'application/json'], '{"subject":"Vertical path"}', 'test-create');
expect($created->status === 201, 'valid ticket is created');
expect($created->headers['X-Request-ID'] === 'test-create', 'request ID crosses response boundary');
expect($db->query('SELECT COUNT(*) FROM tickets')->fetchColumn() === 2, 'ticket persisted');
$listed = $app->handle('GET', '/api/tickets', [], '', 'test-list');
expect(str_contains($listed->body, 'Vertical path'), 'persisted ticket is returned');
expect($app->handle('POST', '/api/tickets', ['content-type' => 'text/plain'], '{}', 'test-media')->status === 415, 'wrong content type rejected');
expect($app->handle('POST', '/api/tickets', ['content-type' => 'application/json'], '{', 'test-json')->status === 400, 'malformed JSON rejected');
expect($app->handle('POST', '/api/tickets', ['content-type' => 'application/json'], '{"subject":""}', 'test-validation')->status === 422, 'empty subject rejected');
expect($app->handle('PUT', '/api/tickets', [], '', 'test-method')->status === 405, 'unsupported method rejected');
expect($app->handle('GET', '/api/tickets', ['x-lab-fault' => 'column-mismatch'], '', 'test-fault')->status === 500, 'column mismatch is deterministic');
echo "Application behavior tests passed.\n";
