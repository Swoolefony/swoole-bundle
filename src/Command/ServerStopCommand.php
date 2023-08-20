<?php

declare(strict_types=1);

namespace Swoolefony\SwooleBundle\Command;

use Psr\Cache\CacheItemPoolInterface;
use Swoolefony\SwooleBundle\Server\CacheKey;
use Swoolefony\SwooleBundle\Server\Status;
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
        $cacheItem = $this->cache->getItem(CacheKey::ServerStatus->value);
        if (!$cacheItem->isHit())  {
            $output->writeln('<error>The server is not running.</error>');

            return self::FAILURE;
        }
        /** @var Status $serverStatus */
        $serverStatus = $cacheItem->get();
        $pids = [
            $serverStatus->getMainPid(),
            $serverStatus->getManagerPid(),
            $serverStatus->getWorkerPid(),
            $serverStatus->getPhpPid(),
        ];

        foreach ($pids as $pid) {
            // If we daemonized, we ignore the potentially null php pid.
            if ($pid === null) {
                continue;
            }

            $result = $input->getOption('force')
                ? $this->processTerminator->forceStop($pid)
                : $this->processTerminator->stop($pid);

            if (!$result) {
                $output->writeln(sprintf(
                    '<error>Unable to stop the server with PID %d.</error>',
                    $pid,
                ));

                return self::FAILURE;
            }
        }

        // If it was force stopped the shutdown handler will not run, which would normally do this.
        if ($input->getOption('force')) {
            $this->cache->deleteItem(CacheKey::ServerStatus->value);
            $this->cache->deleteItem(CacheKey::ServerPid->value);
        }

        return self::SUCCESS;
    }
}
