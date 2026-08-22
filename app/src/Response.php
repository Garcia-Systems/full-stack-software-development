<?php
declare(strict_types=1);
namespace RelayDesk;

final class Response
{
    public function __construct(public int $status, public array $headers, public string $body) {}
    public static function json(int $status, array $data, string $requestId, array $headers = []): self
    {
        return new self($status, $headers + ['Content-Type' => 'application/json; charset=utf-8', 'X-Request-ID' => $requestId, 'Cache-Control' => 'no-store'], json_encode($data, JSON_THROW_ON_ERROR));
    }
}
