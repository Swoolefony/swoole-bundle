<?php

declare(strict_types=1);

namespace Swoolefony\SwooleBundle\Kernel;

use Swoolefony\SwooleBundle\Server\ServerInterface;

interface SwooleKernelInterface
{
    public function useSwooleServer(?ServerInterface $server): static;

    public function getSwooleServer(): ?ServerInterface;
}
