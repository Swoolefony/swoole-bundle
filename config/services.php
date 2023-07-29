<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Swoolefony\SwooleBundle\Command\ServerStopCommand;
use Swoolefony\SwooleBundle\Server\HandlerFactory;
use Swoolefony\SwooleBundle\Server\ServerFactory;
use Swoolefony\SwooleBundle\Server\ServerInterface;
use Swoolefony\SwooleBundle\Server\Task\Dispatcher;
use Symfony\Contracts\Cache\CacheInterface;

return function(ContainerConfigurator $container): void {
    $services = $container->services()
        ->defaults()
        ->autowire(false)
        ->autoconfigure( false)
    ;

    $services
        ->set(ServerInterface::class)
            ->synthetic()
        ->set(Dispatcher::class)
        ->set(HandlerFactory::class)
            ->arg(
                '$cache',
                service(CacheInterface::class)
            )
            ->arg(
                '$taskDispatcher',
                service(Dispatcher::class)
            )
        ->set(ServerFactory::class)
            ->arg(
                '$handlerFactory',
                service(HandlerFactory::class)
            )
            ->public()
        ->set(ServerStopCommand::class)
            ->arg(
                '$cache',
                service(CacheInterface::class)
            )
            ->tag('console.command')
    ;
};
