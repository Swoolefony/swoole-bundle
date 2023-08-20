<?php

declare(strict_types=1);

namespace Swoolefony\SwooleBundle\Command;

use DateTimeImmutable;
use DateTimeInterface;
use Psr\Cache\CacheItemPoolInterface;
use Swoolefony\SwooleBundle\Server\CacheKey;
use Swoolefony\SwooleBundle\Server\Status;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class ServerStatusCommand extends Command
{
    public function __construct(private readonly CacheItemPoolInterface $cache)
    {
        parent::__construct();
    }

    public function configure(): void
    {
        $this
            ->setName('swoolefony:server:status')
            ->setHelp('Check the status of the server.')
        ;
    }

    public function execute(
        InputInterface $input,
        OutputInterface $output
    ): int {
        $serverStatusItem = $this->cache->getItem(CacheKey::ServerStatus->value);

        if (!$serverStatusItem->isHit()) {
            $output->writeln('The server is not running.');

            return 1;
        }
        /** @var Status $serverStatus */
        $serverStatus = $serverStatusItem->get();
        $stats = $serverStatus->getStats()->toArray();

        $rowHeaders = [
            'ip',
            'port',
            'php_pid',
            'main_pid',
            'manager_pid',
            'worker_pid',
            ...array_keys($stats),
        ];
        $rowValues = [
            $serverStatus->getIp(),
            $serverStatus->getPort(),
            $serverStatus->getPhpPid(),
            $serverStatus->getMainPid(),
            $serverStatus->getManagerPid(),
            $serverStatus->getWorkerPid(),
            /** @phpstan-ignore-next-line */
            ...array_values(self::statusArrayTransform($stats)),
        ];

        (new Table($output))
            ->setHeaderTitle(sprintf(
                'The server is running on PID %d',
                $serverStatus->getPhpPid(),
            ))
            ->setHeaders($rowHeaders)
            ->setRows([$rowValues])
            ->setVertical()
            ->render()
        ;

        return 0;
    }

    /**
     * @param array<string, scalar|DateTimeImmutable> $status
     *
     * @return array<string, scalar|DateTimeImmutable>
     */
    private static function statusArrayTransform(array $status): array
    {
        foreach ($status as $key => $value) {
            if ($value instanceof DateTimeImmutable) {
                $status[$key] = $value->format(DateTimeInterface::ATOM);
            }
        }

        return $status;
    }
}
