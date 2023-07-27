<?php

declare(strict_types=1);

namespace Swoolefony\SwooleBundle\Server\Type;

use RuntimeException;
use Swoole\Http\Server as SwooleServer;
use Swoolefony\SwooleBundle\Server\CacheKey;
use Swoolefony\SwooleBundle\Server\Handler\HttpRequestHandler;
use Swoolefony\SwooleBundle\Server\Options;
use Swoolefony\SwooleBundle\Server\Stats;
use Swoolefony\SwooleBundle\Server\ServerInterface;
use Symfony\Contracts\Cache\CacheInterface;

class HttpServer implements ServerInterface
{
    private ?SwooleServer $swooleServer = null;

    public function __construct(
        private readonly Options $options,
        private readonly HttpRequestHandler $requestHandler,
        private readonly CacheInterface $cache,
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

        $this->swooleServer->on(
            'request',
            $this->requestHandler,
        );
        $this->swooleServer->on(
            'start',
            $this->resetServerCachePid(...)
        );

        return $this->swooleServer;
    }

    private function resetServerCachePid(): void
    {
        $this->cache->delete(CacheKey::ServerPid->value);

        $this->cache->get(
            CacheKey::ServerPid->value,
            fn () => $this->server()->getMasterPid(),
        );
    }
}
