<?php

declare(strict_types=1);

namespace Swoolefony\SwooleBundle\Tests\Unit\Swoole\Response\Sender;

use Mockery;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use Swoole\Http\Response as SwooleResponse;
use Swoolefony\SwooleBundle\Swoole\Response\Sender\StandardSender;
use Swoolefony\SwooleBundle\Tests\Unit\TestCase;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

#[CoversClass(StandardSender::class)]
class StandardSenderTest extends TestCase
{
    private StandardSender $subject;

    private SwooleResponse&MockInterface $mockResponse;

    public function setUp(): void
    {
        parent::setUp();

        $this->mockResponse = Mockery::mock(SwooleResponse::class);
        $this->subject = new StandardSender($this->mockResponse);
    }

    public function testItSendsTheResponse(): void
    {
        $this->mockResponse->allows([
            'header' => true,
            'end' => true,
        ]);

        $response = new SymfonyResponse('foo');
        $response->headers->set('Stuff', ['And', 'Things']);
        $response->headers->set('More', ['Stuff']);

        $this->subject->send($response);

        $this->mockResponse
            ->shouldHaveReceived(
                'header',
                ['Stuff', ['And', 'Things']]
            );
        $this->mockResponse
            ->shouldHaveReceived(
                'header',
                ['More', ['Stuff']]
            );
        $this->mockResponse
            ->shouldHaveReceived(
                'end',
                ['foo']
            );
    }
}
