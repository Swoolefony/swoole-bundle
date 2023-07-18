<?php

declare(strict_types=1);

namespace Swoolefony\SwooleBundle\Runtime;

use Swoolefony\SwooleBundle\Kernel\SwooleKernelInterface;
use Swoolefony\SwooleBundle\Server\Factory;
use Swoolefony\SwooleBundle\Server\Options;
use Swoolefony\SwooleBundle\Server\ServerInterface;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\Runtime\RunnerInterface;

readonly class ServerRunner implements RunnerInterface
{
    public function __construct(
        private Options $serverOptions,
        private HttpKernelInterface $kernel,
        private Factory $serverFactory = new Factory(),
    ) {
    }

    public function run(): int
    {
        $server = $this->serverFactory->makeFromOptions(
            $this->serverOptions,
            $this->kernel,
        );

        $this->injectSwooleInKernelIfSupported(
            $this->kernel,
            $server,
        );

        $server->run();

        return 0;
    }

    private function injectSwooleInKernelIfSupported(
        HttpKernelInterface $kernel,
        ServerInterface $server,
    ): void {
        if ($kernel instanceof SwooleKernelInterface) {
            $kernel->useSwooleServer($server);
        }
    }
}
