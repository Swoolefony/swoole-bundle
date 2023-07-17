<?php

declare(strict_types=1);

namespace Swoolefony\SwooleBundle\Kernel;

use Swoolefony\SwooleBundle\Server\ServerInterface;

trait SwooleKernelTrait
{
    private ?ServerInterface $server = null;

    public function useSwooleServer(?ServerInterface $server): static
    {
        $this->server = $server;

        return $this;
    }

    public function getSwooleServer(): ?ServerInterface
    {
        return $this->server;
    }

    protected function initializeContainer(): void
    {
        parent::initializeContainer();

        if ($this->server) {
            $this->container->set(
                'swoolefony.server',
                $this->server,
            );
        }
    }

    public function __wakeup()
    {
        self::__construct(
            $this->environment,
            $this->debug,
        );

        $this->useSwooleServer($this->server);
    }
}
