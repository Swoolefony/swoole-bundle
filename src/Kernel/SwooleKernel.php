<?php

declare(strict_types=1);

namespace Swoolefony\SwooleBundle\Kernel;

use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;

class SwooleKernel extends BaseKernel implements SwooleKernelInterface
{
    use MicroKernelTrait;
    use SwooleKernelTrait;
}
