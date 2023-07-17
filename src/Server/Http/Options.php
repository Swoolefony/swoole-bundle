<?php

declare(strict_types=1);

namespace Swoolefony\SwooleBundle\Server\Http;

use Closure;

class Options
{
    private Closure $requestHandler;

    public function __construct(
        private string $ip = '0.0.0.0',
        private int $port = 80,
    ) {
        $this->requestHandler = function () {};
    }

    public function getIpAddress(): string
    {
        return $this->ip;
    }

    public function getPort(): int
    {
        return $this->port;
    }

    public function getRequestHandler(): Closure
    {
        return $this->requestHandler;
    }

    public function setRequestHandler(Closure $requestHandler): self
    {
        $this->requestHandler = $requestHandler;

        return $this;
    }
}
