<?php

declare(strict_types=1);

namespace Swoolefony\SwooleBundle\Server;

use Closure;
use Psr\Cache\CacheItemPoolInterface;
use Swoolefony\SwooleBundle\Server\Handler\HttpRequestHandler;
use Swoolefony\SwooleBundle\Server\Handler\ServerShutdownHandler;
use Swoolefony\SwooleBundle\Server\Handler\ServerStartHandler;
use Swoolefony\SwooleBundle\Server\Handler\TaskHandler;
use Swoolefony\SwooleBundle\Server\Task\Dispatcher;
use Symfony\Component\HttpKernel\HttpKernelInterface;

class HandlerFactory
{
    public function __construct(
        private readonly CacheItemPoolInterface $cache,
        private readonly Dispatcher $taskDispatcher,
    ) {
    }

    public function makeForEvent(
        EventName $eventName,
        object $app,
    ): ?Closure {
        return match ($eventName) {
            EventName::Request => $this->makeRequestHandlerForApp($app),
            EventName::Start => (new ServerStartHandler($this->cache))(...),
            EventName::Shutdown => (new ServerShutdownHandler($this->cache))(...),
            EventName::Task => (new TaskHandler($this->taskDispatcher))(...),
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
