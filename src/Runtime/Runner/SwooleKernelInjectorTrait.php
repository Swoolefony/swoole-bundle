<?php

declare(strict_types=1);

namespace Swoolefony\SwooleBundle\Runtime\Runner;

use Swoolefony\SwooleBundle\Kernel\SwooleKernelInterface;
use Swoolefony\SwooleBundle\Server\ServerInterface;
use Symfony\Component\HttpKernel\HttpKernelInterface;

trait SwooleKernelInjectorTrait
{
    private function injectSwooleInKernelIfSupported(
        HttpKernelInterface $kernel,
        ServerInterface $server,
    ): void {
        if ($kernel instanceof SwooleKernelInterface) {
            $kernel->useSwooleServer($server);
        }
    }
}
