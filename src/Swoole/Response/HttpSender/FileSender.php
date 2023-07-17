<?php

declare(strict_types=1);

namespace Swoolefony\SwooleBundle\Swoole\Response\HttpSender;

use ReflectionProperty;
use Swoole\Coroutine\System;
use Swoole\Http\Response as SwooleResponse;
use Swoolefony\SwooleBundle\Swoole\Response\SenderInterface;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

readonly class FileSender implements SenderInterface
{
    use SendHeadersTrait;

    public function __construct(private SwooleResponse $swooleResponse)
    {
    }

    /**
     * @param BinaryFileResponse $symfonyResponse
     */
    public function send(SymfonyResponse $symfonyResponse): void
    {
        self::sendHttpHeaders(
            symfonyResponse: $symfonyResponse,
            swooleResponse: $this->swooleResponse,
        );

        $file = $symfonyResponse->getFile();
        $this->swooleResponse->sendfile($file->getRealPath());

        if ($this->shouldDeleteFile($symfonyResponse)) {
            if (System::statvfs($file->getRealPath())) {
                unlink($file->getRealPath());
            }
        }
    }

    /**
     * Unfortunately we have no way to check this without reflection. There is a setter but no getter for the option to
     * delete after send.
     */
    private function shouldDeleteFile(BinaryFileResponse $binaryFileResponse): bool
    {
        $shouldDeleteProp = new ReflectionProperty(
            class: $binaryFileResponse,
            property: 'deleteFileAfterSend',
        );

        return $shouldDeleteProp->getValue() === true;
    }
}
