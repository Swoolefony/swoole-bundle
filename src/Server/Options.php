<?php

declare(strict_types=1);

namespace Swoolefony\SwooleBundle\Server;

use Swoolefony\SwooleBundle\Runtime\Mode;

class Options
{
    public function __construct(
        private string $ip = '0.0.0.0',
        private int $port = 80,
        private Mode $mode = Mode::Http,
    ) {
    }

    public function getIpAddress(): string
    {
        return $this->ip;
    }

    public function getPort(): int
    {
        return $this->port;
    }

    public function getMode(): Mode
    {
        return $this->mode;
    }
}
