<?php

declare(strict_types=1);

namespace Swoolefony\SwooleBundle\Swoole\Response\Sender;

use Swoole\Http\Response as SwooleResponse;
use Swoolefony\SwooleBundle\Swoole\Response\SenderInterface;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

readonly class StreamedSender implements SenderInterface
{
    use SendHeadersTrait;

    /**
     * Per the docs, a default of 1 turns into 4096. Just defining that explicitly rather than using our own value.
     */
    private const CHUNK_SIZE = 4096;

    public function __construct(private SwooleResponse $swooleResponse)
    {
    }

    /**
     * @param StreamedResponse $symfonyResponse
     */
    public function send(SymfonyResponse $symfonyResponse): void
    {
        self::sendHttpHeaders(
            symfonyResponse: $symfonyResponse,
            swooleResponse: $this->swooleResponse,
        );

        $this->streamContent($symfonyResponse);

        $this->swooleResponse->end();
    }

    private function sendOutput(string $output): string
    {
        $this->swooleResponse->write($output);

        return '';
    }

    private function streamContent(StreamedResponse $symfonyResponse): void
    {
        ob_start(
            callback: $this->sendOutput(...),
            chunk_size: self::CHUNK_SIZE,
        );

        $symfonyResponse->sendContent();

        ob_end_clean();
    }
}
