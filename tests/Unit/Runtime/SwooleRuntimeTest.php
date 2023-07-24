<?php

declare(strict_types=1);

namespace Swoolefony\SwooleBundle\Tests\Unit\Runtime;

use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use Swoolefony\SwooleBundle\Runtime\Mode;
use Swoolefony\SwooleBundle\Runtime\ServerRunner;
use Swoolefony\SwooleBundle\Runtime\SwooleRuntime;
use Swoolefony\SwooleBundle\Tests\Unit\TestCase;
use Symfony\Component\HttpKernel\HttpKernelInterface;

#[CoversClass(SwooleRuntime::class)]
class SwooleRuntimeTest extends TestCase
{
    public function testItConstructsTheRunnerWithDefaultOptions(): void
    {
        $subject = new SwooleRuntime();

        /** @var HttpKernelInterface&MockInterface $mockKernel */
        $mockKernel = \Mockery::mock(HttpKernelInterface::class);

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
