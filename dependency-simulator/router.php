<?php
// Development-only deterministic HTTP dependency. Never accepts credentials.
$stateFile = __DIR__.'/.state.json';
$state = is_file($stateFile) ? json_decode(file_get_contents($stateFile), true) : ['mode' => 'success', 'attempt' => 0];
if ($_SERVER['REQUEST_METHOD'] === 'PUT' && $_SERVER['REQUEST_URI'] === '/mode') {
    $input = json_decode(file_get_contents('php://input'), true) ?? [];
    $allowed = ['success', 'delay', 'transient', 'persistent', 'malformed', 'client-error'];
    if (!in_array($input['mode'] ?? '', $allowed, true)) { http_response_code(422); echo json_encode(['error' => 'invalid mode']); exit; }
    $state = ['mode' => $input['mode'], 'attempt' => 0]; file_put_contents($stateFile, json_encode($state));
    header('Content-Type: application/json'); echo json_encode($state); exit;
}
if ($_SERVER['REQUEST_URI'] === '/state') { header('Content-Type: application/json'); echo json_encode($state); exit; }
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || $_SERVER['REQUEST_URI'] !== '/deliveries') { http_response_code(404); exit; }
$state['attempt']++; file_put_contents($stateFile, json_encode($state));
header('X-Simulator-Attempt: '.(string) $state['attempt']);
if ($state['mode'] === 'delay') usleep(1500000);
if ($state['mode'] === 'transient' && $state['attempt'] < 3) { http_response_code(503); echo '{"error":"transient"}'; exit; }
if ($state['mode'] === 'persistent') { http_response_code(503); echo '{"error":"persistent"}'; exit; }
if ($state['mode'] === 'client-error') { http_response_code(422); echo '{"error":"rejected"}'; exit; }
if ($state['mode'] === 'malformed') { echo 'not-json'; exit; }
$body = json_decode(file_get_contents('php://input'), true);
header('Content-Type: application/json'); echo json_encode(['accepted' => true, 'deliveryId' => 'fake-'.($body['ticketId'] ?? 'unknown')]);
