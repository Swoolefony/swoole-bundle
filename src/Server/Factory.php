<?php

declare(strict_types=1);

namespace Swoolefony\SwooleBundle\Server;

use Swoole\Http\Server as SwooleHttpServer;
use Swoole\WebSocket\Server as SwooleWebsocketServer;
use Swoolefony\SwooleBundle\Runtime\Mode;
use Swoolefony\SwooleBundle\Server\Handler\HttpRequestHandler;
use Swoolefony\SwooleBundle\Server\Type\HttpServer as HttpServer;
use Swoolefony\SwooleBundle\Server\Type\WebsocketServer;
use Symfony\Component\HttpKernel\HttpKernelInterface;

class Factory
{
    public function makeFromOptions(
        Options $options,
        object $app,
    ): ServerInterface {
        return match ($options->getMode()) {
            Mode::Http => $this->makeHttpServer(
                $options,
                $app,
            ),
            Mode::Websocket => $this->makeWebsocketServer(
                $options,
            ),
        };
    }

    private function makeHttpServer(
        Options $options,
        object $app,
    ): HttpServer {
        if (!$app instanceof HttpKernelInterface) {
            throw new \RuntimeException(sprintf(
                'Class of "%s" not supported for mode HTTP. Must be an instance of %s.',
                get_class($app),
                HttpKernelInterface::class,
            ));
        }

        $server = new SwooleHttpServer(
            $options->getIpAddress(),
            $options->getPort(),
        );

        $server->set([
            'hook_flags' => SWOOLE_HOOK_ALL,
        ]);

        $server->on(
            'request',
            new HttpRequestHandler($app)
        );

        return new HttpServer($server);
    }

    private function makeWebsocketServer(Options $options): WebsocketServer
    {
        $server = new SwooleWebsocketServer(
            $options->getIpAddress(),
            $options->getPort(),
        );

        $server->set([
            'hook_flags' => SWOOLE_HOOK_ALL,
        ]);

        return new WebsocketServer($server);
    }
}
