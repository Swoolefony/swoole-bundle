<?php

declare(strict_types=1);

namespace Swoolefony\SwooleBundle\Server;

readonly class Status
{
    public function __construct(
        private int $mainPid,
        private int $managerPid,
        private int $port,
        private string $ip,
        private Stats $stats,
    ) {
    }

    public function getStats(): Stats
    {
        return $this->stats;
    }

    public function getMainPid(): int
    {
        return $this->mainPid;
    }

    public function getManagerPid(): int
    {
        return $this->managerPid;
    }

    public function getPort(): int
    {
        return $this->port;
    }

    public function getIp(): string
    {
        return $this->ip;
    }
}
