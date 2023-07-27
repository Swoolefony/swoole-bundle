<?php

declare(strict_types=1);

namespace Swoolefony\SwooleBundle\Tests\Unit\Server;

use Mockery;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use Swoolefony\SwooleBundle\Runtime\Mode;
use Swoolefony\SwooleBundle\Server\Factory;
use Swoolefony\SwooleBundle\Server\Options;
use Swoolefony\SwooleBundle\Server\Type\HttpServer;
use Swoolefony\SwooleBundle\Server\Type\WebsocketServer;
use Swoolefony\SwooleBundle\Tests\Unit\TestCase;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Contracts\Cache\CacheInterface;

#[CoversClass(Factory::class)]
class FactoryTest extends TestCase
{
    private Factory $subject;

    public function setUp(): void
    {
        parent::setUp();

        /** @var CacheInterface&MockInterface $mockCache */
        $mockCache = Mockery::mock(CacheInterface::class);
        $this->subject = new Factory($mockCache);
    }

    public function testItMakesTheHttpServer(): void
    {
        /** @var HttpKernelInterface&MockInterface $mockKernel */
        $mockKernel = Mockery::mock(HttpKernelInterface::class);

        $result = $this->subject->makeFromOptions(
            new Options(),
            $mockKernel,
        );

        $this->assertInstanceOf(
            HttpServer::class,
            $result
        );
    }

    public function testItMakesTheWebsocketServer(): void
    {
        /** @var HttpKernelInterface&MockInterface $mockKernel */
        $mockKernel = Mockery::mock(HttpKernelInterface::class);

        $result = $this->subject->makeFromOptions(
            new Options(mode: Mode::Websocket),
            $mockKernel,
        );

        $this->assertInstanceOf(
            WebsocketServer::class,
            $result
        );
    }
}
