<?php

declare(strict_types=1);

namespace Swoolefony\SwooleBundle\Server\Http;

use Swoole\Http\Server;
use Swoolefony\SwooleBundle\Server\Http\Server as HttpServer;

class HttpServerFactory
{
    public function makeFromOptions(Options $options): HttpServer
    {
        $swooleServer = new Server(
            $options->getIpAddress(),
            $options->getPort(),
        );
        $swooleServer->set(['hook_flags' => SWOOLE_HOOK_ALL]);

        $swooleServer->on(
            'request',
            $options->getRequestHandler()
        );

        return new HttpServer($swooleServer);
    }
}
