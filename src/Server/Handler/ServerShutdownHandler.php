<?php

declare(strict_types=1);

namespace Swoolefony\SwooleBundle\Server\Handler;

use Psr\Cache\CacheItemPoolInterface;
use Swoole\Server;
use Swoolefony\SwooleBundle\Server\CacheKey;

readonly class ServerShutdownHandler
{
    public function __construct(private CacheItemPoolInterface $cache)
    {
    }

    public function __invoke(Server $server): void
    {
        $this->cache->deleteItem(CacheKey::ServerPid->value);
    }
}
