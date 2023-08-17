<?php

declare(strict_types=1);

namespace Swoolefony\SwooleBundle\Tests\Unit\Server\Handler;

use Mockery;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use Psr\Cache\CacheItemPoolInterface;
use Swoole\Server;
use Swoolefony\SwooleBundle\Server\CacheKey;
use Swoolefony\SwooleBundle\Server\Handler\ServerShutdownHandler;
use Swoolefony\SwooleBundle\Tests\Unit\TestCase;

#[CoversClass(ServerShutdownHandler::class)]
class ServerShutdownHandlerTest extends TestCase
{
    public function testItRemovesTheServerPidFromCache(): void
    {
        /** @var Server&MockInterface $mockServer */
        $mockServer = Mockery::spy(Server::class);
        /** @var CacheItemPoolInterface&MockInterface $mockCache */
        $mockCache = Mockery::mock(CacheItemPoolInterface::class);

        $mockCache->allows([
            'deleteItem' => true,
        ]);
        $mockServer->allows([
            'getMasterPid' => 7,
        ]);

        (new ServerShutdownHandler($mockCache))->__invoke($mockServer);

        $mockCache
            ->shouldHaveReceived(
                'deleteItem',
                [CacheKey::ServerPid->value]
            );
    }
}
