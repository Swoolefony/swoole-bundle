<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Swoolefony\SwooleBundle\Server\ServerInterface;

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
    ;
};
