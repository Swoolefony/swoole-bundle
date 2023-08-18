<?php

declare(strict_types=1);

namespace Swoolefony\SwooleBundle\Command;

use Psr\Cache\CacheItemPoolInterface;
use Swoole\Coroutine\System;
use Swoolefony\SwooleBundle\Server\CacheKey;
use Swoolefony\SwooleBundle\Swoole\ProcessTerminator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class ServerStopCommand extends Command
{
    public function __construct(
        private readonly CacheItemPoolInterface $cache,
        private readonly ProcessTerminator $processTerminator,
    ) {
        parent::__construct();
    }

    public function configure(): void
    {
        $this
            ->setName('swoolefony:server:stop')
            ->setHelp('Stop the currently running server.')
            ->addOption(
                name: 'force',
                description: "Force stop the server (using SIGKILL).",
            )
        ;
    }

    /**
     * @inheritDoc
     */
    public function execute(
        InputInterface $input,
        OutputInterface $output
    ): int {
        $cacheItem = $this->cache->getItem(CacheKey::ServerPid->value);
        if (!$cacheItem->isHit())  {
            $output->writeln('<error>The server is not running.</error>');

            return self::FAILURE;
        }
        /** @var int $serverPid */
        $serverPid = $cacheItem->get();
        $result = $input->getOption('force')
                ? $this->processTerminator->forceStop($serverPid)
                : $this->processTerminator->stop($serverPid);

        if (!$result) {
            $output->writeln(sprintf(
                '<error>Unable to stop the server with PID %d.</error>',
                $serverPid,
            ));

            return self::FAILURE;
        }

        // If it was force stopped the shutdown handler will not run, which would normally do this.
        if ($input->getOption('force')) {
            $this->cache->deleteItem(CacheKey::ServerPid->value);
        }

        return self::SUCCESS;
    }
}
