<?php

declare(strict_types=1);

namespace Swoolefony\SwooleBundle\Runtime\Runner;

use Swoolefony\SwooleBundle\Server\Http\HttpServerFactory;
use Swoolefony\SwooleBundle\Server\Http\Options;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\Runtime\RunnerInterface;

readonly class HttpRunner implements RunnerInterface
{
    use SwooleKernelInjectorTrait;

    public function __construct(
        private HttpServerFactory $httpServerFactory,
        private Options $httpServerOptions,
        private HttpKernelInterface $kernel,
    ) {
    }

    public function run(): int
    {
        $server = $this->httpServerFactory->makeFromOptions($this->httpServerOptions);

        $this->injectSwooleInKernelIfSupported(
            $this->kernel,
            $server,
        );

        $server->run();

        return 0;
    }
}
