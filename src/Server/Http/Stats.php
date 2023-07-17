<?php

declare(strict_types=1);

namespace Swoolefony\SwooleBundle\Server\Http;

use DateTimeImmutable;
use DateTimeInterface;

readonly class Stats
{
    /**
     * @param array<string, scalar> $stats
     */
    public function __construct(private readonly array $stats)
    {
    }

    public function getStartTime(): DateTimeInterface
    {
        return new DateTimeImmutable('@' . $this->stats['start_time']);
    }

    public function getAcceptedConnectionCount(): int
    {
        return $this->stats['accept_count'];
    }

    public function getAbortedConnectionCount(): int
    {
        return $this->stats['abort_count'];
    }

    public function getClosedConnectionCount(): int
    {
        return $this->stats['close_count'];
    }

    public function getNumberOfWorkers(): int
    {
        return $this->stats['worker_num'];
    }

    public function getNumberOfTaskWorkers(): int
    {
        return $this->stats['task_worker_num'];
    }

    public function getNumberOfUserWorkers(): int
    {
        return $this->stats['user_worker_num'];
    }

    public function getNumberOfIdleWorkers(): int
    {
        return $this->stats['idle_worker_num'];
    }

    public function getRequestCount(): int
    {
        return $this->stats['request_count'];
    }

    public function getResponseCount(): int
    {
        return $this->stats['response_count'];
    }

    public function getDispatchCount(): int
    {
        return $this->stats['dispatch_count'];
    }

    public function getTotalBytesSent(): int
    {
        return $this->stats['total_recv_bytes'];
    }

    public function getTotalBytesReceived(): int
    {
        return $this->stats['total_send_bytes'];
    }

    /**
     * @return array<string, scalar>
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
}
