<?php

declare(strict_types=1);

namespace Swoolefony\SwooleBundle\Swoole\Response;

use Swoole\Http\Response as SwooleResponse;
use Swoolefony\SwooleBundle\Swoole\Response\Sender\FileSender;
use Swoolefony\SwooleBundle\Swoole\Response\Sender\StandardSender;
use Swoolefony\SwooleBundle\Swoole\Response\Sender\StreamedSender;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

readonly class HttpSender
{
    public function send(
        SwooleResponse $swooleResponse,
        SymfonyResponse $symfonyResponse,
    ): void {
        $sender = match (true) {
            $symfonyResponse instanceof BinaryFileResponse => new FileSender($swooleResponse),
            $symfonyResponse instanceof StreamedResponse => new StreamedSender($swooleResponse),
            default => new StandardSender($swooleResponse),
        };

        $sender->send($symfonyResponse);
    }
}
