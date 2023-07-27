<?php

declare(strict_types=1);

namespace Swoolefony\SwooleBundle\Server\Handler;

use Swoole\Server;
use Swoolefony\SwooleBundle\Server\CacheKey;
use Symfony\Contracts\Cache\CacheInterface;

readonly class ServerShutdownHandler
{
    public function __construct(private CacheInterface $cache)
    {
    }

    public function __invoke(Server $server): void
    {
        $this->cache->delete(CacheKey::ServerPid->value);
    }
}
