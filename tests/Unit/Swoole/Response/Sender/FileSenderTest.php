<?php

declare(strict_types=1);

namespace Swoolefony\SwooleBundle\Tests\Unit\Swoole\Response\Sender;

use Mockery;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use SplFileInfo;
use Swoole\Http\Response;
use Swoolefony\SwooleBundle\Swoole\Response\Sender\FileSender;
use Swoolefony\SwooleBundle\Tests\Unit\TestCase;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

#[CoversClass(FileSender::class)]
class FileSenderTest extends TestCase
{
    private const TEMP_FILE = '/tmp/foo.txt';

    private FileSender $subject;

    private Response&MockInterface $mockResponse;

    public function setUp(): void
    {
        parent::setUp();

        touch(self::TEMP_FILE);

        /** @var Response&MockInterface $mockResponse */
        $mockResponse = Mockery::mock(Response::class);
        $this->mockResponse = $mockResponse;
        $this->subject = new FileSender($this->mockResponse);
    }

    public function tearDown(): void
    {
        parent::tearDown();

        unlink(self::TEMP_FILE);
    }

    public function testItSendsTheFile(): void
    {
        $file = new SplFileInfo(self::TEMP_FILE);
        $response = new BinaryFileResponse($file);

        $this->mockResponse->allows([
            'header' => true,
            'sendFile' => true,
        ]);

        $this->mockResponse->shouldReceive('sendfile')
            ->with(Mockery::on(
                fn(string $path) => str_ends_with($path, self::TEMP_FILE)
            ));

        $this->subject->send($response);

        $this->mockResponse->shouldHaveReceived('sendfile');
    }
}
