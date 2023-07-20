<?php

declare(strict_types=1);

namespace Swoolefony\SwooleBundle\Tests\Unit\Kernel;

use Mockery;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use Swoolefony\SwooleBundle\Kernel\SwooleKernel;
use Swoolefony\SwooleBundle\Server\ServerInterface;
use Swoolefony\SwooleBundle\Tests\Unit\TestCase;

#[CoversClass(SwooleKernel::class)]
class SwooleKernelTest extends TestCase
{
    public function testItCanSetAndGetTheSwooleServer(): void
    {
        $subject = new SwooleKernel(
            'dev',
            true
        );

        /** @var ServerInterface&MockInterface $server */
        $server = Mockery::mock(ServerInterface::class);
        $subject->useSwooleServer($server);

        $this->assertSame(
            $server,
            $subject->getSwooleServer()
        );
    }
}
