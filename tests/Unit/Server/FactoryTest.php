<?php

declare(strict_types=1);

namespace Swoolefony\SwooleBundle\Tests\Unit\Server;

use Mockery;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use Swoolefony\SwooleBundle\Runtime\Mode;
use Swoolefony\SwooleBundle\Server\HandlerFactory;
use Swoolefony\SwooleBundle\Server\ServerFactory;
use Swoolefony\SwooleBundle\Server\Options;
use Swoolefony\SwooleBundle\Server\Server;
use Swoolefony\SwooleBundle\Server\Type\WebsocketServer;
use Swoolefony\SwooleBundle\Tests\Unit\TestCase;
use Symfony\Component\HttpKernel\HttpKernelInterface;

#[CoversClass(ServerFactory::class)]
class FactoryTest extends TestCase
{
    private ServerFactory $subject;

    public function setUp(): void
    {
        parent::setUp();

        /** @var HandlerFactory&MockInterface $mockHandlerFactory */
        $mockHandlerFactory = Mockery::mock(HandlerFactory::class);
        $this->subject = new ServerFactory($mockHandlerFactory);
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
            Server::class,
            $result
        );
    }
}
