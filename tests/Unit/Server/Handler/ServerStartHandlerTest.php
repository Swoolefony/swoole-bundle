<?php

declare(strict_types=1);

namespace Swoolefony\SwooleBundle\Tests\Unit\Server\Handler;

use Mockery;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use Swoole\Server;
use Swoolefony\SwooleBundle\Server\CacheKey;
use Swoolefony\SwooleBundle\Server\Handler\ServerStartHandler;
use Swoolefony\SwooleBundle\Tests\Unit\TestCase;
use Symfony\Contracts\Cache\CacheInterface;

#[CoversClass(ServerStartHandler::class)]
class ServerStartHandlerTest extends TestCase
{
    public function testItRecordsTheServerPid(): void
    {
        /** @var Server&MockInterface $mockServer */
        $mockServer = Mockery::spy(Server::class);
        /** @var CacheInterface&MockInterface $mockCache */
        $mockCache = Mockery::mock(CacheInterface::class);

        $mockCache->allows([
           'delete' => true,
           'get' => null,
        ]);
        $mockServer->allows([
            'getMasterPid' => 7,
        ]);

        (new ServerStartHandler($mockCache))->__invoke($mockServer);

        $mockCache
            ->shouldHaveReceived(
                'delete',
                [CacheKey::ServerPid->value]
            );
    }
}
