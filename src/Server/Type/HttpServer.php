<?php

declare(strict_types=1);

namespace Swoolefony\SwooleBundle\Server\Type;

use RuntimeException;
use Swoole\Http\Server as SwooleServer;
use Swoolefony\SwooleBundle\Server\EventName;
use Swoolefony\SwooleBundle\Server\HandlerFactory;
use Swoolefony\SwooleBundle\Server\Options;
use Swoolefony\SwooleBundle\Server\Stats;
use Swoolefony\SwooleBundle\Server\ServerInterface;

class HttpServer implements ServerInterface
{
    private const HANDLED_EVENTS = [
        EventName::Request,
        EventName::Start,
        EventName::Shutdown,
    ];

    private ?SwooleServer $swooleServer = null;

    public function __construct(
        private readonly Options $options,
        private readonly HandlerFactory $handlerFactory,
        private readonly object $app,
    ) {
    }

    public function run(): void
    {
        if (!$this->server()->start()) {
            throw new RuntimeException('Unable to start HTTP server.');
        }
    }

    public function stop(): void
    {
        if ($this->swooleServer) {
            $this->server()->shutdown();
        }
    }

    public function getStats(): Stats
    {
        return new Stats((array) $this->server()->stats());
    }

    private function server(): SwooleServer
    {
        if ($this->swooleServer !== null) {
            return $this->swooleServer;
        }
        $this->swooleServer = new SwooleServer(
            $this->options->getIpAddress(),
            $this->options->getPort(),
        );

        $this->swooleServer->set($this->options->toSwooleOptionsArray());

        foreach (self::HANDLED_EVENTS as $eventName) {
            $handler = $this->handlerFactory->makeForEvent(
                $eventName,
                $this->app,
            );
            if ($handler !== null) {
                $this->swooleServer->on(
                    $eventName->value,
                    $handler,
                );
            }
        }

        return $this->swooleServer;
    }
}
