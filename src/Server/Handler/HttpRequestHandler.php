<?php

declare(strict_types=1);

namespace Swoolefony\SwooleBundle\Server\Handler;

use Swoolefony\SwooleBundle\Runtime\Mode;
use Swoolefony\SwooleBundle\Swoole\Request\Converter;
use Swoolefony\SwooleBundle\Swoole\Response\HttpSender;
use Swoole\Http\Request;
use Swoole\Http\Response;
use Symfony\Component\HttpKernel\HttpKernelInterface;

readonly class HttpRequestHandler
{
    public function __construct(
        private HttpKernelInterface $httpKernel,
        private Mode $mode = Mode::Http,
        private Converter $requestConverter = new Converter(),
        private HttpSender $httpSender = new HttpSender(),
    ) {
    }

    public function __invoke(
        Request $request,
        Response $response,
    ): void {
        $symfonyRequest = $this->requestConverter->httpToSymfony(
            request: $request,
            mode: $this->mode,
        );

        $symfonyResponse = $this->httpKernel->handle($symfonyRequest);

        $this->httpSender->send(
            swooleResponse: $response,
            symfonyResponse: $symfonyResponse,
        );
    }
}
