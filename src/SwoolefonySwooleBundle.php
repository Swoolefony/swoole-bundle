<?php

declare(strict_types=1);

namespace Swoolefony\SwooleBundle;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;
use function dirname;

final class SwoolefonySwooleBundle extends AbstractBundle
{
    /**
     * @inheritDoc
     */
    public function getPath(): string
    {
        return dirname(__DIR__);
    }

    /**
     * @inheritDoc
     *
     * @param array<string, mixed> $config
     */
    public function loadExtension(
        array $config,
        ContainerConfigurator $container,
        ContainerBuilder $builder
    ): void {
        $container->import('../config/services.php');
    }
}
