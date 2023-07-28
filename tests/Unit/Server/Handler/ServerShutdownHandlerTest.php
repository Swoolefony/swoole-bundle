<?php

declare(strict_types=1);

namespace Swoolefony\SwooleBundle\Tests\Unit\Server\Handler;

use Mockery;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use Swoole\Server;
use Swoolefony\SwooleBundle\Server\CacheKey;
use Swoolefony\SwooleBundle\Server\Handler\ServerShutdownHandler;
use Swoolefony\SwooleBundle\Tests\Unit\TestCase;
use Symfony\Contracts\Cache\CacheInterface;

#[CoversClass(ServerShutdownHandler::class)]
class ServerShutdownHandlerTest extends TestCase
{
    public function testItRemovesTheServerPidFromCache(): void
    {
        /** @var Server&MockInterface $mockServer */
        $mockServer = Mockery::spy(Server::class);
        /** @var CacheInterface&MockInterface $mockCache */
        $mockCache = Mockery::mock(CacheInterface::class);

        $mockCache->allows([
            'delete' => true,
        ]);
        $mockServer->allows([
            'getMasterPid' => 7,
        ]);

        (new ServerShutdownHandler($mockCache))->__invoke($mockServer);

        $mockCache
            ->shouldHaveReceived(
                'delete',
                [CacheKey::ServerPid->value]
            );
    }
}
