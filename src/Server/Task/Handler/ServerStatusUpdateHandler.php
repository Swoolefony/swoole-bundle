<?php

declare(strict_types=1);

namespace Swoolefony\SwooleBundle\Server\Task\Handler;

use Psr\Cache\CacheItemPoolInterface;
use Swoolefony\SwooleBundle\Server\CacheKey;
use Swoolefony\SwooleBundle\Server\ServerInterface;
use Swoolefony\SwooleBundle\Server\Task;
use Swoolefony\SwooleBundle\Server\Task\TaskType;

readonly class ServerStatusUpdateHandler implements TaskHandlerInterface
{
    public function __construct(
        private CacheItemPoolInterface $cache,
        private ServerInterface $server,
    ) {
    }

    public function __invoke(Task $task): void
    {
        $item = $this->cache->getItem(CacheKey::ServerStatus->value);
        $item->set($this->server->getStatus());

        $this->cache->save($item);
    }

    public function supports(): TaskType
    {
        return TaskType::Tick;
    }
}
