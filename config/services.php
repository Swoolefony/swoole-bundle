<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Swoolefony\SwooleBundle\Server\Factory;
use Swoolefony\SwooleBundle\Server\ServerInterface;
use Symfony\Contracts\Cache\CacheInterface;

return function(ContainerConfigurator $container): void {
    $services = $container->services()
        ->defaults()
        ->autowire(false)
        ->autoconfigure( false)
    ;

    $services
        ->set('swoolefony.server')
            ->class(ServerInterface::class)
            ->synthetic()
        ->set(Factory::class)
            ->arg(
                '$cache',
                service(CacheInterface::class)
            )
            ->public()
    ;
};
