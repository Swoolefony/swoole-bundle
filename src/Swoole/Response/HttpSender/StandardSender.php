<?php

declare(strict_types=1);

namespace Swoolefony\SwooleBundle\Swoole\Response\Sender;

use Swoole\Http\Response as SwooleResponse;
use Swoolefony\SwooleBundle\Swoole\Response\SenderInterface;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

readonly class StandardSender implements SenderInterface
{
    use SendHeadersTrait;

    public function __construct(private SwooleResponse $swooleResponse)
    {
    }

    public function send(SymfonyResponse $symfonyResponse): void
    {
        self::sendHttpHeaders(
            symfonyResponse: $symfonyResponse,
            swooleResponse: $this->swooleResponse,
        );

        $content = $symfonyResponse->getContent();

        $this->swooleResponse->end($content === false ? null : $content);
    }
}
