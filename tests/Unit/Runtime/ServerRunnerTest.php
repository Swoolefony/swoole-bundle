<?php

declare(strict_types=1);

namespace Swoolefony\SwooleBundle\Tests\Unit\Runtime;

use Mockery;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use Swoolefony\SwooleBundle\Kernel\SwooleKernel;
use Swoolefony\SwooleBundle\Runtime\ServerRunner;
use Swoolefony\SwooleBundle\Server\Factory;
use Swoolefony\SwooleBundle\Server\Options;
use Swoolefony\SwooleBundle\Server\ServerInterface;
use Swoolefony\SwooleBundle\Tests\Unit\TestCase;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\Kernel;

#[CoversClass(ServerRunner::class)]
class ServerRunnerTest extends TestCase
{
    private ServerRunner $subject;

    private Options $options;

    private HttpKernelInterface&MockInterface $mockKernel;

    private Factory&MockInterface $mockFactory;

    public function setUp(): void
    {
        parent::setUp();

        /** @var Kernel&MockInterface $mockKernel */
        $mockKernel = Mockery::mock(Kernel::class);
        $mockKernel->allows('getContainer->set');

        /** @var Factory&MockInterface $mockFactory */
        $mockFactory = Mockery::mock(Factory::class);

        $this->mockKernel = $mockKernel;
        $this->mockFactory = $mockFactory;

        $this->options = new Options();
        $this->subject = new ServerRunner(
            serverOptions: $this->options,
            kernel: $mockKernel,
            serverFactory: $mockFactory,
        );
    }

    public function testItRunsTheServer(): void
    {
        /** @var ServerInterface&MockInterface $server */
        $server = Mockery::mock(ServerInterface::class);

        $this->mockFactory
            ->shouldReceive('makeFromOptions')
            ->with($this->options, $this->mockKernel)
            ->andReturn($server);

        $server
            ->shouldReceive('run')
            ->andReturnUndefined();

        $this->assertEquals(
            0,
            $this->subject->run()
        );

        $server->shouldHaveReceived('run');
    }

    public function testItInjectsTheSwooleServerIfItCan(): void
    {
        /** @var ServerInterface&MockInterface $server */
        $server = Mockery::mock(ServerInterface::class);

        $this->mockFactory
            ->shouldReceive('makeFromOptions')
            ->andReturn($server);
        $server
            ->shouldReceive('run')
            ->andReturnUndefined();

        /** @var SwooleKernel&MockInterface $swooleKernel */
        $swooleKernel = Mockery::mock(SwooleKernel::class);
        $swooleKernel->allows('getContainer->set');

        $swooleKernel
            ->shouldReceive('useSwooleServer')
            ->with($server)
            ->andReturnSelf();

        $subject = new ServerRunner(
            serverOptions: $this->options,
            kernel: $swooleKernel,
            serverFactory: $this->mockFactory,
        );

        $subject->run();

        $swooleKernel->shouldHaveReceived('useSwooleServer');
    }
}
