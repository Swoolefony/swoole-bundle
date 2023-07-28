<?php

declare(strict_types=1);

namespace Swoolefony\SwooleBundle\Command;

use Swoole\Process;
use Swoolefony\SwooleBundle\Server\CacheKey;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Contracts\Cache\CacheInterface;

final class ServerStopCommand extends Command
{
    private const SIGTERM = 15;

    private const SIGKILL = 9;

    public function __construct(private readonly CacheInterface $cache)
    {
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

    public function execute(
        InputInterface $input,
        OutputInterface $output
    ): int {
        /** @var int|null $serverPid */
        $serverPid = $this->cache->get(
            CacheKey::ServerPid->value,
            fn () => null,
        );
        $signal = $input->getOption('force')
            ? self::SIGKILL
            : self::SIGTERM;

        if ($serverPid === null)  {
            $output->writeln('<warning>The server is not running.</warning>');

            return self::FAILURE;
        }

        if (!Process::kill($serverPid, $signal)) {
            $output->writeln(sprintf(
                '<error>Unable to stop the server with PID %d.</error>',
                $serverPid
            ));

            return self::FAILURE;
        }

        // If it was force stopped the shutdown handler will not run, which would normally do this.
        if ($signal === self::SIGKILL) {
            $this->cache->delete(CacheKey::ServerPid->value);
        }

        return self::SUCCESS;
    }
}
