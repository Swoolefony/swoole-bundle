<?php

declare(strict_types=1);

namespace Swoolefony\SwooleBundle\Server;

use Closure;
use Swoolefony\SwooleBundle\Server\Handler\HttpRequestHandler;
use Swoolefony\SwooleBundle\Server\Handler\ServerShutdownHandler;
use Swoolefony\SwooleBundle\Server\Handler\ServerStartHandler;
use Symfony\Component\Cache\Adapter\NullAdapter;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Contracts\Cache\CacheInterface;

class HandlerFactory
{
    public function __construct(private readonly CacheInterface $cache = new NullAdapter())
    {
    }

    public function makeForEvent(
        EventName $eventName,
        object $app,
    ): ?Closure {
        return match ($eventName) {
            EventName::Request => $this->makeRequestHandlerForApp($app),
            EventName::Start => (new ServerStartHandler($this->cache))(...),
            EventName::Shutdown => (new ServerShutdownHandler($this->cache))(...),
        };
    }

    private function makeRequestHandlerForApp(object $app): ?Closure
    {
        return match (true) {
            $app instanceof HttpKernelInterface => (new HttpRequestHandler($app))(...),
            default => null,
        };
    }
}