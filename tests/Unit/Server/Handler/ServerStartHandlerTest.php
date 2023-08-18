<?php

declare(strict_types=1);

namespace Swoolefony\SwooleBundle\Tests\Unit\Server\Handler;

use Mockery;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use Psr\Cache\CacheItemInterface;
use Psr\Cache\CacheItemPoolInterface;
use Swoole\Server;
use Swoolefony\SwooleBundle\Server\CacheKey;
use Swoolefony\SwooleBundle\Server\Handler\ServerStartHandler;
use Swoolefony\SwooleBundle\Tests\Unit\TestCase;

#[CoversClass(ServerStartHandler::class)]
class ServerStartHandlerTest extends TestCase
{
    public function testItRecordsTheServerPid(): void
    {
        /** @var Server&MockInterface $mockServer */
        $mockServer = Mockery::spy(Server::class);
        /** @var CacheItemPoolInterface&MockInterface $mockCache */
        $mockCache = Mockery::mock(CacheItemPoolInterface::class);
        /** @var CacheItemInterface&MockInterface $mockItem */
        $mockItem = Mockery::mock(CacheItemInterface::class);

        $mockCache->allows([
            'deleteItem' => true,
            'getItem' => $mockItem,
            'save' => true,
        ]);
        $mockItem->allows([
           'set' => $mockItem,
        ]);
        $mockServer->allows([
            'getMasterPid' => 7,
        ]);

        (new ServerStartHandler(
            cache: $mockCache,
            shouldRegisterTasks: false,
        ))->__invoke($mockServer);

        $mockCache
            ->shouldHaveReceived(
                'deleteItem',
                [CacheKey::ServerPid->value]
            );
        $mockCache
            ->shouldHaveReceived(
                'save',
                [$mockItem]
            );
    }
}
