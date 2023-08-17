<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Psr\Cache\CacheItemPoolInterface;
use Swoolefony\SwooleBundle\Command\ServerStatusCommand;
use Swoolefony\SwooleBundle\Command\ServerStopCommand;
use Swoolefony\SwooleBundle\Server\HandlerFactory;
use Swoolefony\SwooleBundle\Server\ServerFactory;
use Swoolefony\SwooleBundle\Server\ServerInterface;
use Swoolefony\SwooleBundle\Server\Task\Dispatcher;
use Swoolefony\SwooleBundle\Server\Task\Handler\ServerStatusUpdateHandler;

return function(ContainerConfigurator $container): void {
    $services = $container->services()
        ->defaults()
        ->autowire(false)
        ->autoconfigure( false)
    ;

    $services
        ->set(ServerInterface::class)
            ->synthetic()
        ->set(ServerStatusUpdateHandler::class)
            ->arg(
                '$cache',
                service(CacheItemPoolInterface::class)
            )
            ->arg(
                '$server',
                service(ServerInterface::class)
            )
            ->tag('swoolefony.server.task_handler')
        ->set(Dispatcher::class)
            ->args([tagged_iterator('swoolefony.server.task_handler')])
        ->set(HandlerFactory::class)
            ->arg(
                '$cache',
                service(CacheItemPoolInterface::class)
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
                service(CacheItemPoolInterface::class)
            )
            ->tag('console.command')
        ->set(ServerStatusCommand::class)
            ->arg(
                '$cache',
                service(CacheItemPoolInterface::class)
            )
            ->tag('console.command')
    ;
};
