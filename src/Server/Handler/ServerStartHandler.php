<?php

declare(strict_types=1);

namespace Swoolefony\SwooleBundle\Server\Handler;

use Psr\Cache\CacheItemPoolInterface;
use Swoole\Server;
use Swoole\Timer;
use Swoolefony\SwooleBundle\Server\CacheKey;
use Swoolefony\SwooleBundle\Server\Task;
use Swoolefony\SwooleBundle\Server\Task\TaskType;

readonly class ServerStartHandler
{
    public function __construct(
        private CacheItemPoolInterface $cache,
        private bool $shouldRegisterTasks = true,
    ) {
    }

    public function __invoke(Server $server): void
    {
        $this->cache->deleteItem(CacheKey::ServerPid->value);

        $item = $this->cache->getItem(CacheKey::ServerPid->value);
        $item->set($server->getMasterPid());

        $this->cache->save($item);

        if ($this->shouldRegisterTasks) {
            Timer::tick(
                1000,
                fn() => $server->task(new Task(TaskType::Tick))
            );
        }
    }
}
