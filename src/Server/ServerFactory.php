<?php

declare(strict_types=1);

namespace Swoolefony\SwooleBundle\Server;

use RuntimeException;
use Swoolefony\SwooleBundle\Runtime\Mode;
use Symfony\Component\HttpKernel\HttpKernelInterface;

class ServerFactory
{
    public function __construct(private readonly HandlerFactory $handlerFactory)
    {
    }

    public function makeFromOptions(
        Options $options,
        object $app,
    ): ServerInterface {
        return match ($options->getMode()) {
            Mode::Http => $this->makeHttpServer(
                $options,
                $app,
            ),
            Mode::Websocket => new Server(
                options: $options,
                handlerFactory: $this->handlerFactory,
                app: $app,
            ),
        };
    }

    private function makeHttpServer(
        Options $options,
        object $app,
    ): Server {
        if (!$app instanceof HttpKernelInterface) {
            throw new RuntimeException(sprintf(
                'Class of "%s" not supported for mode HTTP. Must be an instance of %s.',
                get_class($app),
                HttpKernelInterface::class,
            ));
        }

        return new Server(
            options: $options,
            handlerFactory: $this->handlerFactory,
            app: $app,
        );
    }
}
