<?php

declare(strict_types=1);

namespace Swoolefony\SwooleBundle\Tests\Unit\Swoole\Request;

use Mockery;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use Swoole\Http\Request;
use Swoolefony\SwooleBundle\Runtime\Mode;
use Swoolefony\SwooleBundle\Swoole\Request\Attribute;
use Swoolefony\SwooleBundle\Swoole\Request\Converter;
use Swoolefony\SwooleBundle\Tests\Unit\TestCase;

#[CoversClass(Converter::class)]
final class ConverterTest extends TestCase
{
    private Converter $subject;

    public function setUp(): void
    {
        parent::setUp();

        $this->subject = new Converter();
    }

    public function testItSetsAttributesForTheRequest(): void
    {
        $swooleRequest = self::makeMockSwooleRequest();

        $symfonyRequest = $this->subject->httpToSymfony(
            $swooleRequest,
            Mode::Http,
        );

        $this->assertSame(
            'http',
            $symfonyRequest->attributes->get(Attribute::Mode->value),
        );
        $this->assertSame(
            1,
            $symfonyRequest->attributes->get(Attribute::Id->value),
        );
    }

    private static function makeMockSwooleRequest(
        int $fd = 1,
        string $path = '/foo',
        string $content = 'bar',
        string $method = 'GET',
    ): Request&MockInterface {
        $swooleRequest = Mockery::mock(Request::class);
        $swooleRequest->fd = $fd;
        $swooleRequest->server = ['request_uri' => $path];

        $swooleRequest->allows([
            'getContent' => $content,
            'getMethod' => $method,
        ]);

        return $swooleRequest;
    }
}
