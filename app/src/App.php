<?php
declare(strict_types=1);

namespace RelayDesk;

use PDO;
use Throwable;

final class App
{
    public function __construct(private PDO $db, private bool $faultsEnabled = false) {}

    public function handle(string $method, string $path, array $headers, string $body, string $requestId): Response
    {
        $fault = strtolower($headers['x-lab-fault'] ?? '');
        try {
            if ($path === '/api/tickets' && $method === 'GET') {
                $column = $this->faultsEnabled && $fault === 'column-mismatch' ? 'title' : 'subject';
                $rows = $this->db->query("SELECT id, $column AS subject, created_at FROM tickets ORDER BY id")->fetchAll(PDO::FETCH_ASSOC);
                if ($this->faultsEnabled && $fault === 'response-field') {
                    foreach ($rows as &$row) { $row['title'] = $row['subject']; unset($row['subject']); }
                }
                return Response::json(200, ['tickets' => $rows], $requestId);
            }
            if ($path === '/api/tickets' && $method === 'POST') {
                if (!str_contains(strtolower($headers['content-type'] ?? ''), 'application/json')) {
                    return Response::json(415, ['error' => 'Content-Type must be application/json'], $requestId);
                }
                $input = json_decode($body, true);
                if (!is_array($input)) {
                    return Response::json(400, ['error' => 'Request body is not valid JSON'], $requestId);
                }
                $subject = trim((string) ($input['subject'] ?? ''));
                if ($subject === '' || mb_strlen($subject) > 120) {
                    return Response::json(422, ['error' => 'subject is required and must be 1-120 characters'], $requestId);
                }
                $statement = $this->db->prepare('INSERT INTO tickets (subject) VALUES (:subject)');
                $statement->execute(['subject' => $subject]);
                $id = (int) $this->db->lastInsertId();
                $row = $this->db->query("SELECT id, subject, created_at FROM tickets WHERE id = $id")->fetch(PDO::FETCH_ASSOC);
                return Response::json(201, ['ticket' => $row], $requestId, ['Location' => "/api/tickets/$id"]);
            }
            if ($path === '/api/tickets') {
                return Response::json(405, ['error' => 'Method not allowed'], $requestId, ['Allow' => 'GET, POST']);
            }
            if ($path === '/api/fail' && $this->faultsEnabled) {
                throw new \RuntimeException('Controlled application exception');
            }
            return Response::json(404, ['error' => 'Not found'], $requestId);
        } catch (Throwable $error) {
            error_log(json_encode(['event' => 'request.failed', 'request_id' => $requestId, 'message' => $error->getMessage()]));
            return Response::json(500, ['error' => 'Internal server error'], $requestId);
        }
    }
}
