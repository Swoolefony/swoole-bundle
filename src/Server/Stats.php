<?php

declare(strict_types=1);

namespace Swoolefony\SwooleBundle\Server;

use DateTimeImmutable;
use DateTimeInterface;

readonly class Stats
{
    /**
     * @param array<string, mixed> $stats
     */
    public function __construct(private array $stats)
    {
    }

    public function getStartTime(): DateTimeInterface
    {
        return new DateTimeImmutable('@' . $this->getIntOrDefaultFromKey('start_time'));
    }

    public function getAcceptedConnectionCount(): int
    {
        return $this->getIntOrDefaultFromKey('accept_count');
    }

    public function getAbortedConnectionCount(): int
    {
        return $this->getIntOrDefaultFromKey('abort_count');
    }

    public function getClosedConnectionCount(): int
    {
        return $this->getIntOrDefaultFromKey('close_count');
    }

    public function getNumberOfWorkers(): int
    {
        return $this->getIntOrDefaultFromKey('worker_num');
    }

    public function getNumberOfTaskWorkers(): int
    {
        return $this->getIntOrDefaultFromKey('task_worker_num');
    }

    public function getNumberOfUserWorkers(): int
    {
        return $this->getIntOrDefaultFromKey('user_worker_num');
    }

    public function getNumberOfIdleWorkers(): int
    {
        return $this->getIntOrDefaultFromKey('idle_worker_num');
    }

    public function getRequestCount(): int
    {
        return $this->getIntOrDefaultFromKey('request_count');
    }

    public function getResponseCount(): int
    {
        return $this->getIntOrDefaultFromKey('response_count');
    }

    public function getDispatchCount(): int
    {
        return $this->getIntOrDefaultFromKey('dispatch_count');
    }

    public function getTotalBytesSent(): int
    {
        return $this->getIntOrDefaultFromKey('total_send_bytes');
    }

    public function getTotalBytesReceived(): int
    {
        return $this->getIntOrDefaultFromKey('total_recv_bytes');
    }

    /**
     * @return array<string, DateTimeInterface|int>
     */
    public function toArray(): array
    {
        return [
            'start_time' => $this->getStartTime(),
            'accept_count' => $this->getAcceptedConnectionCount(),
            'abort_count' => $this->getAbortedConnectionCount(),
            'close_count' => $this->getClosedConnectionCount(),
            'worker_num' => $this->getNumberOfWorkers(),
            'task_worker_num' => $this->getNumberOfTaskWorkers(),
            'user_worker_num' => $this->getNumberOfUserWorkers(),
            'idle_worker_num' => $this->getNumberOfIdleWorkers(),
            'request_count' => $this->getRequestCount(),
            'response_count' => $this->getResponseCount(),
            'dispatch_count' => $this->getDispatchCount(),
            'total_recv_bytes' => $this->getTotalBytesReceived(),
            'total_send_bytes' => $this->getTotalBytesSent(),
        ];
    }

    private function getIntOrDefaultFromKey(string $keyName, int $default = 0): int
    {
        if (isset($this->stats[$keyName]) && is_int($this->stats[$keyName])) {
            return $this->stats[$keyName];
        }

        return $default;
    }
}
