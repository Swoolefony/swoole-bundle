<?php

declare(strict_types=1);

namespace Swoolefony\SwooleBundle\Runtime;

use Swoolefony\SwooleBundle\Kernel\SwooleKernelInterface;
use Swoolefony\SwooleBundle\Server\ServerFactory;
use Swoolefony\SwooleBundle\Server\Options;
use Swoolefony\SwooleBundle\Server\ServerInterface;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Runtime\RunnerInterface;

readonly class ServerRunner implements RunnerInterface
{
    public function __construct(
        private Options $serverOptions,
        private Kernel $kernel,
        private ServerFactory $serverFactory,
    ) {
    }

    public function getOptions(): Options
    {
        return $this->serverOptions;
    }

    public function run(): int
    {
        $server = $this->serverFactory->makeFromOptions(
            $this->serverOptions,
            $this->kernel,
        );

        $this->kernel
            ->getContainer()
            ->set(
                id: ServerInterface::class,
                service: $server,
            );

        $this->injectSwooleInKernelIfSupported(
            $this->kernel,
            $server,
        );

        $server->run();

        return 0;
    }

    private function injectSwooleInKernelIfSupported(
        Kernel $kernel,
        ServerInterface $server,
    ): void {
        if ($kernel instanceof SwooleKernelInterface) {
            $kernel->useSwooleServer($server);
        }
    }
}
