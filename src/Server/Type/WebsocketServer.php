<?php

declare(strict_types=1);

namespace Swoolefony\SwooleBundle\Server\Type;

use Swoole\WebSocket\Server as SwooleServer;
use Swoolefony\SwooleBundle\Server\Stats;
use Swoolefony\SwooleBundle\Server\ServerInterface;

readonly class WebsocketServer implements ServerInterface
{
    public function __construct(private SwooleServer $swooleServer)
    {
    }

    public function run(): void
    {
        if (!$this->swooleServer->start()) {
            throw new \RuntimeException('Unable to start websocket server.');
        }
    }

    public function getStats(): Stats
    {
        return new Stats((array) $this->swooleServer->stats());
    }
}
