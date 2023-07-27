<?php

declare(strict_types=1);

namespace Swoolefony\SwooleBundle\Tests\Unit\Runtime;

use Mockery;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use Swoolefony\SwooleBundle\Runtime\Mode;
use Swoolefony\SwooleBundle\Runtime\ServerRunner;
use Swoolefony\SwooleBundle\Runtime\SwooleRuntime;
use Swoolefony\SwooleBundle\Server\ServerFactory;
use Swoolefony\SwooleBundle\Tests\Unit\TestCase;
use Symfony\Component\HttpKernel\Kernel;

#[CoversClass(SwooleRuntime::class)]
class SwooleRuntimeTest extends TestCase
{
    public function testItConstructsTheRunnerWithDefaultOptions(): void
    {
        $subject = new SwooleRuntime();

        /** @var Kernel&MockInterface $mockKernel */
        $mockKernel = Mockery::mock(Kernel::class);
        $mockKernel->allows('boot');
        $mockKernel->shouldReceive('getContainer->get')
            ->with(ServerFactory::class)
            ->andReturn(Mockery::mock(ServerFactory::class));

        $result = $subject->getRunner($mockKernel);

        $this->assertInstanceOf(
            ServerRunner::class,
            $result
        );
        $options = $result->getOptions();

        $this->assertSame(
            '0.0.0.0',
            $options->getIpAddress()
        );
        $this->assertSame(
            Mode::Http,
            $options->getMode()
        );
        $this->assertSame(
            80,
            $options->getPort()
        );
        $this->assertSame(
            80,
            $options->getPort()
        );
        $this->assertNull($options->getSslCertFile());
        $this->assertNull($options->getSslKeyFile());
    }
}
