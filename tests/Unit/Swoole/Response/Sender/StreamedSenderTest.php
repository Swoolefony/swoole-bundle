<?php

declare(strict_types=1);

namespace Swoolefony\SwooleBundle\Tests\Unit\Swoole\Response\Sender;

use Mockery;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use Swoole\Http\Response as SwooleResponse;
use Swoolefony\SwooleBundle\Swoole\Response\Sender\StreamedSender;
use Swoolefony\SwooleBundle\Tests\Unit\TestCase;
use Symfony\Component\HttpFoundation\StreamedResponse;

#[CoversClass(StreamedSender::class)]
class StreamedSenderTest extends TestCase
{
    private StreamedSender $subject;

    private SwooleResponse&MockInterface $mockResponse;

    public function setUp(): void
    {
        parent::setUp();

        /** @var SwooleResponse&MockInterface $mockResponse */
        $mockResponse = Mockery::mock(SwooleResponse::class);
        $this->mockResponse = $mockResponse;
        $this->subject = new StreamedSender($this->mockResponse);
    }

    public function testItSendsTheStream(): void
    {
        $streamedResponse = new StreamedResponse(function () {
            $timesCalled = 0;

            while ($timesCalled < 3) {
                echo str_repeat('f', 4096);

                $timesCalled++;
            }
        });

        $this->mockResponse->allows([
            'header' => true,
            'end' => true,
            'write' => true,
        ]);

        $this->subject->send($streamedResponse);

        $this->mockResponse->shouldHaveReceived('write');
        $this->mockResponse->shouldHaveReceived('end');
    }
}
