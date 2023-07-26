<?php

declare(strict_types=1);

namespace Swoolefony\SwooleBundle\Server;

use RuntimeException;
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
            Mode::Websocket => new WebsocketServer($options),
        };
    }

    private function makeHttpServer(
        Options $options,
        object $app,
    ): HttpServer {
        if (!$app instanceof HttpKernelInterface) {
            throw new RuntimeException(sprintf(
                'Class of "%s" not supported for mode HTTP. Must be an instance of %s.',
                get_class($app),
                HttpKernelInterface::class,
            ));
        }

        return new HttpServer(
            options: $options,
            requestHandler: new HttpRequestHandler($app),
        );
    }
}
