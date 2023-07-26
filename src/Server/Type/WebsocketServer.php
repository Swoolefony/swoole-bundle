<?php

declare(strict_types=1);

namespace Swoolefony\SwooleBundle\Server\Type;

use RuntimeException;
use Swoole\WebSocket\Server as SwooleServer;
use Swoolefony\SwooleBundle\Server\Options;
use Swoolefony\SwooleBundle\Server\Stats;
use Swoolefony\SwooleBundle\Server\ServerInterface;

class WebsocketServer implements ServerInterface
{
    private ?SwooleServer $server = null;

    public function __construct(private readonly Options $options)
    {
    }

    public function stop(): void
    {
        if ($this->server) {
            $this->server()->shutdown();
        }
    }

    public function run(): void
    {
        if (!$this->server()->start()) {
            throw new RuntimeException('Unable to start websocket server.');
        }
    }

    public function getStats(): Stats
    {
        return new Stats((array) $this->server()->stats());
    }

    private function server(): SwooleServer
    {
        if ($this->server !== null) {
            return $this->server;
        }
        $this->server = new SwooleServer(
            $this->options->getIpAddress(),
            $this->options->getPort(),
        );

        $this->server->set($this->options->toSwooleOptionsArray());

        return $this->server;
    }
}
