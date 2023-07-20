<?php

declare(strict_types=1);

namespace Swoolefony\SwooleBundle\Tests\Unit\Server\Handler;

use Mockery;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use Swoole\Http\Request;
use Swoole\Http\Response;
use Swoolefony\SwooleBundle\Runtime\Mode;
use Swoolefony\SwooleBundle\Server\Handler\HttpRequestHandler;
use Swoolefony\SwooleBundle\Swoole\Request\Converter;
use Swoolefony\SwooleBundle\Swoole\Response\HttpSender;
use Swoolefony\SwooleBundle\Tests\Unit\TestCase;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;

#[CoversClass(HttpRequestHandler::class)]
class HttpRequestHandlerTest extends TestCase
{
    private HttpRequestHandler $subject;

    private HttpKernelInterface&MockInterface $mockHttpKernel;

    private HttpSender&MockInterface $mockHttpSender;

    private Converter&MockInterface $mockConverter;

    public function setUp(): void
    {
        parent::setUp();

        /** @var HttpKernelInterface&MockInterface $mockHttpKernel */
        $mockHttpKernel = Mockery::mock(HttpKernelInterface::class);
        /** @var Converter&MockInterface $mockRequestConverter */
        $mockRequestConverter = Mockery::mock(Converter::class);
        /** @var HttpSender&MockInterface $mockHttpSender */
        $mockHttpSender = Mockery::mock(HttpSender::class);

        $this->mockHttpKernel = $mockHttpKernel;
        $this->mockHttpSender = $mockHttpSender;
        $this->mockConverter = $mockRequestConverter;

        $this->subject = new HttpRequestHandler(
            $mockHttpKernel,
            Mode::Http,
            $mockRequestConverter,
            $mockHttpSender,
        );
    }

    public function testItHandlesTheRequestWithTheResponse(): void
    {
        /** @var Response&MockInterface $response */
        $response = Mockery::mock(Response::class);
        /** @var Request&MockInterface $request */
        $request = Mockery::mock(Request::class);

        $kernelRequest = SymfonyRequest::create('/foo');
        $kernelResponse = new SymfonyResponse('oh, hai mark');

        $this->mockConverter
            ->shouldReceive('httpToSymfony')
            ->with($request, Mode::Http)
            ->andReturn($kernelRequest);

        $this->mockHttpKernel
            ->shouldReceive('handle')
            ->with($kernelRequest)
            ->andReturn($kernelResponse);

        $this->mockHttpSender
            ->shouldReceive('send')
            ->with($response, $kernelResponse);

        $this->subject->__invoke(
            $request,
            $response
        );

        $this->mockHttpSender->shouldHaveReceived('send');
    }
}
