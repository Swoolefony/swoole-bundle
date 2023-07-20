<?php

declare(strict_types=1);

namespace Swoolefony\SwooleBundle\Swoole\Response\Sender;

use Symfony\Component\HttpFoundation\Response as SymfonyResponse;
use Swoole\Http\Response as SwooleResponse;

trait SendHeadersTrait
{
    private static function sendHttpHeaders(
        SymfonyResponse $symfonyResponse,
        SwooleResponse $swooleResponse,
    ): void {
        foreach ($symfonyResponse->headers->all() as $header => $values) {
            $swooleResponse->header(
                $header,
                $values,
            );
        }
    }
}
