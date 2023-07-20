<?php

declare(strict_types=1);

namespace Swoolefony\SwooleBundle\Tests\Unit\Server;

use PHPUnit\Framework\Attributes\CoversClass;
use Swoolefony\SwooleBundle\Runtime\Mode;
use Swoolefony\SwooleBundle\Server\Options;
use Swoolefony\SwooleBundle\Tests\Unit\TestCase;

#[CoversClass(Options::class)]
class OptionsTest extends TestCase
{
    public function testItHasTheExpectedDefaults(): void
    {
        $subject = new Options();

        $this->assertSame(
            '0.0.0.0',
            $subject->getIpAddress(),
        );
        $this->assertSame(
            Mode::Http,
            $subject->getMode(),
        );
        $this->assertSame(
            80,
            $subject->getPort()
        );
    }

    public function testItSetsAndGetsThePort(): void
    {
        $subject = new Options(port: 8080);

        $this->assertSame(
            8080,
            $subject->getPort(),
        );
    }

    public function testItSetsAndGetsTheIp(): void
    {
        $subject = new Options(ip: '127.0.0.1');

        $this->assertSame(
            '127.0.0.1',
            $subject->getIpAddress(),
        );
    }

    public function testItSetsAndGetsTheMode(): void
    {
        $subject = new Options(mode: Mode::Websocket);

        $this->assertSame(
            Mode::Websocket,
            $subject->getMode(),
        );
    }
}
