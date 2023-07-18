<?php

declare(strict_types=1);

namespace Swoolefony\SwooleBundle\Server\Type;

use Swoole\Http\Server as SwooleServer;
use Swoolefony\SwooleBundle\Server\Stats;
use Swoolefony\SwooleBundle\Server\ServerInterface;

readonly class HttpServer implements ServerInterface
{
    public function __construct(private SwooleServer $swooleServer)
    {
    }

    public function run(): void
    {
        if (!$this->swooleServer->start()) {
            throw new \RuntimeException('Unable to start HTTP server.');
        }
    }

    public function getStats(): Stats
    {
        return new Stats($this->swooleServer->stats());
    }
}
