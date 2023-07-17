<?php

declare(strict_types=1);

namespace Swoolefony\SwooleBundle\Swoole\Response;

use Symfony\Component\HttpFoundation\Response;

interface SenderInterface
{
    public function send(Response $symfonyResponse): void;
}
